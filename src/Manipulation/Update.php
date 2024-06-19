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
 * Class Update.
 */
class Update extends AbstractCreationalQuery
{
    /**
     * @var int
     */
    protected int $limitStart;

    /**
     * @var array
     */
    protected array $orderBy = [];

	public bool $ignore = false;

    /**
     * @return string
     */
    public function partName(): string
    {
        return 'UPDATE';
    }

	public function Ignore(bool $ignore = true): static
	{
		$this->ignore = $ignore;
		return $this;
	}

    /**
     * @return int|null
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
