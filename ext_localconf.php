<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Loss.glcrossword',
    'Pi1',
    array(
        'Glcrossword' => 'main',
    ),
    // non-cacheable actions
    array(
        'Glcrossword' => 'main',
    )
);

// register new content element wizard 
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    <INCLUDE_TYPOSCRIPT: source="FILE:EXT:glcrossword/Configuration/TSconfig/ContentElementWizard.txt">
');

// register Wizard icon
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'ext-glcrossword-wizard-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:glcrossword/Resources/Public/Icons/Extension.svg']
);
