<?php
namespace PoP\Comments\TypeResolvers;

use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;
use PoP\Comments\TypeDataResolvers\CommentTypeDataResolver;

class CommentTypeResolver extends AbstractTypeResolver
{
    public const NAME = 'comments';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getId($resultItem)
    {
        $cmscommentsresolver = \PoP\Comments\ObjectPropertyResolverFactory::getInstance();
        $comment = $resultItem;
        return $cmscommentsresolver->getCommentId($comment);
    }

    public function getIdFieldTypeDataResolverClass(): string
    {
        return CommentTypeDataResolver::class;
    }
}

