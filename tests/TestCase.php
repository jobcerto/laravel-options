<?php

namespace Jobcerto\Options\Tests;

use Jobcerto\Options\OptionsServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {

        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'sqlite']);

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            OptionsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('app.key', 'base64:6Cu/ozj4gPtIjmXjr8EdVnGFNsdRqZfHfVjQkmTlg4Y=');
    }

    protected function setUpDatabase()
    {
        include_once __DIR__ . '/../database/migrations/create_options_table.php.stub';

        (new \CreateOptionsTable())->up();
    }

}
