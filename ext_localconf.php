<?php
if (!defined('TYPO3')) {
    die('Do not access the file ext_localconf.php directly.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Glcrossword',
    'Crossword',
    [
        \Loss\Glcrossword\Controller\GlcrosswordController::class => 'main',
    ],
    // non-cacheable actions
    [
        \Loss\Glcrossword\Controller\GlcrosswordController::class => 'main',
    ],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

// register Wizard icon
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'ext-glcrossword-wizard-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:glcrossword/Resources/Public/Icons/Extension.svg']
);
