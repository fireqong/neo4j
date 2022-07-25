<?php

namespace Church\Neo4j;

class Transaction
{
    protected StatementRepository $statements;

    protected Application $app;

    protected Result $commitResult;

    public function __construct(Application $app, StatementRepository $statements)
    {
        $this->app = $app;
        $this->statements = $statements;
    }

    /**
     * @return Result
     */
    public function getCommitResult(): Result
    {
        return $this->commitResult;
    }

    /**
     * 开始一个事务
     *
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function begin(): self
    {
        $this->commitResult = $this->app->post('/db/neo4j/tx/', $this->statements->getData());
        return $this;
    }

    /**
     * 提交
     *
     * @param StatementRepository|null $statements
     * @return bool
     * @throws \Exception
     */
    public function commit(StatementRepository $statements = null): bool
    {
        return $this->doTransaction(function ($transaction) use ($statements) {
            $commitUrl = $transaction->commitResult['commit'];
            return $transaction->app->post($commitUrl, $statements->getData());
        });
    }

    /**
     * 开始和提交
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function beginAndCommit(): Result
    {
        return $this->app->post('/db/neo4j/tx/commit', $this->statements->getData());
    }

    /**
     * 回滚事务
     *
     * @return bool
     * @throws \Exception
     */
    public function rollback(): bool
    {
        $result = $this->doTransaction(function ($transaction) {
            return $transaction->app->delete($transaction->commitResult->getRawResponse()->getHeaderLine('Location'));
        });

        return $result->getRawResponse()->getStatusCode() == 200;
    }

    /**
     * 延长事务过期时间，保活
     *
     * @return bool
     * @throws \Exception
     */
    public function keepAlive(): bool
    {
        $result = $this->doTransaction(function ($transaction) {
            return $transaction->app->post(
                $transaction->commitResult->getRawResponse()->getHeaderLine('Location'),
                StatementRepository::getInstance(true)->getData()
            );
        });

        return $result->getRawResponse()->getStatusCode() == 200;
    }

    /**
     * 执行操作
     *
     * @param callable $callback
     * @return bool
     * @throws \Exception
     */
    public function doTransaction(callable $callback): ?Result
    {
        if ($this->commitResult->getRawResponse()->getStatusCode() == 201) {
            if (strtotime($this->commitResult['transaction']['expires']) < time()) {
                throw new \Exception('事务已过期');
            }

            return call_user_func($callback, $this);
        }

        return null;
    }
}