<?php

//---------------------------------------------
// no_NO inflection rules
//---------------------------------------------

return array
(
	// Inflection rules

	'rules' => array
	(
		// Plural noun forms

		'plural' => array
		(
			'/e$/i' => "er",
			'/$/'   => "er",
		),

		// Irregular words

		'irregular' => array
		(
			'and'  => 'ender',
			'barn' => 'barn',
			'bok'  => 'bøker',
			'fisk' => 'fisk',
			'fot'  => 'føtter',
			'gås'  => 'gjess',
			'hus'  => 'hus',
			'land' => 'land',
			'ris'  => 'ris',
			'tann' => 'tenner',
			'tre'  => 'trær',
			'tå'   => 'tær',
			'vann' => 'vann',
		),
	),

	// Pluralization function

	'pluralize' => function($word, $count, $rules)
	{
		if($count !== 1)
		{
			if(isset($rules['irregular'][$word]))
			{
				$word = $rules['irregular'][$word];
			}
			else
			{
				foreach($rules['plural'] as $search => $replace)
				{
					if(preg_match($search, $word))
					{
						$word = preg_replace($search, $replace, $word);

						break;
					}
				}
			}
		}

		return $word;
	},
);

/** -------------------- End of file --------------------**/