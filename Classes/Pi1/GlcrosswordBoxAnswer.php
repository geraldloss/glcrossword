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
namespace Loss\Glcrossword\Pi1;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Loss\Glcrossword\Controller\GlcrosswordController;


/**
 * Class to define a box with the answer letters
 *
 * @author	Gerald Loﬂ <gerald.loss@gmx.de>
 * @package	glcrossword
*/
class GlcrosswordBoxAnswer extends GlcrosswordBox {
	
	/**
	 * Default index for the anserbox content object
	 * @var integer
	 */
	const C_INT_CONTENT_INDEX = 0;
	
	/**
	 * Key for question object in the array with the causing questions
	 * @var string
	 */
	const C_STR_KEY_CAUSING_QUESTION = 'question';
	
	/**
	 * Key for direction in the array with the causing questions
	 * @var unknown
	 */
	const C_STR_KEY_CAUSING_DIRECTION = 'direction';

	/**
	 * Extra Border on the left side. If we dont have another question, empty box or 
	 * crossword border at the end of an answer.
	 * 
	 * @var boolean
	 */
	protected $m_blnExtraBorderLeft = false;
	
	/**
	 * Extra Border on the top side. If we dont have another question, empty box or 
	 * crossword border at the end of an answer.
	 * 
	 * @var boolean
	 */
	protected $m_blnExtraBorderTop = false;
	
	/**
	 * Extra Border on the right side. If we dont have another question, empty box or 
	 * crossword border at the end of an answer.
	 * 
	 * @var boolean
	 */
	protected $m_blnExtraBorderRight = false;
	
		/**
	 * Extra Border on the bottom side. If we dont have another question, empty box or 
	 * crossword border at the end of an answer.
	 * 
	 * @var boolean
	 */
	protected $m_blnExtraBorderBottom = false;
	
/**
	 * Array with the queststions, which causes this answer letter.
	 * Every entry is one array with on object of type GlcrosswordBoxQuestions
	 * and the direction of the question. 
	 * First Index: Counter with all causing questions starting with 0.
	 * Value:
	 * 			First Index: "question" => The question object which caused this answer letter
	 * 			Second Index: "direction" => The direction of this causing question 
	 * @var array
	 */
	protected $m_arrCausingQuestions;

	/**
	 * Factory for an answer box object.
	 * @param integer 							$i_intX 			X position of the box in the crossword
	 * @param integer 					        $i_intY 			Y position of the box in the crossword
	 * @param GlcrosswordContentAnswerfield	    $i_objAnswerField	Object with the answer in this field
	 * @param GlcrosswordBoxQuestions			$i_objQuestionBox	Object with the questionbox, which causes this answer
	 * @param integer							$i_intDirection		Direction of the Question, which causes this answer
	 * @param GlcrosswordCrossword				$i_objCrossword		Object of the crossword class	 
	 * @return GlcrosswordBox 										The created answer box.
	 */
	public static function boxAnswerFactory($i_intX, $i_intY, $i_objAnswerField, $i_objQuestionBox, $i_intDirection, $i_objCrossword) {
		// new question object
		/* @var $l_objNewAnswerObject GlcrosswordBoxAnswer */ 
		$l_objNewAnswerObject = NULL;
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
			// create the answer box
			$l_objNewAnswerObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
			                                                    GlcrosswordBoxAnswer::class,
																$i_intX,
																$i_intY,
																$i_objAnswerField,
																$i_objQuestionBox,
																$i_intDirection,
																$i_objCrossword );
			// add the new box object
			$i_objCrossword->addBox($l_objNewAnswerObject);

			// return the current answer box object
			return $l_objNewAnswerObject;
				
				
		// if there exists already an object on this coordinates
		} else {
			// read the current type
			$l_strCurrentType = $l_objCurrentBox->get_strType();
				
			// if there is no type missmatch
			if ($l_strCurrentType == GlcrosswordBox::C_STR_TYPE_ANSWER) {
		
				// adds the answer letter, if there is already an different answer letter, then set the error text
				$l_objCurrentBox->addAnswerLetter($i_objAnswerField, $i_objQuestionBox, $i_intDirection);
				
				// return the current answer box object
				return $l_objCurrentBox;
				
			// if there is a type missmatch
			} else {
			    // read the current type
			    $l_strCurrentType = $l_objCurrentBox->get_strType();
			    
			    // get all informations of this object for the error message
				GlcrosswordBox::getCausingQuestionInformation($l_objCurrentBox, $l_strCurrentQuestionText, $l_intCurrentQuestionUID);
		
				// In this field is a box of type %s causing of question "%s" with UID %u
				// and a box of type %s causing of question "%s" with UID %u
				// at the same time.
				$l_strTempErrorText = LocalizationUtility::translate('code.error.box.type.missmatch',
				                                                     GlcrosswordController::c_strExtensionName );
				$l_strTempErrorText = sprintf($l_strTempErrorText,
											  $l_strCurrentType,
											  filter_var($l_strCurrentQuestionText,FILTER_SANITIZE_FULL_SPECIAL_CHARS),
											  $l_intCurrentQuestionUID,
											  GlcrosswordBox::C_STR_TYPE_ANSWER,
				                              filter_var($i_objQuestionBox->get_objQuestion($i_intDirection)->get_strQuestion(),FILTER_SANITIZE_FULL_SPECIAL_CHARS),
				                              $i_objQuestionBox->get_objQuestion($i_intDirection)->get_intUid() );
		
				// if the priority of this box is higher
				if (GlcrosswordBox::typeHasHigherPriority($l_strCurrentType, GlcrosswordBox::C_STR_TYPE_ANSWER)) {
					// create the answer box
					$l_objNewAnswerObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(	
					                                                    GlcrosswordBoxAnswer::class,
																		$i_intX,
																		$i_intY,
																		$i_objAnswerField,
																		$i_objQuestionBox,
																		$i_intDirection,
																		$i_objCrossword );
					// replace the old box object
					$i_objCrossword->addBox($l_objNewAnswerObject);
					
				}
				// read now the current box object
				$l_objCurrentBox = $i_objCrossword->getBox($i_intX, $i_intY);
				// set the error flag
				$l_objCurrentBox->set_blnIsError(true);
				// set the error text
				$l_objCurrentBox->set_strErrorText($l_strTempErrorText);
				
				// return the current answer box object
				return $l_objCurrentBox;
				
				
			} // if there is a type missmatch
		} // if there exists already an object on this coordinates
	}
	
	/**
	 * Constructor of this class
	 * @param integer 								$i_intX 			X position of the box in the crossword
	 * @param integer 								$i_intY 			Y position of the box in the crossword
	 * @param GlcrosswordContentAnswerfield	$i_objAnswerField	Object with the answer in this field
	 * @param GlcrosswordBoxQuestions			$i_objQuestionBox	Object with the questionbox, which causes this answer
	 * @param integer								$i_intDirection		Direction of the Question, which causes this answer
	 * @param GlcrosswordCrossword				$i_objCrossword		Object of crossword class
	 */
	public function __construct($i_intX, $i_intY, $i_objAnswerField, $i_objQuestionBox, $i_intDirection, $i_objCrossword) {
		parent::__construct($i_intX, $i_intY, GlcrosswordBox::C_STR_TYPE_ANSWER, $i_objCrossword);
		
		// initialise the array
		$this->m_arrCausingQuestions = array();
		
		// adds the answer letter, if there is already an different answer letter, then set the error text
		$this->addAnswerLetter($i_objAnswerField, $i_objQuestionBox, $i_intDirection);
	}
	
	/**
	 * Returns the all causing questions
	 * @return array: Array with with all cousing questions.
	 */
	public function getAllCausingQuestions() {
		return $this->m_arrCausingQuestions;
	}
	
	/**
	 * Returns the first causing question
	 * @return array: Array with an object of type GlcrosswordBoxQuestions and
	 * 				  the direction of the causing question.
	 */
	public function getFirstCausingQuestion() {
		return $this->m_arrCausingQuestions[0];
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
		$l_strContent = $this->getSingleBox($i_fltXScale, $i_fltYScale, $i_intBorderWidth, 'glcrossword_answer');
		
		$this->setExtraHtmlBorders($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_strContent);
		
		// get some more options for the content of this box and some other stuff
		$l_strContent .= $this->getContentHtmlOptions($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
		
		return $l_strContent;
	}	
	
	/**
	 * Getter for the Flag extra border on the left side.
	 * 
	 * @return boolean
	 */
	public function get_blnExtraBorderLeft() {
		return $this->m_blnExtraBorderLeft;
	}
	
	/**
	 * Setter for the Flag extra border on the left side.
	 * 
	 * @param boolean $i_blnValue
	 */
	public function set_blnExtraBorderLeft( $i_blnValue) {
		$this->m_blnExtraBorderLeft = $i_blnValue;
	}
	
	/**
	 * Getter for the Flag extra border on the top side.
	 * 
	 * @return boolean
	 */
	public function get_blnExtraBorderTop() {
		return $this->m_blnExtraBorderTop;
	}
	
	/**
	 * Setter for the Flag extra border on the top side.
	 * 
	 * @param boolean $i_blnValue
	 */
	public function set_blnExtraBorderTop( $i_blnValue) {
		$this->m_blnExtraBorderTop = $i_blnValue;
	}
	
	/**
	 * Getter for the Flag extra border on the rigth side.
	 * 
	 * @return boolean
	 */
	public function get_blnExtraBorderRight() {
		return $this->m_blnExtraBorderRight;
	}
	
	/**
	 * Setter for the Flag extra border on the right side.
	 * 
	 * @param boolean $i_blnValue
	 */
	public function set_blnExtraBorderRight( $i_blnValue) {
		$this->m_blnExtraBorderRight = $i_blnValue;
	}
	
	/**
	 * Getter for the Flag extra border on the bottom side.
	 * 
	 * @return boolean
	 */
	public function get_blnExtraBorderBottom() {
		return $this->m_blnExtraBorderBottom;
	}
	
	/**
	 * Setter for the Flag extra border on the bottom side.
	 * 
	 * @param boolean $i_blnValue
	 */
	public function set_blnExtraBorderBottom( $i_blnValue) {
		$this->m_blnExtraBorderBottom = $i_blnValue;
	}
	
	/**
	 * Set the extra border for the given question direction
	 * 
	 * @param integer $i_intQuestionDirection
	 */
	public function setExtraBorder($i_intQuestionDirection) {
		switch ($i_intQuestionDirection) {
			// if the question goes to the left
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_LEFT:
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT:
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP_LEFT:
				
				// set the left border
				$this->set_blnExtraBorderLeft(true);
				break;

			// if the question goes to the top 
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_TOP:
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP:
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_TOP:
							
				// set the top border
				$this->set_blnExtraBorderTop(true);
			 	break;

			// if the question goes to the right
		    case GlcrosswordBoxQuestions::C_INT_DIR_TOP_RIGHT:
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT:
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_RIGTH:
							
				// set the right border
				$this->set_blnExtraBorderRight(true);
				break;
		
			// if the question goes to the bottom
		    case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_DOWN:
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN:
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_DOWN:
							
				// set the bottom border
				$this->set_blnExtraBorderBottom(true);
				break;
		}
	}
	
	/**
	 * Get the flag of the border in the opposite direction.
	 * 
	 * @param integer $i_intQuestionDirection Direction of the question.
	 */
	public function getOppositeBorderFlag($i_intQuestionDirection) {
		switch ($i_intQuestionDirection) {
			// if the question goes to the left
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_LEFT:
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT:
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP_LEFT:
				
				// get the right border
				return $this->get_blnExtraBorderRight();

			// if the question goes to the top 
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_TOP:
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP:
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_TOP:
							
				// get the bottom border
				return $this->get_blnExtraBorderBottom();

			// if the question goes to the right
		    case GlcrosswordBoxQuestions::C_INT_DIR_TOP_RIGHT:
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT:
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_RIGTH:
							
				// set the left border
				return $this->get_blnExtraBorderLeft();
		
			// if the question goes to the bottom
		    case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_DOWN:
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN:
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_DOWN:
							
				// set the top border
				return $this->get_blnExtraBorderTop();
		}
	}
	
//	****************************************************************************************************************
//  Start of the Protected Section	
//	****************************************************************************************************************
	
	/**
	 * Try to add an answer letter to this box
	 * @param GlcrosswordContentAnswerfield	$i_objAnswerField	Object with the answer in this field
	 * @param GlcrosswordBoxQuestions			$i_objQuestionBox	Object with the questionbox, which causes this answer
	 * @param integer								$i_intDirection		Direction of the Question, which causes this answer
	 */
	protected function addAnswerLetter($i_objAnswerField, $i_objQuestionBox, $i_intDirection) {
		/* @var $l_objOldAnswerField GlcrosswordContentAnswerfield */
		$l_objOldAnswerField = NULL;
		/* @var $l_objCausingQuestion GlcrosswordBoxQuestions */ 
		$l_objCausingQuestion = NULL;
		$l_intCausingDirection = 0;
		// the error text
		$l_strErrorText = '';
		
		// if NOT already exist
		if ( $this->addContent(GlcrosswordBoxAnswer::C_INT_CONTENT_INDEX, $i_objAnswerField)) { 

			// add question, which causes this answer
			array_push(	$this->m_arrCausingQuestions,
						array( GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_QUESTION => $i_objQuestionBox,
						GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_DIRECTION => $i_intDirection ));
			
		// if already exist
		} else {
			// get old answer letter object
			$l_objOldAnswerField = $this->getContentObject(GlcrosswordBoxAnswer::C_INT_CONTENT_INDEX);
			// if it is the same answer letter
			if (   $l_objOldAnswerField->get_strAnswerLetter() == $i_objAnswerField->get_strAnswerLetter() 
				&& $l_objOldAnswerField->get_intLength() == $i_objAnswerField->get_intLength() ) {
				// add question, which causes this answer
				array_push(	$this->m_arrCausingQuestions,
							array( GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_QUESTION => $i_objQuestionBox,
							GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_DIRECTION => $i_intDirection ));
				
			// if it is a different answer letter
			} else {
				// set error text
				// There are two different letters in this field.\n
				// From question "%s" with UID %u comes letter "%s"
				// and from question "%s" with UID %u comes letter "%s"
			    $l_strErrorText = LocalizationUtility::translate('code.error.double.answerletter',
			                                                     GlcrosswordController::c_strExtensionName );
		
				// there must be at least one causing question in a case of error,
				// so we can use always the first question
				$l_objCausingQuestion = $this->m_arrCausingQuestions[0][GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_QUESTION];
				$l_intCausingDirection = $this->m_arrCausingQuestions[0][GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_DIRECTION];
		
				// insert the variables in the error text
				$l_strErrorText = sprintf($l_strErrorText,
												filter_var($l_objCausingQuestion->get_objQuestion($l_intCausingDirection)->get_strQuestion(),
														   FILTER_SANITIZE_FULL_SPECIAL_CHARS),
												$l_objCausingQuestion->get_objQuestion($l_intCausingDirection)->get_intUid(),
												$l_objOldAnswerField->get_strAnswerLetter(),
												filter_var($i_objQuestionBox->get_objQuestion($i_intDirection)->get_strQuestion(),
														   FILTER_SANITIZE_FULL_SPECIAL_CHARS),
												$i_objQuestionBox->get_objQuestion($i_intDirection)->get_intUid(),
												$i_objAnswerField->get_strAnswerLetter() );
				// set the error text
				$this->set_strErrorText($l_strErrorText);
				// set error flag true
				$this->set_blnIsError(true);
			} // if it is a different answer letter
		}// if already exist
	}

	/**
	 * Returns the HTML content of the content for this answer box  
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string						Returns the generated HTML content.
	 */
	protected function getContentHtmlOptions($i_fltXScale, $i_fltYScale, $i_intBorderWidth) {
		
		// the own content object with the answer
		/* @var $l_objContentAnswerfield GlcrosswordContentAnswerfield */
		$l_objContentAnswerfield = NULL;
		// returnung content
		$l_strContent = '';
		// the font size
		$l_intFontSize = 0;
		// the with and the height of the info box
		$l_intXInfoBoxWidth = 0;
		$l_intYInfoBoxWidth = 0;
		// the font size of the info box
		$l_intInfoBoxFontSize = 0;
		// size for the Table with the answer content
		$l_intXTableSize = 0;
		$l_intYTableSize = 0;
		// the caret id
		$l_strCaretId = '';
		// the size parameters of the caret
		$l_intCaretLeft = 0;
		$l_intCaretTop = 0;
		$l_intCaretWidth = 0;
		// the extra border width
		$l_intExtraBorderWidth = 0;
		
		
		// get the smaller scale option, this will determine the size of the font
		if ($i_fltXScale < $i_fltYScale) {
			$l_fltFontScale = $i_fltXScale;
		} else {
			$l_fltFontScale = $i_fltYScale;
		}
		
		// compute the font size
		$l_intFontSize = round( 36 * $l_fltFontScale );
		
		$l_objContentAnswerfield = $this->getContentObject(self::C_INT_CONTENT_INDEX); 
		
		// if this answer letter are 1 
		if ($l_objContentAnswerfield->get_intLength() <= 1) {

			$l_strContent = '">' . "\n";
			
		// if the answer is longer then 1 letters in this box
		} else {
			// then we have to deal with tables, because the vertical-align: middle; parameter 
			// dont works with div elements
			
			// get the font size in dependece of the letter count
			$l_intFontSize = $this->getFontSize($l_objContentAnswerfield->get_intLength(), $l_fltFontScale);
			// compute the X and Y size of the content table
			$l_intXTableSize = round((GlcrosswordBox::C_INT_BOX_SIZE - 1) * $i_fltXScale);
			$l_intYTableSize = round((GlcrosswordBox::C_INT_BOX_SIZE - 1) * $i_fltYScale);
			
			// if we have an extra border at the bottom
			if ($this->get_blnExtraBorderBottom() == true) {
				// we need to shorten the table a little bit
				$l_intExtraBorderWidth = $i_intBorderWidth * 3;
				$l_intYTableSize -= $l_intExtraBorderWidth;
			}
			
			// close the open div element
			$l_strContent = '" >' . "\n";
			$l_strContent .= "\t\t\t\t" . '<table class="glcrossword_answer_text" style="width: ' . $l_intXTableSize . 
							 'px; height: ' . $l_intYTableSize . 'px;">' . "\n";
            $l_strContent .= "\t\t\t\t\t" . '<tr><td class="glcrossword_answer_text" id="content' . $this->m_intX . 'x' . 
            										$this->m_intY . '_' . $this->m_objCrossword->get_uniqueId() . 
            										'" style="font-size: ' . $l_intFontSize . 'px; padding: 0px;">' . "\n";
  	        $l_strContent .= "\t\t\t\t\t" . '</td></tr>' . "\n";
            $l_strContent .= "\t\t\t\t" . '</table>' . "\n";
		}
		
		// if there is 1 letter for this box
		if ($l_objContentAnswerfield->get_intLength() == 1) {
			// append the content area for this kind of boxes
			$l_strContent .= "\t\t\t" . '<div id="content' . $this->m_intX . 'x' . $this->m_intY . '_' . 
				$this->m_objCrossword->get_uniqueId() .
				'" style="font-size: ' . $l_intFontSize . 'px;"></div>' . "\n";
		}
		
		// if there are more then one letter in this box
		if ($l_objContentAnswerfield->get_intLength() > 1) {
			// the with and the height of the info box 
			$l_intXInfoBoxWidth = round($i_fltXScale * 10);
			$l_intYInfoBoxWidth = round($i_fltYScale * 10);
			// the font size of the info box
			$l_intInfoBoxFontSize = round(10 * $l_fltFontScale);
				
			// insert an info box with the number of letters in the corner
			$l_strContent .= "\t\t\t" . '<div class="glcrossword_letter_count" style="width: ' . $l_intXInfoBoxWidth . 
							 'px; height: ' . $l_intYInfoBoxWidth . 'px; font-size:' . $l_intInfoBoxFontSize . 'px;">' . 
							  $l_objContentAnswerfield->get_intLength() . '</div>' . "\n";
		}

		// parameter for the invisible caret
		$l_strCaretId = 'caret' . $this->m_intX . 'x' . $this->m_intY . '_' . $this->m_objCrossword->get_uniqueId();
		$l_intCaretLeft = round($i_fltXScale * 5);
		$l_intCaretTop = round($i_fltYScale * 54);
		$l_intCaretWidth = round($i_fltXScale * 50);
		
		//always insert an invisible caret
		$l_strContent .= "\t\t\t" . '<div id="' . $l_strCaretId . '" class="glcrossword_caret_inactive" '; 
		$l_strContent .= 'style="left: ' . $l_intCaretLeft . 'px; top: ' . $l_intCaretTop . 'px; ';
		$l_strContent .= 'width: ' . $l_intCaretWidth . 'px; height: 0px; border-width: 1px"></div>';
		
		// at the end the closing div
		$l_strContent .= "\t\t\t" . '</div>' . "\n";
		
		return $l_strContent;
	}
	
	/**
	 * Set the extra borders if ther is the flag is set.
	 * 
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @param string 	$c_strContent		HTML content, which needs to be changed for extra borders.
	 */
	protected function setExtraHtmlBorders($i_fltXScale, $i_fltYScale, $i_intBorderWidth, &$c_strContent) {
		
		// the width of the extra border
		$l_intExtraBorderWidth = 0;
		// the coordinates from the top
		$l_intTop = 0;
		// the width of the box
		$l_intWidth = 0;
		// the height of the box
		$l_intHeight = 0;
		// coordinates from the left side.
		$l_intLeft = 0;
		
		// compute the extra border width
		$l_intExtraBorderWidth = $i_intBorderWidth * 3;
		
		// get the box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
		
		
		// if we have an extra border at the left side
		if ($this->get_blnExtraBorderLeft() == true) {
			$c_strContent .= ' border-bottom-width: ' . $l_intExtraBorderWidth . 'px;';
		}

		// if we have an extra border at the top side
		if ($this->get_blnExtraBorderTop() == true) {
			$c_strContent .= ' border-top-width: ' . $l_intExtraBorderWidth . 'px;';
		}
	
		// if we have an extra border at the right side
		if ($this->get_blnExtraBorderRight() == true) {
			$c_strContent .= ' border-right-width: ' . $l_intExtraBorderWidth . 'px;';
		}

		// if we have an extra border at the bottom side
		if ($this->get_blnExtraBorderBottom() == true) {
			$c_strContent .= 'border-bottom-width: ' . $l_intExtraBorderWidth . 'px;';
		}
	}

	
	/**
	 * Returns the font size in dependence of the number of letters
	 * @param integer 	$i_intLetterCount	Number of letters in this box.
	 * @param float 	$i_fltFontScale		Scale factor of the font size.
	 * @return integer						The font size.
	 */
	protected function getFontSize($i_intLetterCount, $i_fltFontScale) {
		
		$l_intFontSize = 0;
		
		// set the font size in dependence of the letter count
		switch ($i_intLetterCount){
			case 2:
				$l_intFontSize = 28;
				break;
			case 3:
				$l_intFontSize = 17;
				break;
			case 4:
				$l_intFontSize = 14;
				break;
			case 5:
				$l_intFontSize = 12;
				break;
			case 6:
				$l_intFontSize = 10;
				break;
			case 7:
				$l_intFontSize = 9;
				break;
			case 8:
				$l_intFontSize = 8;
				break;
			case 9:
				$l_intFontSize = 7;
				break;
		}
		
		// return the font size with the scale factor
		return round($l_intFontSize * $i_fltFontScale);
	}
}
?>