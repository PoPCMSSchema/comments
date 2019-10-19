<?php
namespace PoP\Comments\Hooks\RESTFields;

use PoP\Hooks\Contracts\HooksAPIInterface;
use PoP\Engine\Hooks\AbstractHookSet;
use PoP\Translation\Contracts\TranslationAPIInterface;

class PostHooks extends AbstractHookSet
{
    const COMMENT_RESTFIELDS = 'comments.id|content';

    public function __construct(
        HooksAPIInterface $hooksAPI,
        TranslationAPIInterface $translationAPI
    ) {
        parent::__construct($hooksAPI, $translationAPI);

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
