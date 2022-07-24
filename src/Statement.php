<?php

namespace Church\Neo4j;

class Statement
{
    protected string $statement;

    protected array $params;

    public function __construct(string $statement)
    {
        $this->statement = $statement;
        return $this;
    }

    public function params(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatement(): string
    {
        return $this->statement;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function __toString(): string
    {
        return json_encode([
            'statement' => $this->statement,
            'parameters' => $this->params,
        ]);
    }
}