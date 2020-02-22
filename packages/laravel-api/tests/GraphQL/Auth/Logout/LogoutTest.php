<?php

namespace Tests\GraphQL\Auth\Logout;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQL\GraphQLTestCase;
use App\User;

class LogoutTest extends GraphQLTestCase
{
    use RefreshDatabase;

    private $response;

    public function setUp(): void
    {
        parent::setUp();

        /**
         * Create a user and persist it to the database.
         */
        $user = factory(User::class)->create();

        $this->createClientPersonal($user);
        $token = $user->createToken('test Token');
        $token = $token->accessToken;

        // Create the user
        $this->response = $this->postGraphQL([
            'query' => 'mutation {
                logout {
                    status
                    message
                }
            }',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
    }

    /** @test */
    public function should_return_HTTP_status_code_200()
    {
        $this->response->assertStatus(200);
    }

    /** @test */
    public function should_revoke_the_token_with_status_TOKEN_REVOKED()
    {
        $STATUS = "TOKEN_REVOKED";

        $this->response->assertJson([
            'data' => [
                'logout' => [
                    "status" => $STATUS
                ]
            ]
        ]);
    }
}
