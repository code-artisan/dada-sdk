<?php

namespace DadaSDK\Account;

use DadaSDK\Kernel\BaseClient;

class Client extends BaseClient
{
    public function balance(int $category)
    {
        return $this->httpPostJson('/api/balance/query', $this->getRequestBody(\compact('category')));
    }

    public function buildRechargeLink(array $params)
    {
        return $this->httpPostJson('/api/recharge', $this->getRequestBody($params));
    }
}
