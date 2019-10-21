<?php
namespace PoP\Comments\Hooks\RESTFields;

use PoP\Hooks\Contracts\HooksAPIInterface;
use PoP\Engine\Hooks\AbstractHookSet;
use PoP\Translation\Contracts\TranslationAPIInterface;

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
