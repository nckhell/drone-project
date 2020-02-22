<?php

namespace Tests\GraphQL\Auth\PasswordReset;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQL\GraphQLTestCase;
use App\User;

class ForgotPasswordTest extends GraphQLTestCase
{
    use RefreshDatabase;

    private $response;

    public function setUp(): void
    {
        parent::setUp();

        /**
         * Create a user and persist it to the database.
         */
        factory(User::class)->create();

        $this->response = $this->graphQL(
            /** @lang GraphQL */
            '
            mutation ForgotPassword ($email: String!) {
                forgotPassword(input: {
                    email: $email
                }) {
                    status
                    message
                }
            }
            ',
            [
                'email' => User::first()->email
            ]
        );
    }

    /** @test */
    public function should_return_HTTP_status_code_200()
    {
        $this->response->assertStatus(200);
    }

    /** @test */
    public function should_return_status_that_email_has_been_sent()
    {
        $STATUS =  "EMAIL_SENT";

        $this->response->assertStatus(200)->assertJson([
            'data' => [
                'forgotPassword' => [
                    'status' => $STATUS
                ]
            ]
        ]);
    }

    /** @test */
    public function should_have_created_an_entry_in_the_password_resets_table()
    {
        $this->assertDatabaseHas('password_resets', [
            'email' => User::first()->email
        ]);
    }
}
