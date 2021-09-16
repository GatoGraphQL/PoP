<?php
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\FieldResolvers\ObjectType\AbstractObjectTypeFieldResolver;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\Schema\SchemaTypeModifiers;
use PoP\ComponentModel\State\ApplicationState;
use PoP\ComponentModel\TypeResolvers\ObjectType\ObjectTypeResolverInterface;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoPSchema\Comments\ConditionalOnComponent\Users\Facades\CommentTypeAPIFacade as UserCommentTypeAPIFacade;
use PoPSchema\Comments\Facades\CommentTypeAPIFacade;
use PoPSchema\Comments\TypeResolvers\ObjectType\CommentObjectTypeResolver;
use PoPSchema\CustomPosts\Facades\CustomPostTypeAPIFacade;
use PoPSchema\Notifications\TypeResolvers\ObjectType\NotificationObjectTypeResolver;
use PoPSchema\Users\Facades\UserTypeAPIFacade;

class PoP_AddComments_DataLoad_ObjectTypeFieldResolver_Notifications extends AbstractObjectTypeFieldResolver
{
    public function getObjectTypeResolverClassesToAttachTo(): array
    {
        return [
            NotificationObjectTypeResolver::class,
        ];
    }

    public function getFieldNamesToResolve(): array
    {
        return [
            'commentObject',
            'commentObjectID',
            'icon',
            'url',
            'message',
        ];
    }

    public function getSchemaFieldType(RelationalTypeResolverInterface $relationalTypeResolver, string $fieldName): string
    {
        return match ($fieldName) {
            'commentObjectID' => SchemaDefinition::TYPE_ID,
            'icon' => SchemaDefinition::TYPE_STRING,
            'url' => SchemaDefinition::TYPE_URL,
            'message' => SchemaDefinition::TYPE_STRING,
            default => parent::getSchemaFieldType($relationalTypeResolver, $fieldName),
        };
    }

    public function getSchemaFieldTypeModifiers(RelationalTypeResolverInterface $relationalTypeResolver, string $fieldName): ?int
    {
        $nonNullableFieldNames = [
            'commentObject',
            'commentObjectID',
        ];
        if (in_array($fieldName, $nonNullableFieldNames)) {
            return SchemaTypeModifiers::NON_NULLABLE;
        }
        return parent::getSchemaFieldTypeModifiers($relationalTypeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(RelationalTypeResolverInterface $relationalTypeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'commentObject' => $translationAPI->__('', ''),
            'commentObjectID' => $translationAPI->__('', ''),
            'icon' => $translationAPI->__('', ''),
            'url' => $translationAPI->__('', ''),
            'message' => $translationAPI->__('', ''),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($relationalTypeResolver, $fieldName);
    }

    /**
     * @param array<string, mixed> $fieldArgs
     */
    public function resolveCanProcessObject(
        RelationalTypeResolverInterface $relationalTypeResolver,
        object $object,
        string $fieldName,
        array $fieldArgs = []
    ): bool {
        $notification = $object;
        return $notification->object_type == 'Comments' && in_array(
            $notification->action,
            [
                AAL_POP_ACTION_COMMENT_ADDED,
                AAL_POP_ACTION_COMMENT_SPAMMEDCOMMENT,
            ]
        );
    }

    /**
     * @param array<string, mixed> $fieldArgs
     * @param array<string, mixed>|null $variables
     * @param array<string, mixed>|null $expressions
     * @param array<string, mixed> $options
     */
    public function resolveValue(
        RelationalTypeResolverInterface $relationalTypeResolver,
        object $object,
        string $fieldName,
        array $fieldArgs = [],
        ?array $variables = null,
        ?array $expressions = null,
        array $options = []
    ): mixed {
        $vars = ApplicationState::getVars();
        $commentTypeAPI = CommentTypeAPIFacade::getInstance();
        $userTypeAPI = UserTypeAPIFacade::getInstance();
        $customPostTypeAPI = CustomPostTypeAPIFacade::getInstance();
        $notification = $object;
        switch ($fieldName) {
            // Specific fields to be used by the subcomponents, based on a combination of Object Type + Action
            // Needed to, for instance, load the comment immediately, already from the notification
            case 'commentObject':
            case 'commentObjectID':
                switch ($notification->action) {
                    case AAL_POP_ACTION_COMMENT_ADDED:
                        // comment-object-id is the object-id
                        return $relationalTypeResolver->resolveValue($object, FieldQueryInterpreterFacade::getInstance()->getField('objectID', $fieldArgs), $variables, $expressions, $options);
                }
                return null;

            case 'icon':
                // URL depends basically on the action performed on the object type
                switch ($notification->action) {
                    case AAL_POP_ACTION_COMMENT_SPAMMEDCOMMENT:
                        $icons = array(
                            AAL_POP_ACTION_COMMENT_SPAMMEDCOMMENT => 'fa-trash',
                        );
                        return $icons[$notification->action];

                    case AAL_POP_ACTION_COMMENT_ADDED:
                        $routes = array(
                            AAL_POP_ACTION_COMMENT_ADDED => POP_ADDCOMMENTS_ROUTE_ADDCOMMENT,
                        );
                        return getRouteIcon($routes[$notification->action], false);
                }
                return null;

            case 'url':
                // URL depends basically on the action performed on the object type
                switch ($notification->action) {
                    case AAL_POP_ACTION_COMMENT_SPAMMEDCOMMENT:
                        // $pages = array(
                        //     AAL_POP_ACTION_COMMENT_SPAMMEDCOMMENT => POP_ADDCOMMENTS_PAGEPLACEHOLDER_SPAMMEDCOMMENTNOTIFICATION,
                        // );
                        // return $cmspagesapi->getPageURL($pages[$notification->action]);
                        return POP_ADDCOMMENTS_URLPLACEHOLDER_SPAMMEDCOMMENTNOTIFICATION;
                }
                return null;

            case 'message':
                // Message depends basically on the action performed on the object type
                switch ($notification->action) {
                    case AAL_POP_ACTION_COMMENT_SPAMMEDCOMMENT:
                        $comment = $commentTypeAPI->getComment($notification->object_id);
                        $messages = array(
                            AAL_POP_ACTION_COMMENT_SPAMMEDCOMMENT => TranslationAPIFacade::getInstance()->__('Your comment on %1$s <strong>%2$s</strong> was deleted for not adhering to our content guidelines', 'pop-notifications'),
                        );
                        return sprintf(
                            $messages[$notification->action],
                            gdGetPostname($commentTypeAPI->getCommentPostId($comment), 'lc'),
                            $customPostTypeAPI->getTitle($commentTypeAPI->getCommentPostId($comment))
                        );

                    case AAL_POP_ACTION_COMMENT_ADDED:
                        // TODO: Integrate with `CommentsComponentConfiguration::mustUserBeLoggedInToAddComment()`
                        $comment = $commentTypeAPI->getComment($notification->object_id);
                        $user_id = $vars['global-userstate']['current-user-id'];

                        // Change the message if the comment is a response to the user's comment
                        $message = TranslationAPIFacade::getInstance()->__('<strong>%1$s</strong> commented in %2$s <strong>%3$s</strong>', 'pop-notifications');
                        if ($comment_parent_id = $commentTypeAPI->getCommentParent($comment)) {
                            $comment_parent = $commentTypeAPI->getComment($comment_parent_id);
                            $userCommentTypeAPI = UserCommentTypeAPIFacade::getInstance();
                            if ($userCommentTypeAPI->getCommentUserId($comment_parent) == $user_id) {
                                $message = TranslationAPIFacade::getInstance()->__('<strong>%1$s</strong> replied to your comment in %2$s <strong>%3$s</strong>', 'pop-notifications');
                            }
                        }

                        // Allow PoP Social Network to make the message more precise
                        $message = HooksAPIFacade::getInstance()->applyFilters(
                            'PoP_AddComments_DataLoad_TypeResolver_Notifications_Hook:comment-added:message',
                            $message,
                            $notification
                        );

                        return sprintf(
                            $message,
                            $userTypeAPI->getUserDisplayName($notification->user_id),
                            gdGetPostname($commentTypeAPI->getCommentPostId($comment), 'lc'),
                            $customPostTypeAPI->getTitle($commentTypeAPI->getCommentPostId($comment))
                        );
                }
                return null;
        }

        return parent::resolveValue($relationalTypeResolver, $object, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function getFieldTypeResolverClass(ObjectTypeResolverInterface $objectTypeResolver, string $fieldName): string
    {
        switch ($fieldName) {
            case 'commentObjectID':
                return CommentObjectTypeResolver::class;
        }

        return parent::getFieldTypeResolverClass($objectTypeResolver, $fieldName);
    }
}

// Static Initialization: Attach
(new PoP_AddComments_DataLoad_ObjectTypeFieldResolver_Notifications())->attach(\PoP\ComponentModel\AttachableExtensions\AttachableExtensionGroups::OBJECT_TYPE_FIELD_RESOLVERS, 20);
