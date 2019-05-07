<?php

namespace Dada\Gratuity;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['gratuity'] = function ($app) {
            return new Client($app);
        };
    }
}
