<?php

namespace Dada\Reason;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['reason'] = function ($app) {
            return new Client($app);
        };
    }
}
