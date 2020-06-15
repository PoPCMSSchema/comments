<?php

declare(strict_types=1);

namespace PoP\Comments\Conditional\RESTAPI\Hooks\RESTFields;

use PoP\Engine\Hooks\AbstractHookSet;
use PoP\CustomPosts\Conditional\RESTAPI\RouteModuleProcessors\EntryRouteModuleProcessorHelpers;

class CustomPostHooks extends AbstractHookSet
{
    const COMMENT_RESTFIELDS = 'comments.id|content';

    protected function init()
    {
        $this->hooksAPI->addFilter(
            EntryRouteModuleProcessorHelpers::HOOK_REST_FIELDS,
            [$this, 'getRESTFields']
        );
    }

    public function getRESTFields($restFields): string
    {
        return $restFields . ',' . self::COMMENT_RESTFIELDS;
    }
}
