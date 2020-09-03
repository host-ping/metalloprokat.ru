<?php

namespace Metal\ContentBundle\Exception;


class InstagramException extends \Exception
{
    const CODE_IS_PRIVATE = 1; //This account is private
    const CODE_NO_SHARE_DATA = 2; //No shared data.
    const CODE_REJECT_ACCOUNT_SITE = 3;
    const CODE_EMPTY_ENTRY_DATA = 4;
    const CODE_PAGE_REMOVE = 5;
}
