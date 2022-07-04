<?php

declare(strict_types=1);

namespace PoPSitesWassup\EverythingElseMutations\SchemaServices\MutationResolverBridges;

use PoP\GraphQLParser\Spec\Parser\Ast\FieldInterface;
use PoP\GraphQLParser\Spec\Parser\Ast\Argument;
use PoP\GraphQLParser\StaticHelpers\LocationHelper;
use PoP\ComponentModel\MutationResolverBridges\AbstractComponentMutationResolverBridge;
use PoP\ComponentModel\MutationResolvers\MutationResolverInterface;
use PoP\GraphQLParser\Spec\Parser\Ast\ArgumentValue\InputList;
use PoP\GraphQLParser\Spec\Parser\Ast\ArgumentValue\Literal;
use PoP\Root\App;
use PoP_Module_Processor_UserProfileCheckboxFormInputs;
use PoPSitesWassup\EverythingElseMutations\SchemaServices\MutationResolvers\UpdateMyPreferencesMutationResolver;

class UpdateMyPreferencesMutationResolverBridge extends AbstractComponentMutationResolverBridge
{
    private ?UpdateMyPreferencesMutationResolver $updateMyPreferencesMutationResolver = null;
    
    final public function setUpdateMyPreferencesMutationResolver(UpdateMyPreferencesMutationResolver $updateMyPreferencesMutationResolver): void
    {
        $this->updateMyPreferencesMutationResolver = $updateMyPreferencesMutationResolver;
    }
    final protected function getUpdateMyPreferencesMutationResolver(): UpdateMyPreferencesMutationResolver
    {
        return $this->updateMyPreferencesMutationResolver ??= $this->instanceManager->getInstance(UpdateMyPreferencesMutationResolver::class);
    }
    
    public function getMutationResolver(): MutationResolverInterface
    {
        return $this->getUpdateMyPreferencesMutationResolver();
    }

    public function appendMutationDataToFieldDataAccessor(\PoP\ComponentModel\Mutation\FieldDataAccessorInterface $fieldDataAccessor): void
    {
        $user_id = App::getState('is-user-logged-in') ? App::getState('current-user-id') : '';
        
        $fieldDataAccessor->add('user_id', $user_id);
            // We can just get the value for any one forminput from the My Preferences form, since they all have the same name (and even if the forminput was actually removed from the form!)
        $fieldDataAccessor->add('userPreferences', $this->getComponentProcessorManager()->getComponentProcessor([PoP_Module_Processor_UserProfileCheckboxFormInputs::class, PoP_Module_Processor_UserProfileCheckboxFormInputs::COMPONENT_FORMINPUT_EMAILNOTIFICATIONS_GENERAL_NEWPOST])->getValue([PoP_Module_Processor_UserProfileCheckboxFormInputs::class, PoP_Module_Processor_UserProfileCheckboxFormInputs::COMPONENT_FORMINPUT_EMAILNOTIFICATIONS_GENERAL_NEWPOST]));
    }
}
