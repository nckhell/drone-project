<?php

namespace Tests\GraphQL\Auth\PasswordReset;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\GraphQL\GraphQLTestCase;
use App\User;

class ResetPasswordTest extends GraphQLTestCase
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
        $token = Password::createToken($user);

        $this->response = $this->graphQL(
            /** @lang GraphQL */
            '
            mutation UpdateForgottenPassword(
                    $email: String!
                    $token: String!
                    $password: String!
                    $confirmPassword: String!
                ) {
                updateForgottenPassword(input: {
                    email: $email
                    token: $token
                    password: $password
                    password_confirmation: $confirmPassword
                }) {
                    status
                    message
                }
            }
            ',
            [
                'email' => User::first()->email,
                'token' => $token,
                'password' => 'new_password',
                'confirmPassword' => 'new_password'
            ]
        );
    }

    /** @test */
    public function should_return_HTTP_status_code_200()
    {
        $this->response->assertStatus(200);
    }

    /** @test */
    public function should_return_status_that_password_has_been_updated()
    {
        $STATUS =  "PASSWORD_UPDATED";

        $this->response->assertStatus(200)->assertJson([
            'data' => [
                'updateForgottenPassword' => [
                    'status' => $STATUS
                ]
            ]
        ]);
    }
}
