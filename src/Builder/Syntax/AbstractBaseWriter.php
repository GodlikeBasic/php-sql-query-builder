<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/24/14
 * Time: 1:14 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sql\QueryBuilder\Builder\Syntax;

use Sql\QueryBuilder\Builder\GenericBuilder;
use Sql\QueryBuilder\Manipulation\AbstractBaseQuery;

/**
 * Class AbstractBaseWriter.
 */
abstract class AbstractBaseWriter
{
    /**
     * @var GenericBuilder
     */
    protected GenericBuilder $writer;

    /**
     * @var PlaceholderWriter
     */
    protected PlaceholderWriter $placeholderWriter;

    /**
     * @var ColumnWriter
     */
    protected ColumnWriter $columnWriter;

    /**
     * @param GenericBuilder $writer
     * @param PlaceholderWriter $placeholder
     */
    public function __construct(GenericBuilder $writer, PlaceholderWriter $placeholder)
    {
        $this->writer = $writer;
        $this->placeholderWriter = $placeholder;

        $this->columnWriter = WriterFactory::createColumnWriter($writer, $placeholder);
    }

    /**
     * @param AbstractBaseQuery $class
     *
     * @return string
     */
    public static function writeQueryComment(AbstractBaseQuery $class): string
    {
        $comment = '';
        if ('' !== $class->getComment()) {
            $comment = $class->getComment();
        }

        return $comment;
    }

    /**
     * @param AbstractBaseQuery $class
     * @param GenericBuilder $writer
     * @param PlaceholderWriter $placeholderWriter
     * @param array $parts
     */
    public static function writeWhereCondition(
        AbstractBaseQuery $class,
        GenericBuilder    $writer,
        PlaceholderWriter $placeholderWriter,
        array             &$parts
    ): void
    {
        if (!is_null($class->getWhere())) {
            $whereWriter = WriterFactory::createWhereWriter($writer, $placeholderWriter);
            $parts[] = "WHERE {$whereWriter->writeWhere($class->getWhere())}";
        }
    }

    /**
     * @param AbstractBaseQuery $class
     * @param PlaceholderWriter $placeholderWriter
     * @param array $parts
     */
    public static function writeLimitCondition(
        AbstractBaseQuery $class,
        PlaceholderWriter $placeholderWriter,
        array             &$parts
    ): void
    {
        if (!is_null($class->getLimitStart())) {
            $start = $placeholderWriter->add($class->getLimitStart());
            $parts[] = "LIMIT {$start}";
        }
    }
}
