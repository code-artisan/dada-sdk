<?php

namespace DadaSDK\Store;

use DadaSDK\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Create store.
     *
     * @return mixed
     */
    public function create(array $params)
    {
        return $this->httpPostJson('/api/shop/add', $this->getRequestBody($params));
    }

    public function update(array $params)
    {
        return $this->httpPostJson('/api/shop/update', $this->getRequestBody($params));
    }

    public function show(string $origin_shop_id)
    {
        return $this->httpPostJson('/api/shop/detail', $this->getRequestBody(\compact('origin_shop_id')));
    }
}
