<?php

namespace Capco\AppBundle\Repository;

use Capco\AppBundle\Entity\Debate\Debate;
use Capco\AppBundle\Entity\Debate\DebateArgument;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method DebateArgument|null find($id, $lockMode = null, $lockVersion = null)
 * @method DebateArgument|null findOneBy(array $criteria, array $orderBy = null)
 * @method DebateArgument[]    findAll()
 * @method DebateArgument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DebateArgumentRepository extends EntityRepository
{
    public function getByDebate(
        Debate $debate,
        int $limit,
        int $offset,
        array $filters = [],
        ?array $orderBy = null
    ): Paginator {
        $qb = $this
            ->getByDebateQueryBuilder($debate, $filters)
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        if ($orderBy) {
            $qb->orderBy('argument.'.$orderBy['field'], $orderBy['direction']);
        }

        return new Paginator($qb);
    }

    public function countByDebate(
        Debate $debate,
        array $filters = []
    ): int {
        $query = $this
            ->getByDebateQueryBuilder($debate, $filters)
            ->select('COUNT(argument)')
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    private function getByDebateQueryBuilder(
        Debate $debate,
        array $filters = []
    ): QueryBuilder {
        $qb = $this
            ->createQueryBuilder('argument')
            ->where('argument.debate = :debate')
            ->setParameter('debate', $debate);
        if (isset($filters['value'])) {
            $qb
                ->andWhere('argument.type = :value')
                ->setParameter('value', $filters['value']);
        }
        if (isset($filters['publishedOnly'])) {
            $qb->andWhere('argument.published = true');
        }

        return $qb;
    }
}
