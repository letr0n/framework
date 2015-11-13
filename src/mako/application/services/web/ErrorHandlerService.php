<?php

/**
 * @copyright  Frederic G. Østby
 * @license    http://www.makoframework.com/license
 */

namespace mako\application\services\web;

use mako\application\services\Service;
use mako\error\ErrorHandler;
use mako\error\handlers\WebHandler;

/**
 * Error handler service.
 *
 * @author  Frederic G. Østby
 */

class ErrorHandlerService extends Service
{
	/**
	 * Helper method that ensures lazy loading of the logger.
	 *
	 * @access  protected
	 * @param   \mako\error\ErrorHandler  $errorHandler  Error handler instance
	 */

	protected function setLogger($errorHandler)
	{
		if($this->container->get('config')->get('application.error_handler.log_errors'))
		{
			$errorHandler->setLogger($this->container->get('logger'));
		}
	}

	/**
	 * {@inheritdoc}
	 */

	public function register()
	{
		$errorHandler = new ErrorHandler;

		$displayErrors = $this->container->get('config')->get('application.error_handler.display_errors');

		$errorHandler->handle('\Throwable', function($exception) use ($errorHandler, $displayErrors)
		{
			$this->setLogger($errorHandler);

			$webHandler = new WebHandler($exception, $this->container->get('request'), $this->container->getFresh('response'), $this->container->get('view'));

			return $webHandler->handle($displayErrors);
		});

		$this->container->registerInstance(['mako\error\ErrorHandler', 'errorHandler'], $errorHandler);
	}
}