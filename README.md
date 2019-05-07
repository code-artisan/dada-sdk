# dada-sdk

## 安装

```shell
$ composer require artisan/dada-sdk -vvv
```

## 示例

```php
<?php

use Dada\Factory;

$app = Factory::make([
    'app_key'    => 'your app key.',
    'app_secret' => 'your app secret.',
    'source_id'  => 'source id',
    'env' => 'development / production',
]);

$app->order->create($params);

$app->order->cancel($order_id, $cancel_reason_id, $cancel_reason);

$app->gratuity->add($params);
```
