<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 6/4/14
 * Time: 12:02 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sql\QueryBuilder\Builder\Syntax;

/**
 * Class PlaceholderWriter.
 */
class PlaceholderWriter
{
    /**
     * @var int
     */
    protected $counter = 1;

	protected bool $named_placeholders = true;

    /**
     * @var array
     */
    protected $placeholders = [];

    /**
     * @return array
     */
    public function get()
    {
        return $this->placeholders;
    }

    /**
     * @return $this
     */
    public function reset(): static
    {
        $this->counter = 1;
        $this->placeholders = [];

        return $this;
    }

	public function enabledUnnamed(): static
	{
		$this->named_placeholders = false;
		return $this;
	}

    /**
     * @param $value
     *
     * @return string
     */
    public function add($value): string
	{
		if($this->named_placeholders)
		{
			$placeholderKey = ':v' . $this->counter;
			$this->placeholders[$placeholderKey] = $this->setValidSqlValue($value);

			++$this->counter;
		}
		else
		{
			$placeholderKey = '?';
			$this->placeholders[] = $this->setValidSqlValue($value);
		}

        return $placeholderKey;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function setValidSqlValue($value)
    {
        $value = $this->writeNullSqlString($value);
        $value = $this->writeStringAsSqlString($value);
        $value = $this->writeBooleanSqlString($value);

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function writeNullSqlString($value)
    {
        if (\is_null($value) || (\is_string($value) && empty($value))) {
            $value = $this->writeNull();
        }

        return $value;
    }

    /**
     * @return string
     */
    protected function writeNull()
    {
        return 'NULL';
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function writeStringAsSqlString($value)
    {
        if (\is_string($value)) {
            $value = $this->writeString($value);
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function writeString($value)
    {
        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function writeBooleanSqlString($value)
    {
        if (\is_bool($value)) {
            $value = $this->writeBoolean($value);
        }

        return $value;
    }

    /**
     * @param bool $value
     *
     * @return string
     */
    protected function writeBoolean($value)
    {
        $value = \filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return ($value) ? '1' : '0';
    }
}
