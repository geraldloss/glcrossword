<?php
if (!defined('TYPO3')) {
    die('Do not access the file tx_glcrossword_questions.php directly.');
}

$ll = 'LLL:EXT:glcrossword/Resources/Private/Language/locallang_db.xlf:';

$tx_glcrossword_questions = array(
    'ctrl' => array(
        'title'     => $ll . 'tx_glcrossword_questions',
        'label'     => 'question',
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'origUid' => 't3_origuid',
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY crdate',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'security' => [
            'ignoreWebMountRestriction' => false,
            'ignoreRootLevelRestriction' => false,
        ],
        'iconfile'          => 'EXT:glcrossword/Resources/Public/Icons/icon_tx_glcrossword_questions.gif',
    ),
	'columns' => array(
		't3ver_label' => array(		
			'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max'  => '30',
			)
		),
		'sys_language_uid' => [
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'language',
			],
		],
		'l10n_parent' => array(		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'label'       => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xml:LGL.l18n_parent',
			'config'      => array(
				'type'  => 'select',
				'items' => array(
					[
						'label' => '',
						'value' => 0
					],
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
			'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xml:LGL.hidden',
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
				'required' => true,	
				'eval' => 'trim',
			)
		),
		'answer' => array(		
			'exclude' => 0,		
			'label' => $ll . 'tx_glcrossword_questions.answer',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
				'required' => true,	
				'eval' => 'trim,upper',
			)
		),
		'mask' => array(		
			'exclude' => 0,		
			'label' => $ll . 'tx_glcrossword_questions.mask',		
			'config' => array(
				'type' => 'number',	
				'size' => '30',
			)
		),
		'xpos' => array(		
			'exclude' => 0,		
			'label' => $ll . 'tx_glcrossword_questions.xpos',		
			'config' => array(
				'type' => 'number',
				'size' => '5',
				'required' => true,
				'range' => array(
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
				'type' => 'number',
				'size' => '5',
				'required' => true,
				'range' => array(
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
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.0',
						'value' => '0'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.1',
						'value' => '1'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.2',
						'value' => '2'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.3',
						'value' => '3'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.4',
						'value' => '4'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.5',
						'value' => '5'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.6',
						'value' => '6'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.7',
						'value' => '7'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.8',
						'value' => '8'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.9',
						'value' => '9'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.10',
						'value' => '10'
					],
					[
						'label' => $ll . 'tx_glcrossword_questions.direction.I.11',
						'value' => '11'
					],
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