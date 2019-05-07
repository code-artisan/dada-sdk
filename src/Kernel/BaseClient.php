<?php

namespace Dada\Kernel;

// use Dada\Kernel\Traits\HasHttpRequests;
use GuzzleHttp\Client;

class BaseClient
{
    // use HasHttpRequests { request as performRequest; }

    /**
     * @var \Dada\Kernel\ServiceContainer
     */
    protected $app;

    /**
     * @var array
     */
    protected static $defaults = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
        'headers' => [
            'content-type' => 'application/json',
        ]
    ];

    /**
     * BaseClient constructor.
     *
     * @param \Dada\Kernel\ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

    protected function getAppSecret()
    {
        return $this->app->config['app_secret'];
    }

    /**
     * Signature.
     */
    protected function signature(array $data, string $appSecret)
    {
        ksort($data);

        $params = '';
        foreach ($data as $key => $value) {
            $params .= $key . $value;
        }

        $params = $appSecret . $params . $appSecret;

        return strtoupper(md5($params));
    }

    protected function getRequestBody(array $params = [])
    {
        $data = [
            'app_key'   => $this->app->config['app_key'],
            'format'    => 'json',
            'v'         => '1.0',
            'source_id' => $this->app->config['source_id'],
            'timestamp' => (string) time(),
            'body'      => count($params) ? json_encode($params, JSON_UNESCAPED_SLASHES) : '',
        ];

        return array_merge($data, [
            'signature' => $this->signature($data, $this->getAppSecret()),
        ]);
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array  $query
     *
     * @throws \Dada\Kernel\Exceptions\InvalidConfigException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function httpGet(string $url, array $query = [])
    {
        return $this->request($url, 'GET', compact('query'));
    }

    /**
     * POST request.
     *
     * @param string $url
     * @param array  $data
     *
     * @throws \Dada\Kernel\Exceptions\InvalidConfigException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function httpPost(string $url, array $data = [])
    {
        return $this->request($url, 'POST', ['form_params' => $data]);
    }

    /**
     * PUT request.
     *
     * @param string $url
     * @param array  $data
     *
     * @throws \Dada\Kernel\Exceptions\InvalidConfigException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function httpPut(string $url, array $data = [])
    {
        return $this->request($url, 'PUT', ['form_params' => $data]);
    }

    /**
     * DELETE request.
     *
     * @param string $url
     * @param array  $data
     *
     * @throws \Dada\Kernel\Exceptions\InvalidConfigException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function httpDelete(string $url, array $data = [])
    {
        return $this->request($url, 'DELETE', ['form_params' => $data]);
    }

    /**
     * JSON request.
     *
     * @param string       $url
     * @param string|array $data
     * @param array        $query
     *
     * @throws \Dada\Kernel\Exceptions\InvalidConfigException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function httpPostJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function fixJsonIssue(array $options): array
    {
        if (isset($options['json']) && is_array($options['json'])) {
            $options['headers'] = array_merge($options['headers'] ?? [], ['Content-Type' => 'application/json']);

            if (empty($options['json'])) {
                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_FORCE_OBJECT);
            } else {
                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_UNESCAPED_UNICODE);
            }

            unset($options['json']);
        }

        return $options;
    }

    /**
     * Make a request.
     *
     * @param string $url
     * @param string $method
     * @param array  $options
     *
     * @return mixed
     */
    public function request($url, $method = 'GET', $options = [], $returnRow = true)
    {
        $options = array_merge(self::$defaults, $options, [
            'headers' => array_merge(isset($options['headers']) ? $options['headers'] : []),
            'http_errors' => false,
        ]);

        $options = $this->fixJsonIssue($options);

        $response = $this->app->http_client->request(strtoupper($method), $url, $options);
        $response->getBody()->rewind();

        return $returnRow ? json_decode($response->getBody()->getContents(), true) : $response;
    }
}
