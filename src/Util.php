<?php

namespace Church\Neo4j;

class Util
{
    /**
     * 构建验证头
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    public static function buildPayload(string $username, string $password)
    {
        return base64_encode(sprintf("%s:%s", $username, $password));
    }
}