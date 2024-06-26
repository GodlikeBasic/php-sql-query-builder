<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/7/14
 * Time: 11:44 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sql\QueryBuilder\Manipulation;

/**
 * Class BaseQueryTest.
 */
class BaseQueryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Tests\Sql\QueryBuilder\Manipulation\Resources\DummyQuery
     */
    private $query;

    /**
     * @var string
     */
    private $whereClass = '\Sql\QueryBuilder\Syntax\Where';

    /**
     *
     */
    protected function setUp(): void
    {
        $this->query = new Resources\DummyQuery();
        $this->query->setTable('tablename');
    }

    /**
     *
     */
    protected function tearDown(): void
    {
        $this->query = null;
    }

    /**
     * @test
     */
    public function itShouldBeAbleToSetTableName()
    {
        $this->assertSame('tablename', $this->query->getTable()->getName());
    }

    /**
     * @test
     */
    public function itShouldGetWhere()
    {
        $this->assertNull($this->query->getWhere());

        $this->query->where();
        $this->assertInstanceOf($this->whereClass, $this->query->getWhere());
    }

    /**
     * @test
     */
    public function itShouldGetWhereOperator()
    {
        $this->assertSame('AND', $this->query->getWhereOperator());

        $this->query->where('OR');
        $this->assertSame('OR', $this->query->getWhereOperator());
    }
}
