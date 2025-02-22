<?php

namespace Capco\AppBundle\Controller\Site;

use Capco\AppBundle\CapcoAppBundleEvents;
use Capco\AppBundle\Entity\Comment;
use Capco\AppBundle\Enum\ModerationStatus;
use Capco\AppBundle\Event\CommentChangedEvent;
use Capco\AppBundle\Form\CommentType as CommentForm;
use Capco\AppBundle\GraphQL\DataLoader\Commentable\CommentableCommentsDataLoader;
use Capco\AppBundle\Manager\CommentResolver;
use Capco\AppBundle\Toggle\Manager;
use Capco\UserBundle\Security\Exception\ProjectAccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommentController extends Controller
{
    private EventDispatcherInterface $eventDispatcher;
    private CommentableCommentsDataLoader $commentableCommentsDataLoader;
    private CommentResolver $commentResolver;
    private TranslatorInterface $translator;
    private EntityManagerInterface $em;
    private SessionInterface $session;
    private Manager $manager;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        CommentableCommentsDataLoader $commentableCommentsDataLoader,
        CommentResolver $commentResolver,
        TranslatorInterface $translator,
        EntityManagerInterface $em,
        SessionInterface $session,
        Manager $manager
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->commentableCommentsDataLoader = $commentableCommentsDataLoader;
        $this->commentResolver = $commentResolver;
        $this->translator = $translator;
        $this->em = $em;
        $this->session = $session;
        $this->manager = $manager;
    }

    /**
     * @Route("/comments/{commentId}/edit", name="app_comment_edit")
     * @Template("CapcoAppBundle:Comment:update.html.twig")
     * @Entity("comment", class="CapcoAppBundle:Comment", options={"mapping" = {"commentId": "id"}, "repository_method"= "find", "map_method_signature" = true})
     * @Security("has_role('ROLE_USER')")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws ProjectAccessDeniedException
     */
    public function updateCommentAction(Request $request, Comment $comment)
    {
        if (false === $comment->canContribute($this->getUser())) {
            throw new ProjectAccessDeniedException(
                $this->translator->trans('comment.error.no_contribute', [], 'CapcoAppBundle')
            );
        }

        $isAuthorAdmin = $comment->getAuthor() ? $comment->getAuthor()->isAdmin() : false;
        $isModerationEnabled = $this->manager->isActive(Manager::moderation_comment);
        if (
            $isModerationEnabled &&
            $comment->isApproved() &&
            !$isAuthorAdmin
        ) {
            throw new ProjectAccessDeniedException(
                $this->translator->trans(
                    'cannot-edit-already-approved-comment',
                    [],
                    'CapcoAppBundle'
                )
            );
        }

        $userCurrent = $this->getUser();
        $userPostComment = $comment->getAuthor();

        if ($userCurrent !== $userPostComment) {
            throw new ProjectAccessDeniedException(
                $this->translator->trans('comment.error.not_author', [], 'CapcoAppBundle')
            );
        }

        $form = $this->createForm(CommentForm::class, $comment, ['actionType' => 'edit']);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            // We create a session for flashBag
            $flashBag = $this->session->getFlashBag();

            if ($form->isValid()) {
                $comment->resetVotes();
                $this->em->persist($comment);
                $this->em->flush();
                $this->eventDispatcher->dispatch(
                    CapcoAppBundleEvents::COMMENT_CHANGED,
                    new CommentChangedEvent($comment, 'update')
                );

                $flashBag->add('success', $this->translator->trans('comment.update.success'));

                return $this->redirect($this->commentResolver->getUrlOfRelatedObject($comment));
            }
            $flashBag->add('danger', $this->translator->trans('comment.update.error'));
        }

        return ['form' => $form->createView(), 'comment' => $comment];
    }

    /**
     * @Route("/comments/{commentId}/delete", name="app_comment_delete")
     * @Entity("comment", options={"mapping": {"commentId" : "id"}})
     * @Template("CapcoAppBundle:Comment:delete.html.twig")
     * @Security("has_role('ROLE_USER')")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws ProjectAccessDeniedException
     */
    public function deleteCommentAction(Request $request, Comment $comment)
    {
        if (false === $comment->canContribute($this->getUser())) {
            throw new ProjectAccessDeniedException(
                $this->translator->trans('comment.error.no_contribute', [], 'CapcoAppBundle')
            );
        }

        $userCurrent = $this->getUser()->getId();
        $userPostComment = $comment->getAuthor()->getId();

        if ($userCurrent !== $userPostComment) {
            throw new ProjectAccessDeniedException(
                $this->translator->trans('comment.error.not_author', [], 'CapcoAppBundle')
            );
        }

        //Champ CSRF
        $form = $this->createFormBuilder()->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            // We create a session for flashBag
            $flashBag = $this->session->getFlashBag();

            if ($form->isValid()) {
                $this->em->remove($comment);
                $this->eventDispatcher->dispatch(
                    CapcoAppBundleEvents::COMMENT_CHANGED,
                    new CommentChangedEvent($comment, 'remove')
                );
                $this->em->flush();

                $flashBag->add('info', $this->translator->trans('comment.delete.success'));

                $this->commentableCommentsDataLoader->invalidate(
                    $comment->getRelatedObject()->getId()
                );

                return $this->redirect($this->commentResolver->getUrlOfRelatedObject($comment));
            }
            $flashBag->add('danger', $this->translator->trans('comment.delete.error'));
        }

        return ['form' => $form->createView(), 'comment' => $comment];
    }
}
