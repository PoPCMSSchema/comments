<?php
namespace PoP\Comments\FieldResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Posts\TypeResolvers\PostTypeResolver;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\LooseContracts\Facades\NameResolverFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\Comments\TypeResolvers\CommentTypeResolver;

class PostFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(PostTypeResolver::class);
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'comments-url',
            'comments-count',
            'has-comments',
            'published-with-comments',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
			'comments-url' => SchemaDefinition::TYPE_URL,
            'comments-count' => SchemaDefinition::TYPE_INT,
            'has-comments' => SchemaDefinition::TYPE_BOOL,
            'published-with-comments' => SchemaDefinition::TYPE_BOOL,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
			'comments-url' => $translationAPI->__('URL of the comments section in the post page', 'pop-comments'),
            'comments-count' => $translationAPI->__('Number of comments added to the post', 'pop-comments'),
            'has-comments' => $translationAPI->__('Does the post have comments?', 'pop-comments'),
            'published-with-comments' => $translationAPI->__('Is the post published and does it have comments?', 'pop-comments'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $cmscommentsapi = \PoP\Comments\FunctionAPIFactory::getInstance();
        $post = $resultItem;
        switch ($fieldName) {
            case 'comments-url':
                return $typeResolver->resolveValue($post, 'url', $variables, $expressions, $options);

            case 'comments-count':
                return $cmscommentsapi->getCommentsNumber($typeResolver->getId($post));

            case 'has-comments':
                return $typeResolver->resolveValue($post, 'comments-count', $variables, $expressions, $options) > 0;

            case 'published-with-comments':
                return $typeResolver->resolveValue($post, FieldQueryInterpreterFacade::getInstance()->getField('is-status', ['status' => POP_POSTSTATUS_PUBLISHED]), $variables, $expressions, $options) && $typeResolver->resolveValue($post, 'has-comments', $variables, $expressions, $options);
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
