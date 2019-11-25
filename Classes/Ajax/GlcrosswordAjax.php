<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Gerald Loﬂ <gerald.loss@gmx.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
namespace Loss\Glcrossword\Ajax;

use Loss\Glcrossword\Pi1\GlcrosswordCrossword;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Plugin 'glcrossword AJAX manager' for the 'glcrossword' extension.
 *
 * @author	Gerald Loﬂ <gerald.loss@gmx.de>
 * @package	glcrossword
 */
class GlcrosswordAjax {

	protected $m_strLocalLang = '';
	
	public function handleAjaxRequest() {
		
		// the unique ID
		$l_intUniqueId = 0;
		// the requested process
		$l_strfuncRequestedProcess = '';
		// the parameters of the ajax backend method
		$l_arrParams = array();
		/** @var $feUserObject \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication */
		$feUserObject = NULL;
		
        // read the stored crosswords from the session
		GlcrosswordCrossword::$m_arrCrosswords = $GLOBALS['TSFE']->fe_user->getKey('ses', 'glcrossword_arrCrosswords');
		
		// get the unique ID of the requestet crossword
		$l_intUniqueId = GeneralUtility::_GET('intUniqueId');
		// get the requestet process
		$l_strfuncRequestedProcess = GeneralUtility::_GET('strProcess');
		// get the parameters of the backend method
		$l_arrParams = GeneralUtility::_GET('params');
		
		return json_encode(array('result' => $this->$l_strfuncRequestedProcess($l_intUniqueId, $l_arrParams),
								 'intUniqueId' => $l_intUniqueId ));
	}
	
	/**
	 * Returns array with all texts of the questions and some othe important data of the crossword.
	 * 
	 * @param 	integer $i_intUniqueId 	The unique ID of the crossword.
	 * @param	array	$i_arrParams	Array with parameters for this method.
	 * @return 	array					Array with all the important data of the crossword.
	 */
	protected function getGeneralCrosswordData($i_intUniqueId, $i_arrParams) {
		
		// the crossword object
		/* @var GlcrosswordCrossword $l_objCrossword */
		$l_objCrossword = NULL;
		// the array with the result of this request
		$l_arrResult = array();
		
		// get the Crossword with this ID
		$l_objCrossword = GlcrosswordCrossword::get_Crossword($i_intUniqueId);
		
		// create all data infostructures of the crossword for the frontend
		$l_arrResult = array(
					// array with all questiondata
					'questions' 	=> $l_objCrossword->getQuestionsArray(),
					// array with all errors in the crossword setup if the are some
					'errors'		=> $l_objCrossword->getErrorArray(),
					// array with the out of bound errors
					'errorsOOB'	=> $l_objCrossword->getErrorArrayOOB(),
					// flag if there are errors in the array above
					'isError'		=> $l_objCrossword->get_blnIsError(),
					// the texts for the local lang texts in the crossword
					'LLTexts' => $l_objCrossword->getLLTexts(),
					// the relativ path of this extension
		            'relPath' => PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('glcrossword')),
		           // the general size of the crossword
					'size'		=> array(
										'x'	=>	$l_objCrossword->get_WidthOfCrossword(),
										'y'	=>	$l_objCrossword->get_HeightOfCrossword()
										),
					// the border width of the crossword
					'borderWidth' => $l_objCrossword->get_intBorderWidth(),
					// the size of one box in the crossword
					'boxSize' => $l_objCrossword->getHtmlBoxSize(),
					// returns the edit matrix with the data for every box for editing it
					'editMatrix'	=> $l_objCrossword->getEditMatrix(),
					// return the edit causing question array
					'editCausingQuestions' => $l_objCrossword->getEditCausingQuestionArray() 
				);
		
		// returns the crossword data array
		return $l_arrResult;
	}
	
	/**
	 * Get the all data with the solution of the crossword
	 * 
	 * @param 	integer $i_intUniqueId 	The unique ID of the crossword.
	 * @param	array	$i_arrParams	Array with parameters for this method.
	 * @return 	array 					Array with all the solution data of the crossword.
	 */
	protected function getSolutionData($i_intUniqueId, $i_arrParams) {
		// the crossword object
		/* @var GlcrosswordCrossword $l_objCrossword */
		$l_objCrossword = NULL;
		// the array with the result of this request
		$l_arrResult = array();
		
		// get the Crossword with this ID
		$l_objCrossword = GlcrosswordCrossword::get_Crossword($i_intUniqueId);
		
		// create all data infostructures of the crossword for the frontend
		$l_arrResult = array(
					'solution' => $l_objCrossword->getSolutionData() 				
				);

		// returns the crossword data array
		return $l_arrResult;
	}

	/**
	 * Get an hint for ane answer box.
	 * 
	 * @param 	integer $i_intUniqueId The unique ID of the crossword.
	 * @param	array	$i_arrParams	Array with parameters for this method.
	 * @return 	array					Array with all the solution data of the crossword.
	 */
	protected function getHintData($i_intUniqueId, $i_arrParams) {
		// the crossword object
		/* @var GlcrosswordCrossword $l_objCrossword */
		$l_objCrossword = NULL;
		// the array with the result of this request
		$l_arrResult = array();
		
		// get the Crossword with this ID
		$l_objCrossword = GlcrosswordCrossword::get_Crossword($i_intUniqueId);
		
		// create all data infostructures of the crossword for the frontend
		$l_arrResult = array(
				'hint' => $l_objCrossword->getHintForAnswerBox($i_arrParams)
		);
		
		// returns the crossword data array
		return $l_arrResult;
	}
}
?>