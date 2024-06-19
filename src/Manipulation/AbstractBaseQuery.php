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

use Sql\QueryBuilder\Builder\BuilderInterface;
use Sql\QueryBuilder\Syntax\OrderBy;
use Sql\QueryBuilder\Syntax\QueryPartInterface;
use Sql\QueryBuilder\Syntax\SyntaxFactory;
use Sql\QueryBuilder\Syntax\Table;
use Sql\QueryBuilder\Syntax\Where;

// Builder injects itself into query for convestion to SQL string.

/**
 * Class AbstractBaseQuery.
 */
abstract class AbstractBaseQuery implements QueryInterface, QueryPartInterface
{
    /**
     * @var string
     */
    protected string $comment = '';

    /**
     * @var BuilderInterface
     */
    protected BuilderInterface $builder;

    /**
     * @var Table
     */
    protected Table $table;

    /**
     * @var string
     */
    protected string $whereOperator = 'AND';

    /**
     * @var Where
     */
    protected $where;

    /**
     * @var array
     */
    protected array $joins = [];

    /**
     * @var int
     */
    protected int $limitStart;

    /**
     * @var int
     */
    protected int $limitCount;

    /**
     * @var array
     */
    protected array $orderBy = [];

    /**
     * @return Where
     */
    protected function filter(): Where
    {
        if (!isset($this->where)) {
            $this->where = QueryFactory::createWhere($this);
        }

        return $this->where;
    }

    /**
     * Stores the builder that created this query.
     *
     * @param BuilderInterface $builder
     *
     * @return $this
     */
    final public function setBuilder(BuilderInterface $builder): static
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @return BuilderInterface
     *
     * @throws \RuntimeException when builder has not been injected
     */
    final public function getBuilder(): BuilderInterface
    {

        return $this->builder;
    }

    /**
     * Converts this query into an SQL string by using the injected builder.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getSql();
        } catch (\Exception $e) {
            return \sprintf('[%s] %s', \get_class($e), $e->getMessage());
        }
    }

    /**
     * Converts this query into an SQL string by using the injected builder.
     * Optionally can return the SQL with formatted structure.
     *
     * @param bool $formatted
     *
     * @return string
     */
    public function getSql(bool $formatted = false): string
    {
        if ($formatted) {
            return $this->getBuilder()->writeFormatted($this);
        }

        return $this->getBuilder()->write($this);
    }

    /**
     * @return string
     */
    abstract public function partName(): string;

    /**
     * @return Where|null
     */
    public function getWhere(): Where|null
    {
        return $this->where ?? null;
    }

    /**
     * @return null|Table
     */
    public function getTable(): null|Table
    {
        return $this->table ?? null;
    }

    /**
     * @param string|Table $table
     *
     * @return $this
     */
    public function setTable(string|Table $table): static
    {
        if (!(is_object($table) && is_a($table, Table::class))) {
            $table = new Table(
                (string)$table
            );
        }

        $this->table = $table;

        return $this;
    }

    /**
     * @param string $whereOperator
     *
     * @return Where
     * @throws QueryException
     */
    public function where(string $whereOperator = 'AND'): Where
    {
        if (!isset($this->where)) {
            $this->where = $this->filter();
        }

        $this->where->conjunction($whereOperator);

        return $this->where;
    }

    /**
     * @return string
     */
    public function getWhereOperator(): string
    {
        if (!isset($this->where)) {
            $this->where = $this->filter();
        }

        return $this->where->getConjunction();
    }

    /**
     * @param string $column
     * @param string $direction
     * @param null $table
     *
     * @return $this
     */
    public function orderBy(string $column, string $direction = OrderBy::ASC, $table = null): static
    {
        $column = SyntaxFactory::createColumn(array($column), \is_null($table) ? $this->getTable() : $table);
        $this->orderBy[] = new OrderBy($column, $direction);

        return $this;
    }

    /**
     * @return int
     */
    public function getLimitCount(): int|null
    {
        return $this->limitCount ?? null;
    }

    /**
     * @return int
     */
    public function getLimitStart(): int|null
    {
        return $this->limitStart ?? null;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment(string $comment): static
    {
        // Make each line of the comment prefixed with "--",
        // and remove any trailing whitespace.
        $comment = '-- ' . str_replace("\n", "\n-- ", \rtrim($comment));

        // Trim off any trailing "-- ", to ensure that the comment is valid.
        $this->comment = \rtrim($comment, '- ');

        if ($this->comment) {
            $this->comment .= "\n";
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
