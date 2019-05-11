<?php

namespace DadaSDK;

use Closure;
use DadaSDK\Kernel\ServiceContainer;

class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Order\ServiceProvider::class,
        Store\ServiceProvider::class,
        Reason\ServiceProvider::class,
        Account\ServiceProvider::class,
        Message\ServiceProvider::class,
        Gratuity\ServiceProvider::class,
    ];

    public function handleOrderNotify(Closure $callback)
    {
        return \call_user_func($callback);
    }

    public function handleMessageNotify(Closure $callback)
    {
        return \call_user_func($callback);
    }

    public function getOpenedCityCode()
    {
        
    }
}
