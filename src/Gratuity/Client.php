<?php

namespace Dada\Gratuity;

use Dada\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Add gratuity.
     *
     * @return mixed
     */
    public function add(array $params)
    {
        return $this->httpPostJson('/api/order/addTip', $this->getRequestBody($params));
    }
}
