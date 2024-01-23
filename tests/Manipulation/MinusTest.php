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

use Sql\QueryBuilder\Manipulation\Minus;
use Sql\QueryBuilder\Manipulation\Select;

/**
 * Class MinusTest.
 */
class MinusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Minus
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
        $this->query = new Minus(new Select('user'), new Select('user_email'));
    }

    /**
     * @test
     */
    public function itShouldGetPartName()
    {
        $this->assertSame('MINUS', $this->query->partName());
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
    public function itShouldGetMinusSelects()
    {
        $this->assertEquals(new Select('user'), $this->query->getFirst());
        $this->assertEquals(new Select('user_email'), $this->query->getSecond());
    }
}
