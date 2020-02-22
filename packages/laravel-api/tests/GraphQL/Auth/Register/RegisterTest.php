<?php

namespace Tests\GraphQL\Auth\Register;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GraphQL\GraphQLTestCase;
use App\User;

class RegisterTest extends GraphQLTestCase
{
    use RefreshDatabase;

    private $user;
    private $response;

    public function setUp(): void
    {
        parent::setUp();

        /**
         * Make a user but don't save it in the database
         */
        $this->user = factory(User::class)->make([
            'email_verified_at' => NULL,
        ]);

        /**
         * Register user via GraphQL endpoint
         * User will be added to the database
         */
        $this->response = $this->graphQL(
            /** @lang GraphQL */
            '
            mutation Register($name: String!, $email: String!) {
                register(input: {
                    name: $name,
                    email: $email,
                    password: "I-love-GraphQL",
                    password_confirmation: "I-love-GraphQL"
                }) {
                    tokens {
                        access_token
                        refresh_token
                        user {
                            id
                            name
                            email
                        }
                    }
                    status
                }
            }
            ',
            [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]
        );
    }

    /** @test */
    public function a_user_can_register_and_status_needs_to_be_MUST_VERIFY_EMAIL()
    {
        $STATUS =  "MUST_VERIFY_EMAIL";

        $this->response->assertStatus(200)->assertJson([
            'data' => [
                'register' => [
                    'tokens' => [
                        'access_token' => null,
                        'refresh_token' => null,
                        'user' => null
                    ],
                    'status' => $STATUS
                ]
            ]
        ]);
    }

    /** @test */
    public function should_have_created_the_user_in_the_database()
    {
        $this->assertDatabaseHas($this->user->getTable(), User::first()->toArray());
    }

    /** @test */
    public function should_not_yet_have_verified_the_user()
    {
        $this->assertFalse(User::first()->hasVerifiedEmail());
    }
}
