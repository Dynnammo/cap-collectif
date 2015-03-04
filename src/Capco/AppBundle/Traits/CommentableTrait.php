<?php

namespace Capco\AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

use Capco\AppBundle\Entity\AbstractComment;

trait CommentableTrait
{
    /**
     * @var integer
     *
     * @ORM\Column(name="comments_count", type="integer")
     */
    private $commentsCount = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_commentable", type="boolean")
     */
    private $isCommentable = true;


    abstract public function getClassName();

    public function increaseCommentsCount($nb) {
        $this->commentsCount += $nb;
    }

    public function decreaseCommentsCount($nb) {
        if ($this->commentsCount >= $nb) {
            $this->commentsCount -= $nb;
        }
    }

    /**
     * @return int
     */
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    /**
     * @param int $commentsCount
     */
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param $comment
     * @return $this
     */
    public function addComment(AbstractComment $comment)
    {
        if (!$this->comments->contains($comment)) {
            $this->increaseCommentsCount(1);
            $this->comments->add($comment);
        }
        return $this;
    }

    /**
     * @param $comment
     * @return $this
     */
    public function removeComment(AbstractComment $comment)
    {
        if ($this->comments->removeElement($comment)) {
            $this->decreaseCommentsCount(1);
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsCommentable()
    {
        return $this->isCommentable;
    }

    /**
     * @param boolean $isCommentable
     */
    public function setIsCommentable($isCommentable)
    {
        $this->isCommentable = $isCommentable;
    }

    /**
     * @return bool
     */
    public function canDisplay() {
        return $this->isEnabled;
    }

    /**
     * @return bool
     */
    public function canContribute() {
        return $this->isEnabled && (!isset($this->isTrashed) || !$this->isTrashed);
    }

    /**
     * @return bool
     */
    public function canComment() {
        return $this->canContribute() && $this->isCommentable;
    }
}