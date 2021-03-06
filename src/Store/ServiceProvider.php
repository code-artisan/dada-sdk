<?php

namespace DadaSDK\Store;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['store'] = function ($app) {
            return new Client($app);
        };
    }
}
