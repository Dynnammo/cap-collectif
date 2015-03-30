<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Capco\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends ).
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
class User extends BaseUser implements EncoderAwareInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var int
     */
    protected $facebook_id;

    /**
     * @var int
     */
    protected $facebook_access_token;

    /**
     * @var int
     */
    protected $google_id;

    /**
     * @var int
     */
    protected $google_access_token;

    /**
     * @var string
     */
    protected $twitter_id;

    /**
     * @var string
     */
    protected $twitter_access_token;

    /**
     * @var Capco\MediaBundle\Entity\Media
     */
    protected $Media;

    /**
     * @var bool
     */
    protected $isTermsAccepted = false;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $encoder;

    protected $opinions;

    protected $ideas;

    protected $comments;

    protected $arguments;

    protected $votes;

    protected $sources;

    /**
     * @var integer
     */
    protected $votesCount = 0;

    /**
     * @var integer
     */
    protected $sourcesCount = 0;

    /**
     * @var integer
     */
    protected $argumentsCount = 0;

    /**
     * @var integer
     */
    protected $commentsCount = 0;

    /**
     * @var integer
     */
    protected $ideasCount = 0;

    /**
     * @var integer
     */
    protected $opinionsCount = 0;

    public function __construct($encoder = null)
    {
        parent::__construct();

        $this->encoder = $encoder;
        $this->roles = array('ROLE_USER');
        $this->opinions = new ArrayCollection();
        $this->ideas = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->arguments = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->sources = new ArrayCollection();
    }

    public function getEncoderName()
    {
        return $this->encoder;
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return int
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * @param int $google_id
     */
    public function setGoogleId($google_id)
    {
        $this->google_id = $google_id;
    }

    /**
     * @return int
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * @param int $facebook_access_token
     */
    public function setFacebookAccessToken($facebook_access_token)
    {
        $this->facebook_access_token = $facebook_access_token;
    }

    /**
     * @return int
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * @param int $facebook_id
     */
    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = $facebook_id;
    }

    /**
     * @return int
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * @param int $google_access_token
     */
    public function setGoogleAccessToken($google_access_token)
    {
        $this->google_access_token = $google_access_token;
    }

    public function setTwitterId($twitter_id)
    {
        $this->twitter_id = $twitter_id;

        return $this;
    }

    public function getTwitterId()
    {
        return $this->twitter_id;
    }

    public function setTwitterAccessToken($twitter_access_token)
    {
        $this->twitter_access_token = $twitter_access_token;
    }

    public function getTwitterAccessToken()
    {
        return $this->twitter_access_token;
    }

    /**
     * Set media.
     *
     * @param \Capco\MediaBundle\Entity\Media $media
     *
     * @return User
     */
    public function setMedia(\Capco\MediaBundle\Entity\Media $media = null)
    {
        $this->Media = $media;

        return $this;
    }

    /**
     * Get media.
     *
     * @return \Capco\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->Media;
    }

    /**
     * @return bool
     */
    public function getIsTermsAccepted()
    {
        return $this->isTermsAccepted;
    }

    /**
     * @param bool $is_terms_accepted
     */
    public function setIsTermsAccepted($isTermsAccepted)
    {
        $this->isTermsAccepted = $isTermsAccepted;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getOpinions()
    {
        return $this->opinions;
    }

    public function getIdeas()
    {
        return $this->ideas;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getVotes()
    {
        return $this->votes;
    }

    public function getSources()
    {
        return $this->sources;
    }

    public function getFullname()
    {
        return sprintf(
            '%s %s',
            $this->getFirstname(),
            $this->getLastname()
        );
    }

    public static function getGenderList()
    {
        return array(
            UserInterface::GENDER_UNKNOWN => 'user.gender.unknown',
            UserInterface::GENDER_FEMALE  => 'user.gender.female',
            UserInterface::GENDER_MALE    => 'user.gender.male',
        );
    }

    public function getContributionsCount()
    {
        return $this->votesCount + $this->sourcesCount + $this->ideasCount + $this->argumentsCount + $this->opinionsCount;
    }

    /**
     * Gets the value of votesCount.
     *
     * @return integer
     */
    public function getVotesCount()
    {
        return $this->votesCount;
    }

    /**
     * Gets the value of sourcesCount.
     *
     * @return integer
     */
    public function getSourcesCount()
    {
        return $this->sourcesCount;
    }

    /**
     * Gets the value of argumentsCount.
     *
     * @return integer
     */
    public function getArgumentsCount()
    {
        return $this->argumentsCount;
    }

    /**
     * Gets the value of commentsCount.
     *
     * @return integer
     */
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    /**
     * Gets the value of ideasCount.
     *
     * @return integer
     */
    public function getIdeasCount()
    {
        return $this->ideasCount;
    }

    /**
     * Gets the value of opinionsCount.
     *
     * @return integer
     */
    public function getOpinionsCount()
    {
        return $this->opinionsCount;
    }

    /**
     * Sets the value of opinionsCount.
     *
     * @param integer $opinionsCount the opinions count
     *
     * @return self
     */
    public function setOpinionsCount($opinionsCount)
    {
        $this->opinionsCount = $opinionsCount;

        return $this;
    }

    /**
     * Sets the value of ideasCount.
     *
     * @param integer $ideasCount the ideas count
     *
     * @return self
     */
    public function setIdeasCount($ideasCount)
    {
        $this->ideasCount = $ideasCount;

        return $this;
    }

    /**
     * Sets the value of commentsCount.
     *
     * @param integer $commentsCount the comments count
     *
     * @return self
     */
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;

        return $this;
    }

    /**
     * Sets the value of argumentsCount.
     *
     * @param integer $argumentsCount the arguments count
     *
     * @return self
     */
    public function setArgumentsCount($argumentsCount)
    {
        $this->argumentsCount = $argumentsCount;

        return $this;
    }

    /**
     * Sets the value of sourcesCount.
     *
     * @param integer $sourcesCount the sources count
     *
     * @return self
     */
    public function setSourcesCount($sourcesCount)
    {
        $this->sourcesCount = $sourcesCount;

        return $this;
    }

    /**
     * Sets the value of votes.
     *
     * @param mixed $votes the votes
     *
     * @return self
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Sets the value of votesCount.
     *
     * @param integer $votesCount the votes count
     *
     * @return self
     */
    public function setVotesCount($votesCount)
    {
        $this->votesCount = $votesCount;

        return $this;
    }
}
