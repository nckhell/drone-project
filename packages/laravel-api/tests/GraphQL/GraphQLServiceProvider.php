<?php

namespace Tests\GraphQL;

use Laravel\Passport\Passport;

class GraphQLServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        Passport::routes();
        Passport::loadKeysFrom(__DIR__ . '/__testdata__/storage');
    }
}
