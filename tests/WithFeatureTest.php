<?php

namespace Tests;

use App\Utils\ModelUtils;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class WithFeatureTest extends WithUserTest
{
    protected const string BASE_URL = "http://localhost/api";

    /**
     * @var int
     */
    protected int $nonExistingKey = 100000000;

    /**
     * @var string
     */
    protected string $nonExistingKeyString = 'zzz';

    /**
     * @var array<string, string>
     */
    protected array $authorizationHeader;

    /**
     * Setup the test environment.
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authorizationHeader = ['Authorization' => "Bearer $this->token"];
    }

    /**
     * Builds full uri based on base uri and query params
     *
     * @param string $baseUri
     * @param array<string, mixed> $queryParams
     *
     * @return string
     */
    protected function buildFullUri(string $baseUri, array $queryParams = []): string
    {
        return $baseUri . (empty($queryParams) ? '' : '?' . http_build_query($queryParams));
    }

    /**
     * Perform a DELETE request injecting authentication headers.
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     *
     * @return TestResponse<JsonResponse>
     */
    public function deleteWithAuth(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->delete($uri, $data, array_merge($this->authorizationHeader, $headers));
    }

    /**
     * Perform a DELETE request injecting authentication headers and expecting a JSON response.
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @param int $options
     *
     * @return TestResponse<JsonResponse>
     */
    public function deleteJsonWithAuth(string $uri, array $data = [], array $headers = [], int $options = 0): TestResponse
    {
        return $this->deleteJson($uri, $data, array_merge($this->authorizationHeader, $headers), $options);
    }

    /**
     * Perform a GET request injecting authentication headers.
     *
     * @param string $uri
     * @param array<string, string> $headers
     *
     * @return TestResponse<JsonResponse>
     */
    public function getWithAuth(string $uri, array $headers = []): TestResponse
    {
        return $this->get($uri, array_merge($this->authorizationHeader, $headers));
    }

    /**
     * Perform a GET request injecting authentication headers and expecting a JSON response.
     *
     * @param string $uri
     * @param array<string, string> $headers
     * @param int $options
     *
     * @return TestResponse<JsonResponse>
     */
    public function getJsonWithAuth(string $uri, array $headers = [], int $options = 0): TestResponse
    {
        return $this->getJson($uri, array_merge($this->authorizationHeader, $headers), $options);
    }

    /**
     * Perform a POST request injecting authentication headers.
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     *
     * @return TestResponse<JsonResponse>
     */
    public function postWithAuth(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->post($uri, $data, array_merge($this->authorizationHeader, $headers));
    }

    /**
     * Perform a POST request injecting authentication headers and expecting a JSON response.
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @param int $options
     *
     * @return TestResponse<JsonResponse>
     */
    public function postJsonWithAuth(string $uri, array $data = [], array $headers = [], int $options = 0): TestResponse
    {
        return $this->postJson($uri, $data, array_merge($this->authorizationHeader, $headers), $options);
    }

    /**
     * Perform a PUT request injecting authentication headers.
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     *
     * @return TestResponse<JsonResponse>
     */
    public function putWithAuth(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->put($uri, $data, array_merge($this->authorizationHeader, $headers));
    }

    /**
     * Perform a PUT request injecting authentication headers and expecting a JSON response.
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @param int $options
     *
     * @return TestResponse<JsonResponse>
     */
    public function putJsonWithAuth(string $uri, array $data = [], array $headers = [], int $options = 0): TestResponse
    {
        return $this->putJson($uri, $data, array_merge($this->authorizationHeader, $headers), $options);
    }

    /**
     * Perform a PATCH request injecting authentication headers.
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     *
     * @return TestResponse<JsonResponse>
     */
    public function patchWithAuth(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->patch($uri, $data, array_merge($this->authorizationHeader, $headers));
    }

    /**
     * Perform a PATCH request injecting authentication headers and expecting a JSON response.
     *
     * @param string $uri
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @param int $options
     *
     * @return TestResponse<JsonResponse>
     */
    public function patchJsonWithAuth(string $uri, array $data = [], array $headers = [], int $options = 0): TestResponse
    {
        return $this->patchJson($uri, $data, array_merge($this->authorizationHeader, $headers), $options);
    }

    /**
     * Generate some properties for CRUD interaction purposes based on a given model factory.
     *
     * @param Factory<Model> $factory
     *
     * @return array<string, mixed>
     */
    protected function generateEntityProperties(Factory $factory): array
    {
        /** @var Model $entity */
        $entity = $factory->count(1)->make()->get(0);
        $properties = ModelUtils::extractToPlainArray($entity);
        return $properties;
    }
}
