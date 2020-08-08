<?php

declare(strict_types=1);

namespace PoPSchema\Comments\Facades;

use PoPSchema\Comments\TypeAPIs\CommentTypeAPIInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class CommentTypeAPIFacade
{
    public static function getInstance(): CommentTypeAPIInterface
    {
        return ContainerBuilderFactory::getInstance()->get('comment_type_api');
    }
}
