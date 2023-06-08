<?php

declare(strict_types=1);

namespace PHPUnitForGatoGraphQL\WebserverRequests;

use GuzzleHttp\RequestOptions;
use PoP\ComponentModel\Misc\GeneralUtils;
use RuntimeException;

abstract class AbstractEndpointWebserverRequestTestCase extends AbstractWebserverRequestTestCase
{
    public const RESPONSE_COMPARISON_EQUALS = 0;
    public const RESPONSE_COMPARISON_NOT_EQUALS = 1;
    public const RESPONSE_COMPARISON_REGEX = 2;

    /**
     * @param array<string,mixed> $params
     * @param array<string,mixed> $variables
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideEndpointEntries')]
    public function testEndpoints(
        string $expectedContentType,
        ?string $expectedResponseBody,
        string $endpoint,
        array $params = [],
        string $query = '',
        array $variables = [],
        ?string $operationName = null,
        ?string $method = null,
    ): void {
        $method ??= $this->getMethod();
        if (!in_array($method, ["POST", "GET"])) {
            throw new RuntimeException(sprintf(
                'Unsupported method \'%s\' for testing a GraphQL endpoint',
                $method
            ));
        }

        $doingGET = $method === 'GET';
        $doingPOST = $method === 'POST';

        $client = static::getClient();
        $endpointURL = static::getWebserverHomeURL() . '/' . $endpoint;
        $options = static::getRequestBasicOptions();

        if ($doingPOST) {
            $options[RequestOptions::BODY] = json_encode(array_merge(
                [
                    'operationName' => $operationName ?? '',
                    'query' => $query,
                ],
                $variables !== []
                    ? [
                        'variables' => $variables,
                    ] : []
            ));
        } elseif ($doingGET) {
            $options[RequestOptions::QUERY] = array_merge(
                $options[RequestOptions::QUERY] ?? [],
                [
                    'operationName' => $operationName ?? '',
                    'query' => $query,
                ],
                $variables !== []
                    ? [
                        'variables' => $variables,
                    ] : []
            );
        }

        if ($params !== []) {
            /** @var array<string,mixed> */
            $params = static::maybeAddXDebugTriggerParam($params);
            $options[RequestOptions::QUERY] = $params;
        } else {
            /** @var string */
            $endpointURL = static::maybeAddXDebugTriggerParam($endpointURL);
        }

        if ($doingGET) {
            /**
             * Because by passing option "query" Guzzle will ignore
             * any URL param in the endpoint, copy these as queryParams.
             *
             * URL params override the GraphQL vars, so that passing
             * ?query=... overrides the query passed in the unit test.
             *
             * @see PassQueryViaURLParamQueryExecutionFixtureWebserverRequestTest.php
             */
            $options[RequestOptions::QUERY] = array_merge(
                $options[RequestOptions::QUERY],
                GeneralUtils::getURLQueryParams($endpointURL)
            );
        }

        $expectedResponseStatusCode = static::getExpectedResponseStatusCode();
        if ($expectedResponseStatusCode !== 200) {
            $options[RequestOptions::HTTP_ERRORS] = false;
        }

        $options = static::customizeRequestOptions($options);

        $response = $client->request(
            $method,
            $endpointURL,
            $options
        );

        static::assertEquals($expectedResponseStatusCode, $response->getStatusCode());
        static::assertStringStartsWith($expectedContentType, $response->getHeaderLine('content-type'));
        if ($expectedResponseBody !== null) {
            $responseBody = $response->getBody()->__toString();
            // Allow to modify the URLs for the "PROD Integration Tests"
            $responseBody = $this->adaptResponseBody($responseBody);
            $responseComparisonType = static::getResponseComparisonType();
            if ($responseComparisonType === self::RESPONSE_COMPARISON_EQUALS) {
                static::assertJsonStringEqualsJsonString($expectedResponseBody, $responseBody);
            } elseif ($responseComparisonType === self::RESPONSE_COMPARISON_NOT_EQUALS) {
                static::assertJsonStringNotEqualsJsonString($expectedResponseBody, $responseBody);
            } elseif ($responseComparisonType === self::RESPONSE_COMPARISON_REGEX) {
                static::assertMatchesRegularExpression($expectedResponseBody, $responseBody);
            }
        }
    }

    protected function adaptResponseBody(string $responseBody): string
    {
        return $responseBody;
    }

    protected static function getResponseComparisonType(): ?int
    {
        return self::RESPONSE_COMPARISON_EQUALS;
    }

    /**
     * @param string|array<string,mixed> $urlOrParams
     * @return string|array<string,mixed>
     */
    protected static function maybeAddXDebugTriggerParam(string|array $urlOrParams): string|array
    {
        if (getenv('XDEBUG_TRIGGER') === false) {
            return $urlOrParams;
        }
        $xdebugParams = [
            'XDEBUG_TRIGGER' => getenv('XDEBUG_TRIGGER'),
            /**
             * Must also pass ?XDEBUG_SESSION_STOP=1 in the URL to avoid
             * setting cookie XDEBUG_SESSION="1", which launches the
             * debugger every single time
             */
            'XDEBUG_SESSION_STOP' => '1',
        ];
        if (is_array($urlOrParams)) {
            /** @var string[] */
            $params = $urlOrParams;
            return array_merge(
                $params,
                $xdebugParams
            );
        }
        /** @var string */
        $url = $urlOrParams;
        return GeneralUtils::addQueryArgs($xdebugParams, $url);
    }

    /**
     * @return array<string,mixed>
     */
    protected static function getRequestBasicOptions(): array
    {
        $options = parent::getRequestBasicOptions();
        $options[RequestOptions::HEADERS]['Content-Type'] = 'application/json';
        return $options;
    }

    protected static function getExpectedResponseStatusCode(): int
    {
        return 200;
    }

    /**
     * @param array<string,mixed> $options
     * @return array<string,mixed>
     */
    protected static function customizeRequestOptions(array $options): array
    {
        return $options;
    }

    /**
     * @return array<string,array<mixed>>
     */
    abstract public static function provideEndpointEntries(): array;

    protected function getMethod(): string
    {
        return 'POST';
    }
}
