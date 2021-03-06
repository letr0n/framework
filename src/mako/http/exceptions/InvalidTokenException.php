<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\http\exceptions;

use Throwable;

use mako\http\exceptions\RequestException;

/**
 * Invalid token exception.
 *
 * @author Frederic G. Østby
 */
class InvalidTokenException extends RequestException
{
	/**
	 * Constructor.
	 *
	 * @param string|null     $message  Exception message
	 * @param \Throwable|null $previous Previous exception
	 */
	public function __construct(string $message = null, Throwable $previous = null)
	{
		parent::__construct(498, $message, $previous);
	}
}
