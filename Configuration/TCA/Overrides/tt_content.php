<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

/***************
 * Plugin
 */
ExtensionUtility::registerPlugin(
    'glcrossword',
    'Pi1',
    'Crossword',
    'EXT:glcrossword/ext_icon.gif'
    );

// insert external flexforms definition
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['glcrossword_pi1'] = 'layout,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['glcrossword_pi1'] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue('glcrossword_pi1', 'FILE:EXT:glcrossword/Configuration/FlexForms/Glcrossword.xml');
