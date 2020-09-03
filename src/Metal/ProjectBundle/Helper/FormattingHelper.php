<?php

namespace Metal\ProjectBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

class FormattingHelper extends HelperAbstract
{
    public function formatDate(\DateTime $time, $format = null)
    {
        return $time->format('j').' '.$this->formatYearMonth($time, $format);
    }

    public function formatYearMonth(\DateTime $time, $format = null)
    {
        return $this->getMonthLocalized($time, $format).' '.$time->format('Y');
    }

    public function formatDateRange(\DateTime $dateFrom, \DateTime $dateTo, $format = null)
    {
        if ($dateFrom->format('Y') == $dateTo->format('Y')) {
            return $dateFrom->format('j').' '.$this->getMonthLocalized($dateFrom, $format).' — '.$this->formatDate(
                    $dateTo,
                    $format
                );
        }

        return $this->formatDate($dateFrom, $format).' — '.$this->formatDate($dateTo, $format);
    }

    public function getMonthLocalized(\DateTime $time, $format = null)
    {
        $month = $time->format('m');

        return $this->getMonthes($format)[(int)$month];
    }

    public function getMonthes($format)
    {
        $monthes = array(
            1 => 'янв',
            2 => 'фев',
            3 => 'мар',
            4 => 'апр',
            5 => 'май',
            6 => 'июн',
            7 => 'июл',
            8 => 'авг',
            9 => 'сен',
            10 => 'окт',
            11 => 'ноя',
            12 => 'дек',
        );

        if ($format === 'full') {
            $monthes = array(
                1 => 'января',
                2 => 'февраля',
                3 => 'марта',
                4 => 'апреля',
                5 => 'мая',
                6 => 'июня',
                7 => 'июля',
                8 => 'августа',
                9 => 'сентября',
                10 => 'октября',
                11 => 'ноября',
                12 => 'декабря',
            );
        } elseif ($format === 'normal') {
            $monthes = array(
                1 => 'январь',
                2 => 'февраль',
                3 => 'март',
                4 => 'апрель',
                5 => 'май',
                6 => 'июнь',
                7 => 'июль',
                8 => 'август',
                9 => 'сентябрь',
                10 => 'октябрь',
                11 => 'ноябрь',
                12 => 'декабрь',
            );
        }

        return $monthes;
    }

    public function timeToNewDay()
    {
        $expire = 86400 - 3600 * date('H') - 60 * date('i');
        $timeString = date('H:i', $expire);

        $timeArray = explode(':', $timeString);
        $timeNamedArray['h'] = $timeArray[0];
        $timeNamedArray['m'] = $timeArray[1];

        $arrayNumber = array('1', '21');
        $arrayNumbers = array('2', '3', '4', '22', '23', '24');

        $hour = null;
        $minute = 'мин.';

        if (in_array($timeNamedArray['h'], $arrayNumber)) {
            $hour = 'час';
        } elseif (in_array($timeNamedArray['h'], $arrayNumbers)) {
            $hour = 'часа';
        } else {
            $hour = 'часов';
        }

        return $timeNamedArray['h'].' '.$hour.' '.$timeNamedArray['m'].' '.$minute;
    }

    public function formatDateTime(\DateTime $time = null)
    {
        if (null === $time) {
            return null;
        }

        return $this->formatDate($time).' '.$time->format('H:i');
    }

    public function getDomain($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    public function getTimeLocalized(\DateTime $time)
    {
        $oldness = time() - $time->getTimestamp();
        $translator = $this->container->get('translator');

        if ($oldness < 60) {
            return $translator->transChoice(
                'time.seconds_ago',
                $oldness,
                array('%count%' => $oldness),
                'MetalProjectBundle'
            );
        }

        if ($oldness < 3600) {
            $val = floor($oldness / 60);

            return $translator->transChoice('time.minutes_ago', $val, array('%count%' => $val), 'MetalProjectBundle');
        }

        if ($oldness < 24 * 3600) {
            $val = floor($oldness / 3600);

            return $translator->transChoice('time.hours_ago', $val, array('%count%' => $val), 'MetalProjectBundle');
        }

        if ($oldness < 7 * 24 * 3600) {
            $val = floor($oldness / (24 * 3600));

            return $translator->transChoice('time.days_ago', $val, array('%count%' => $val), 'MetalProjectBundle');
        }

        if ($oldness < 30 * 24 * 3600) {
            $val = floor($oldness / (7 * 24 * 3600));

            return $translator->transChoice('time.weeks_ago', $val, array('%count%' => $val), 'MetalProjectBundle');
        }

        if ($oldness < 12 * 30 * 24 * 3600) {
            $val = floor($oldness / (30 * 24 * 3600));

            return $translator->transChoice('time.monthes_ago', $val, array('%count%' => $val), 'MetalProjectBundle');
        }

        $val = floor($oldness / (12 * 30 * 24 * 3600));

        return $translator->transChoice('time.years_ago', $val, array('%count%' => $val), 'MetalProjectBundle');
    }

    /**
     * @param \DateTime $dateEnd
     *
     * @return int Количество дней до наступления $dateEnd, 0 если дата уже наступила (сегодня)
     */
    public function getDaysEnd(\DateTime $dateEnd)
    {
        return ($dateEnd->getTimestamp() - (new \DateTime('midnight'))->getTimestamp()) / 86400;
    }

    public function formatStatsDate(\DateTime $date, $mode, \DateTime $previousDate = null)
    {
        switch ($mode) {
            case 'week':
                $dateStart = clone $date;
                $dateStart->modify('this week monday');
                $dateEnd = clone $date;
                $dateEnd->modify('this week sunday');
                $previousDateStart = null;
                $previousDateEnd = null;
                if ($previousDate) {
                    $previousDateStart = clone $previousDate;
                    $previousDateStart->modify('this week monday');
                    $previousDateEnd = clone $previousDate;
                    $previousDateEnd->modify('this week sunday');
                }

                $dateFromFormatted = $dateStart->format('j');
                if (!$previousDateEnd || $dateStart->format('Y') != $previousDateEnd->format('Y')) {
                    $dateFromFormatted = $this->formatDate($dateStart);
                } elseif ($dateStart->format('m') != $previousDateEnd->format('m')) {
                    $dateFromFormatted = $dateStart->format('j').' '.$this->getMonthLocalized($dateStart);
                }

                $dateToFormatted = $dateEnd->format('j');
                if ($dateStart->format('Y') != $dateEnd->format('Y')) {
                    $dateToFormatted = $this->formatDate($dateEnd);
                } elseif ($dateStart->format('m') != $dateEnd->format('m')) {
                    $dateToFormatted = $dateEnd->format('j').' '.$this->getMonthLocalized($dateEnd);
                }

                return 'с '.$dateFromFormatted.'<br /> по '.$dateToFormatted;

            case 'month':
                if (!$previousDate || $date->format('Y') != $previousDate->format('Y')) {
                    return $this->formatYearMonth($date);
                }

                return $this->getMonthLocalized($date);

            case 'day':
                if (!$previousDate || $date->format('Y') != $previousDate->format('Y')) {
                    return $this->formatDate($date);
                }
                if ($date->format('m') != $previousDate->format('m')) {
                    return $date->format('j').' '.$this->getMonthLocalized($date);
                }

                return $date->format('j');
        }

        throw new \InvalidArgumentException('Wrong mode.');
    }

    public function isWeekend(\DateTime $date)
    {
        return $date->format('N') >= 6;
    }

    public function isCurrentDay(\DateTime $date)
    {
        return (new \DateTime())->format('Y-m-d') === $date->format('Y-m-d');
    }

    public function dateEndOfPackage($days)
    {
        $dateEnd = new \DateTime('+'.$days.'days');

        return $this->formatDate($dateEnd, 'full');
    }

    public function formatFileSize($bytes, $precision = 2)
    {
        //TODO: нужно учитывать правила форматирования текущей локали и неплохо бы переводить рамеры
        $units = array(
            'bytes',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
        );

        $unit = 0;
        while ($bytes >= 1024) {
            $bytes /= 1024;
            $unit++;
        }

        return round($bytes, $precision).' '.$units[$unit];
    }

    public static function canonicalizePhone($phone)
    {
        $newPhone = trim($phone);

        if (!$newPhone) {
            return null;
        }

        if (mb_strlen($newPhone) > 11 && preg_match('/[.,;]/', $newPhone)) {
            $phones = preg_split('/[.,;]/', $newPhone);
            if (count($phones) > 0) {
                $newPhone = $phones[0];
            }
        }

        $firstChar = mb_substr($newPhone, 0, 1);
        $newPhone = preg_replace('/\D/ui', '', $newPhone);

        if (mb_strlen($newPhone) > 10) {
            $newPhone = preg_replace('/^8/ui', '7', $newPhone);
        }

        if ('+' !== $firstChar && '7' !== $firstChar) {
            $newPhone = '7'.$newPhone;
        }

        if (mb_strlen($newPhone) < 10) {
            $newPhone = null;
        }

        return $newPhone;
    }

    public function getFullSentences($str, $maxlength, $delimiter)
    {
        if (mb_strlen($str) <= $maxlength){
            return $str;
        }

        $cutStr = mb_substr($str, 0, $maxlength);

        $sentences = preg_split('/['.$delimiter.']/', $cutStr);

        $returnStr = '';

        foreach ($sentences as $key => $sentence){
            if ($key != (count($sentences) - 1)){
                $returnStr .= $sentence.'.';
            }
        }

        return $returnStr;
    }
}
