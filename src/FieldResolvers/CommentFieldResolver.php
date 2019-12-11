<?php
namespace PoP\Comments\FieldResolvers;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\LooseContracts\Facades\NameResolverFacade;
use PoP\Comments\TypeResolvers\CommentTypeResolver;
use PoP\Posts\TypeResolvers\PostConvertibleTypeResolver;
use PoP\Users\TypeResolvers\UserTypeResolver;

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
            'author-name',
            'author-url',
            'author-email',
            'author',
            'post',
            'post-id',
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
            'author-name' => SchemaDefinition::TYPE_STRING,
            'author-url' => SchemaDefinition::TYPE_URL,
            'author-email' => SchemaDefinition::TYPE_EMAIL,
            'author' => SchemaDefinition::TYPE_ID,
            'post' => SchemaDefinition::TYPE_ID,
            'post-id' => SchemaDefinition::TYPE_ID,
            'approved' => SchemaDefinition::TYPE_BOOL,
            'type' => SchemaDefinition::TYPE_STRING,
            'parent' => SchemaDefinition::TYPE_ID,
            'date' => SchemaDefinition::TYPE_DATE,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
			'content' => $translationAPI->__('Comment\'s content', 'pop-comments'),
            'author-name' => $translationAPI->__('Comment author\'s name', 'pop-comments'),
            'author-url' => $translationAPI->__('Comment author\'s URL', 'pop-comments'),
            'author-email' => $translationAPI->__('Comment author\'s email', 'pop-comments'),
            'author' => $translationAPI->__('ID of the comment\'s author', 'pop-comments'),
            'post' => $translationAPI->__('ID of the post to which the comment was added', 'pop-comments'),
            'post-id' => $translationAPI->__('ID of the post to which the comment was added', 'pop-comments'),
            'approved' => $translationAPI->__('Is the comment approved?', 'pop-comments'),
            'type' => $translationAPI->__('Type of comment', 'pop-comments'),
            'parent' => $translationAPI->__('ID of the parent comment (if this comment is a response to another one)', 'pop-comments'),
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

            case 'author-name':
                return $cmsusersapi->getUserDisplayName($cmscommentsresolver->getCommentUserId($comment));

            case 'author-url':
                return $cmsusersapi->getUserURL($cmscommentsresolver->getCommentUserId($comment));

            case 'author-email':
                return $cmsusersapi->getUserEmail($cmscommentsresolver->getCommentUserId($comment));

            case 'author':
                return $cmscommentsresolver->getCommentUserId($comment);

            case 'post':
            case 'post-id':
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
        $translationAPI = TranslationAPIFacade::getInstance();
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        switch ($fieldName) {
            case 'date':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'format',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => sprintf(
                            $translationAPI->__('Date format, as defined in %s. By default it is \'%s\'', 'pop-comments'),
                            'https://www.php.net/manual/en/function.date.php',
                            $cmsengineapi->getOption(NameResolverFacade::getInstance()->getName('popcms:option:dateFormat'))
                        ),
                    ],
                ];
        }

        return parent::getSchemaFieldArgs($typeResolver, $fieldName);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'author':
                return UserTypeResolver::class;

            case 'post':
            case 'post-id':
                return PostConvertibleTypeResolver::class;

            case 'parent':
                return CommentTypeResolver::class;
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
