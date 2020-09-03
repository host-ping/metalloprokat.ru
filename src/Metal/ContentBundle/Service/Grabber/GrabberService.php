<?php

namespace Metal\ContentBundle\Service\Grabber;

use Metal\ContentBundle\Exception\InstagramException;

class GrabberService
{
    const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.76 Safari/537.36';

    /**
     * @param string $url
     *
     * @return string
     */
    public static function getPageData($url)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_REFERER, $url);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);

        $contents = curl_exec($c);
        curl_close($c);

        return $contents;
    }

    /**
     * @param string $content HTML
     * @return array
     *
     * @throws InstagramException
     */
    public static function getSharedData($content)
    {
        $jsonMatches = null;
        preg_match('/window\._sharedData = (.+);<\/script>/ui', $content, $jsonMatches);

        if (empty($jsonMatches[1])) {
            throw new InstagramException('No shared data.', InstagramException::CODE_NO_SHARE_DATA);
        }

        $data = json_decode($jsonMatches[1], true);
        if (empty($data['entry_data'])) {
            throw new InstagramException('Page was removed.', InstagramException::CODE_PAGE_REMOVE);
        }

        return $data;
    }

    /**
     * @param $title
     * @param $onlyCyrillicTag
     *
     * @return array
     */
    public static function getAllPhotoTags($title, $onlyCyrillicTag = true)
    {
        $matches = null;
        preg_match_all('/#(\w+)/ui', $title, $matches);

        $tags = array();
        if (!isset($matches[1])) {
            return $tags;
        }

        foreach ((array)$matches[1] as $tag) {
            if (!$onlyCyrillicTag || preg_match('/[а-я]/ui', $tag, $matches)) {
                $tags[] = trim($tag);
            }
        }

        return $tags;
    }
}
