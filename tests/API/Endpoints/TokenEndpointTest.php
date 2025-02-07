<?php

namespace Tests\API\Endpoints;

use Tests\API\FakeAPIService;
use Tests\BaseTestCase;
use YouCan\Pay\API\Endpoints\TokenEndpoint;
use YouCan\Pay\API\Endpoints\TransactionEndpoint;
use YouCan\Pay\API\Exceptions\InvalidResponseException;
use YouCan\Pay\API\Exceptions\ValidationException;
use YouCan\Pay\API\Response;

class TokenEndpointTest extends BaseTestCase
{
    public function test_generate_token_successfully()
    {
        $response = new Response(
            200,
            [
                "token" => [
                    "transaction_id" => "b76d6561-c995-4262-bab2-a828c48a20bd",
                    "id"             => "123",
                    "updated_at"     => "2021-10-01 15:44:00",
                    "created_at"     => "2021-10-01 15:44:00"
                ]
            ]
        );
        $fakeAPIService = new FakeAPIService($response);

        $tokenEndpoint = new TokenEndpoint($fakeAPIService);
        $token = $tokenEndpoint->create("123", "1000", "MAD", "123.123.123.123");

        $this->assertEquals($token->getId(), "123");
    }

    public function test_validation_exception()
    {
        $this->expectException(ValidationException::class);

        $response = new Response(
            422,
            [
                    "success" => false,
                    "message" => "the amount is less than minimum transaction amount"
            ]
        );
        $fakeAPIService = new FakeAPIService($response);

        $tokenEndpoint = new TokenEndpoint($fakeAPIService);
        $tokenEndpoint->create("123", "10", "MAD", "123.123.123.123");
    }

    public function test_internal_error()
    {
        $this->expectException(InvalidResponseException::class);

        $response = new Response(
            500,
            []
        );
        $fakeAPIService = new FakeAPIService($response);

        $tokenEndpoint = new TokenEndpoint($fakeAPIService);
        $tokenEndpoint->create("123", "10", "MAD", "123.123.123.123");
    }

    protected function setUp()
    {
        parent::setUp();
    }
}
