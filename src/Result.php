<?php

namespace Church\Neo4j;

use GuzzleHttp\Psr7\Response;

class Result implements \IteratorAggregate, \ArrayAccess
{
    /** @var Response */
    protected $rawResponse;

    protected $data;

    /**
     * 构造函数
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->rawResponse = $response;
        $this->data = json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 获取迭代器
     *
     * @return array|mixed|\Traversable
     */
    public function getIterator()
    {
        return $this->data;
    }

    /**
     * 获取原始返回数据
     *
     * @return Response
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function __toString()
    {
        return json_encode($this->data);
    }
}