<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sql\QueryBuilder\Manipulation;

use Sql\QueryBuilder\Syntax\Table;

/**
 * Interface QueryInterface.
 */
interface QueryInterface
{
    /**
     * @return string
     */
    public function partName();

    /**
     * @return null|Table
     */
    public function getTable(): null|Table;

    /**
     * @return \Sql\QueryBuilder\Syntax\Where
     */
    public function getWhere();

    /**
     * @return \Sql\QueryBuilder\Syntax\Where
     */
    public function where();
}
