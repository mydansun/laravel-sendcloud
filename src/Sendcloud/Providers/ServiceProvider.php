<?php

namespace Mydansun\Sendcloud\Providers;

use Mydansun\Sendcloud\Sendcloud;
use Mydansun\Sendcloud\Transport\SendcloudTransport;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $manager = $this->app['swift.transport'];
        if ($manager instanceof \Illuminate\Support\Manager) {
            $manager->extend('sendcloud', function () {
                $config = $this->app['config']['services']['sendcloud'];
                return new SendcloudTransport($this->app['sendcloud'], $config['unsub_template']);
            });
        }
    }

    public function register()
    {
        $this->app->singleton('sendcloud', function ($app) {
            $config = $app['config']['services']['sendcloud'];
            return new Sendcloud($config['api_user'], $config['api_key'], $config['from']);
        });
    }
}