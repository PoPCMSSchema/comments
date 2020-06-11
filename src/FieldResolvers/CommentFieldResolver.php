<?php

declare(strict_types=1);

namespace PoP\Comments\FieldResolvers;

use PoP\Users\TypeResolvers\UserTypeResolver;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\LooseContracts\Facades\NameResolverFacade;
use PoP\Comments\TypeResolvers\CommentTypeResolver;
use PoP\ComponentModel\TypeResolvers\UnionTypeHelpers;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\CustomPosts\TypeResolvers\CustomPostUnionTypeResolver;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;

class CommentFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(CommentTypeResolver::class);
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'content',
            'authorName',
            'authorURL',
            'authorEmail',
            'author',
            'post',
            'postID',
            'approved',
            'type',
            'parent',
            'date',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'content' => SchemaDefinition::TYPE_STRING,
            'authorName' => SchemaDefinition::TYPE_STRING,
            'authorURL' => SchemaDefinition::TYPE_URL,
            'authorEmail' => SchemaDefinition::TYPE_EMAIL,
            'author' => SchemaDefinition::TYPE_ID,
            'post' => SchemaDefinition::TYPE_ID,
            'postID' => SchemaDefinition::TYPE_ID,//SchemaDefinition::TYPE_UNRESOLVED_ID,
            'approved' => SchemaDefinition::TYPE_BOOL,
            'type' => SchemaDefinition::TYPE_STRING,
            'parent' => SchemaDefinition::TYPE_ID,
            'date' => SchemaDefinition::TYPE_DATE,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function isSchemaFieldResponseNonNullable(TypeResolverInterface $typeResolver, string $fieldName): bool
    {
        switch ($fieldName) {
            case 'content':
            case 'post':
            case 'postID':
            case 'approved':
            case 'type':
            case 'date':
                return true;
        }
        return parent::isSchemaFieldResponseNonNullable($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'content' => $translationAPI->__('Comment\'s content', 'pop-comments'),
            'authorName' => $translationAPI->__('Comment author\'s name', 'pop-comments'),
            'authorURL' => $translationAPI->__('Comment author\'s URL', 'pop-comments'),
            'authorEmail' => $translationAPI->__('Comment author\'s email', 'pop-comments'),
            'author' => $translationAPI->__('Comment\'s author', 'pop-comments'),
            'post' => $translationAPI->__('Post to which the comment was added', 'pop-comments'),
            'postID' => $translationAPI->__('Post to which the comment was added', 'pop-comments'),
            'approved' => $translationAPI->__('Is the comment approved?', 'pop-comments'),
            'type' => $translationAPI->__('Type of comment', 'pop-comments'),
            'parent' => $translationAPI->__('Parent comment (if this comment is a response to another one)', 'pop-comments'),
            'date' => $translationAPI->__('Date when the comment was added', 'pop-comments'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $cmscommentsresolver = \PoP\Comments\ObjectPropertyResolverFactory::getInstance();
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        $cmsusersapi = \PoP\Users\FunctionAPIFactory::getInstance();
        $comment = $resultItem;
        switch ($fieldName) {
            case 'content':
                return $cmscommentsresolver->getCommentContent($comment);

            case 'authorName':
                return $cmsusersapi->getUserDisplayName($cmscommentsresolver->getCommentUserId($comment));

            case 'authorURL':
                return $cmsusersapi->getUserURL($cmscommentsresolver->getCommentUserId($comment));

            case 'authorEmail':
                return $cmsusersapi->getUserEmail($cmscommentsresolver->getCommentUserId($comment));

            case 'author':
                return $cmscommentsresolver->getCommentUserId($comment);

            case 'post':
            case 'postID':
                return $cmscommentsresolver->getCommentPostId($comment);

            case 'approved':
                return $cmscommentsresolver->isCommentApproved($comment);

            case 'type':
                return $cmscommentsresolver->getCommentType($comment);

            case 'parent':
                return $cmscommentsresolver->getCommentParent($comment);

            case 'date':
                $format = $fieldArgs['format'] ?? $cmsengineapi->getOption(NameResolverFacade::getInstance()->getName('popcms:option:dateFormat'));
                return $cmsengineapi->getDate($format, $cmscommentsresolver->getCommentDateGmt($comment));
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function getSchemaFieldArgs(TypeResolverInterface $typeResolver, string $fieldName): array
    {
        $schemaFieldArgs = parent::getSchemaFieldArgs($typeResolver, $fieldName);
        $translationAPI = TranslationAPIFacade::getInstance();
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        switch ($fieldName) {
            case 'date':
                return array_merge(
                    $schemaFieldArgs,
                    [
                        [
                            SchemaDefinition::ARGNAME_NAME => 'format',
                            SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                            SchemaDefinition::ARGNAME_DESCRIPTION => sprintf(
                                $translationAPI->__('Date format, as defined in %s', 'pop-comments'),
                                'https://www.php.net/manual/en/function.date.php'
                            ),
                            SchemaDefinition::ARGNAME_DEFAULT_VALUE => $cmsengineapi->getOption(NameResolverFacade::getInstance()->getName('popcms:option:dateFormat')),
                        ],
                    ]
                );
        }

        return $schemaFieldArgs;
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'author':
                return UserTypeResolver::class;

            case 'post':
                return UnionTypeHelpers::getUnionOrTargetTypeResolverClass(CustomPostUnionTypeResolver::class);

            case 'parent':
                return CommentTypeResolver::class;
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
