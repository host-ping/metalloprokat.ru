<?php

namespace Metal\ContentBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\ContentBundle\Entity\ValueObject\SubjectTypeProvider;

class DefaultHelper extends HelperAbstract
{
    public function getSubjects()
    {
        $subjects = array(0 => array('id' => null, 'title' => 'Все темы'));
        $subjects = array_merge($subjects, SubjectTypeProvider::getAllTypes());
        return $subjects;
    }

    public function getPeriods()
    {
        return array(
            1 => array('query' => 1, 'title' => 'За сутки'),
            2 => array('query' => 2, 'title' => 'За неделю'),
            3 => array('query' => 3, 'title' => 'За месяц'),
            'all' => array('query' => null, 'title' => 'За все время')
        );
    }

    public function getOrdersByCount()
    {
        return array(
            2 => array('query' => 2, 'title' => 'Самое комментируемое'),
            'all' => array('query' => null, 'title' => 'Новое')
        );
    }

    public function getOrdersForSearch()
    {
        return array(
            'date' => array('query' => 'date', 'title' => 'Новое'),
            2 => array('query' => 2, 'title' => 'Самое комментируемое'),
            'all' => array('query' => null, 'title' => 'По релевантности')
        );
    }

    public function getFiltersByType()
    {
        return array(
            1 => array('query' => 1, 'title' => 'Темы'),
            2 => array('query' => 2, 'title' => 'Вопросы'),
            'all' => array('query' => null, 'title' => 'Все подряд')
        );
    }
}