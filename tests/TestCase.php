<?php

namespace Tests;

use Litstack\Rehearsal\TestCase as LitstackTestCase;
use Litstack\TwoFA\TwoFAServiceProvider;

class TestCase extends LitstackTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            TwoFAServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('app.key', 'base64:6WcDen+qJuWakE1XejUfFub83gMyw18YMFTNApwmtl0=');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
