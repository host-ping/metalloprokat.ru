<?php

namespace Metal\ProjectBundle\Http;

use Symfony\Component\HttpFoundation\Response;

class TransparentPixelResponse extends Response
{
    /**
     * Base 64 encoded contents for 1px transparent gif and png
     *
     * @var string
     */
    const IMAGE_CONTENT = 'R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==';

    /**
     * The response content type
     *
     * @var string
     */
    const CONTENT_TYPE = 'image/gif';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(base64_decode(self::IMAGE_CONTENT));

        $this->headers->set('Content-Type', self::CONTENT_TYPE);
        $this->setPrivate();
        $this->headers->addCacheControlDirective('no-cache');
        $this->headers->addCacheControlDirective('must-revalidate');
    }
}
