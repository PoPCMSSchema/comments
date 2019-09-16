<?php
namespace PoP\Comments\Hooks\RESTFields;
use PoP\Hooks\Facades\HooksAPIFacade;

class PostHooks
{
    const COMMENT_RESTFIELDS = 'comments.id|content';

    public function __construct() {
        HooksAPIFacade::getInstance()->addFilter(
            'Posts:RESTFields',
            [$this, 'getRESTFields']
        );
    }

    public function getRESTFields($restFields): string
    {
        return $restFields.','.self::COMMENT_RESTFIELDS;
    }
}
