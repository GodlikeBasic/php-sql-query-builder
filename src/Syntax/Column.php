<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sql\QueryBuilder\Syntax;

use Sql\QueryBuilder\Manipulation\QueryException;

/**
 * Class Column.
 */
class Column implements QueryPartInterface
{
    const ALL = '*';

    /**
     * @var Table
     */
    protected Table $table;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string|null
     */
    protected string|null $alias;

	/**
	 * @param string $name
	 * @param string|Table $table
	 * @param string|null $alias
	 * @throws QueryException
	 */
    public function __construct(string $name, string|Table $table, ?string $alias = '')
    {
        $this->setName($name);
        $this->setTable($table);
        $this->setAlias($alias);
    }

    /**
     * @return string
     */
    public function partName(): string
    {
        return 'COLUMN';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

	/**
	 * @param string|Table $table
	 *
	 * @return $this
	 */
    public function setTable(string|Table $table): static
    {
		if(is_string($table))
        	$this->table = SyntaxFactory::createTable($table);
		else
			$this->table = $table;

        return $this;
    }

	/**
	 * @return string|null
	 */
    public function getAlias(): ?string
	{
        return $this->alias;
    }

    /**
     * @param string|null $alias
     *
     * @return $this
     *
     * @throws QueryException
     */
    public function setAlias(?string $alias): static
    {
        if ($alias === null || 0 == \strlen($alias)) {
            $this->alias = null;

            return $this;
        }

        if ($this->isAll()) {
            throw new QueryException("Can't use alias because column name is ALL (*)");
        }

        $this->alias = $alias;

        return $this;
    }

    /**
     * Check whether column name is '*' or not.
     *
     * @return bool
     */
    public function isAll(): bool
	{
        return $this->getName() == self::ALL;
    }
}
