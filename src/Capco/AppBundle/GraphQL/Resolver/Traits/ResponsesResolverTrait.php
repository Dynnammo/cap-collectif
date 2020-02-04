<?php

namespace Capco\AppBundle\GraphQL\Resolver\Traits;

use Capco\AppBundle\Entity\Reply;
use Capco\UserBundle\Entity\User;
use Capco\AppBundle\Entity\Proposal;
use Capco\AppBundle\Helper\EnvHelper;
use Doctrine\Common\Collections\Collection;
use Capco\AppBundle\Entity\ProposalEvaluation;
use Doctrine\Common\Collections\ArrayCollection;
use Capco\AppBundle\Entity\Questions\MediaQuestion;
use Capco\AppBundle\Entity\Responses\MediaResponse;
use Capco\AppBundle\Entity\Responses\ValueResponse;
use Capco\AppBundle\Entity\Questions\AbstractQuestion;
use Capco\AppBundle\Repository\AbstractQuestionRepository;
use Capco\AppBundle\Repository\AbstractResponseRepository;
use Capco\AppBundle\Entity\Questions\MultipleChoiceQuestion;

trait ResponsesResolverTrait
{
    /**
     * @var AbstractQuestionRepository
     */
    private $abstractQuestionRepository;
    /**
     * @var AbstractResponseRepository
     */
    private $abstractResponseRepository;

    public function filterVisibleResponses(
        iterable $responses,
        User $author,
        $viewer,
        \ArrayObject $context
    ): iterable {
        $skipVerification =
            $context &&
            $context->offsetExists('disable_acl') &&
            true === $context->offsetGet('disable_acl');
        $isAuthor = $author === $viewer;
        $viewerCanSeePrivateResponses =
            $skipVerification || $isAuthor || ($viewer instanceof User && $viewer->isAdmin());

        /**
         * Evaluers currently can not see responses on private questions.
         * 
         * This is a small hack to allow visibility for https://jeparticipe.meuse.fr/.
         * Because this client doesn't want evaluers to be administrators. 
         * 
         * See issue https://github.com/cap-collectif/platform/issues/9941
         * 
         * TODO: Remove me after.
         */
        $instanceName = EnvHelper::get('SYMFONY_INSTANCE_NAME');
        if ($instanceName === 'meuse' && $viewer instanceof User && $viewer->isEvaluer() ) {
            $viewerCanSeePrivateResponses = true;
        }

        return $responses->filter(function ($response) use ($viewerCanSeePrivateResponses) {
            return !$response->getQuestion()->isPrivate() || $viewerCanSeePrivateResponses;
        });
    }

    private function getResponsesForProposal(Proposal $proposal): Collection
    {
        $responses = new ArrayCollection(
            $this->abstractResponseRepository->getByProposal($proposal, true) ?? []
        );
        $questions = $this->abstractQuestionRepository->findByProposalForm($proposal->getForm());

        foreach ($questions as $question) {
            $this->handleQuestionResponses($question, $responses);
        }

        return $responses;
    }

    private function getResponsesForReply(Reply $reply): Collection
    {
        $responses = new ArrayCollection(
            $this->abstractResponseRepository->getByReply($reply, true) ?? []
        );
        $questions = $reply->getQuestionnaire()
            ? $this->abstractQuestionRepository->findByQuestionnaire($reply->getQuestionnaire())
            : new ArrayCollection();

        foreach ($questions as $question) {
            $this->handleQuestionResponses($question, $responses);
        }

        return $responses;
    }

    private function getResponsesForEvaluation(ProposalEvaluation $evaluation): Collection
    {
        $responses = new ArrayCollection(
            $this->abstractResponseRepository->getByEvaluation($evaluation, true) ?? []
        );
        $proposalForm = $evaluation->getProposal()->getProposalForm();
        $questions = $proposalForm->getEvaluationForm()
            ? $this->abstractQuestionRepository->findByQuestionnaire(
                $proposalForm->getEvaluationForm()
            )
            : new ArrayCollection();

        foreach ($questions as $question) {
            $this->handleQuestionResponses($question, $responses);
        }

        return $responses;
    }

    private function handleQuestionResponses(
        AbstractQuestion $question,
        Collection $responses
    ): void {
        $found = false;
        foreach ($responses as $response) {
            if ($response->getQuestion()->getId() === $question->getId()) {
                $found = true;
            }
        }
        if (!$found) {
            if ($question instanceof MediaQuestion) {
                $response = new MediaResponse();
            } elseif ($question instanceof MultipleChoiceQuestion) {
                $response = new ValueResponse();
                $value =
                    'select' === $question->getInputType()
                        ? null
                        : ['labels' => [], 'other' => null];
                $response->setValue($value);
            } else {
                $response = new ValueResponse();
            }
            $response->setQuestion($question);
            $responses->add($response);
        }
    }
}
