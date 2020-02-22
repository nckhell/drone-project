<?php

namespace Tests\GraphQL\Auth\Login;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQL\GraphQLTestCase;
use App\User;

class RefreshTokenTest extends GraphQLTestCase
{
    use RefreshDatabase;

    private $user;
    private $responseRefreshed;
    private $acces_token_before_refresh;
    private $acces_token_after_refresh;

    public function setUp(): void
    {
        parent::setUp();

        /**
         * Create a user and persist it to the database.
         */
        $this->user = factory(User::class)->create([
            'name'     => 'Foo Bar',
            'email'    => 'foe.bar@testing.com',
            'password' => bcrypt($password = 'password'),
        ]);

        $loginResponse = $this->graphQL(
            /** @lang GraphQL */
            '
            mutation Login($username: String!, $password: String!) {
                login(input: {
                    username: $username,
                    password: $password
                }) {
                    access_token
                    refresh_token,
                    token_type
                    user {
                        id
                        name
                        email
                    }
                }
            }
            ',
            [
                'username' => $this->user->email,
                'password' => $password,
            ]
        );

        $responseLoginBody = json_decode($loginResponse->getContent(), true);
        $refreshToken = $responseLoginBody['data']['login']['refresh_token'];
        $this->acces_token_before_refresh = $responseLoginBody['data']['login']['access_token'];

        $this->responseRefreshed = $this->graphQL(
            /** @lang GraphQL */
            '
            mutation RefreshToken($refresh_token: String!) {
                refreshToken(input: {
                    refresh_token: $refresh_token
                }) {
                    access_token
                    refresh_token
                }
            }
            ',
            [
                'refresh_token' => $refreshToken
            ]
        );

        $responseRefreshedBody = json_decode($this->responseRefreshed->getContent(), true);
        $this->acces_token_after_refresh = $responseRefreshedBody['data']['refreshToken']['access_token'];
    }

    /** @test */
    public function should_return_HTTP_status_code_200()
    {
        $this->responseRefreshed->assertStatus(200);
    }

    /** @test */
    public function should_return_a_new_access_token()
    {
        $this->assertNotEquals($this->acces_token_before_refresh, $this->acces_token_after_refresh);
    }
}
