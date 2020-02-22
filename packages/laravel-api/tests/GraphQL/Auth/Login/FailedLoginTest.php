<?php

namespace Tests\GraphQL\Auth\Login;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQL\GraphQLTestCase;
use App\User;

class FailedLoginTest extends GraphQLTestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_HTTP_status_code_200_and_graphql_error_when_credentials_are_missing()
    {
        $this->graphQL(
            /** @lang GraphQL */
            '
            mutation {
                login(input: {}) {
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
            '
        )->assertStatus(200)->assertJson([
            'errors' => [
                [
                    'extensions' => [
                        "category" => "graphql",
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function should_return_HTTP_status_code_200_and_authentication_error_when_user_cannot_be_authenticated()
    {
        $this->response = $this->graphQL(
            /** @lang GraphQL */
            '
            mutation {
                login(input: {
                    username: "not-existing-user@testing.com",
                    password: "a password"
                }) {
                    user {
                        id
                    }
                }
            }
            '
        )->assertStatus(200)->assertJson([
            'errors' => [
                [
                    'extensions' => [
                        "category" => "authentication",
                    ]
                ]
            ]
        ]);
    }
}
