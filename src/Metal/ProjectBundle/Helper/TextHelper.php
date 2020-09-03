<?php

namespace Metal\ProjectBundle\Helper;

use Behat\Transliterator\Transliterator;
use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

class TextHelper extends HelperAbstract
{
    public function declineWordGenitive($word)
    {
        return TextHelperStatic::declineWordGenitive($word);
    }

    public function declinePhraseGenitive($phrase)
    {
        return TextHelperStatic::declinePhraseGenitive($phrase);
    }

    public function declineWordAccusative($word)
    {
        return TextHelperStatic::declineWordAccusative($word);
    }

    public function declinePhraseAccusative($phrase)
    {
        return TextHelperStatic::declinePhraseAccusative($phrase);
    }

    public function declineWordLocative($word)
    {
        return TextHelperStatic::declineWordLocative($word);
    }

    public function declinePhraseLocative($phrase)
    {
        return TextHelperStatic::declinePhraseLocative($phrase);
    }

    public function lcFirst($text)
    {
        return mb_strtolower(mb_substr($text, 0, 1)).mb_substr($text, 1);
    }

    public function ucFirst($text)
    {
        return mb_strtoupper(mb_substr($text, 0, 1)).mb_substr($text, 1);
    }

    public function slugifyCompanyTitle($slug)
    {
        $slug = trim(preg_replace('/\(.+\)/ui', '', $slug));

        $slugify = $this->container->get('slugify');
        $slug = $slugify->slugify($slug);
        $slug = Transliterator::urlize($slug);

        $slug = trim($slug, " \x00A0\xA0\n\r\t'\"");
        //TODO: я не знаю как настроить транслитератор, что б он сам это резал
        $slug = preg_replace('#[ʹʺ]#u', 'j', $slug);
        $slug = str_replace('_', '-', $slug);
        $slug = preg_replace('#[^a-z-]{1,}#', '-', $slug);
        $slug = trim($slug, ' ._-');
        $partsToRemove = array('ooo', 'oao', 'zao', 'cp', 'tk', 'po', 'torgovaa-kompania');

        $slug = preg_replace(sprintf('#(^(%s)-|-(%1$s)$)#', implode('|', $partsToRemove)), '', $slug);

        $slug = preg_replace('#[-]+#', '-', $slug);
        $slug = trim($slug, ' .-');

        return $slug;
    }

    public function formatCompanyDescription($description)
    {
        $minisiteHostnamesPattern = $this->container->getParameter('minisite_hostnames_pattern');
        $description = twig_escape_filter($this->container->get('twig'), $description);
        $pattern = '/([^;]|^|\s)(https?:\/\/[^\s]+('.$minisiteHostnamesPattern.')[^\s\<]+)/ui';

        $description = preg_replace($pattern, '$1<a target="_blank" rel="noopener noreferrer" href="$2">$2</a>', nl2br($description));

        return $this->processYoutubeUrls($description);
    }

    public function formatDescription($description)
    {
        $description = $this->formatTagsInDescription($description);
        # http://parts-hall/questions_1145.html
        # избавляемся от битых ссылок. В базе текст не трогаем, а при выводе проверяем
        # если ссылка не содержит в начале www. или stroy. , считаем ее неверной
        # ссылку удаляем и оставляем просто текст ссылки, чтоб не ломать предложение
        $pattern = '/(\[url=(https?:\/\/)?)(?!www\.stroy|stroy\.)(.*?)([A-Za-zА-Яа-я ]*?)(\[\/url\])/ui';

        $description = preg_replace_callback(
            $pattern,
            function ($matches) use ($description) {
                return isset($matches[4]) ? $matches[4] : '';
            },
            $description
        );

        $description = $this->deleteBadUrls($description);

        $badWords = array(
            'ru',
            'html'
        );

        foreach ($badWords as $badWord) {
            if ($description === $badWord) {
                $description = '';
            }
        }

        return $description;
    }

    public function deleteBadUrls($description)
    {
        $imageTypes = array_merge($this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Image')->getImageTypes(), array('jpeg'));

        $allowed = array('stroy', 'youtube', 'publications_', 'tag_', 'questions_');

        $pattern = '/(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[\w]{2,}|(www\.)?[\S]{2,}\.[\w]{2,})/ui';

        return preg_replace_callback(
            $pattern,
            function ($matches) use ($description, $imageTypes, $allowed) {
                foreach ($imageTypes as $imageType) {
                    if (false !== stripos($matches[0], $imageType)) {
                        return $matches[0];
                    }
                }

                foreach ($allowed as $allow) {
                    if (false !== stripos($matches[0], $allow)) {
                        return $matches[0];
                    }
                }

                return '';
            },
            $description
        );
    }

    public function formatTagsInDescription($description)
    {
        // <h3><b> -> <h2>
        $description = preg_replace('#<h3><b>(.*?)</b></h3>#ui', '<h2>$1</h2>', $description);
        // <b>||<strong> -> ...

        return preg_replace('#(<b>|\[b\]|<strong>)(.*?)(</b>|\[/b\]|</strong>)#ui', '$2', $description);
    }

    public function limitByWords($title, $length = 5)
    {
        $pieces = explode(' ', $title);

        return implode(' ', array_splice($pieces, 0, $length));
    }

    public function processYoutubeUrls($string)
    {
        $replaceYouTubeVideo = function ($url) {
            $values = null;
            if (preg_match('/youtube\.com\/watch\?v=([^&\?\/]+)/', $url[0], $id)) {
                $values = $id[1];
            } else if (preg_match('/youtube\.com\/embed\/([^&\?\/]+)/', $url[0], $id)) {
                $values = $id[1];
            } else if (preg_match('/youtube\.com\/v\/([^&\?\/]+)/', $url[0], $id)) {
                $values = $id[1];
            } else if (preg_match('/youtu\.be\/([^&\?\/]+)/', $url[0], $id)) {
                $values = $id[1];
            } else if (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^&\?\/]+)/', $url[0], $id)) {
                $values = $id[1];
            }

            if (null === $values) {
                return $url[0];
            }

            return sprintf(
                ' <br/><embed src="https://youtube.com/embed/%s?rel=0&autoplay=0&loop=0&wmode=opaque&theme=light" 
                    frameborder="0" marginwidth="0" marginheight="0" width="540" height="344"></embed>',
                $values
            );
        };

        return preg_replace_callback('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|(?:[^,[:punct:]\s]|/))#ui', $replaceYouTubeVideo, $string);
    }
}
