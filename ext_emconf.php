<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "glcrossword".
 *
 * Auto generated 30-09-2014 22:56
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Crossword',
	'description' => 'This fontend extension supplies a versatile crossword game. 
		You can define crosswords of every size, answer fields with more then one letter 
        and you can define answers in every posible direction.
		All you need is to create your own questions and answers and assign it with your crossword.
		In the frontend you can edit the crossword with javascript very user friendly. 
		The communication with the backend is performed with ajax.
		See under https://www.schulze-thulin.de/en/games/welsh-crossword for an online example.
		This extension use jQuery 3.x and Bootstrap 3.x.',
	'category' => 'plugin',
	'version' => '8.0.0',
	'state' => 'stable',
	'uploadfolder' => false,
	'createDirs' => '',
	'clearcacheonload' => true,
	'author' => 'Gerald Loß',
	'author_email' => 'gerald.loss@gmx.de',
	'author_company' => '',
	'constraints' => 
	array (
		'depends' => 
		array (
			'php' => '8.0.0-8.4.99',
		    'typo3' => '12.0.00-12.4.99',
		),
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
	),
);

