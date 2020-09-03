<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\WidgetsBundle\Widget\ConditionallyRenderedWidget;
use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Отвечает за показ попапа пользователю, который присоединился к компании при регистрации
 */
class ApprovedUserWidget extends TwigWidget implements ConditionallyRenderedWidget
{
    private $checker;

    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefined(array('redirect_url'))
            ->setRequired(array('main_user'));
    }

    public function shouldBeRendered()
    {
        return $this->checker->isGranted('ROLE_SUPPLIER') && !$this->checker->isGranted('ROLE_APPROVED_USER');
    }
}
