<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:26 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sql\QueryBuilder\Manipulation;

use Sql\QueryBuilder\Manipulation\UnionAll;
use Sql\QueryBuilder\Manipulation\Select;

/**
 * Class UnionAllTest.
 */
class UnionAllTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UnionAll
     */
    private $query;

    /**
     * @var string
     */
    private $exceptionClass = '\Sql\QueryBuilder\Manipulation\QueryException';

    /**
     *
     */
    protected function setUp(): void
    {
        $this->query = new UnionAll();
    }

    /**
     * @test
     */
    public function itShouldGetPartName()
    {
        $this->assertSame('UNION ALL', $this->query->partName());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionForUnsupportedGetTable()
    {
        $this->expectException($this->exceptionClass);
        $this->query->getTable();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionForUnsupportedGetWhere()
    {
        $this->expectException($this->exceptionClass);
        $this->query->getWhere();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionForUnsupportedWhere()
    {
        $this->expectException($this->exceptionClass);
        $this->query->where();
    }

    /**
     * @test
     */
    public function itShouldGetIntersectSelects()
    {
        $this->assertEquals(array(), $this->query->getUnions());

        $select1 = new Select('user');
        $select2 = new Select('user_email');

        $this->query->add($select1);
        $this->query->add($select2);

        $this->assertEquals(array($select1, $select2), $this->query->getUnions());
    }
}
