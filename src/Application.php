<?php

namespace Church\Neo4j;

use GuzzleHttp\Client;

class Application
{
    protected string $host;

    protected string $username;

    protected string $password;

    protected static $client;

    /**
     * 构造函数
     *
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $username, string $password)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * 发现
     *
     * @return Result
     */
    public function discovery(): Result
    {
        return $this->get('/');
    }

    /**
     * 获取HTTP客户端
     *
     * @param Application $app
     * @return Client
     */
    public static function getClient(Application $app)
    {
        if (! self::$client) {
            self::$client = new Client(['base_uri' => $app->host]);
        }

        return self::$client;
    }

    /**
     * 事务处理
     *
     * @param StatementRepository $statements
     * @return Transaction
     */
    public function transaction(StatementRepository $statements)
    {
        return new Transaction($this, $statements);
    }

    /**
     * GET方法
     *
     * @param string $uri
     * @param array $queryParams
     * @return Result
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $uri, array $queryParams = []): Result
    {
        $client = self::getClient($this);
        $response = $client->get($uri, [
            'query' => $queryParams,
            'headers' => [
                'Accept' => 'application/json; charset=UTF-8',
                'Authorization' => 'Basic ' . Util::buildPayload($this->username, $this->password),
            ]
        ]);

        return new Result($response);
    }

    /**
     * POST方法
     *
     * @param string $uri
     * @param array $params
     * @return Result
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $uri, array $params = []): Result
    {
        $client = self::getClient($this);
        $response = $client->post($uri, [
            'headers' => [
                'Accept' => 'application/json;charset=UTF-8',
                'Authorization' => 'Basic ' . Util::buildPayload($this->username, $this->password),
            ],
            'json' => $params
        ]);

        return new Result($response);
    }

    /**
     * DELETE方法
     *
     * @param string $uri
     * @return Result
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $uri)
    {
        $client = self::getClient($this);
        $response = $client->delete($uri, [
            'headers' => [
                'Accept' => 'application/json;charset=UTF-8',
                'Authorization' => 'Basic ' . Util::buildPayload($this->username, $this->password),
            ]
        ]);

        return new Result($response);
    }
}