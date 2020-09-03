<?php

namespace Metal\ContentBundle\Service\Grabber;

use Metal\ContentBundle\Exception\InstagramException;
use Metal\ContentBundle\Service\Grabber\Model\Photo;
use Metal\ContentBundle\Service\Grabber\Model\Profile;

class Grabber
{
    const MAX_RETRY_GET_SHARED_DATA = 5;

    private static $retryGetSharedData = 0;

    private $sharedDataCache = array();

    private $identityMap = array();

    /**
     * @param string $code
     *
     * @return Photo
     */
    public function loadPhoto($code)
    {
        if (isset($this->identityMap[Photo::class][$code])) {
            return $this->identityMap[Photo::class][$code];
        }

        $post = new Photo();
        $post->updateFromSharedData($this->getSharedDataForEndpoint('p/'.$code));

        $this->identityMap[Photo::class][$code] = $post;

        return $post;
    }

    /**
     * @param string $username
     *
     * @return Profile
     */
    public function loadProfile($username)
    {
        if (isset($this->identityMap[Profile::class][$username])) {
            return $this->identityMap[Profile::class][$username];
        }

        $profile = new Profile();
        $profile->updateFromSharedData($this->getSharedDataForEndpoint($username));

        $this->identityMap[Profile::class][$username] = $profile;

        return $profile;
    }

    /**
     * @param string $username
     * @param string|null $endCursor
     *
     * @return Photo[]
     */
    public function getProfilePhotos($username, $endCursor = null)
    {
        //У нас используется при ошибки continue и в while будет не проиницилизированная переменная, так что нужно объявлять
        $hasNextPage = false;
        do {
            try {
                $sharedData = $this->getSharedDataForEndpoint($username, $endCursor);
            } catch (InstagramException $instagramException) {
                continue;
            }

            $data = $sharedData['entry_data']['ProfilePage'][0]['user'];

            $endCursor = $data['media']['page_info']['end_cursor'];
            $hasNextPage = $data['media']['page_info']['has_next_page'];

            foreach ($data['media']['nodes'] as $node) {
                $photo = new Photo();
                $photo->updateFromData($node);

                yield $endCursor => $photo;
            }
        } while ($hasNextPage);
    }

    /**
     * @param string $tag
     * @param string|null $endCursor
     *
     * @return Photo[]
     */
    public function getTagPhotos($tag, $endCursor = null)
    {
        $hasNextPage = false;
        do {
            try {
                $sharedData = $this->getSharedDataForEndpoint('explore/tags/'.$tag, $endCursor);
            } catch (InstagramException $instagramException) {
                continue;
            }

            $data = $sharedData['entry_data']['TagPage'][0]['tag'];

            $endCursor = $data['media']['page_info']['end_cursor'];
            $hasNextPage = $data['media']['page_info']['has_next_page'];

            foreach ($data['media']['nodes'] as $node) {
                $photo = new Photo();
                $photo->updateFromData($node);

                yield $endCursor => $photo;
            }
        } while ($hasNextPage);
    }

    /**
     * @param string $location
     * @param string|null $endCursor
     *
     * @return Photo[]
     */
    public function getLocationPhotos($location, $endCursor = null)
    {
        $hasNextPage = false;
        do {
            try {
                $sharedData = $this->getSharedDataForEndpoint('explore/locations/'.$location, $endCursor);
            } catch (InstagramException $instagramException) {
                continue;
            }

            $data = $sharedData['entry_data']['LocationsPage'][0]['location'];

            $endCursor = $data['media']['page_info']['end_cursor'];
            $hasNextPage = $data['media']['page_info']['has_next_page'];

            foreach ($data['media']['nodes'] as $node) {
                $photo = new Photo();
                $photo->updateFromData($node);

                yield $endCursor => $photo;
            }
        } while ($hasNextPage);
    }

    private function getSharedDataForEndpoint($endpoint, $maxId = null)
    {
        $url = sprintf('https://www.instagram.com/%s/%s', $endpoint, $maxId ? '?max_id='.$maxId : $maxId);

        if (!empty($this->sharedDataCache[$url])) {
            return $this->sharedDataCache[$url];
        }

        //TODO: Будут прокси можно будет убрать.
        sleep(mt_rand(1, 3));

        $sharedData = null;
        try {
            $content = GrabberService::getPageData($url);
            $sharedData = GrabberService::getSharedData($content);
        } catch (InstagramException $instagramException) {

            if ($instagramException->getCode() === InstagramException::CODE_PAGE_REMOVE) {
                throw $instagramException;
            }

            self::$retryGetSharedData++;

            if (self::$retryGetSharedData >= self::MAX_RETRY_GET_SHARED_DATA) {
                self::$retryGetSharedData = 0;
                throw $instagramException;
            }

            sleep(5);
            return $this->getSharedDataForEndpoint($endpoint, $maxId);
        }

        self::$retryGetSharedData = 0;

        return $this->sharedDataCache[$url] = $sharedData;
    }
}
