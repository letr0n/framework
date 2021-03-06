<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\database\query;

use Closure;

use mako\database\query\Query;

/**
 * Subquery container.
 *
 * @author Frederic G. Østby
 */
class Subquery
{
	/**
	 * Query builder.
	 *
	 * @var \mako\database\query\Query
	 */
	protected $query;

	/**
	 * Alias.
	 *
	 * @var string
	 */
	protected $alias = null;

	/**
	 * Constructor.
	 *
	 * @param \Closure|\mako\database\query\Query $query Query builder
	 * @param string                              $alias Subquery alias
	 */
	public function __construct($query, string $alias = null)
	{
		$this->query = $query;
		$this->alias = $alias;
	}

	/**
	 * Converts a subquery closure to query a builder instance.
	 *
	 * @param  \mako\database\query\Query    $query Query builder instance
	 * @return \mako\database\query\Subquery
	 */
	public function build(Query $query): Subquery
	{
		if($this->query instanceof Closure)
		{
			$subquery = $this->query;

			$this->query = $query->newInstance();

			$subquery($this->query);
		}

		return $this;
	}

	/**
	 * Returns the compiled query.
	 *
	 * @param  bool  $enclose Should the query be enclosed in parentheses?
	 * @return array
	 */
	public function get(bool $enclose = true): array
	{
		$query = $this->query->getCompiler()->select();

		$query['sql'] = $enclose ? '(' . $query['sql'] . ')' : $query['sql'];

		if($this->alias !== null)
		{
			$query['sql'] .= ' AS ' . $this->query->getCompiler()->escapeIdentifier($this->alias);
		}

		return $query;
	}
}
