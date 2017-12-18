<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\onion;

use Closure;

use mako\onion\OnionException;
use mako\syringe\Container;

/**
 * Middleware stack.
 *
 * @author Yamada Taro
 */
class Onion
{
	/**
	 * Container.
	 *
	 * @var \mako\syringe\Container
	 */
	protected $container;

	/**
	 * Method to call on the decoracted class.
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * Expected middleware interface.
	 *
	 * @var string|null
	 */
	protected $expectedInterface;

	/**
	 * Middleware parameter setter method.
	 *
	 * @var string|null
	 */
	protected $parameterSetter;

	/**
	 * Middleware layers.
	 *
	 * @var array
	 */
	protected $layers = [];

	/**
	 * Middleware parameters.
	 *
	 * @var array
	 */
	protected $parameters = [];

	/**
	 * Constructor.
	 *
	 * @param \mako\syringe\Container|null $container         Container
	 * @param string|null                  $method            Method to call on the decoracted class
	 * @param string|null                  $expectedInterface Expected middleware interface
	 * @param string|null                  $parameterSetter   Parameter setter name
	 */
	public function __construct(Container $container = null, string $method = null, string $expectedInterface = null, string $parameterSetter = null)
	{
		$this->container = $container ?? new Container;

		$this->method = $method ?? 'handle';

		$this->exeptedInterface = $expectedInterface;

		$this->parameterSetter = $parameterSetter;
	}

	/**
	 * Add a new middleware layer.
	 *
	 * @param  string     $class      Class
	 * @param  array|null $parameters Middleware parameters
	 * @param  bool       $inner      Add an inner layer?
	 * @return int
	 */
	public function addLayer(string $class, array $parameters = null, bool $inner = true): int
	{
		$this->parameters[$class] = $parameters;

		return $inner ? array_unshift($this->layers, $class) : array_push($this->layers, $class);
	}

	/**
	 * Add a inner layer to the middleware stack.
	 *
	 * @param  string     $class      Class
	 * @param  array|null $parameters Middleware parameters
	 * @return int
	 */
	public function addInnerLayer(string $class, array $parameters = null): int
	{
		return $this->addLayer($class, $parameters);
	}

	/**
	 * Add an outer layer to the middleware stack.
	 *
	 * @param  string     $class      Class
	 * @param  array|null $parameters Middleware parameters
	 * @return int
	 */
	public function addOuterLayer(string $class, array $parameters = null): int
	{
		return $this->addLayer($class, $parameters, false);
	}

	/**
	 * Builds the core closure.
	 *
	 * @param  object   $object The object that we're decorating
	 * @return \Closure
	 */
	protected function buildCoreClosure($object): Closure
	{
		return function(...$arguments) use ($object)
		{
			$callable = $object instanceof Closure ? $object : [$object, $this->method];

			return $callable(...$arguments);
		};
	}

	/**
	 * Builds a layer closure.
	 *
	 * @param  object   $layer Middleware object
	 * @param  \Closure $next  The next middleware layer
	 * @return \Closure
	 */
	protected function buildLayerClosure($layer, Closure $next): Closure
	{
		return function(...$arguments) use ($layer, $next)
		{
			return $layer->execute(...array_merge($arguments, [$next]));
		};
	}

	/**
	 * Returns the parameters of the requested middleware.
	 *
	 * @param  array  $parameters Parameters array
	 * @param  string $middleware Middleware name
	 * @return array
	 */
	protected function getMiddlewareParameters(array $parameters, string $middleware): array
	{
		return ($parameters[$middleware] ?? []) + ($this->parameters[$middleware] ?? []);
	}

	/**
	 * Middleware factory.
	 *
	 * @param  string $layer                Class name
	 * @param  array  $middlewareParameters Middleware parameters
	 * @return object
	 */
	protected function middlewareFactory(string $layer, array $middlewareParameters)
	{
		// Merge middleware parameters

		$parameters = $this->getMiddlewareParameters($middlewareParameters, $layer);

		// Create middleware instance

		$middleware = $this->parameterSetter === null ? $this->container->get($layer, $parameters) : $this->container->get($layer);

		// Check if the middleware implements the expected interface

		if($this->exeptedInterface !== null && ($middleware instanceof $this->exeptedInterface) === false)
		{
			throw new OnionException(vsprintf("The Onion instance expects middleware to be an instance of [ %s ].", [$this->exeptedInterface]));
		}

		// Set parameters if the middleware uses a setter

		if($this->parameterSetter !== null)
		{
			$middleware->{$this->parameterSetter}($parameters);
		}

		// Return middleware instance

		return $middleware;
	}

	/**
	 * Executes the middleware stack.
	 *
	 * @param  object $object               The object that we're decorating
	 * @param  array  $parameters           Parameters
	 * @param  array  $middlewareParameters Middleware parameters
	 * @return mixed
	 */
	public function peel($object, array $parameters = [], array $middlewareParameters = [])
	{
		$next = $this->buildCoreClosure($object);

		foreach($this->layers as $layer)
		{
			$layer = $this->middlewareFactory($layer, $middlewareParameters);

			$next = $this->buildLayerClosure($layer, $next);
		}

		return $next(...$parameters);
	}
}
