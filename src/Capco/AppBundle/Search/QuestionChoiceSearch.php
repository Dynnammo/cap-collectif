<?php

namespace Capco\AppBundle\Search;

use Capco\AppBundle\Elasticsearch\ElasticsearchPaginatedResult;
use Capco\AppBundle\Repository\QuestionChoiceRepository;
use Elastica\Index;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;

class QuestionChoiceSearch extends Search
{
    private $choiceRepository;

    public function __construct(Index $index, QuestionChoiceRepository $choiceRepository)
    {
        parent::__construct($index);
        $this->type = 'questionChoice';
        $this->choiceRepository = $choiceRepository;
    }

    public function searchQuestionChoices(array $questionDatas): array
    {
        $client = $this->index->getClient();
        $multiSearchQuery = new \Elastica\Multi\Search($client);

        foreach ($questionDatas as $questionData) {
            $searchQuery = new \Elastica\Search($client);
            $searchQuery->addType($this->type);
            $boolQuery = new BoolQuery();

            list($term, $cursor, $limit, $random, $isRandomQuestionChoices, $seed) = [
                $questionData['args']->offsetGet('term'),
                $questionData['args']->offsetGet('after'),
                $questionData['args']->offsetGet('first'),
                $questionData['args']->offsetGet('allowRandomize'),
                $questionData['isRandomQuestionChoices'],
                $questionData['seed'] ?: null
            ];
            $boolQuery->addFilter(new Term(['question.id' => $questionData['id']]));

            if ($random && $isRandomQuestionChoices) {
                $query = $this->getRandomSortedQuery($boolQuery, $seed);
                $query->setSort(['_score' => new \stdClass(), 'id' => new \stdClass()]);
            } else {
                $query = new Query();
                if (!$term) {
                    $functionScore = new Query\FunctionScore();
                    $functionScore->addFieldValueFactorFunction(
                        'position',
                        2.5,
                        Query\FunctionScore::FIELD_VALUE_FACTOR_MODIFIER_NONE,
                        1
                    );
                    $functionScore->setQuery($boolQuery);
                    $query->setQuery($functionScore);
                    $query->setSort(['_score' => ['order' => 'asc'], 'id' => new \stdClass()]);
                } else {
                    $this->fuzzyMatchOnLevenshteinDistanceScore($boolQuery, 'label', $term);
                    $query->setQuery($boolQuery);
                    $query->setSort(['_score' => ['order' => 'desc'], 'id' => new \stdClass()]);
                }
            }

            if ($cursor) {
                $this->applyCursor($query, $cursor);
            }
            if ($limit) {
                $query->setSize($limit + 1);
            }
            $searchQuery->setQuery($query);
            $multiSearchQuery->addSearch($searchQuery);
        }

        $responses = $multiSearchQuery->search();
        $results = [];
        foreach ($responses->getResultSets() as $key => $resultSet) {
            $results[] = new ElasticsearchPaginatedResult(
                $this->getHydratedResultsFromResultSet($this->choiceRepository, $resultSet),
                $this->getCursors($resultSet),
                $resultSet->getTotalHits()
            );
        }

        return $results;
    }
}
