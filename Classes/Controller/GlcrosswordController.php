<?php

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2019 Gerald Loß <gerald.loss@gmx.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

namespace Loss\Glcrossword\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Loss\Glcrossword\Pi1\GlcrosswordData;
use Loss\Glcrossword\Pi1\GlcrosswordCrossword;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 *
 *
 * @package glcrossword
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class GlcrosswordController extends ActionController {
									 
	//*****************************************************************************
	// The constants of this class
	//*****************************************************************************
	/**
	 * The vendor of this extension
	 * @var \string
	 */
	const c_strVendor = 'loss';
	
	/**
	 * The name of this extension
	 * @var \string
	 */
	const c_strExtensionName = 'glcrossword';
	
	/**
	 * The Plugin Name
	 * @var \string
	 */
	const c_strPluginName = 'pi1';
	
	/**
	 * The session name of the Crossword game. 
	 * @var string
	 */
	const c_strMorphQuizSessionName = 'glcrossword_session';

	/**
	 * Database table with the questions.
	 * @access protected
	 * @var string
	 */
	const C_STR_DB_TABLE_QUESTIONS = 'tx_glcrossword_questions';
	
	//*****************************************************************************
	// The static members of this class
	//*****************************************************************************
	

	//*****************************************************************************
	// The member attributes of this class
	//*****************************************************************************
	

	/**
	 * All actions which we need to perform before avery other action
	 * @see ActionController::initializeAction()
	 */
	protected function initializeAction() {
	    // path to the css file
	    $l_strPathCss = '';
	    // the header content
	    $l_strHeaderContent = '';
	    // get the UID of the crossword
	    $l_intUniqueId = $this->configurationManager->getContentObject()->data['uid'];
	    
	    // read the alternate CSS file
	    $l_strAlternateCssFile = $this->settings['cssFile'];
	    
	    // if an alternative CSS file is given
	    if ($l_strAlternateCssFile != '') {
	        // if the alternative CSS file starts with /
	        if (substr($l_strAlternateCssFile, 0, 1) == '/') {
	            $l_strPathCss = 'fileadmin' . $l_strAlternateCssFile;
	        }
	        // if the alternative CSS file starts without /
	        else {
	            $l_strPathCss = 'fileadmin/' . $l_strAlternateCssFile;
	        }
	    }
	    // if no alternative CSS file is given
	    else {
	        $l_strPathCss = PathUtility::getAbsoluteWebPath(GeneralUtility::getFileAbsFileName(
	            'EXT:glcrossword' . '/Resources/Public/css/glcrossword.css' ));
	    }
	    
	    
	    // if the link to crossword css not already exist
	    if (!$this->existAdditionalHeaderData($this->response->getAdditionalHeaderData(), $l_strPathCss)) {
	        // set the path to the css file of this extension
	        $l_strPathCss = '<link href="' . $l_strPathCss .  '" rel="stylesheet" type="text/css" />';
	        
	        
	    }
	    
	    $l_strHeaderContent = '<!-- Start of header files of extension glcrossword with ID ' . $l_intUniqueId . ' -->';
	    
	    // inser CSS File
	    $l_strHeaderContent .= "\n" . $l_strPathCss;
	    
	    // insert the start of the main function
	    $l_strHeaderContent .= "\n" . '<script type="text/javascript">' . "\n";
	    $l_strHeaderContent .= "\n\t" . '// init header script for crossword "' . $this->settings['titleOfCrossword'] . '" with ID ' . $l_intUniqueId;
	    $l_strHeaderContent .= "\n\t" .
	   	    '// if this is the first crossword
			if (arrGlcrosswordIds == null) {
		        // create an array with the crossword ID
				var arrGlcrosswordIds = new Array("' . $l_intUniqueId . '");
				    
			// if this is not the first crossword
			} else {
					// add this ID to the current array
					arrGlcrosswordIds.push( "' . $l_intUniqueId . '" );
			}';
	    
	    $l_strHeaderContent .= "\n" . '</script>' . "\n\n";
	
	    $this->response->addAdditionalHeaderData($l_strHeaderContent);
	}
	
	
	/**
	 * action list
	 * 
	 * @return void
	 */
	public function mainAction() {
		// the morphing quiz game
		/* @var GlcrosswordData $l_objCrosswordData */
		$l_objCrosswordData = NULL;
		
		// the height and the width of the crossword
		$l_intHeightOfCrossword = 0;
		$l_intWidthOfCrossword = 0;
		// the UIDs of the related questions
		$l_strRelatedQuestions = ''.
		// the X scale of the crossword size
		$l_fltXScale = 0;
		// the Y scale of the crossword size
		$l_fltYScale = 0;
		// the borderwidth of the crossword
		$l_intBorderWidth = 0;
		// the unique ID of this content element
		$l_intUniqueId = $this->configurationManager->getContentObject()->data['uid'];
		// title of the crossword
		$l_strCrosswordTitle = '';
		
		// read the title of the crossword
		$l_strCrosswordTitle = $this->settings['titleOfCrossword'];
		// read the width of the crossword
		$l_intWidthOfCrossword = $this->settings['widthOfCrossword'];
		// read the height of the crossword
		$l_intHeightOfCrossword = $this->settings['heightOfCrossword'];
		
		// read the PIDs of the related questions
		$l_strRelatedQuestions = $this->settings['relatetQuestions'];
		
		// read the scale factor for the width of the crossword
		$l_fltXScale = $this->settings['scaleXFactor'];
		// read the scale factor for the height of the crossword
		$l_fltYScale = $this->settings['scaleYFactor'];
		// read the border width of the crossword boxes
		$l_intBorderWidth = $this->settings['borderWidth'];
		
		// create a crossword object
		$this->m_objCrossword = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( GlcrosswordCrossword::class,
		    $l_strCrosswordTitle,
		    $l_intUniqueId,
		    $l_intHeightOfCrossword,
		    $l_intWidthOfCrossword,
		    $l_fltXScale,
		    $l_fltYScale,
		    $l_intBorderWidth,
		    $l_strRelatedQuestions );
		
		$l_objCrosswordData = GeneralUtility::makeInstance(GlcrosswordData::class);
		
		// return content
		$l_objCrosswordData->setHtmlContent($this->m_objCrossword->draw());
	
		// write the array with all crosswords back into the session, for later access over the ajax connection
		$GLOBALS['TSFE']->fe_user->setAndSaveSessionData('glcrossword_arrCrosswords', GlcrosswordCrossword::$m_arrCrosswords);
	
		// assign the data to the view
		$this->view->assign('crossworddata', $l_objCrosswordData);
	}

	/**
	 * Check if a special value already exists in the additional header data
	 * @param 	array 	$i_arrAdditionalHeaderData	The array with all additional header datas
	 * @param 	string 	$i_strValue					The value vor which we should search
	 * @return	boolean								True if we have found the value
	 */
	protected function existAdditionalHeaderData($i_arrAdditionalHeaderData, $i_strValue) {
	    // one line in the header data
	    $l_strHeaderLine = '';
	    // the returning value
	    $l_blnReturn = FALSE;
	    
	    // go through every line of the additional header data
	    foreach ($i_arrAdditionalHeaderData as $l_strHeaderLine) {
	        if (strpos($l_strHeaderLine, $i_strValue) == TRUE) {
	            $l_blnReturn = TRUE;
	            break 1;
	        }
	    }
	    
	    // return the result
	    return $l_blnReturn;
	}
}
?>