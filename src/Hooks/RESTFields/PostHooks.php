<?php
namespace PoP\Comments\Hooks\RESTFields;

use PoP\Engine\Hooks\AbstractHookSet;

class PostHooks extends AbstractHookSet
{
    const COMMENT_RESTFIELDS = 'comments.id|content';

    protected function init()
    {
        $this->hooksAPI->addFilter(
            'Posts:RESTFields',
            [$this, 'getRESTFields']
        );
    }

    public function getRESTFields($restFields): string
    {
        return $restFields.','.self::COMMENT_RESTFIELDS;
    }
}
