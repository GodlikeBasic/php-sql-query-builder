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
 * Class SyntaxFactory.
 */
final class SyntaxFactory
{
    /**
     * Creates a collection of Column objects.
     *
     * @param array $arguments
     * @param Table|null $table
     *
     * @return array
     * @throws QueryException
     * @throws QueryException
     * @throws QueryException
     */
    public static function createColumns(array &$arguments, $table = null)
    {
        $createdColumns = [];

        foreach ($arguments as $index => $column) {
            if (!is_object($column)) {
                $newColumn = array($column);
                $column = self::createColumn($newColumn, $table);
                if (!is_numeric($index)) {
                    $column->setAlias($index);
                }

                $createdColumns[] = $column;
            } else if ($column instanceof Column) {
                $createdColumns[] = $column;
            }
        }

        return \array_filter($createdColumns);
    }

    /**
     * Creates a Column object.
     *
     * @param array $argument
     * @param null|Table $table
     *
     * @return Column
     * @throws QueryException
     */
    public static function createColumn(array &$argument, $table = null)
    {
        $columnName = \array_values($argument);
        $columnName = $columnName[0];

        $columnAlias = \array_keys($argument);
        $columnAlias = $columnAlias[0];

        if (\is_numeric($columnAlias) || str_contains($columnName, '*')) {
            $columnAlias = null;
        }

        return new Column($columnName, $table, $columnAlias);
    }

    /**
     * Creates a Table object.
     *
     * @param array|string|null $table
     *
     * @return Table
     */
    public static function createTable(array|string|null $table): Table
    {
        $tableName = $table;
        if (is_array($table)) {
            $tableName = current($table);
            $tableAlias = key($table);
        }

        $newTable = new Table($tableName);

        if (isset($tableAlias) && !is_numeric($tableAlias)) {
            $newTable->setAlias($tableAlias);
        }

        return $newTable;
    }
}
