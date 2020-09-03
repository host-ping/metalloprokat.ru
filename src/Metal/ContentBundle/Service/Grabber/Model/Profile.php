<?php

namespace Metal\ContentBundle\Service\Grabber\Model;

class Profile
{
    protected $id;

    protected $username;

    protected $biography;

    protected $fullName;

    protected $isVerified;

    protected $externalUrl;

    protected $externalUrlLinkShimmed;

    protected $mediaCount;

    /**
     * Количество, на кого подписан профиль
     */
    protected $followsCount;

    /**
     * Количество подписавшихся на этот профиль
     */
    protected $followedByCount;

    protected $isPrivate;

    public function updateFromSharedData(array $data)
    {
        $data = $data['entry_data']['ProfilePage'][0]['user'];

        $this->setId($data['id']);
        $this->setUsername($data['username']);
        $this->setFullName($data['full_name']);
        $this->setExternalUrl($data['external_url']);
        $this->setExternalUrlLinkShimmed($data['external_url_linkshimmed']);
        $this->setIsPrivate($data['is_private']);
        $this->setBiography($data['biography']);
        $this->setFollowedByCount($data['followed_by']['count']);
        $this->setFollowsCount($data['follows']['count']);
        $this->setIsVerified($data['is_verified']);
        $this->setMediaCount($data['media']['count']);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getBiography()
    {
        return $this->biography;
    }

    public function setBiography($biography)
    {
        $this->biography = (string)$biography;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = (string)$fullName;
    }

    public function getIsVerified()
    {
        return $this->isVerified;
    }

    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;
    }

    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = (string)$externalUrl;
    }

    public function getExternalUrlLinkShimmed()
    {
        return $this->externalUrlLinkShimmed;
    }

    public function setExternalUrlLinkShimmed($externalUrlLinkShimmed)
    {
        $this->externalUrlLinkShimmed = (string)$externalUrlLinkShimmed;
    }

    public function getMediaCount()
    {
        return $this->mediaCount;
    }

    public function setMediaCount($mediaCount)
    {
        $this->mediaCount = $mediaCount;
    }

    public function getFollowsCount()
    {
        return $this->followsCount;
    }

    public function setFollowsCount($followsCount)
    {
        $this->followsCount = $followsCount;
    }

    public function getFollowedByCount()
    {
        return $this->followedByCount;
    }

    public function setFollowedByCount($followedByCount)
    {
        $this->followedByCount = $followedByCount;
    }

    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
    }

    public function getProfileWebPath()
    {
        return 'https://www.instagram.com/'.$this->username;
    }
}
