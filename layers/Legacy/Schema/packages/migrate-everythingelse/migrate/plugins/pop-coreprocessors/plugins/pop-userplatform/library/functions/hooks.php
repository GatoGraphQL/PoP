<?php
use PoP\Root\Facades\Hooks\HooksAPIFacade;

HooksAPIFacade::getInstance()->addFilter('PoP_Module_Processor_UserTypeaheadComponentFormInputsBase:thumbprint-query', 'addProfileRole');
function addProfileRole($query)
{
    $query['role'] = GD_ROLE_PROFILE;
    return $query;
}
