<?php

namespace Capco\AppBundle\Repository;

use Capco\AppBundle\Entity\Consultation;
use Capco\AppBundle\Entity\Theme;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ConsultationRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConsultationRepository extends EntityRepository
{
    /**
     * Get one by slug.
     *
     * @param $slug
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOne($slug)
    {
        $qb = $this->getIsEnabledQueryBuilder('c')
            ->addSelect('t', 'cas', 's', 'cov')
            ->leftJoin('c.Themes', 't', 'WITH', 't.isEnabled = :enabled')
            ->leftJoin('c.steps', 'cas')
            ->leftJoin('cas.step', 's')
            ->leftJoin('c.Cover', 'cov')
            ->andWhere('c.slug = :slug')
            ->andWhere('s.isEnabled = :enabled')
            ->setParameter('enabled', true)
            ->setParameter('slug', $slug);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get one by slug with steps, events and posts.
     *
     * @param $slug
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneBySlugWithStepsAndEventsAndPosts($slug)
    {
        $qb = $this->getIsEnabledQueryBuilder('c')
            ->addSelect('t', 'cas', 's', 'cov', 'p', 'e')
            ->leftJoin('c.Themes', 't')
            ->leftJoin('c.steps', 'cas')
            ->leftJoin('cas.step', 's')
            ->leftJoin('c.Cover', 'cov')
            ->leftJoin('c.posts', 'p', 'WITH', 'p.isPublished = :published')
            ->leftJoin('c.events', 'e', 'WITH', 'e.isEnabled = :enabled')
            ->andWhere('c.slug = :slug')
            ->andWhere('s.isEnabled = :enabled')
            ->setParameter('slug', $slug)
            ->setParameter('published', true)
            ->setParameter('enabled', true)
            ->addOrderBy('p.publishedAt', 'DESC')
            ->addOrderBy('e.startAt', 'DESC')
            ->addOrderBy('cas.position', 'ASC')
            ->addOrderBy('s.startAt', 'ASC')
        ;

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get search results.
     *
     * @param int  $nbByPage
     * @param int  $page
     * @param null $theme
     * @param null $sort
     * @param null $term
     *
     * @return Paginator
     */
    public function getSearchResults($nbByPage = 8, $page = 1, $theme = null, $sort = null, $term = null)
    {
        if ((int) $page < 1) {
            throw new \InvalidArgumentException(sprintf(
                'The argument "page" cannot be lower than 1 (current value: "%s")',
                $page
            ));
        }

        $qb = $this->getIsEnabledQueryBuilder()
            ->addSelect('t', 'cas', 's', 'cov')
            ->leftJoin('c.Themes', 't')
            ->leftJoin('c.steps', 'cas')
            ->leftJoin('cas.step', 's')
            ->leftJoin('c.Cover', 'cov')
            ->addOrderBy('c.publishedAt', 'DESC');

        if ($theme !== null && $theme !== Theme::FILTER_ALL) {
            $qb->andWhere('t.slug = :theme')
                ->setParameter('theme', $theme)
            ;
        }

        if ($term !== null) {
            $qb->andWhere('c.title LIKE :term')
                ->setParameter('term', '%'.$term.'%')
            ;
        }

        if (isset(Consultation::$sortOrder[$sort]) && Consultation::$sortOrder[$sort] == Consultation::SORT_ORDER_CONTRIBUTIONS_COUNT) {
            $qb = $this->getOrderedByContributionsNb($qb, 'DESC', 'c');
        } else {
            $qb->orderBy('c.publishedAt', 'DESC');
        }

        $query = $qb->getQuery();

        if ($nbByPage > 0) {
            $query->setFirstResult(($page - 1) * $nbByPage)
                ->setMaxResults($nbByPage);
        }

        return new Paginator($query);
    }

    /**
     * Count search results.
     *
     * @param null $themeSlug
     * @param null $term
     *
     * @return mixed
     */
    public function countSearchResults($themeSlug = null, $term = null)
    {
        $qb = $this->getIsEnabledQueryBuilder()
            ->select('COUNT(c.id)')
            ->innerJoin('c.Themes', 't')
        ;

        if ($themeSlug !== null && $themeSlug !== Theme::FILTER_ALL) {
            $qb->andWhere('t.slug = :themeSlug')
                ->setParameter('themeSlug', $themeSlug)
            ;
        }

        if ($term !== null) {
            $qb->andWhere('c.title LIKE :term')
                ->setParameter('term', '%'.$term.'%')
            ;
        }

        return $qb
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * Get last enabled consultations.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return Paginator
     */
    public function getLastPublished($limit = 1, $offset = 0)
    {
        $qb = $this->getIsEnabledQueryBuilder()
            ->addSelect('t', 'cas', 's', 'cov')
            ->leftJoin('c.Themes', 't')
            ->leftJoin('c.steps', 'cas')
            ->leftJoin('cas.step', 's')
            ->leftJoin('c.Cover', 'cov')
            ->addOrderBy('c.publishedAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return new Paginator($qb, $fetchJoin = true);
    }

    /**
     * Get last consultations by theme.
     *
     * @param theme
     * @param int $limit
     * @param int $offset
     *
     * @return mixed
     */
    public function getLastByTheme($themeId, $limit = null, $offset = null)
    {
        $qb = $this->getIsEnabledQueryBuilder()
            ->addSelect('cov', 't', 'cas', 's')
            ->leftJoin('c.Cover', 'cov')
            ->leftJoin('c.Themes', 't')
            ->leftJoin('c.steps', 'cas')
            ->leftJoin('cas.step', 's')
            ->andWhere(':theme MEMBER OF c.Themes')
            ->setParameter('theme', $themeId)
            ->orderBy('c.publishedAt', 'ASC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    protected function getIsEnabledQueryBuilder()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isEnabled = :isEnabled')
            ->setParameter('isEnabled', true);
    }

    protected function getOrderedByContributionsNb(QueryBuilder $qb, $order, $alias = 'c')
    {
        $qb
            ->addSelect('('.$alias.'.opinionCount + '.$alias.'.trashedOpinionCount + '.$alias.'.argumentCount + '.$alias.'.trashedArgumentCount + '.$alias.'.sourcesCount + '.$alias.'.trashedSourceCount) as HIDDEN contributionsCount')
            ->orderBy('contributionsCount', 'DESC')
        ;

        return $qb;
    }
}
