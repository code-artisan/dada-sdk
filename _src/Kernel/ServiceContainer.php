<?php

namespace DadaSDK\Kernel;

use DadaSDK\Kernel\Providers\ConfigServiceProvider;
use DadaSDK\Kernel\Providers\HttpClientServiceProvider;
use Pimple\Container;

class ServiceContainer extends Container
{
    /**
     * @var array
     */
    protected $config = [
        'env' => 'development'
    ];

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * Constructor.
     *
     * @param array       $config
     * @param string|null $id
     */
    public function __construct(array $config)
    {
        $this->registerProviders($this->getProviders());

        parent::__construct([]);

        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param array $providers
     */
    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return array_merge([
            ConfigServiceProvider::class,
            HttpClientServiceProvider::class,
        ], $this->providers);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $base = [
            // http://docs.guzzlephp.org/en/stable/request-options.html
            'http' => [
                'timeout' => 30.0,
                'base_uri' => $this->config['env'] === 'production' ? 'http://newopen.imdada.cn' : 'http://newopen.qa.imdada.cn',
            ],
            'debug' => false,
        ];

        return array_replace_recursive($base, $this->config);
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }
}
