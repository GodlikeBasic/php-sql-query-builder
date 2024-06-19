<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/24/14
 * Time: 12:30 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sql\QueryBuilder\Manipulation;

/**
 * Class AbstractCreationalQuery.
 */
abstract class AbstractCreationalQuery extends AbstractBaseQuery
{
    /**
     * @var array
     */
    protected array $values = [];

    /**
     * @param null $table
     * @param array|null $values
     */
    public function __construct($table = null, array $values = null)
    {
        if (isset($table)) {
            $this->setTable($table);
        }

        if (!empty($values)) {
            $this->setValues($values);
        }
    }

    /**
     * @return array
     */
    public function getValues(): array
	{
        return $this->values;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function setValues(array $values): static
	{
        $this->values = \array_filter($values, function($value) {
            if (is_int($value)) {
                return true;
            }
            return $value;
        });
        
        return $this;
    }
}
