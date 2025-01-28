<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
// Test
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Loss.glcrossword',
    'Pi1',
    array(
        \Loss\Glcrossword\Controller\GlcrosswordController::class => 'main',
    ),
    // non-cacheable actions
    array(
        \Loss\Glcrossword\Controller\GlcrosswordController::class => 'main',
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
