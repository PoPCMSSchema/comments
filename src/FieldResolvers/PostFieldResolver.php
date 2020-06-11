<?php

declare(strict_types=1);

namespace PoP\Comments\FieldResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\Content\FieldInterfaces\CustomPostFieldInterfaceResolver;
use PoP\Content\Types\Status;

class PostFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return [
            CustomPostFieldInterfaceResolver::class,
        ];
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'commentsURL',
            'commentsCount',
            'hasComments',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'commentsURL' => SchemaDefinition::TYPE_URL,
            'commentsCount' => SchemaDefinition::TYPE_INT,
            'hasComments' => SchemaDefinition::TYPE_BOOL,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function isSchemaFieldResponseNonNullable(TypeResolverInterface $typeResolver, string $fieldName): bool
    {
        switch ($fieldName) {
            case 'commentsCount':
            case 'hasComments':
                return true;
        }
        return parent::isSchemaFieldResponseNonNullable($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'commentsURL' => $translationAPI->__('URL of the comments section in the post page', 'pop-comments'),
            'commentsCount' => $translationAPI->__('Number of comments added to the post', 'pop-comments'),
            'hasComments' => $translationAPI->__('Does the post have comments?', 'pop-comments'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $cmscommentsapi = \PoP\Comments\FunctionAPIFactory::getInstance();
        $post = $resultItem;
        switch ($fieldName) {
            case 'commentsURL':
                return $typeResolver->resolveValue($post, 'url', $variables, $expressions, $options);

            case 'commentsCount':
                return $cmscommentsapi->getCommentsNumber($typeResolver->getID($post));

            case 'hasComments':
                return $typeResolver->resolveValue($post, 'commentsCount', $variables, $expressions, $options) > 0;
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
