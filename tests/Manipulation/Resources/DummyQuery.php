<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:58 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sql\QueryBuilder\Manipulation\Resources;

use Sql\QueryBuilder\Manipulation\AbstractBaseQuery;

/**
 * Class DummyQuery.
 */
class DummyQuery extends AbstractBaseQuery
{
    /**
     * @return string
     */
    public function partName(): string
    {
        return 'DUMMY';
    }
}
