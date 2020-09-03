<?php

namespace Metal\ContentBundle\Service\Grabber\Model;

class Photo
{
    protected $id;

    protected $code;

    protected $owner;

    protected $ownerUsername;

    protected $caption;

    protected $date;

    protected $isVideo;

    protected $commentsCount;

    protected $likesCount;

    protected $commentsDisabled;

    protected $displaySrc;

    protected $thumbnailSrc;

    protected $dimensions = array();

    public function updateFromSharedData(array $data)
    {
        if (isset($data['entry_data']['PostPage'][0]['graphql']['shortcode_media'])) {
            $this->updateFromData($data['entry_data']['PostPage'][0]['graphql']['shortcode_media']);
        } elseif (isset($data['entry_data']['PostPage'][0]['media'])) {
            $this->updateFromData($data['entry_data']['PostPage'][0]['media']);
        } else {
            throw new \RuntimeException('Data is not valid');
        }
    }

    /**
     * @return float Среднее количество лайков фото в минуту
     */
    public function getLikesDelta()
    {
        return round($this->likesCount / ((time() - $this->getDate()->getTimestamp()) / 60));
    }

    public function updateFromData(array $data)
    {
        if (isset($data['caption'])) {
            $this->setCaption($data['caption']);
        } elseif (isset($data['edge_media_to_caption']['edges'][0]['node']['text'])) {
            $this->setCaption($data['edge_media_to_caption']['edges'][0]['node']['text']);
        }

        if (isset($data['code'])) {
            $this->setCode($data['code']);
        } elseif (isset($data['shortcode'])) {
            $this->setCode($data['shortcode']);
        }

        if (isset($data['comments_disabled'])) {
            $this->setCommentsDisabled($data['comments_disabled']);
        }

        if (isset($data['thumbnail_src'])) {
            $this->setThumbnailSrc($data['thumbnail_src']);
        }

        if (isset($data['dimensions'])) {
            $this->setDimensions(
                array('width' => $data['dimensions']['width'], 'height' => $data['dimensions']['height'])
            );
        }

        if (isset($data['owner'])) {
            if (isset($data['owner']['id'])) {
                $this->setOwner($data['owner']['id']);
            }

            if (isset($data['owner']['username'])) {
                $this->setOwnerUsername($data['owner']['username']);
            }
        }

        if (isset($data['date'])) {
            $this->setDate((new \DateTime())->setTimestamp($data['date']));
        } elseif (isset($data['taken_at_timestamp'])) {
            $this->setDate((new \DateTime())->setTimestamp($data['taken_at_timestamp']));
        }

        if (isset($data['id'])) {
            $this->setId($data['id']);
        }

        if (isset($data['is_video'])) {
            $this->setIsVideo($data['is_video']);
        }

        if (isset($data['display_src'])) {
            $this->setDisplaySrc($data['display_src']);
        } elseif (isset($data['display_url'])) {
            $this->setDisplaySrc($data['display_url']);
        }

        if (isset($data['comments']['count'])) {
            $this->setCommentsCount($data['comments']['count']);
        } elseif (isset($data['edge_media_to_comment']['count'])) {
            $this->setCommentsCount($data['edge_media_to_comment']['count']);
        }

        if (isset($data['likes']['count'])) {
            $this->setLikesCount($data['likes']['count']);
        } elseif (isset($data['edge_media_preview_like']['count'])) {
            $this->setLikesCount($data['edge_media_preview_like']['count']);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getOwnerUsername()
    {
        return $this->ownerUsername;
    }

    public function setOwnerUsername($ownerUsername)
    {
        $this->ownerUsername = $ownerUsername;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    public function getIsVideo()
    {
        return $this->isVideo;
    }

    public function setIsVideo($isVideo)
    {
        $this->isVideo = $isVideo;
    }

    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;
    }

    public function getLikesCount()
    {
        return $this->likesCount;
    }

    public function setLikesCount($likesCount)
    {
        $this->likesCount = $likesCount;
    }

    public function getCommentsDisabled()
    {
        return $this->commentsDisabled;
    }

    public function setCommentsDisabled($commentsDisabled)
    {
        $this->commentsDisabled = $commentsDisabled;
    }

    public function getDisplaySrc()
    {
        return $this->displaySrc;
    }

    public function setDisplaySrc($displaySrc)
    {
        $this->displaySrc = $displaySrc;
    }

    public function getThumbnailSrc()
    {
        return $this->thumbnailSrc;
    }

    public function setThumbnailSrc($thumbnailSrc)
    {
        $this->thumbnailSrc = $thumbnailSrc;
    }

    /**
     * @return array
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * @param array $dimensions
     */
    public function setDimensions(array $dimensions)
    {
        $this->dimensions = $dimensions;
    }

    /**
     * @param bool $upper
     * @return array
     */
    public function getAllTags($upper = false)
    {
        $matches = null;
        preg_match_all('/#(\w+)/ui', $this->caption, $matches);
        $tags = array();

        if (!isset($matches[1])) {
            return $tags;
        }

        foreach ((array)$matches[1] as $tag) {

            $tags[] = $upper ? mb_strtoupper(trim($tag)) : trim($tag);
        }

        return $tags;
    }

    /**
     * @return array
     */
    public function getRussianTags()
    {
        $matches = null;
        preg_match_all('/#(\w+)/ui', $this->caption, $matches);
        $tags = array();

        if (!isset($matches[1])) {
            return $tags;
        }

        foreach ((array)$matches[1] as $tag) {
            preg_match('/[а-я]/ui', $tag, $matches);
            if (!empty($matches[0])) {
                $tags[] = trim($tag);
            }
        }

        return $tags;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function getLink()
    {
        return sprintf('https://www.instagram.com/p/%s', $this->code);
    }
}
