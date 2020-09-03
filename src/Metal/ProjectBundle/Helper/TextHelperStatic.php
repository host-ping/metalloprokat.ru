<?php

namespace Metal\ProjectBundle\Helper;

class TextHelperStatic
{
    public static function declineWordGenitive($word)
    {
        if ($word == '') {
            return $word;
        }

        if (preg_match('/\d+/', $word, $matches)) {
            return trim($word);
        }

        $endSymbol = '';
        if (substr($word, -1) == ')') {
            $word = substr($word, 0, -1);
            $endSymbol = ')';
        }

        static $compliances = array(
            'щая' => 'щей',
            'щий' => 'щего',
            'лия' => 'лий',
            'нец' => 'нца',
            'шок' => 'шка',
            'тке' => 'тке',
            // серебро - серебра
            'бро' => 'бра',
            // ревизия - ревизии
            'зия' => 'зии',
            // соединения - соединений
            'ния' => 'ний',
            // цинкование - цинкования
            'ние' => 'ния',
            // колонны - колонн
            'нны' => 'нн',
            // площадки - площадок
            'дки' => 'док',
            'ще' => 'ща',
            'жи' => 'жей',
            'ля' => 'ля',
            'ей' => 'ей',
            'ый' => 'ого',
            'ые' => 'ых',
            'ая' => 'ой',
            'ия' => 'ия',
            'ых' => 'ых',
            'из' => 'из',
            'ов' => 'ов',
            'ом' => 'ом',
            'ие' => 'их',
            'ка' => 'ки',
            'га' => 'ги',
            'бь' => 'би',
            'пь' => 'пи',
            'ть' => 'ти',
            'дь' => 'ди',
            // услуг - услуг
            'уги' => 'уг',
            // олово - олова
            'во' => 'ва',
            // формах - формах
            'ах' => 'ах',
            // цирконий - циркония
            'ий' => 'ия',
            // связи - связей
            'зи' => 'зей',
            // трубы - труб
            'бы' => 'б',
            'а' => 'ы',
            // шары -> шаров
            'ы' => 'ов',
            // баки - баков
            'и' => 'ов',
            'у' => 'у',
            'ь' => 'я',
        );

        $exceptions = array('по', 'и', 'в', 'с');
        if (in_array($word, $exceptions)) {
            return $word.$endSymbol;
        }

        if (in_array(substr($word, -1), array(',', '.', '-', ';', ':'))) {
            return $word.$endSymbol;
        }

        foreach ($compliances as $src => $to) {
            if (substr($word, -strlen($src)) == $src) {
                $res = substr($word, 0, -strlen($src)).$to;

                return $res.$endSymbol;
            }
        }

        return $word.'а'.$endSymbol;
    }

    public static function declinePhraseGenitive($phrase)
    {

        $pieces = explode(' ', $phrase);
        if ($pieces[0] == 'Республика') {
            $pieces[0] = 'Республике';

            return trim(implode(' ', $pieces));
        }

        $pieces = array_map(array('self', 'declineWordGenitive'), $pieces);

        return trim(implode(' ', $pieces));
    }

    public static function declineWordAccusative($word)
    {
        if ($word == '') {
            return $word;
        }

        if (preg_match('/\d+/', $word, $matches)) {
            return trim($word);
        }

        $endSymbol = '';
        if (substr($word, -1) == ')') {
            $word = substr($word, 0, -1);
            $endSymbol = ')';
        }

        $compliances = array(
            'ая' => 'ую',
            'а' => 'у',
        );

        foreach ($compliances as $src => $to) {
            if (substr($word, -strlen($src)) == $src) {
                $res = substr($word, 0, -strlen($src)).$to;

                return $res.$endSymbol;
            }
        }

        return $word.$endSymbol;
    }

    public static function declinePhraseAccusative($phrase)
    {
        $pieces = explode(' ', $phrase);
        if ($pieces[0] == 'Республика') {
            $pieces[0] = 'Республике';

            return trim(implode(' ', $pieces));
        }

        $pieces = array_map(array('self', 'declineWordAccusative'), $pieces);

        return trim(implode(' ', $pieces));
    }

    public static function declineWordLocative($word)
    {
        if ($word == '') {
            return $word;
        }

        if (preg_match('/\d+/', $word, $matches)) {
            return trim($word);
        }

        if ($word == 'и') {
            return $word;
        }

        $endSymbol = '';
        if (substr($word, -1) == ')') {
            $word = substr($word, 0, -1);
            $endSymbol = ')';
        }

        //TODO: "Костанай" преобразуется в "Костанай е" . 'най' - 'най' не помогло
        $source = array('восток', 'Киев', 'Харьков', 'тти', 'очи','во', 'ата','аты','сий','ний','лий','ова','ина','ев','ов','ок','ец','ин','ий','ие','ые','ой','ей','ея','ая','яя','ай','яй','ое','ый','ые','ни','ли','ти','ки','щи','чи','ри','дь','ль','ь','й','ы','а','я','о','О','у','и','э');
        $target = array('востоке', 'Киеве', 'Харькове', 'тти', 'очи','вом', 'ата','ате','сьем','нем','льем','овой','иной','евом','овом','ке','це','ином','ом','их','ых','ом','ее','ее','ой','ей','ае','яе','ом','ом','ых','нях','лях','тях','ках','щах','чах','рях','де','ле','и','е','ах','е','е','о', 'О','у','и','э');

        foreach ($source as $i => $src) {
            if (substr($word, -strlen($src)) == $src) {
                $res = substr($word, 0, -strlen($src)).$target[$i];

                return $res.$endSymbol;
            }
        }

        return $word.'е'.$endSymbol;
    }

    public static function declinePhraseLocative($phrase)
    {
        $pieces = explode(' ', $phrase);
        if ($pieces[0] == 'Республика') {
            $pieces[0] = 'Республике';

            return trim(implode(' ', $pieces));
        }

        $pieces = array_map(array('self', 'declineWordLocative'), $pieces);

        return trim(implode(' ', $pieces));
    }

    public static function declineWordPrepositional($word)
    {
        return self::declineWordLocative($word);
    }

    public static function declinePhrasePrepositional($phrase)
    {
        $pieces = explode(' ', $phrase);

        $pieces = array_map(array('self', 'declineWordPrepositional'), $pieces);

        return trim(implode(' ', $pieces));
    }

    public static function declineWordAblative($word)
    {
        return $word;
    }

    public static function declinePhraseAblative($phrase)
    {
        $pieces = explode(' ', $phrase);

        $pieces = array_map(array('self', 'declineWordAblative'), $pieces);

        return trim(implode(' ', $pieces));
    }

    public static function normalizeTitleForEmbed($title)
    {
        $normalizeTitle = array();
        $parts = explode('/', $title);
        foreach ($parts as $part) {
            if (!preg_match('/[A-ZА-Я]{2,}/u', $part)) {
                $part = mb_strtolower(mb_substr($part, 0, 1)).mb_substr($part, 1);
            }

            $normalizeTitle[] = $part;
        }

        return implode('/', $normalizeTitle);
    }
}
