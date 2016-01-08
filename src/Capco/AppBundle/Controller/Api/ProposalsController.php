<?php

namespace Capco\AppBundle\Controller\Api;

use Capco\AppBundle\Entity\Proposal;
use Capco\AppBundle\Entity\ProposalForm;
use Capco\AppBundle\Entity\ProposalComment;
use Capco\AppBundle\Event\ProposalEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Capco\AppBundle\Form\CommentType;
use Capco\AppBundle\Event\CommentChangedEvent;
use Capco\AppBundle\CapcoAppBundleEvents;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Assert;

class ProposalsController extends FOSRestController
{
    /**
     * @Post("/proposal_forms/{proposal_form_id}/proposals/search")
     * @ParamConverter("proposalForm", options={"mapping": {"proposal_form_id": "id"}})
     * @QueryParam(name="page", requirements="[0-9.]+", default="1")
     * @QueryParam(name="pagination", requirements="[0-9.]+", default="100")
     * @QueryParam(name="order", requirements="(old|last|popular|comments)", default="last")
     * @View(statusCode=200, serializerGroups={"Proposals", "ProposalResponses", "UsersInfos", "UserMedias"})
     *
     * @param Request               $request
     * @param ProposalForm          $proposalForm
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return array
     */
    public function getProposalsByFormAction(Request $request, ProposalForm $proposalForm, ParamFetcherInterface $paramFetcher)
    {
        $page = intval($paramFetcher->get('page'));
        $pagination = intval($paramFetcher->get('pagination'));
        $order = $paramFetcher->get('order');

        $terms = $request->request->has('terms') ? $request->request->get('terms') : null;

        // Filters
        $providedFilters = $request->request->has('filters') ? $request->request->get('filters') : [];
        $providedFilters['proposalForm'] = $proposalForm->getId();

        return $this->get('capco.search.resolver')->searchProposals($page, $pagination, $order, $terms, $providedFilters);
    }

    /**
     * Get a proposal.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get a proposal",
     *  statusCodes={
     *    200 = "Returned when successful",
     *    404 = "Returned when opinion is not found",
     *  }
     * )
     *
     * @Get("/proposal_forms/{proposal_form_id}/proposals/{proposal_id}")
     * @ParamConverter("proposalForm", options={"mapping": {"proposal_form_id": "id"}, "repository_method": "find", "map_method_signature": true})
     * @ParamConverter("proposal", options={"mapping": {"proposal_id": "id"}, "repository_method": "find", "map_method_signature": true})
     * @View(statusCode=200, serializerGroups={"Proposals", "ProposalResponses", "UsersInfos", "UserMedias", "Themes"})
     *
     * @param ProposalForm $proposalForm
     * @param Proposal     $proposal
     *
     * @return array
     */
    public function getProposalAction(ProposalForm $proposalForm, Proposal $proposal)
    {
        return $proposal;
    }

    /**
     * Add a proposal.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Post a proposal",
     *  statusCodes={
     *    201 = "Returned when successful",
     *    401 = "Proposal does not exist",
     *  }
     * )
     *
     * @Security("has_role('ROLE_USER')")
     * @Post("/proposal_forms/{proposal_form_id}/proposals")
     * @ParamConverter("proposalForm", options={"mapping": {"proposal_form_id": "id"}, "repository_method": "getOne", "map_method_signature": true})
     * @View(statusCode=201, serializerGroups={"ProposalForms", "Proposals", "ProposalResponses", "UsersInfos", "UserMedias"})
     *
     * @param Request      $request
     * @param ProposalForm $proposalForm
     *
     * @return Form
     */
    public function postProposalAction(Request $request, ProposalForm $proposalForm)
    {
        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');

        if (!$proposalForm->canContribute()) {
            throw new BadRequestHttpException('You can no longer contribute to this collect step.');
        }

        $proposal = (new Proposal())
            ->setAuthor($user)
            ->setProposalForm($proposalForm)
            ->setEnabled(true)
        ;

        $defaultStatuses = $this->getDoctrine()->getManager()
            ->getRepository('CapcoAppBundle:Status')
            ->getByCollectStep($proposalForm->getStep())
        ;

        if ($defaultStatuses && !empty($defaultStatuses)) {
            $proposal->setStatus($defaultStatuses[0]);
        }

        $form = $this->createForm('proposal', $proposal);
        $form->submit($request->request->all());

        $questions = $proposal->getProposalForm()->getQuestions();
        $validationRulesForResponses = $this->getValidationRulesForProposalResponses($questions);
        $proposalResponses = $request->get('proposalResponses');

        foreach($proposalResponses as $proposalResponse) {
            if (array_key_exists($proposalResponse['question'], $validationRulesForResponses)) {
                $errorList = $this->get('validator')->validate(
                    $proposalResponse['value'],
                    $validationRulesForResponses[$proposalResponse['question']]['constraint']
                );
                if (count($errorList) > 0) {
                    $form->addError((new FormError($errorList[0]->getMessage())));
                }
            }
        }

        if (!$form->isValid()) {
            return $form;
        }

        $em->persist($proposal);
        $em->flush();

        return $proposal;
    }

    /**
     * @Get("/proposal_forms/{form}/proposals/{proposal}/comments")
     * @ParamConverter("form", options={"mapping": {"form": "id"}})
     * @ParamConverter("proposal", options={"mapping": {"proposal": "id"}})
     * @QueryParam(name="offset", requirements="[0-9.]+", default="0")
     * @QueryParam(name="limit", requirements="[0-9.]+", default="10")
     * @QueryParam(name="filter", requirements="(old|last|popular)", default="last")
     * @View(serializerGroups={"Comments", "UsersInfos"})
     */
    public function getProposalCommentsAction(ProposalForm $form, Proposal $proposal, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $filter = $paramFetcher->get('filter');

        $paginator = $this->getDoctrine()->getManager()
                    ->getRepository('CapcoAppBundle:ProposalComment')
                    ->getEnabledByProposal($proposal, $offset, $limit, $filter);

        $comments = [];
        foreach ($paginator as $comment) {
            $comments[] = $comment;
        }

        $countWithAnswers = $this->getDoctrine()->getManager()
                      ->getRepository('CapcoAppBundle:ProposalComment')
                      ->countCommentsAndAnswersEnabledByProposal($proposal);

        return [
            'comments_and_answers_count' => intval($countWithAnswers),
            'comments_count' => count($paginator),
            'comments' => $comments,
        ];
    }

    /**
     * @Post("/proposal_forms/{form}/proposals/{proposal}/comments")
     * @ParamConverter("form", options={"mapping": {"form": "id"}})
     * @ParamConverter("proposal", options={"mapping": {"proposal": "id"}})
     * @View(statusCode=201, serializerGroups={"Comments", "UsersInfos"})
     */
    public function postProposalCommentsAction(Request $request, ProposalForm $form, Proposal $proposal)
    {
        if (!$proposal->canComment()) {
            throw new BadRequestHttpException('You can not comment this proposal.');
        }

        $user = $this->getUser();

        $comment = (new ProposalComment())
                    ->setAuthorIp($request->getClientIp())
                    ->setAuthor($user)
                    ->setProposal($proposal)
                    ->setIsEnabled(true)
                ;

        $form = $this->createForm(new CommentType($user), $comment);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $form;
        }

        $parent = $comment->getParent();
        if ($parent) {
            if (!$parent instanceof ProposalComment || $proposal != $parent->getProposal()) {
                throw $this->createNotFoundException('This parent comment is not linked to this proposal');
            }
            if ($parent->getParent() != null) {
                throw new BadRequestHttpException('You can\'t answer the answer of a comment.');
            }
        }

        $proposal->setCommentsCount($proposal->getCommentsCount() + 1);
        $this->getDoctrine()->getManager()->persist($comment);
        $this->getDoctrine()->getManager()->flush();
        $this->get('event_dispatcher')->dispatch(
            CapcoAppBundleEvents::COMMENT_CHANGED,
            new CommentChangedEvent($comment, 'add')
        );
    }

    /**
     * Update a proposal.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Update a proposal",
     *  statusCodes={
     *    200 = "Returned when successful",
     *    404 = "Returned when proposal is not found",
     *  }
     * )
     *
     * @Security("has_role('ROLE_USER')")
     * @Put("/proposal_forms/{proposal_form_id}/proposals/{proposal_id}")
     * @ParamConverter("proposalForm", options={"mapping": {"proposal_form_id": "id"}, "repository_method": "find", "map_method_signature": true})
     * @ParamConverter("proposal", options={"mapping": {"proposal_id": "id"}, "repository_method": "find", "map_method_signature": true})
     * @View(statusCode=200)
     *
     * @param ProposalForm $proposalForm
     * @param Proposal     $proposal
     *
     * @return bool
     */
    public function putProposalAction(Request $request, ProposalForm $proposalForm, Proposal $proposal)
    {
        if (!$proposal->canContribute()) {
            throw new BadRequestHttpException('This proposal is no longer editable.');
        }

        $user = $this->getUser();
        if ($user !== $proposal->getAuthor()) {
            throw new AccessDeniedException();
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $form = $this->createForm('proposal', $proposal);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $em->persist($proposal);
            $em->flush();

            return $proposal;
        }

        $view = $this->view($form->getErrors(true), Codes::HTTP_BAD_REQUEST);

        return $view;
    }

    /**
     * Delete a proposal.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a proposal",
     *  statusCodes={
     *    200 = "Returned when successful",
     *    404 = "Returned when proposal is not found",
     *  }
     * )
     *
     * @Security("has_role('ROLE_USER')")
     * @Delete("/proposal_forms/{proposal_form_id}/proposals/{proposal_id}")
     * @ParamConverter("proposalForm", options={"mapping": {"proposal_form_id": "id"}, "repository_method": "find", "map_method_signature": true})
     * @ParamConverter("proposal", options={"mapping": {"proposal_id": "id"}, "repository_method": "find", "map_method_signature": true})
     * @View(statusCode=204)
     *
     * @param ProposalForm $proposalForm
     * @param Proposal     $proposal
     *
     * @return bool
     */
    public function deleteProposalAction(ProposalForm $proposalForm, Proposal $proposal)
    {
        if ($this->getUser() !== $proposal->getAuthor()) {
            throw new BadRequestHttpException('You are not the author of this proposal');
        }

        $em = $this->getDoctrine()->getManager();

        if (!$proposal) {
            throw $this->createNotFoundException('Proposal not found');
        }

        $em->remove($proposal);
        $em->flush();

        $this->get('event_dispatcher')->dispatch(
            CapcoAppBundleEvents::PROPOSAL_DELETED,
            new ProposalEvent($proposal, 'remove')
        );

        return [];
    }

    private function getValidationRulesForProposalResponses($questions) {
        $rules = [];

        foreach($questions as $question) {
            if ($question->isRequired()) {
                $rules[$question->getId()]['constraint'] = new Assert\NotBlank();
            }
        }

        return $rules;
    }
}
