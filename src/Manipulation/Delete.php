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

/**
 * Class Delete.
 */
class Delete extends AbstractBaseQuery
{
    /**
     * @var int
     */
    protected int $limitStart;

    /**
     * @param string $table
     */
    public function __construct($table = null)
    {
        if (isset($table)) {
            $this->setTable($table);
        }
    }

    /**
     * @return string
     */
    public function partName(): string
    {
        return 'DELETE';
    }

    /**
     * @return int
     */
    public function getLimitStart(): int|null
    {
        return $this->limitStart ?? null;
    }

    /**
     * @param int $start
     *
     * @return $this
     */
    public function limit($start)
    {
        $this->limitStart = $start;

        return $this;
    }
}
