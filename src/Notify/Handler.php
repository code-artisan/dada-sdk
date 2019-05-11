<?php

namespace DadaSDK\Notify;

use Closure;
use DadaSDK\Kernel\Exceptions\Exception;
use DadaSDK\Kernel\Exceptions\InvalidSignException;
use Symfony\Component\HttpFoundation\Response;

abstract class Handler
{
    const SUCCESS = 'success';
    const FAIL = 'fail';

    /**
     * @var \DadaSDK\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $message;

    /**
     * @var string|null
     */
    protected $fail;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Check sign.
     * If failed, throws an exception.
     *
     * @var bool
     */
    protected $check = true;

    /**
     * Respond with sign.
     *
     * @var bool
     */
    protected $sign = false;

    /**
     * @param \DadaSDK\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Handle incoming notify.
     *
     * @param \Closure $closure
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    abstract public function handle(Closure $closure);

    /**
     * @param string $message
     */
    public function fail(string $message)
    {
        $this->fail = $message;
    }

    /**
     * @param array $attributes
     * @param bool  $sign
     *
     * @return $this
     */
    public function respondWith(array $attributes, bool $sign = false)
    {
        $this->attributes = $attributes;
        $this->sign = $sign;

        return $this;
    }

    /**
     * Build xml and return the response to WeChat.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse(): Response
    {
        $isSuccessed = is_null($this->fail);

        $base = [
            'status' => $isSuccessed ? static::SUCCESS : static::FAIL,
        ];

        $attributes = array_merge($base, $this->attributes);

        if ($this->sign) {
            $attributes['signature'] = $this->signature($attributes);
        }

        return new Response(json_encode($attributes), $isSuccessed ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Return the notify message from request.
     *
     * @return array
     *
     * @throws \DadaSDK\Kernel\Exceptions\Exception
     */
    public function getMessage(): array
    {
        if (!empty($this->message)) {
            return $this->message;
        }

        try {
            $message = json_decode($this->app->request->getContent(), true);
        } catch (\Throwable $th) {
            throw new Exception('Invalid request JSON: '.$th->getMessage(), 400);
        }

        if (!is_array($message) || empty($message)) {
            throw new Exception('Invalid request JSON.', 400);
        }

        if ($this->check) {
            $this->validate($message);
        }

        return $this->message = $message;
    }

    public function getAppSecret()
    {
        return $this->app->config['app_secret'];
    }

    /**
     * Signature.
     */
    public function signature(array $data)
    {
        $data = [
            'client_id' => $data['client_id'],
            'order_id' => $data['order_id'],
            'update_time' => strval($data['update_time']),
        ];

        $values = array_values($data);
        sort($values);

        return md5(implode('', $values));
    }

    /**
     * Validate the request params.
     *
     * @param array $message
     *
     * @throws \DadaSDK\Payment\Kernel\Exceptions\InvalidSignException
     * @throws \DadaSDK\Kernel\Exceptions\InvalidArgumentException
     */
    protected function validate(array $message)
    {
        $sign = $message['signature'];
        unset($message['signature']);

        if ($this->signature($message) !== $sign) {
            throw new InvalidSignException();
        }
    }

    /**
     * @param mixed $result
     */
    protected function strict($result)
    {
        if (true !== $result && is_null($this->fail)) {
            $this->fail(strval($result));
        }
    }
}
