<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\http\routing;

use mako\http\Request;
use mako\http\routing\Routes;

/**
 * URL builder.
 *
 * @author Frederic G. Østby
 */
class URLBuilder
{
	/**
	 * Request instance.
	 *
	 * @var \mako\http\Request
	 */
	protected $request;

	/**
	 * Route collection.
	 *
	 * @var \mako\http\routing\Routes
	 */
	protected $routes;

	/**
	 * Create "clean" URLs?
	 *
	 * @var bool
	 */
	protected $cleanURLs;

	/**
	 * Base URL.
	 *
	 * @var string
	 */
	protected $baseURL;

	/**
	 * Script name.
	 *
	 * @var string
	 */
	protected $scriptName;

	/**
	 * Language prefix.
	 *
	 * @var string
	 */
	protected $languagePrefix;

	/**
	 * Constructor.
	 *
	 * @param \mako\http\Request        $request   Request instance
	 * @param \mako\http\routing\Routes $routes    Route collection
	 * @param bool                      $cleanURLs Create "clean" URLs?
	 * @param string|null               $baseURL   Base URL
	 */
	public function __construct(Request $request, Routes $routes, bool $cleanURLs = false, string $baseURL = null)
	{
		$this->request   = $request;
		$this->routes    = $routes;
		$this->cleanURLs = $cleanURLs;
		$this->baseURL   = $baseURL;

		$this->scriptName = $request->scriptName();

		if(!empty($language = $request->languagePrefix()))
		{
			$this->languagePrefix = '/' . $language;
		}
	}

	/**
	 * Returns TRUE if the pattern matches the current route and FALSE if not.
	 *
	 * @param  string $pattern Pattern to match
	 * @return bool
	 */
	public function matches(string $pattern): bool
	{
		return (bool) preg_match('#' . $pattern . '#', $this->request->path());
	}

	/**
	 * Returns the base URL of the application.
	 *
	 * @return string
	 */
	public function base(): string
	{
		if(empty($this->baseURL))
		{
			$this->baseURL = $this->request->baseURL();
		}

		return $this->baseURL;
	}

	/**
	 * Returns the URL of the specified path.
	 *
	 * @param  string $path        Path
	 * @param  array  $queryParams Associative array used to build URL-encoded query string
	 * @param  string $separator   Argument separator
	 * @param  mixed  $language    Request language
	 * @return string
	 */
	public function to(string $path, array $queryParams = [], string $separator = '&amp;', $language = true): string
	{
		$url = $this->base() . ($this->cleanURLs ? '' : '/' . $this->scriptName) . ($language === true ? $this->languagePrefix : (!$language ? '' : '/' . $language)) . $path;

		if(!empty($queryParams))
		{
			$url .= '?' . http_build_query($queryParams, '', $separator);
		}

		return $url;
	}

	/**
	 * Returns the URL of a named route.
	 *
	 * @param  string $routeName   Route name
	 * @param  array  $routeParams Route parameters
	 * @param  array  $queryParams Associative array used to build URL-encoded query string
	 * @param  string $separator   Argument separator
	 * @param  mixed  $language    Request language
	 * @return string
	 */
	public function toRoute(string $routeName, array $routeParams = [], array $queryParams = [], string $separator = '&amp;', $language = true): string
	{
		$route = $this->routes->getNamedRoute($routeName)->getRoute();

		foreach($routeParams as $key => $value)
		{
			if(empty($value) && ($value === '' || $value === false || $value === null))
			{
				continue;
			}

			$route = preg_replace('/{' . $key . '}\??/', $value, $route, 1);
		}

		if(strpos($route, '?') !== false)
		{
			$route = preg_replace('/\/{\w+}\?/', '', $route);
		}

		return $this->to($route, $queryParams, $separator, $language);
	}

	/**
	 * Returns the current URL of the request.
	 *
	 * @param  array  $queryParams Associative array used to build URL-encoded query string
	 * @param  string $separator   Argument separator
	 * @param  mixed  $language    Request language
	 * @return string
	 */
	public function current(array $queryParams = [], string $separator = '&amp;', $language = true): string
	{
		$queryParams = $queryParams ?: $this->request->getQuery()->all();

		return $this->to($this->request->path(), $queryParams, $separator, $language);
	}

	/**
	 * Returns the URL of the specified route.
	 *
	 * @param  string $route       URL segments
	 * @param  mixed  $language    Request language
	 * @param  array  $queryParams Associative array used to build URL-encoded query string
	 * @param  string $separator   Argument separator
	 * @return string
	 */
	public function toLanguage(string $route, $language, array $queryParams = [], string $separator = '&amp;'): string
	{
		return $this->to($route, $queryParams, $separator, $language);
	}

	/**
	 * Returns the URL of a named route.
	 *
	 * @param  string $routeName   Route name
	 * @param  string $language    Request language
	 * @param  array  $routeParams Route parameters
	 * @param  array  $queryParams Associative array used to build URL-encoded query string
	 * @param  string $separator   Argument separator
	 * @return string
	 */
	public function toRouteLanguage(string $routeName, string $language, array $routeParams = [], array $queryParams = [], string $separator = '&amp;'): string
	{
		return $this->toRoute($routeName, $routeParams, $queryParams, $separator, $language);
	}

	/**
	 * Returns the current URL of the request.
	 *
	 * @param  string $language    Request language
	 * @param  array  $queryParams Query parameters
	 * @param  string $separator   Argument separator
	 * @return string
	 */
	public function currentLanguage(string $language, array $queryParams = [], string $separator = '&amp;'): string
	{
		return $this->current($queryParams, $separator, $language);
	}
}
