<?php

declare(strict_types=1);

namespace PoP\Comments\Facades;

use PoP\Comments\TypeAPIs\CommentTypeAPIInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class CommentTypeAPIFacade
{
    public static function getInstance(): CommentTypeAPIInterface
    {
        return ContainerBuilderFactory::getInstance()->get('comment_type_api');
    }
}
