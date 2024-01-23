<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/3/14
 * Time: 12:07 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sql\QueryBuilder\Builder;

use ReflectionException;
use Sql\QueryBuilder\Builder\Syntax\WriterFactory;
use Sql\QueryBuilder\Manipulation\AbstractBaseQuery;
use Sql\QueryBuilder\Manipulation\Intersect;
use Sql\QueryBuilder\Manipulation\Minus;
use Sql\QueryBuilder\Manipulation\QueryInterface;
use Sql\QueryBuilder\Manipulation\QueryFactory;
use Sql\QueryBuilder\Manipulation\Select;
use Sql\QueryBuilder\Manipulation\Union;
use Sql\QueryBuilder\Manipulation\UnionAll;
use Sql\QueryBuilder\Syntax\Column;
use Sql\QueryBuilder\Syntax\Table;

/**
 * Class Generic.
 */
class GenericBuilder implements BuilderInterface
{
    /**
     * The placeholder parameter bag.
     *
     * @var \Sql\QueryBuilder\Builder\Syntax\PlaceholderWriter
     */
    protected $placeholderWriter;

    /**
     * The Where writer.
     *
     * @var \Sql\QueryBuilder\Builder\Syntax\WhereWriter
     */
    protected $whereWriter;

    /**
     * The SQL formatter.
     *
     * @var \Sql\QueryFormatter\Formatter
     */
    protected $sqlFormatter;

    /**
     * Class namespace for the query pretty output formatter.
     * Required to create the instance only if required.
     *
     * @var string
     */
    protected $sqlFormatterClass = 'Sql\QueryFormatter\Formatter';

    /**
     * Array holding the writers for each query part. Methods are called upon request and stored in
     * the $queryWriterInstances array.
     *
     * @var array
     */
    protected $queryWriterArray = [
        'SELECT' => '\Sql\QueryBuilder\Builder\Syntax\WriterFactory::createSelectWriter',
        'INSERT' => '\Sql\QueryBuilder\Builder\Syntax\WriterFactory::createInsertWriter',
        'UPDATE' => '\Sql\QueryBuilder\Builder\Syntax\WriterFactory::createUpdateWriter',
        'DELETE' => '\Sql\QueryBuilder\Builder\Syntax\WriterFactory::createDeleteWriter',
        'INTERSECT' => '\Sql\QueryBuilder\Builder\Syntax\WriterFactory::createIntersectWriter',
        'MINUS' => '\Sql\QueryBuilder\Builder\Syntax\WriterFactory::createMinusWriter',
        'UNION' => '\Sql\QueryBuilder\Builder\Syntax\WriterFactory::createUnionWriter',
        'UNION ALL' => '\Sql\QueryBuilder\Builder\Syntax\WriterFactory::createUnionAllWriter',
    ];

    /**
     * Array that stores instances of query writers.
     *
     * @var array
     */
    protected $queryWriterInstances = [
        'SELECT' => null,
        'INSERT' => null,
        'UPDATE' => null,
        'DELETE' => null,
        'INTERSECT' => null,
        'MINUS' => null,
        'UNION' => null,
        'UNION ALL' => null,
    ];

    /**
     * Creates writers.
     */
    public function __construct()
    {
        $this->placeholderWriter = WriterFactory::createPlaceholderWriter();
    }

    /**
     * @param null $table
     * @param array|null $columns
     *
     * @return AbstractBaseQuery
     */
    public function select($table = null, array $columns = null): AbstractBaseQuery
    {
        return $this->injectBuilder(QueryFactory::createSelect($table, $columns));
    }

    /**
     * @param AbstractBaseQuery $query
     *
     * @return AbstractBaseQuery
     */
    protected function injectBuilder(AbstractBaseQuery $query): AbstractBaseQuery
    {
        return $query->setBuilder($this);
    }

    /**
     * @param null $table
     * @param array|null $values
     *
     * @return AbstractBaseQuery
     */
    public function insert($table = null, array $values = null)
    {
        return $this->injectBuilder(QueryFactory::createInsert($table, $values));
    }

    /**
     * @param null $table
     * @param array|null $values
     *
     * @return AbstractBaseQuery
     */
    public function update($table = null, array $values = null)
    {
        return $this->injectBuilder(QueryFactory::createUpdate($table, $values));
    }

    /**
     * @param string|null $table
     *
     * @return AbstractBaseQuery
     */
    public function delete(string $table = null): AbstractBaseQuery
    {
        return $this->injectBuilder(QueryFactory::createDelete($table));
    }

    /**
     * @return Intersect
     */
    public function intersect(): Intersect
    {
        return QueryFactory::createIntersect();
    }

    /**
     * @return Union
     */
    public function union()
    {
        return QueryFactory::createUnion();
    }

    /**
     * @return UnionAll
     */
    public function unionAll(): UnionAll
    {
        return QueryFactory::createUnionAll();
    }

    /**
     * @param Select $first
     * @param Select $second
     *
     * @return Minus
     */
    public function minus(Select $first, Select $second): Minus
    {
        return QueryFactory::createMinus($first, $second);
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->placeholderWriter->get();
    }

    /**
     * @return array
     */
    public function getValuesWithNoColons(): array
    {
        $placeholders = $this->placeholderWriter->get();

        foreach($placeholders as $placeholder => $value) {
            $placeholders[substr($placeholder, 1)] = $value;

            unset($placeholders[$placeholder]);
        }

        return $placeholders;
    }

    /**
     * Returns a SQL string in a readable human-friendly format.
     *
     * @param QueryInterface $query
     *
     * @return string
     * @throws ReflectionException
     */
    public function writeFormatted(QueryInterface $query): string
    {
        if (null === $this->sqlFormatter) {
            $this->sqlFormatter = (new \ReflectionClass($this->sqlFormatterClass))->newInstance();
        }

        return $this->sqlFormatter->format($this->write($query));
    }

    /**
     * @param QueryInterface $query
     * @param bool $resetPlaceholders
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function write(QueryInterface $query, bool $resetPlaceholders = true): string
    {
        if ($resetPlaceholders) {
            $this->placeholderWriter->reset();
        }

        $queryPart = $query->partName();

        if (false === empty($this->queryWriterArray[$queryPart])) {
            $this->createQueryObject($queryPart);

            return $this->queryWriterInstances[$queryPart]->write($query);
        }

        throw new \RuntimeException('Query builder part not defined.');
    }

    /**
     * @param Select $select
     *
     * @return string
     */
    public function writeJoin(Select $select): string
    {
        if (null === $this->whereWriter) {
            $this->whereWriter = WriterFactory::createWhereWriter($this, $this->placeholderWriter);
        }

        $sql = ($select->getJoinType()) ? "{$select->getJoinType()} " : '';
        $sql .= 'JOIN ';
        $sql .= $this->writeTableWithAlias($select->getTable());
        $sql .= ' ON ';
        $sql .= $this->whereWriter->writeWhere($select->getJoinCondition());

        return $sql;
    }

    /**
     * @param Table $table
     *
     * @return string
     */
    public function writeTableWithAlias(Table $table)
    {
        $alias = ($table->getAlias()) ? " AS {$this->writeTableAlias($table->getAlias())}" : '';
        $schema = ($table->getSchema()) ? "{$table->getSchema()}." : '';

        return $schema.$this->writeTableName($table).$alias;
    }

    /**
     * @param $alias
     *
     * @return mixed
     */
    public function writeTableAlias($alias)
    {
        return $alias;
    }

    /**
     * Returns the table name.
     *
     * @param Table $table
     *
     * @return string
     */
    public function writeTableName(Table $table)
    {
        return $table->getName();
    }

    /**
     * @param string $alias
     *
     * @return string
     */
    public function writeColumnAlias($alias)
    {
        return sprintf('"%s"', $alias);
    }

    /**
     * @param Table $table
     *
     * @return string
     */
    public function writeTable(Table $table)
    {
        $schema = ($table->getSchema()) ? "{$table->getSchema()}." : '';

        return $schema.$this->writeTableName($table);
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function writeValues(array &$values)
    {
        \array_walk(
            $values,
            function (&$value) {
                $value = $this->writePlaceholderValue($value);
            }
        );

        return $values;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function writePlaceholderValue($value)
    {
        return $this->placeholderWriter->add($value);
    }

    /**
     * @param $operator
     *
     * @return string
     */
    public function writeConjunction($operator)
    {
        return ' '.$operator.' ';
    }

    /**
     * @return string
     */
    public function writeIsNull()
    {
        return ' IS NULL';
    }

    /**
     * @return string
     */
    public function writeIsNotNull()
    {
        return ' IS NOT NULL';
    }

    /**
     * Returns the column name.
     *
     * @param Column $column
     *
     * @return string
     */
    public function writeColumnName(Column $column)
    {
        $name = $column->getName();

        if ($name === Column::ALL) {
            return $this->writeColumnAll();
        }

        return $name;
    }

    /**
     * @return string
     */
    protected function writeColumnAll()
    {
        return '*';
    }

    /**
     * @param string $queryPart
     */
    protected function createQueryObject($queryPart)
    {
        if (null === $this->queryWriterInstances[$queryPart]) {
            $this->queryWriterInstances[$queryPart] = \call_user_func_array(
                \explode('::', $this->queryWriterArray[$queryPart]),
                [$this, $this->placeholderWriter]
            );
        }
    }
}
