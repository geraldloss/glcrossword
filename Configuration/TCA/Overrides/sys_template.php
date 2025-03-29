<?php
declare(strict_types=1);

defined('TYPO3') or die();

//register static template with the name Crossword
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('glcrossword', 
                                                                  'Configuration/TypoScript', 
                                                                  'Crossword');
