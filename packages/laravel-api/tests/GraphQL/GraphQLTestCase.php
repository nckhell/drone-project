<?php

namespace Tests\GraphQL;

use Orchestra\Testbench\TestCase as Orchestra;
use Laravel\Passport\ClientRepository;
use Tests\CreatesApplication;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;


abstract class GraphQLTestCase extends Orchestra
{
    use CreatesApplication, MakesGraphQLRequests;

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            GraphQLServiceProvider::class
        ];
    }

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->createClient();
    }

    /**
     * Create a passport client for testing.
     */
    public function createClient()
    {
        $client = app(ClientRepository::class)->createPasswordGrantClient(null, 'test', 'http://localhost');
        config()->set('lighthouse-graphql-passport.client_id', $client->id);
        config()->set('lighthouse-graphql-passport.client_secret', $client->secret);
    }

    /**
     * Create a passport client for testing.
     */
    public function createClientPersonal($user)
    {
        $client = app(ClientRepository::class)->createPersonalAccessClient($user->id, 'test', 'http://localhost');
        config()->set('lighthouse-graphql-passport.client_id', $client->id);
        config()->set('lighthouse-graphql-passport.client_secret', $client->secret);
    }
}
