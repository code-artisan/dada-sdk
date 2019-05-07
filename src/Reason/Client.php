<?php

namespace Dada\Reason;

use Dada\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Get order cancel reason or complaint reason.
     *
     * @return mixed
     */
    public function get(string $scope)
    {
        $url = $scope === 'order' ? '/api/order/cancel/reasons' : '/api/complaint/reasons';

        return $this->httpPostJson($url, $this->getRequestBody());
    }
}
