<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/16/14
 * Time: 8:50 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sql\QueryBuilder\Manipulation;

use Sql\QueryBuilder\Manipulation\QueryFactory;
use Sql\QueryBuilder\Manipulation\Select;

/**
 * Class QueryFactoryTest.
 */
class QueryFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function itShouldCreateSelectObject()
    {
        $className = '\Sql\QueryBuilder\Manipulation\Select';
        $this->assertInstanceOf($className, QueryFactory::createSelect());
    }

    /**
     * @test
     */
    public function itShouldCreateInsertObject()
    {
        $className = '\Sql\QueryBuilder\Manipulation\Insert';
        $this->assertInstanceOf($className, QueryFactory::createInsert());
    }

    /**
     * @test
     */
    public function itShouldCreateUpdateObject()
    {
        $className = '\Sql\QueryBuilder\Manipulation\Update';
        $this->assertInstanceOf($className, QueryFactory::createUpdate());
    }

    /**
     * @test
     */
    public function itShouldCreateDeleteObject()
    {
        $className = '\Sql\QueryBuilder\Manipulation\Delete';
        $this->assertInstanceOf($className, QueryFactory::createDelete());
    }

    /**
     * @test
     */
    public function itShouldCreateMinusObject()
    {
        $className = '\Sql\QueryBuilder\Manipulation\Minus';
        $this->assertInstanceOf($className, QueryFactory::createMinus(new Select('table1'), new Select('table2')));
    }

    /**
     * @test
     */
    public function itShouldCreateUnionObject()
    {
        $className = '\Sql\QueryBuilder\Manipulation\Union';
        $this->assertInstanceOf($className, QueryFactory::createUnion());
    }

    /**
     * @test
     */
    public function itShouldCreateUnionAllObject()
    {
        $className = '\Sql\QueryBuilder\Manipulation\UnionAll';
        $this->assertInstanceOf($className, QueryFactory::createUnionAll());
    }

    /**
     * @test
     */
    public function itShouldCreateWhereObject()
    {
        $mockClass = '\Sql\QueryBuilder\Manipulation\QueryInterface';

        $query = $this->getMockBuilder($mockClass)
            ->disableOriginalConstructor()
            ->getMock();

        $className = '\Sql\QueryBuilder\Syntax\Where';
        $this->assertInstanceOf($className, QueryFactory::createWhere($query));
    }
}
