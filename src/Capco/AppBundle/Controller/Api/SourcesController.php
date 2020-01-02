<?php

namespace Capco\AppBundle\Controller\Api;

use Capco\AppBundle\Entity\Source;
use Capco\AppBundle\Entity\Reporting;
use Capco\AppBundle\Form\ReportingType;
use Capco\AppBundle\Entity\OpinionVersion;
use Capco\AppBundle\GraphQL\Resolver\GlobalIdResolver;
use Capco\AppBundle\Model\Contribution;
use Capco\AppBundle\Notifier\ReportNotifier;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SourcesController extends AbstractFOSRestController
{
    private $globalIdResolver;

    public function __construct(GlobalIdResolver $globalIdResolver)
    {
        $this->globalIdResolver = $globalIdResolver;
    }

    /**
     * @Post("/opinions/{opinionId}/sources/{sourceId}/reports")
     * @View(statusCode=201, serializerGroups={"Default"})
     */
    public function postOpinionSourceReportAction(
        Request $request,
        string $opinionId,
        string $sourceId
    ) {
        $viewer = $this->getUser();
        $opinion = $this->getContributionFromGlobalId($opinionId, $viewer);
        /** @var Source $source */
        $source = $this->getContributionFromGlobalId($sourceId, $viewer);
        if (!$viewer || 'anon.' === $viewer) {
            throw new AccessDeniedHttpException('Not authorized.');
        }

        if ($viewer === $source->getAuthor()) {
            throw $this->createAccessDeniedException();
        }

        if ($source->getOpinion() !== $opinion) {
            throw new BadRequestHttpException('Not a child.');
        }

        return $this->createReport($request, $source);
    }

    /**
     * @Post("/opinions/{opinionId}/versions/{versionId}/sources/{sourceId}/reports")
     * @Entity("version", options={"mapping": {"versionId": "id"}})
     * @View(statusCode=201, serializerGroups={"Default"})
     */
    public function postOpinionVersionSourceReportAction(
        Request $request,
        OpinionVersion $version,
        string $opinionId,
        string $sourceId
    ) {
        $viewer = $this->getUser();
        $opinion = $this->getContributionFromGlobalId($opinionId, $viewer);
        /** @var Source $source */
        $source = $this->getContributionFromGlobalId($sourceId, $viewer);
        if (!$viewer || 'anon.' === $viewer) {
            throw new AccessDeniedHttpException('Not authorized.');
        }

        if ($viewer === $source->getAuthor()) {
            throw $this->createAccessDeniedException();
        }

        if ($source->getOpinionVersion() !== $version) {
            throw new BadRequestHttpException('Not a child.');
        }

        return $this->createReport($request, $source);
    }

    private function createReport(Request $request, Source $source)
    {
        $report = (new Reporting())->setReporter($this->getUser())->setSource($source);
        $form = $this->createForm(ReportingType::class, $report, ['csrf_protection' => false]);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($report);
        $em->flush();

        $this->get(ReportNotifier::class)->onCreate($report);

        return $report;
    }

    private function getContributionFromGlobalId(
        string $contributionGlobalId,
        $viewer
    ): Contribution {
        $contribution = $this->globalIdResolver->resolve($contributionGlobalId, $viewer);

        if (null === $contribution) {
            throw new EntityNotFoundException('This contribution does not exist.');
        }

        return $contribution;
    }
}
