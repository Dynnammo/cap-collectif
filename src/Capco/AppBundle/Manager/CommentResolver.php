<?php

namespace Capco\AppBundle\Manager;


use Capco\AppBundle\Entity\Event;
use Capco\AppBundle\Entity\EventComment;
use Capco\AppBundle\Entity\IdeaComment;
use Capco\AppBundle\Entity\AbstractComment as Comment;
use Capco\AppBundle\Repository\CommentRepository;
use Capco\AppBundle\Entity\Idea;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;

class CommentResolver
{
    protected $em;
    protected $router;

    public function __construct(EntityManager $em, Router $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function createCommentForType($objectType)
    {
        $comment = null;
        if ($objectType == 'Idea') {
            $comment = new IdeaComment();
        } else if ($objectType == 'Event') {
            $comment = new EventComment();
        }
        return $comment;
    }

    public function getObjectByTypeAndId($objectType, $objectId)
    {
        $object = null;
        if ($objectType == 'Idea'){
            $object = $this->em->getRepository('CapcoAppBundle:Idea')->find($objectId);
        }
        if ($objectType == 'Event'){
            $object = $this->em->getRepository('CapcoAppBundle:Event')->find($objectId);
        }
        return $object;
    }

    public function getCommentsByObject($object)
    {
        if ($object instanceof Idea) {
            return $this->em->getRepository('CapcoAppBundle:IdeaComment')->getEnabledByIdea($object);
        }

        if ($object instanceof Event) {
            return $this->em->getRepository('CapcoAppBundle:EventComment')->getEnabledByEvent($object);
        }
        return null;

    }

    public function getRelatedObject(Comment $comment)
    {
        return $comment->getRelatedObject();

    }

    public function getUrlOfObjectByTypeAndId($objectType, $objectId, $absolute = false)
    {
        $object = $this->getObjectByTypeAndId($objectType, $objectId);
        return $this->getUrlOfObject($object, $absolute);
    }

    public function getUrlOfObject($object, $absolute = false)
    {
        if ($object instanceof Idea) {
            return $this->router->generate('app_idea_show', array('slug' => $object->getSlug()), $absolute);
        }

        if ($object instanceof Event) {
            return $this->router->generate('app_event_show', array('slug' => $object->getSlug()), $absolute);
        }

        return $this->router->generate('app_homepage', array(), $absolute);
    }

    public function getAdminUrlOfObject($object, $absolute = false)
    {
        if ($object instanceof Idea) {
            return $this->router->generate('admin_capco_app_idea_show', array('id' => $object->getId()), $absolute);
        }

        if ($object instanceof Event) {
            return $this->router->generate('admin_capco_app_event_show', array('id' => $object->getId()), $absolute);
        }

        return '';
    }

    public function getUrlOfRelatedObject(Comment $comment, $absolute = false)
    {
        $object = $this->getRelatedObject($comment);
        return $this->getUrlOfObject($object, $absolute);

    }

    public function getAdminUrlOfRelatedObject(Comment $comment, $absolute = false)
    {
        $object = $this->getRelatedObject($comment);
        return $this->getAdminUrlOfObject($object, $absolute);

    }

    public function canShowCommentOn($object)
    {
        return $object->getIsCommentable();
    }

    public function canAddCommentOn($object)
    {
        return $object->canComment();
    }

    public function setObjectOnComment($object, Comment $comment)
    {
        if ($object instanceof Idea) {
            $comment->setIdea($object);
        } else if ($object instanceof Event) {
            $comment->setEvent($object);
        }
        return $comment;

    }
}
