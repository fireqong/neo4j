<?php

namespace Church\Neo4j\Test;

use Church\Neo4j\Application;
use Church\Neo4j\Statement;
use Church\Neo4j\StatementRepository;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    protected $neo4j;

    public function setUp(): void
    {
        $this->neo4j = new Application("http://127.0.0.1:7474", 'neo4j', 'neo4j123');
    }

    public function testDiscovery()
    {
        $result = $this->neo4j->discovery();
        $this->assertEquals(200, $result->getRawResponse()->getStatusCode());
    }

    public function testTransaction()
    {
        $statement = (new Statement('CREATE (n $props) RETURN n'))->params([
            'props' => [
                'name' => 'test'
            ]
        ]);

        $statements = StatementRepository::add($statement);
        $transaction = $this->neo4j->transaction($statements)->begin();
        $this->assertTrue($transaction->rollback());
    }

    public function testBeginAndCommit()
    {
        $statement = (new Statement('CREATE (ee:Person $props) RETURN ee'))->params([
            'props' => [
                'name' => '丹姐',
                'gender' => 'female',
                'age' => 26,
            ]
        ]);

        $statements = StatementRepository::add($statement);
        $result = $this->neo4j->transaction($statements)->beginAndCommit();
        $this->assertTrue($result->getRawResponse()->getStatusCode() == 200);

        $statement = (new Statement('MATCH (ee:Person) WHERE ee.name=$name RETURN ee'))->params([
            'name' => '丹姐'
        ]);

        $result = $this->neo4j->transaction(StatementRepository::add($statement))->beginAndCommit();

        $this->assertTrue($result->getRawResponse()->getStatusCode() == 200);
    }

//    public function testKeepAlive()
//    {
//        $statement = (new Statement('CREATE (n $props) RETURN n'))->params([
//            'props' => [
//                'name' => 'test'
//            ]
//        ]);
//
//        $statements = StatementRepository::add($statement);
//        $transaction = $this->neo4j->transaction($statements)->begin();
//
//        sleep(40);
//
//        $transaction->keepAlive();
//
//        sleep(50);
//
//        $this->assertTrue($transaction->commit());
//    }

    public function tearDown(): void
    {
        unset($this->neo4j);
    }
}
