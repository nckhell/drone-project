<?php

namespace Tests\GraphQL\Auth\Register;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQL\GraphQLTestCase;
use App\User;

class VerifyEmail extends GraphQLTestCase
{
    use RefreshDatabase;

    private $user;
    private $response;

    public function setUp(): void
    {
        parent::setUp();

        /**
         * Create a user and persist it to the database.
         */
        $this->user = factory(User::class)->create();

        /**
         * For reference see:
         * lighthouse-graphql-passport-auth/tests/Integration/GraphQL/Mutations/VerifyEmail
         */
        $token = base64_encode(json_encode([
            'id'         => User::first()->id,
            'hash'       => encrypt(User::first()->getEmailForVerification()),
            'expiration' => encrypt(Carbon::now()->addMinutes(10)->toIso8601String()),
        ]));

        $this->response = $this->graphQL(
            /** @lang GraphQL */
            '
            mutation VerifyEmail($token: String!) {
                verifyEmail(input: {
                    token: $token
                }) {
                    access_token
                    refresh_token
                    user {
                        id
                        name
                        email
                    }
                }
            }
            ',
            [
                'token' => $token
            ]
        );
    }

    /** @test */
    public function should_return_HTTP_status_code_200()
    {
        $this->response->assertStatus(200);
    }

    /** @test */
    public function should_verify_the_user_and_return_access_tokens_and_user_data()
    {
        $this->response->assertJson([
            'data' => [
                'verifyEmail' => [
                    'user' => [
                        'id' => User::first()->id,
                        'name' => User::first()->name,
                        'email' => User::first()->email
                    ]
                ]
            ]
        ]);

        $responseBody = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('access_token', $responseBody['data']['verifyEmail']);
        $this->assertArrayHasKey('refresh_token', $responseBody['data']['verifyEmail']);
    }
}
