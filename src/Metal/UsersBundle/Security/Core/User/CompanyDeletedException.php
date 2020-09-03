<?php

namespace Metal\UsersBundle\Security\Core\User;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class CompanyDeletedException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Компания, в которой вы находитесь, была удалена. Для отсоединения от компании или присоединения к новой компании обращайтесь в службу поддержки.';
    }
}
