<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 9/12/14
 * Time: 7:34 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sql\QueryBuilder\Builder\Syntax;

use Sql\QueryBuilder\Builder\GenericBuilder;
use Sql\QueryBuilder\Builder\Syntax\MinusWriter;
use Sql\QueryBuilder\Manipulation\Minus;
use Sql\QueryBuilder\Manipulation\Select;

/**
 * Class MinusWriterTest.
 */
class MinusWriterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MinusWriter
     */
    private $minusWriter;

    /**
     * @var GenericBuilder
     */
    private $writer;

    /**
     *
     */
    public function setUp(): void
    {
        $this->minusWriter = new MinusWriter(new GenericBuilder());
        $this->writer = new GenericBuilder();
    }

    public function tearDown(): void
    {
        $this->minusWriter = null;
        $this->writer = null;
    }

    /**
     * @test
     */
    public function itShouldWriteMinus()
    {
        $minus = new Minus(new Select('user'), new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
MINUS
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals($expected, $this->minusWriter->write($minus));
    }

    /**
     * @test
     */
    public function itShouldWriteUnionAllFromGenericBuilder()
    {
        $minus = $this->writer->minus(new Select('user'), new Select('user_email'));

        $expected = <<<SQL
SELECT user.* FROM user
MINUS
SELECT user_email.* FROM user_email
SQL;
        $this->assertEquals($expected, $this->writer->write($minus));
    }
}
