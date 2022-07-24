<?php

namespace Church\Neo4j;

class StatementRepository
{
    public array $statements = [];

    protected static $instance = null;

    public static function getInstance($new = false): self
    {
        if (! self::$instance || $new) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function add(Statement $statement): self
    {
        self::getInstance()->statements[] = $statement;
        return self::getInstance();
    }

    public function getData(): array
    {
        $data = [];

        foreach ($this->statements as $statement) {
            $data[] = ['statement' => $statement->getStatement(), 'parameters' => $statement->getParams()];
        }

        return ['statements' => $data];
    }

    public function __toString(): string
    {
        return json_encode($this->getData());
    }
}