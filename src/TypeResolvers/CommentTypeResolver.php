<?php
namespace PoP\Comments\TypeResolvers;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\Comments\TypeDataLoaders\CommentTypeDataLoader;
use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;

class CommentTypeResolver extends AbstractTypeResolver
{
    public const NAME = 'Comment';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Comments added to posts', 'comments');
    }

    public function getID($resultItem)
    {
        $cmscommentsresolver = \PoP\Comments\ObjectPropertyResolverFactory::getInstance();
        $comment = $resultItem;
        return $cmscommentsresolver->getCommentId($comment);
    }

    public function getTypeDataLoaderClass(): string
    {
        return CommentTypeDataLoader::class;
    }
}
