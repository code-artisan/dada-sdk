<?php

namespace Dada\Message;

use Dada\Kernel\BaseClient;

class Client extends BaseClient
{
    public function confirm(int $category)
    {
        return $this->httpPostJson('/api/balance/query', $this->getRequestBody(\compact('category')));
    }
}
