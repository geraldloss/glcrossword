<?php
/***************************************************************
 *  Copyright notice
*
*  (c) 2013 Gerald Loß <gerald.loss@gmx.de>
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
namespace Loss\Glcrossword\Pi1;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Loss\Glcrossword\Controller\GlcrosswordController;

/**
 * Class to define an empty box
 *
 * @author	Gerald Loß <gerald.loss@gmx.de>
 * @package	glcrossword
 */
class GlcrosswordBoxEmpty extends GlcrosswordBox {
	

	/**
	 * Factory for an empty box object.
	 * @param integer 								$i_intX 			X position of the box in the crossword
	 * @param integer 								$i_intY 			Y position of the box in the crossword
	 * @param GlcrosswordCrossword				    $i_objCrossword		The main crossword class	 
	 * @return GlcrosswordBox 										    The created empty box.
	 */
	public static function boxEmptyFactory($i_intX, $i_intY, $i_objCrossword) {
		// new empty object
		/* @var $l_objNewEmptyObject GlcrosswordBoxEmpty */ 
		$l_objNewEmptyObject = NULL;
		// current box object if there already exist one
		/* @var $l_objCurrentBox GlcrosswordBox */ 
		$l_objCurrentBox = NULL;
		// current type of this box
		$l_strCurrentType = '';
		// the current question text
		$l_strCurrentQuestionText = '';
		// temporary error text
		$l_strTempErrorText = '';
		// the current question UID
		$l_intCurrentQuestionUID = 0;
		
		// read the current box
		$l_objCurrentBox = $i_objCrossword->getBox($i_intX, $i_intY);
		
		// if there exists no object on this coordinates
		if (! isset($l_objCurrentBox)) {
			// create the empty box
			$l_objNewEmptyObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 
			                                                 GlcrosswordBoxEmpty::class,
															 $i_intX,
															 $i_intY,
															 $i_objCrossword );
			// add the new box object
			$i_objCrossword->addBox($l_objNewEmptyObject);
			
			// return the new empty box object
			return $l_objNewEmptyObject;
				
		// if there exists already an object on this coordinates
		} else {
			// read the current type
			$l_strCurrentType = $l_objCurrentBox->get_strType();
				
			// if there is no type missmatch
			if ($l_strCurrentType == GlcrosswordBox::C_STR_TYPE_EMPTY) {
				
				// read the current empty box object
				$l_objCurrentBox = $i_objCrossword->getBox($i_intX, $i_intY);
				// return the current empty box object
				return $l_objCurrentBox;
			
			// if there is a type missmatch
			} else {
		
		
				// In this field is a box of type %s causing of question "%s" with UID %u
				// and a box of type %s causing of question "%s" with UID %u
				// at the same time.
				$l_strTempErrorText = LocalizationUtility::translate('code.error.box.type.missmatch',
				                                                     GlcrosswordController::c_strExtensionName );
				$l_strTempErrorText = sprintf($l_strTempErrorText,
											  $l_strCurrentType,
											  filter_var($l_strCurrentQuestionText,FILTER_SANITIZE_FULL_SPECIAL_CHARS),
											  $l_intCurrentQuestionUID,
											  GlcrosswordBox::C_STR_TYPE_EMPTY,
											  '',
											  0 );
		
				// if the priority of this box is higher
				if (GlcrosswordBox::typeHasHigherPriority($l_strCurrentType, GlcrosswordBox::C_STR_TYPE_EMPTY)) {
					// create the empty box
					$l_objNewEmptyObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 
					                                                 GlcrosswordBoxEmpty::class,
																	 $i_intX,
																	 $i_intY,
																	 $i_objCrossword );
					// replace the old box object
					$i_objCrossword->addBox($l_objNewEmptyObject);
				}
				
				// read now the current box object
				$l_objCurrentBox = $i_objCrossword->getBox($i_intX, $i_intY);
				// set the error flag
				$l_objCurrentBox->set_blnIsError(true);
				// set the error text
				$l_objCurrentBox->set_strErrorText($l_strTempErrorText);
				
				// return the current empty box object
				return $l_objCurrentBox;

			} // if there is a type missmatch
		} // if there exists already an object on this coordinates
	}
	
	/**
	 * Constructor of this empty box class
	 * @param integer 								$i_intX 			X position of the box in the crossword
	 * @param integer 								$i_intY 			Y position of the box in the crossword
	 * @param GlcrosswordCrossword				    $i_objCrossword		The main crossword class	 
	 */
	public function __construct($i_intX, $i_intY, $i_objCrossword ) {
	    parent::__construct($i_intX, $i_intY, GlcrosswordBox::C_STR_TYPE_EMPTY, $i_objCrossword);
	}

	/**
	 * Draws a  answer box in HTML.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string						Returns the generated HTML content.
	 */
	public function draw( $i_fltXScale, $i_fltYScale, $i_intBorderWidth) {
		// the HTML content of the qestion box
		$l_strContent = '';
	
		// get the plain box
		$l_strContent = $this->getSingleBox($i_fltXScale, $i_fltYScale, $i_intBorderWidth, 'glcrossword_empty', FALSE, FALSE);
	
		return $l_strContent;
	}
}
?>