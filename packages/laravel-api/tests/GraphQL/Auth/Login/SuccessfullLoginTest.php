<?php

namespace Tests\GraphQL\Auth\Login;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQL\GraphQLTestCase;
use App\User;

class SuccessfulLoginTest extends GraphQLTestCase
{
    use RefreshDatabase;

    private $user;
    private $response;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'name'     => 'Foo Bar',
            'email'    => 'foe.bar@testing.com',
            'password' => bcrypt('password'),
        ]);

        $this->response = $this->graphQL(
            /** @lang GraphQL */
            '
            mutation {
                login(input: {
                    username: $username,
                    password: "password"
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
                'username' => $this->user->email
            ]
        );

        // Test cases
        $this->should_return_HTTP_status_code_200();
        $this->should_return_the_correct_user_and_tokens();
    }

    /** @test */
    public function should_return_HTTP_status_code_200()
    {
        $this->response->assertStatus(200);
    }

    /** @test */
    public function should_return_the_correct_user_and_tokens()
    {
        $this->response->assertJson([
            'data' => [
                'login' => [
                    "token_type" => "Bearer",
                    'user' => [
                        'name' => $this->user->name,
                        'email' => $this->user->email
                    ]
                ]
            ]
        ]);

        $responseBody = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('access_token', $responseBody['data']['login']);
        $this->assertArrayHasKey('refresh_token', $responseBody['data']['login']);
    }
}
