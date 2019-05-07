<?php

namespace DadaSDK\Order;

use DadaSDK\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * 发布/新增订单.
     *
     * @return mixed
     */
    public function create(array $params)
    {
        return $this->httpPostJson('/api/order/addOrder', $this->getRequestBody($params));
    }

    /**
     * 发布/创建订单的别名。
     */
    public function publish($params)
    {
        return $this->create($params);
    }

    /**
     * 订单重发。
     */
    public function republish(array $params)
    {
        return $this->httpPostJson('/api/order/reAddOrder', $this->getRequestBody($params));
    }

    /**
     * 取消订单.
     *
     * @return mixed
     */
    public function cancel($order_id, int $cancel_reason_id = null, string $cancel_reason = null)
    {
        $params = is_array($order_id) ? $order_id : compact('order_id', 'cancel_reason_id', 'cancel_reason');
        return $this->httpPostJson('/api/order/formalCancel', $this->getRequestBody($params));
    }

    /**
     * 查询订单详情.
     */
    public function show(string $order_id)
    {
        return $this->httpPostJson('/api/order/status/query', $this->getRequestBody(\compact('order_id')));
    }

    /**
     * 追加订单.
     */
    public function appoint(array $params)
    {
        return $this->httpPostJson('/api/order/appoint/exist', $this->getRequestBody($params));
    }

    /**
     * 取消追加订单.
     */
    public function cancelAppoint(string $order_id)
    {
        return $this->httpPostJson('/api/order/appoint/cancel', $this->getRequestBody(\compact('order_id')));
    }

    /**
     * 查询追加配送员.
     */
    public function getAppointList(string $shop_no)
    {
        return $this->httpPostJson('/api/order/appoint/list/transporter', $this->getRequestBody(\compact('shop_no')));
    }
}
