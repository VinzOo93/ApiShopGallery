<?php

namespace App\Swagger;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\OpenApi;

class JwtRefreshTokenDecorator implements OpenApiFactoryInterface
{
    /**
     * __construct
     *
     * @param  OpenApiFactoryInterface $decorated
     * @return void
     */
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    /**
     * __invoke
     *
     * @param  array $context
     * @return OpenApi
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['Credentials'] = new \ArrayObject([
            'refresh_token' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'c474c4ca2ecb96633c942382eb286c909bbe503d66862c8ffd51d258ae44105e7403540bb1372c80d3e1acf23f58aadb76120ce66dd9e8496c9cae13f15bd285',
                ],
            ],
        ]);
        $pathItem = new Model\PathItem(
            '/token/refresh',
            null,
            null,
            null,
            null,

            new Model\Operation(
                'postCredentialsItem',
                ['refresh_token'],
                [
                    '200' => [
                        'description' => 'Get JWT refresh token',
                        'content' => [
                            'application/json' => [
                                'refresh_token' => [
                                    '$ref' => 'refresh_token',
                                ],
                            ],
                        ],
                    ],
                ],
                'Get JWT refresh token in order to login without user credantials',
                'token expiration is 1 week',
                null,
                [],
                new Model\RequestBody(
                    'Return the connexion token',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/token/refresh', $pathItem);

        return  $openApi;
    }
}
