<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\cli\input\helpers;

use mako\cli\input\helpers\Question;
use mako\cli\input\Input;
use mako\cli\output\Output;

/**
 * Confirmation helper.
 *
 * @author Frederic G. Østby
 */
class Confirmation extends Question
{
	/**
	 * Input instance.
	 *
	 * @var \mako\cli\input\Input
	 */
	protected $input;

	/**
	 * Output instance.
	 *
	 * @var \mako\cli\output\Output
	 */
	protected $ouput;

	/**
	 * Constructor.
	 *
	 * @param \mako\cli\input\Input   $input  Input instance
	 * @param \mako\cli\output\Output $output Output instance
	 */
	public function __construct(Input $input, Output $output)
	{
		$this->input = $input;

		$this->output = $output;
	}

	/**
	 * Returns an array where all array keys lower case.
	 *
	 * @param  array $array Array
	 * @return array
	 */
	protected function normalizeKeys(array $array): array
	{
		$normalized = [];

		foreach($array as $key => $value)
		{
			$normalized[mb_strtolower($key)] = $value;
		}

		return $normalized;
	}

	/**
	 * Returns a slash-separated list of valid options where the default one is highlighted as upper-case.
	 *
	 * @param  array  $options Answer options
	 * @param  string $default Default answer
	 * @return string
	 */
	protected function getOptions(array $options, string $default): string
	{
		$highlighted = [];

		foreach(array_keys($options) as $option)
		{
			$highlighted[] = $option === $default ? mb_strtoupper($option) : $option;
		}

		return implode('/', $highlighted);
	}

	/**
	 * Asks user for confirmation and returns value corresponding to the chosen value.
	 *
	 * @param  string     $question Question to ask
	 * @param  string     $default  Default answer
	 * @param  array|null $options  Answer options
	 * @return bool
	 */
	public function ask(string $question, $default = 'n', array $options = null)
	{
		$options = $options === null ? ['y' => true, 'n' => false] : $this->normalizeKeys($options);

		$input = parent::ask(trim($question) . ' [' . $this->getOptions($options, $default) . '] ');

		$input = mb_strtolower(empty($input) ? $default : $input);

		if(!isset($options[$input]))
		{
			return $this->ask($question, $default, $options);
		}

		return $options[$input];
	}
}
