<?php

declare(strict_types=1);

namespace PHPUnitForGraphQLAPI\GraphQLAPITesting\RESTAPI\Controllers;

use Exception;
use PHPUnitForGraphQLAPI\GraphQLAPITesting\RESTAPI\Constants\ResponseStatus;
use PHPUnitForGraphQLAPI\GraphQLAPITesting\RESTAPI\RESTResponse;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use function rest_ensure_response;

class ModulesAdminRESTController extends AbstractAdminRESTController
{
	final public const MODULE_STATES = [
		'enabled',
		'disabled',
	];
	final public const PARAM_STATE = 'state';

	protected string $restBase = 'modules';

	/**
	 * @return array<string,array<array<string,mixed>>> Array of [$route => [$options]]
	 */
	protected function getRouteOptions(): array
	{
		return [
			$this->restBase => [
				[
					'methods' => [
						WP_REST_Server::READABLE,
						WP_REST_Server::CREATABLE,
					],
					'callback' => $this->retrieveAllItems(...),
					'permission_callback' => $this->checkAdminPermission(...),
				],
			],
			$this->restBase . '/(?P<module>[a-zA-Z_-]+)' => [
				[
					'methods' => [
						WP_REST_Server::READABLE,
						WP_REST_Server::CREATABLE,
					],
					'callback' => $this->enableOrDisableModule(...),
					'permission_callback' => $this->checkAdminPermission(...),
					'args' => [
						self::PARAM_STATE => [
							'required' => true,
							'validate_callback' => $this->validateCallback(...),
						],
						'module' => [
							'description' => __('Module name', 'graphql-api'),
							'type' => 'string',
							'required' => true,
						],
					],
				],
			],
		];
	}

	protected function validateCallback(string $value): bool|WP_Error
	{
		if (!in_array($value, self::MODULE_STATES)) {			
			return new WP_Error(
				'1',
				sprintf(
					__('Parameter \'state\' can only have one of these values: \'%s\'', 'graphql-api'),
					implode(__('\', \'', 'graphql-api'), self::MODULE_STATES)
				),
				[
					self::PARAM_STATE => $value,
				]
			);
		}
		return true;
	}

	public function retrieveAllItems(WP_REST_Request $request): WP_REST_Response|WP_Error
	{
		$modules = ['a', 'zzzzonga'];
		return rest_ensure_response($modules);
	}

	public function enableOrDisableModule(WP_REST_Request $request): WP_REST_Response|WP_Error
	{
		$response = new RESTResponse();

		try {
			$namespacedRoute = $request->get_route();
			$module = substr($this->getRouteFromNamespacedRoute($namespacedRoute), strlen($this->restBase . '/'));

			$params = $request->get_params();
			$moduleState = $params[self::PARAM_STATE];

			// @todo Remove this temporary code
			$response->data->moduleState = $moduleState;

			// Success!
			$response->status = ResponseStatus::SUCCESS;
			$response->message = sprintf(
				__('Module \'%s\' has been updated successfully %s', 'graphql-api'),
				$module,
				$moduleState
			);
		} catch ( Exception $e ) {
			$response->status = ResponseStatus::ERROR;
			$response->message = $e->getMessage();
		}

		return rest_ensure_response($response);
	}
}
