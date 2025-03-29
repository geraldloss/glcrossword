<?php
declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

/***************
 * Plugin
 */


ExtensionUtility::registerPlugin(
    'glcrossword',
    'crossword',
    'LLL:EXT:glcrossword/Resources/Private/Language/locallang.xlf:pi1_title',
);

//insert external flexforms definition
//$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['glcrossword_crossword'] = 'layout,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['glcrossword_crossword'] = 'recursive,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['glcrossword_crossword'] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue('glcrossword_crossword', 'FILE:EXT:glcrossword/Configuration/FlexForms/Glcrossword.xml');



// $pluginSignature = 'glcrossword_crossword';
// $pluginTitle     = 'LLL:EXT:glcrossword/Resources/Private/Language/locallang.xlf:pi1_title';
// $extensionKey    = 'glcrossword';
// $flexFormPath    = 'FILE:EXT:glcrossword/Configuration/FlexForms/Glcrossword.xml';

//  // Add the plugins to the list of plugins
//  ExtensionManagementUtility::addPlugin(
//     [$pluginTitle, $pluginSignature, 'ext-glcrossword-wizard-icon', 'plugin'],
//     'CType',
//     $extensionKey,
// );


// ExtensionManagementUtility::addToAllTCAtypes(
//     'tt_content',
//     '--div--;Configuration,pi_flexform,',
//     $pluginSignature,
//     'after:subheader'
// );

// ExtensionManagementUtility::addPiFlexFormValue(
//     $pluginSignature,
//     $flexFormPath
// );
