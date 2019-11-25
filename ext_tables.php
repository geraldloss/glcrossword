<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// load CSH for flexform
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tt_content.pi_flexform.glcrossword_pi1.list', 
    'EXT:glcrossword/Resources/Private/Language/locallang_csh_flexforms.xlf');

// load CSH for database fields
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_glcrossword_questions',
    'EXT:glcrossword/Resources/Private/Language/locallang_csh.xlf');
