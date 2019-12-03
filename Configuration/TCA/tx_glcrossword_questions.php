<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$ll = 'LLL:EXT:glcrossword/Resources/Private/Language/locallang_db.xlf:';

$tx_glcrossword_questions = array(
    'ctrl' => array(
        'title'     => $ll . 'tx_glcrossword_questions',
        'label'     => 'question',
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'origUid' => 't3_origuid',
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY crdate',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'iconfile'          => 'EXT:glcrossword/Resources/Public/Icons/icon_tx_glcrossword_questions.gif',
    ),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,question,answer,mask,xpos,ypos,direction'
	),
	'feInterface' => $TCA['tx_glcrossword_questions']['feInterface'],
	'columns' => array(
		't3ver_label' => array(		
			'label'  => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max'  => '30',
			)
		),
		'sys_language_uid' => array(		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xml:LGL.language',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/Resources/Private/Language/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/Resources/Private/Language/locallang_general.xml:LGL.default_value', 0)
				),
				'renderType' => 'selectSingle',
			    'default' => 0,
			)
		),
		'l10n_parent' => array(		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xml:LGL.l18n_parent',
			'config'      => array(
				'type'  => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table'       => 'tx_glcrossword_questions',
				'foreign_table_where' => 'AND tx_glcrossword_questions.pid=###CURRENT_PID### AND tx_glcrossword_questions.sys_language_uid IN (-1,0)',
				'renderType' => 'selectSingle',
			)
		),
		'l10n_diffsource' => array(		
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'question' => array(		
			'exclude' => 0,		
			'label' => $ll . 'tx_glcrossword_questions.question',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
		'answer' => array(		
			'exclude' => 0,		
			'label' => $ll . 'tx_glcrossword_questions.answer',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required, trim, upper',
			)
		),
		'mask' => array(		
			'exclude' => 0,		
			'label' => $ll . 'tx_glcrossword_questions.mask',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'int',
			)
		),
		'xpos' => array(		
			'exclude' => 0,		
			'label' => $ll . 'tx_glcrossword_questions.xpos',		
			'config' => array(
				'type'     => 'input',
				'size'     => '5',
				'max'      => '5',
				'eval'     => 'int,required',
				'checkbox' => '0',
				'range'    => array(
					'upper' => '99999',
					'lower' => '1'
				),
				'default' => 0
			)
		),
		'ypos' => array(		
			'exclude' => 0,		
			'label' => $ll . 'tx_glcrossword_questions.ypos',		
			'config' => array(
				'type'     => 'input',
				'size'     => '5',
				'max'      => '5',
				'eval'     => 'int,required',
				'checkbox' => '0',
				'range'    => array(
					'upper' => '99999',
					'lower' => '1'
				),
				'default' => 0
			)
		),
		'direction' => array(		
			'exclude' => 0,		
			'label' => $ll . 'tx_glcrossword_questions.direction',		
			'config' => array(
				'type' => 'select',
				'items' => array(
					array($ll . 'tx_glcrossword_questions.direction.I.0', '0'),
					array($ll . 'tx_glcrossword_questions.direction.I.1', '1'),
					array($ll . 'tx_glcrossword_questions.direction.I.2', '2'),
					array($ll . 'tx_glcrossword_questions.direction.I.3', '3'),
					array($ll . 'tx_glcrossword_questions.direction.I.4', '4'),
					array($ll . 'tx_glcrossword_questions.direction.I.5', '5'),
					array($ll . 'tx_glcrossword_questions.direction.I.6', '6'),
					array($ll . 'tx_glcrossword_questions.direction.I.7', '7'),
					array($ll . 'tx_glcrossword_questions.direction.I.8', '8'),
					array($ll . 'tx_glcrossword_questions.direction.I.9', '9'),
					array($ll . 'tx_glcrossword_questions.direction.I.10', '10'),
					array($ll . 'tx_glcrossword_questions.direction.I.11', '11'),
				),
				'size' => 1,	
				'maxitems' => 1,
				'renderType' => 'selectSingle',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'l10n_parent, l10n_diffsource, question, answer, mask, xpos, ypos, direction')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);

return $tx_glcrossword_questions;
?>