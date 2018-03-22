<?php

namespace Capco\AppBundle\GraphQL\Resolver;

use Capco\AppBundle\Entity\Post;
use Capco\AppBundle\Entity\Project;
use Capco\AppBundle\Entity\Proposal;
use Capco\AppBundle\Entity\Responses\AbstractResponse;
use Capco\AppBundle\Entity\Responses\MediaResponse;
use Capco\AppBundle\Entity\Responses\ValueResponse;
use Capco\AppBundle\Entity\Steps\AbstractStep;
use Capco\AppBundle\Entity\Steps\CollectStep;
use Capco\AppBundle\Entity\Steps\ConsultationStep;
use Capco\AppBundle\Entity\Steps\OtherStep;
use Capco\AppBundle\Entity\Steps\PresentationStep;
use Capco\AppBundle\Entity\Steps\QuestionnaireStep;
use Capco\AppBundle\Entity\Steps\RankingStep;
use Capco\AppBundle\Entity\Steps\SelectionStep;
use Capco\AppBundle\Entity\Steps\SynthesisStep;
use Capco\UserBundle\Entity\User;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProposalResolver implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolveViewerIsEvaluer(Proposal $proposal, $user): bool
    {
        $repo = $this->container->get('capco.proposal.repository');

        return $user instanceof User ? $repo->isViewerAnEvaluer($proposal, $user) : false;
    }

    public function resolveProjectSteps(Project $project)
    {
        return $project->getRealSteps();
    }

    public function resolveShowUrlBySlug(string $projectSlug, string $stepSlug, string $proposalSlug)
    {
        $router = $this->container->get('router');

        return $router->generate('app_project_show_proposal', compact('projectSlug', 'stepSlug', 'proposalSlug'),
            UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function resolvePostAbstract(Post $post): string
    {
        return $post->getAbstractOrBeginningOfTheText();
    }

    public function resolveNews(Proposal $proposal)
    {
        $postRepo = $this->container->get('capco.blog.post.repository');

        return $postRepo->getPublishedPostsByProposal($proposal);
    }

    public function resolveStepType(AbstractStep $step)
    {
        $typeResolver = $this->container->get('overblog_graphql.type_resolver');
        if ($step instanceof SelectionStep) {
            return $typeResolver->resolve('SelectionStep');
        }
        if ($step instanceof CollectStep) {
            return $typeResolver->resolve('CollectStep');
        }
        if ($step instanceof PresentationStep) {
            return $typeResolver->resolve('PresentationStep');
        }
        if ($step instanceof QuestionnaireStep) {
            return $typeResolver->resolve('QuestionnaireStep');
        }
        if ($step instanceof ConsultationStep) {
            return $typeResolver->resolve('Consultation');
        }
        if ($step instanceof OtherStep) {
            return $typeResolver->resolve('OtherStep');
        }
        if ($step instanceof SynthesisStep) {
            return $typeResolver->resolve('SynthesisStep');
        }
        if ($step instanceof RankingStep) {
            return $typeResolver->resolve('RankingStep');
        }

        throw new UserError('Could not resolve type of Step.');
    }

    public function resolveResponseType(AbstractResponse $response)
    {
        $typeResolver = $this->container->get('overblog_graphql.type_resolver');
        if ($response instanceof MediaResponse) {
            return $typeResolver->resolve('MediaResponse');
        }
        if ($response instanceof ValueResponse) {
            return $typeResolver->resolve('ValueResponse');
        }

        throw new UserError('Could not resolve type of Response.');
    }

    public function resolveProposalPublicationStatus(Proposal $proposal): string
    {
        if ($proposal->isDraft()) {
            return 'DRAFT';
        }
        if ($proposal->isDeleted()) {
            return 'DELETED';
        }
        if ($proposal->isExpired()) {
            return 'EXPIRED';
        }

        if ($proposal->isTrashed()) {
            if ($proposal->isEnabled()) {
                return 'TRASHED';
            }

            return 'TRASHED_NOT_VISIBLE';
        }

        return 'PUBLISHED';
    }

    public function resolveShowUrl(Proposal $proposal): string
    {
        $step = $proposal->getStep();
        $project = $step->getProject();
        if (!$project) {
            return '';
        }

        return $this->container->get('router')->generate('app_project_show_proposal',
            [
                'proposalSlug' => $proposal->getSlug(),
                'projectSlug' => $project->getSlug(),
                'stepSlug' => $step->getSlug(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function resolveAdminUrl(Proposal $proposal): string
    {
        return $this->container->get('router')->generate(
            'admin_capco_app_proposal_edit',
            [
                'id' => $proposal->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function resolveReference(Proposal $proposal): string
    {
        return $proposal->getFullReference();
    }

    public function resolveEvaluation(Proposal $proposal)
    {
        return $proposal->getProposalEvaluation();
    }

    public function resolveDraftProposalsForUserInStep(string $stepId, $user): array
    {
        if (!$user instanceof User) {
            return [];
        }

        $proposalRep = $this->container->get('capco.proposal.repository');

        $proposalForm = $this->container->get('capco.proposal_form.repository')->findOneBy([
            'step' => $stepId,
        ]);

        if (!$proposalForm) {
            throw new UserError(sprintf('Unknown proposal form for step "%d"', $stepId));
        }

        $proposals = $proposalRep->findBy([
            'draft' => true,
            'author' => $user,
            'proposalForm' => $proposalForm,
        ]);

        return $proposals;
    }

    public function resolvePostsCount(Proposal $proposal): int
    {
        return $this->container->get('capco.blog.post.repository')->countPublishedPostsByProposal($proposal);
    }

    public function resolveViewerCanSeeEvaluation(Proposal $proposal): bool
    {
        $evalForm = $proposal->getProposalForm()->getEvaluationForm();

        return null !== $evalForm && !$evalForm->isFullyPrivate();
    }
}
