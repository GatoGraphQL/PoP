<?php

declare(strict_types=1);

namespace PoPSitesWassup\ContactUserMutations\MutationResolvers;

use PoP\ComponentModel\Mutation\FieldDataAccessorInterface;
use PoP_EmailSender_Utils;
use PoP\Root\Exception\AbstractException;
use PoP\Root\App;
use PoP\Application\FunctionAPIFactory;
use PoP\ComponentModel\MutationResolvers\AbstractMutationResolver;
use PoPCMSSchema\Users\TypeAPIs\UserTypeAPIInterface;

class ContactUserMutationResolver extends AbstractMutationResolver
{
    private ?UserTypeAPIInterface $userTypeAPI = null;

    final public function setUserTypeAPI(UserTypeAPIInterface $userTypeAPI): void
    {
        $this->userTypeAPI = $userTypeAPI;
    }
    final protected function getUserTypeAPI(): UserTypeAPIInterface
    {
        return $this->userTypeAPI ??= $this->instanceManager->getInstance(UserTypeAPIInterface::class);
    }

    public function validateErrors(FieldDataAccessorInterface $fieldDataAccessor): array
    {
        $errors = [];
        if (empty($fieldDataAccessor->get('name'))) {
            // @todo Migrate from string to FeedbackItemProvider
            // $errors[] = new FeedbackItemResolution(
            //     MutationErrorFeedbackItemProvider::class,
            //     MutationErrorFeedbackItemProvider::E1,
            // );
            $errors[] = $this->__('Your name cannot be empty.', 'pop-genericforms');
        }

        if (empty($fieldDataAccessor->get('email'))) {
            // @todo Migrate from string to FeedbackItemProvider
            // $errors[] = new FeedbackItemResolution(
            //     MutationErrorFeedbackItemProvider::class,
            //     MutationErrorFeedbackItemProvider::E1,
            // );
            $errors[] = $this->__('Email cannot be empty.', 'pop-genericforms');
        } elseif (!filter_var($fieldDataAccessor->get('email'), FILTER_VALIDATE_EMAIL)) {
            // @todo Migrate from string to FeedbackItemProvider
            // $errors[] = new FeedbackItemResolution(
            //     MutationErrorFeedbackItemProvider::class,
            //     MutationErrorFeedbackItemProvider::E1,
            // );
            $errors[] = $this->__('Email format is incorrect.', 'pop-genericforms');
        }

        if (empty($fieldDataAccessor->get('message'))) {
            // @todo Migrate from string to FeedbackItemProvider
            // $errors[] = new FeedbackItemResolution(
            //     MutationErrorFeedbackItemProvider::class,
            //     MutationErrorFeedbackItemProvider::E1,
            // );
            $errors[] = $this->__('Message cannot be empty.', 'pop-genericforms');
        }

        if (empty($fieldDataAccessor->get('target-id'))) {
            // @todo Migrate from string to FeedbackItemProvider
            // $errors[] = new FeedbackItemResolution(
            //     MutationErrorFeedbackItemProvider::class,
            //     MutationErrorFeedbackItemProvider::E1,
            // );
            $errors[] = $this->__('The requested user cannot be empty.', 'pop-genericforms');
        } else {
            $target = $this->getUserTypeAPI()->getUserByID($fieldDataAccessor->get('target-id'));
            if (!$target) {
                // @todo Migrate from string to FeedbackItemProvider
                // $errors[] = new FeedbackItemResolution(
                //     MutationErrorFeedbackItemProvider::class,
                //     MutationErrorFeedbackItemProvider::E1,
                // );
                $errors[] = $this->__('The requested user does not exist.', 'pop-genericforms');
            }
        }
        return $errors;
    }

    /**
     * Function to override
     */
    protected function additionals(FieldDataAccessorInterface $fieldDataAccessor): void
    {
        App::doAction('pop_contactuser', $fieldDataAccessor);
    }

    protected function doExecute(FieldDataAccessorInterface $fieldDataAccessor)
    {
        $cmsapplicationapi = FunctionAPIFactory::getInstance();
        $websitename = $cmsapplicationapi->getSiteName();
        $subject = sprintf(
            $this->__('[%s]: %s', 'pop-genericforms'),
            $websitename,
            $fieldDataAccessor->get('subject') ? $fieldDataAccessor->get('subject') : sprintf(
                $this->__('%s sends you a message', 'pop-genericforms'),
                $fieldDataAccessor->get('name')
            )
        );
        $placeholder = '<p><b>%s:</b> %s</p>';
        $msg = sprintf(
            '<p>%s</p>',
            sprintf(
                $this->__('You have been sent a message from a user in %s', 'pop-genericforms'),
                $websitename
            )
        ) . sprintf(
            $placeholder,
            $this->__('Name', 'pop-genericforms'),
            $fieldDataAccessor->get('name')
        ) . sprintf(
            $placeholder,
            $this->__('Email', 'pop-genericforms'),
            sprintf(
                '<a href="mailto:%1$s">%1$s</a>',
                $fieldDataAccessor->get('email')
            )
        ) . sprintf(
            $placeholder,
            $this->__('Subject', 'pop-genericforms'),
            $fieldDataAccessor->get('subject')
        ) . sprintf(
            $placeholder,
            $this->__('Message', 'pop-genericforms'),
            $fieldDataAccessor->get('message')
        );

        return PoP_EmailSender_Utils::sendemailToUser($fieldDataAccessor->get('target-id'), $subject, $msg);
    }

    /**
     * @throws AbstractException In case of error
     */
    public function executeMutation(FieldDataAccessorInterface $fieldDataAccessor): mixed
    {
        $result = $this->doExecute($fieldDataAccessor);

        // Allow for additional operations
        $this->additionals($fieldDataAccessor);

        return $result;
    }
}
