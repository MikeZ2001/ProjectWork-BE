<?php

namespace Modules\Account\Tests\Feature;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\TestResponse;
use Mockery\Exception\InvalidCountException;
use Modules\Account\Database\Factories\AccountFeatureTestFactory;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\WithFeatureTest;

class AccountFeatureTest extends WithFeatureTest
{
    private const string BASE_PATH = '/v1/accounts';

    /**
     * @var string
     */
    private string $defaultUri;

    /**
     * @var array<string, mixed>
     */
    private array $accountsData;

    /**
     * Setup the test environment.
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->defaultUri = self::BASE_URL . self::BASE_PATH;
        $this->generateAccountData();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @throws InvalidCountException
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @return void
     */
    #[TestDox('Tests retrieval (GET) of a account list')]
    public function testIndexAccount(): void
    {
        $this->storeAccount();

        $this->getJsonWithAuth($this->defaultUri)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => array_keys($this->accountsData),
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total'
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
            ]);
    }

    /**
     *
     * @return void
     */
    #[TestDox('Tests retrieval (GET) of a account')]
    public function testShowAccount(): void
    {
        /** @var int $accountId */
        $accountId = $this->storeAccount()->json('id');
        $uri = "$this->defaultUri/$accountId";
        $this->getJsonWithAuth($uri)->assertStatus(200);
    }
    /**
     * @return void
     */
    #[TestDox('Tests retrieval (GET) of a non existing account')]
    public function testShowNonExistingAccount(): void
    {
        $uri = "$this->defaultUri/$this->nonExistingKey";
        $this->getJsonWithAuth($uri)->assertStatus(404);
    }

    /**
     * @return void
     */
    #[TestDox('Tests creation (POST) of a account')]
    public function testStoreAccount(): void
    {
        $this->storeAccount()
            ->assertStatus(201)
            ->assertJson($this->accountsData);
    }

    /**
     * @return void
     */
    #[TestDox('Tests update (PUT) of a account')]
    public function testUpdateAccount(): void
    {
        $testResponse = $this->storeAccount();
        /** @var int $accountId */
        $accountId = $testResponse->json('id');
        $uri = "$this->defaultUri/$accountId";

        $this->generateAccountData();

        $this->putJsonWithAuth($uri, $this->accountsData)
            ->assertStatus(200)
            ->assertJson($this->accountsData);
    }

    /**
     * @return void
     */
    #[TestDox('Tests update (PUT) of a non existing account')]
    public function testUpdateNonExistingAccount(): void
    {
        $uri = "$this->defaultUri/$this->nonExistingKey";
        $this->putJsonWithAuth($uri, $this->accountsData)->assertStatus(404);
    }

    /**
     * @return void
     */
    #[TestDox('Tests destroy (DELETE) of a account')]
    public function testDestroyAccount(): void
    {
        /** @var int $accountId */
        $accountId = $this->storeAccount()->json('id');
        $uri = "$this->defaultUri/$accountId";

        $this->deleteJsonWithAuth($uri)->assertStatus(204);
    }

    /**
     * @return void
     */
    #[TestDox('Tests destroy (DELETE) of a non existing account')]
    public function testDestroyNonExistingAccount(): void
    {
        $uri = "$this->defaultUri/$this->nonExistingKey";
        $this->deleteJsonWithAuth($uri)->assertStatus(404);
    }

    /**
     * Generates account data from the factories.
     *
     * @return void
     */
    private function generateAccountData(): void
    {
        /** @var Factory<Model> $accounts */
        $accounts = AccountFeatureTestFactory::new();
        $this->accountsData = $this->generateEntityProperties($accounts);
    }

    /**
     * Create a new account.
     *
     * @return TestResponse<JsonResponse>
     */
    private function storeAccount(): TestResponse
    {
        return $this->postJsonWithAuth($this->defaultUri, $this->accountsData);
    }
}
