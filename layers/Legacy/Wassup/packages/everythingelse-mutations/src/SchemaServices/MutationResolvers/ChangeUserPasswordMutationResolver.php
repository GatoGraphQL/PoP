<?php

declare(strict_types=1);

namespace PoPSitesWassup\EverythingElseMutations\SchemaServices\MutationResolvers;

use PoP\GraphQLParser\Spec\Parser\Ast\WithArgumentsInterface;
use PoP\Root\Exception\AbstractException;
use PoP\Root\App;
use PoP\ComponentModel\MutationResolvers\AbstractMutationResolver;
use PoP\UserAccount\FunctionAPIFactory;

class ChangeUserPasswordMutationResolver extends AbstractMutationResolver
{
    public function validateErrors(WithArgumentsInterface $withArgumentsAST): array
    {
        $errors = [];
        $cmsuseraccountapi = FunctionAPIFactory::getInstance();
        // Validate Password
        // Check current password really belongs to the user
        $current_password = $withArgumentsAST->getArgumentValue('current_password');
        $password = $withArgumentsAST->getArgumentValue('password');
        $repeatpassword =  $withArgumentsAST->getArgumentValue('repeat_password');

        if (!$current_password) {
            // @todo Migrate from string to FeedbackItemProvider
            // $errors[] = new FeedbackItemResolution(
            //     MutationErrorFeedbackItemProvider::class,
            //     MutationErrorFeedbackItemProvider::E1,
            // );
            $errors[] = $this->getTranslationAPI()->__('Please provide the current password.', 'pop-application');
        } elseif (!$cmsuseraccountapi->checkPassword($withArgumentsAST->getArgumentValue('user_id'), $current_password)) {
            // @todo Migrate from string to FeedbackItemProvider
            // $errors[] = new FeedbackItemResolution(
            //     MutationErrorFeedbackItemProvider::class,
            //     MutationErrorFeedbackItemProvider::E1,
            // );
            $errors[] = $this->getTranslationAPI()->__('Current password is incorrect.', 'pop-application');
        }

        if (!$password) {
            // @todo Migrate from string to FeedbackItemProvider
            // $errors[] = new FeedbackItemResolution(
            //     MutationErrorFeedbackItemProvider::class,
            //     MutationErrorFeedbackItemProvider::E1,
            // );
            $errors[] = $this->getTranslationAPI()->__('The password cannot be emtpy.', 'pop-application');
        } elseif (strlen($password) < 8) {
            // @todo Migrate from string to FeedbackItemProvider
            // $errors[] = new FeedbackItemResolution(
            //     MutationErrorFeedbackItemProvider::class,
            //     MutationErrorFeedbackItemProvider::E1,
            // );
            $errors[] = $this->getTranslationAPI()->__('The password must be at least 8 characters long.', 'pop-application');
        } else {
            if (!$repeatpassword) {
                // @todo Migrate from string to FeedbackItemProvider
                // $errors[] = new FeedbackItemResolution(
                //     MutationErrorFeedbackItemProvider::class,
                //     MutationErrorFeedbackItemProvider::E1,
                // );
                $errors[] = $this->getTranslationAPI()->__('Please confirm the password.', 'pop-application');
            } elseif ($password !== $repeatpassword) {
                // @todo Migrate from string to FeedbackItemProvider
                // $errors[] = new FeedbackItemResolution(
                //     MutationErrorFeedbackItemProvider::class,
                //     MutationErrorFeedbackItemProvider::E1,
                // );
                $errors[] = $this->getTranslationAPI()->__('Passwords do not match.', 'pop-application');
            }
        }
        return $errors;
    }

    protected function executeChangepassword($user_data)
    {
        $cmseditusersapi = \PoP\EditUsers\FunctionAPIFactory::getInstance();
        return $cmseditusersapi->updateUser($user_data);
    }

    protected function getChangepasswordData(WithArgumentsInterface $withArgumentsAST)
    {
        $user_data = array(
            'id' => $withArgumentsAST->getArgumentValue('user_id'),
            'password' => $withArgumentsAST->getArgumentValue('password')
        );

        return $user_data;
    }

    /**
     * @throws AbstractException In case of error
     */
    public function executeMutation(WithArgumentsInterface $withArgumentsAST): mixed
    {
        $user_data = $this->getChangepasswordData($withArgumentsAST);
        $result = $this->executeChangepassword($user_data);

        $user_id = $user_data['ID'];

        App::doAction('gd_changepassword_user', $user_id, $withArgumentsAST);

        return $user_id;
    }
}
