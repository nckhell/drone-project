<?php

namespace Tests\GraphQL\Auth\Login;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQL\GraphQLTestCase;
use App\User;

class AuthenticatedUserTest extends GraphQLTestCase
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
        $this->user = factory(User::class)->create([
            'password' => bcrypt($password = 'password'),
        ]);

        /**
         * Log the user in via GraphQL
         */
        $loginResponse = $this->graphQL(
            /** @lang GraphQL */
            '
            mutation Login($username: String!, $password: String!) {
                login(input: {
                    username: $username,
                    password: $password
                }) {
                    access_token
                }
            }
            ',
            [
                'username' => $this->user->email,
                'password' => $password,
            ]
        );

        $responseLoginBody = json_decode($loginResponse->getContent(), true);
        $acces_token = $responseLoginBody['data']['login']['access_token'];

        $this->response = $this->postGraphQL([
            'query' => '{
                me {
                    id
                    name
                    email
                }
            }',
        ], [
            'Authorization' => 'Bearer ' . $acces_token,
        ]);
    }

    /** @test */
    public function should_return_the_authenticated_user()
    {
        $this->response->assertStatus(200)->assertJson([
            'data' => [
                'me' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email
                ]
            ]
        ]);
    }
}
