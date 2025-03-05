<?php
declare(strict_types=1);
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
 * Class to define a box with one or more questions
 *
 * @author	Gerald Loß <gerald.loss@gmx.de>
 * @package	glcrossword
 */
class GlcrosswordBoxQuestions extends GlcrosswordBox {
	
	// Constants for the direction of the question
	const C_INT_DIR_TOP_LEFT = 0;
	const C_INT_DIR_TOP = 1;
	const C_INT_DIR_TOP_RIGHT = 2;
	const C_INT_DIR_RIGHT_TOP = 3;
	const C_INT_DIR_RIGHT = 4;
	const C_INT_DIR_RIGHT_DOWN = 5;
	const C_INT_DIR_DOWN_RIGTH = 6;
	const C_INT_DIR_DOWN = 7;
	const C_INT_DIR_DOWN_LEFT = 8;
	const C_INT_DIR_LEFT_DOWN = 9;
	const C_INT_DIR_LEFT = 10;
	const C_INT_DIR_LEFT_TOP = 11;
	
	/**
	 * Max value of the direction constants.
	 * @var integer
	 */
	const C_INT_DIR_MAX = 11;
	
	/**
	 * Factory of an box question object. 
	 * @param integer 		$i_intX 			X position of the box in the crossword
	 * @param integer 		$i_intY 			Y position of the box in the crossword
	 * @param integer 		$i_intUID 			UID of the question in the database
	 * @param integer 		$i_intDirection 	Direction of the question
	 * @param string  		$i_strQuestion 		Question text
	 * @param string  		$i_strAnswer 		Answer text
	 * @param string 		$i_strEditMask		Edit mask for the answer text
	 * @param GlcrosswordCrossword	$i_objCrossword		The main crossword class 
	 * @return GlcrosswordBox The created box.
	 */
	public static function boxQuestionFactory(int $i_intX, int $i_intY, int $i_intUID, int $i_intDirection, 
											  string $i_strQuestion, string $i_strAnswer, string $i_strEditMask, GlcrosswordCrossword $i_objCrossword): GlcrosswordBox {
		// new question object
		/* @var $l_objNewQuestionObject GlcrosswordBoxQuestions */
		$l_objNewQuestionObject = null;
		// current box object if there already exists one
		/* @var $l_objCurrentBox GlcrosswordBox */
		$l_objCurrentBox = null;
		// current type of this box
		$l_strCurrentType = '';
		// the current question text
		$l_strCurrentQuestionText = '';
		// temporary error text
		$l_strTempErrorText = '';
		// the current question UID
		$l_intCurrentQuestionUID = 0;
		// the Causing Question of a Box
		/* @var GlcrosswordBoxQuestions $l_objCausingQuestion */
		$l_objCausingQuestion = null;
		// the direction of the Causing Question
		$l_intCausingDirection = 0;
		
		// read the box of these coordinates
		$l_objCurrentBox = $i_objCrossword->getBox($i_intX, $i_intY);
		
		// if there exists no object on these coordinates
		if (!isset($l_objCurrentBox)) {
			// create it
			$l_objNewQuestionObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(GlcrosswordBoxQuestions::class,
					$i_intX,
					$i_intY,
					$i_intUID,
					$i_intDirection,
					$i_strQuestion,
					$i_strAnswer,
					$i_strEditMask,
					$i_objCrossword );
			// add the new box object
			$i_objCrossword->addBox($l_objNewQuestionObject);
			
			// return the new question object
			return $l_objNewQuestionObject;
				
			
		// if there exists already an object on these coordinates
		} else {
			// read the current type
			$l_strCurrentType = $l_objCurrentBox->get_strType();
			
			// if there is no type missmatch
			if ($l_strCurrentType == GlcrosswordBox::C_STR_TYPE_QUESTION) {
				
				// add the new question to this box
				$l_objCurrentBox->addQuestion($i_intUID, $i_intDirection, $i_strQuestion, $i_strAnswer, $i_strEditMask);
				
				// return the current question object
				return $l_objCurrentBox;
				
				// if there is a type missmatch
			} else {
				// get all informations of this object for the error message
				GlcrosswordBox::getCausingQuestionInformation($l_objCurrentBox, $l_strCurrentQuestionText, $l_intCurrentQuestionUID,
				                                              $l_intActualLength, $l_objCausingQuestion, $l_intCausingDirection );
				
				// In this field is a box of type %s causing of question "%s" with UID %u
				// and a box of type %s causing of question "%s" with UID %u
				// at the same time.
				$l_strTempErrorText = LocalizationUtility::translate('code.error.box.type.mismatch',
				                                                     GlcrosswordController::c_strExtensionName );
				
				$l_strTempErrorText = sprintf($l_strTempErrorText,
											 $l_strCurrentType,
                         				     filter_var($l_objCausingQuestion
                                				          ->get_objQuestion($l_intCausingDirection)
                                				          ->get_strQuestion()
                                				        ,FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                        				     $l_intCurrentQuestionUID,
											 GlcrosswordBox::C_STR_TYPE_QUESTION,
											 filter_var($i_strQuestion,FILTER_SANITIZE_FULL_SPECIAL_CHARS),
											 $i_intUID );
				
				// if the priority of this box is higher
				if ( GlcrosswordBox::typeHasHigherPriority( $l_strCurrentType, GlcrosswordBox::C_STR_TYPE_QUESTION)) {
					$l_objNewQuestionObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
					        GlcrosswordBoxQuestions::class,
							$i_intX,
							$i_intY,
							$i_intUID,
							$i_intDirection,
							$i_strQuestion,
							$i_strAnswer,
							$i_strEditMask,
							$i_objCrossword );
					// replace the old box object
					$i_objCrossword->addBox($l_objNewQuestionObject);
				}
				// read now the current box object
				$l_objCurrentBox = $i_objCrossword->getBox($i_intX, $i_intY);
				// set the error flag
				$l_objCurrentBox->set_blnIsError(true);
				// set the error text
				$l_objCurrentBox->set_strErrorText($l_strTempErrorText);
				
				// return the current question object
				return $l_objCurrentBox;
			} // if there is a type missmatch
		} // if there exists already an object on these coordinates
		
	}
	
	/**
	 * Constructor of this class
	 * @param integer 		       $i_intX 			    X position of the box in the crossword
	 * @param integer 		       $i_intY 			    Y position of the box in the crossword
	 * @param integer 		       $i_intUID 			UID of the question in the database
	 * @param integer 		       $i_intDirection 	    Direction of the question
	 * @param string  		       $i_strQuestion 		Question text
	 * @param string  		       $i_strAnswer 		Answer text
	 * @param string 		       $i_strEditMask		Edit mask for the answer text
	 * @param GlcrosswordCrossword $i_objCrossword		The main crossword class	 
	 * @param integer		       $i_intUniqueId		The unique ID of the crossword
	 */
	public function __construct(int $i_intX, int $i_intY, int $i_intUID, int $i_intDirection, string $i_strQuestion, string $i_strAnswer, string $i_strEditMask, GlcrosswordCrossword $i_objCrossword) {
		parent::__construct($i_intX, $i_intY, GlcrosswordBox::C_STR_TYPE_QUESTION, $i_objCrossword);
		
		// add the first question to this box
		$this->addQuestion($i_intUID, $i_intDirection, $i_strQuestion, $i_strAnswer, $i_strEditMask);
	}
	
	/**
	 * Adds a question to this box. 
	 * @param integer $i_intUid 		UID of the question in the database
	 * @param integer $i_intDirection 	Direction of the question
	 * @param string  $i_strQuestion 	Question text
	 * @param string  $i_strAnswer 		Answer text
	 * @param string  $i_strEditMask	Edit mask for the answer text
	 */
	public function addQuestion(int $i_intUid, int $i_intDirection, string $i_strQuestion, string $i_strAnswer, string $i_strEditMask): void {
		
		// the error text
		$l_strErrorText = '';
		
		// create question object
		$l_objQuestion = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
		                                          GlcrosswordContentQuestion::class,
												  $i_intUid,
												  $i_strQuestion, 	
												  $i_strAnswer,	
												  $i_strEditMask );
		
		// try to add the question with the direction as index
		// if already question with this direction exists
		if (!$this->addContent($i_intDirection, $l_objQuestion)) {
			// set error flag
			$this->set_blnIsError(true);
			// There are two questions in this box with the same direction.\n
		    // There is the question "%s" with the UID %u and there is the
		    // question "%s" with the UID %u.
			$l_strErrorText = LocalizationUtility::translate('code.error.double.question',
			                                                 GlcrosswordController::c_strExtensionName );
			$l_strErrorText = sprintf($l_strErrorText,
			                          filter_var($this->get_objQuestion($i_intDirection)->get_strQuestion(), FILTER_SANITIZE_FULL_SPECIAL_CHARS),
			                          $this->get_objQuestion($i_intDirection)->get_intUid(),
			                          filter_var($i_strQuestion, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
			                          $i_intUid );
			// set the error text
			$this->set_strErrorText($l_strErrorText);
		}
	}
	
	/**
	 * Getter for the question.
	 * @param integer $i_intDirection Direction of the readed answer.
	 * @return GlcrosswordContentQuestion
	 */
	public function get_objQuestion(int $i_intDirection) {
		return $this->getContentObject($i_intDirection);
	}
	
	/**
	 * Returns the first question in the question array.
	 * @param	integer	$e_intDirection					Direction of the first question.
	 * @return 			GlcrosswordContentQuestion	
	 */
	public function getFirstQuestion(&$e_intDirection = 0) {
		$l_objContentQuestion = null;
		for ($i = 0; $i <= self::C_INT_DIR_MAX; $i++) {
			$l_objContentQuestion = $this->get_objQuestion($i);
			if (isset($l_objContentQuestion)) {
				$e_intDirection = $i;
				break;
			}
		}
		return $l_objContentQuestion;
	}
	
	/**
	 * Get the last answer box of this question in the givven direction.
	 * 
	 * @param 	integer $i_intDirection		The direction of the question.
	 * @return	GlcrosswordBoxAnswer	The last answer box of this question.
	 */
	public function getLastAnswerBoxOfQuestion(int $i_intDirection) {
		
		$l_intAnswerLength = 0;
		
		// get the length of the answer
		$l_intAnswerLength = $this->get_objQuestion($i_intDirection)->get_intActualLength();
		
		// returns the last answer box of this question
		return $this->getBoxFromOffset($i_intDirection, $l_intAnswerLength);
	}
	
	/**
	 * Returns array with all questions of this question box.
	 * @return 	array	Array with all questions of this question box.
	 */
	public function getQuestionContentArray() {
		
		// array with the content objects
		$l_arrContent = array();
		// the current question content object
		/* @var $l_objContentQuestion GlcrosswordContentQuestion */
		$l_objContentQuestion = null; 
		
		// looking in every direction for a question
		for ($i = 0; $i <= self::C_INT_DIR_MAX; $i++) {
			$l_objContentQuestion = $this->get_objQuestion($i);
			if (isset($l_objContentQuestion)) {
				// insert the question text into the array
				$l_arrContent[$i] = $l_objContentQuestion->get_strQuestion();
			
			// else set an empty entry into the array
			} else {
				$l_arrContent[$i] = null;
			}
		}
		
		return $l_arrContent;
	}
	
	/**
	 * Draws a question box in HTML
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string						Returns the generated HTML content.
	 */
	public function draw(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth): string{
		// the HTML content of the qestion box
		$l_strContent = '';
		
		// get the plain box
		$l_strContent = $this->getSingleBox($i_fltXScale, $i_fltYScale, $i_intBorderWidth, 'glcrossword_question', false, false);
		
		return $l_strContent;
	}
	
	
	/**
	 * Draws the arrows of the Question Box. 
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string						Returns the generated HTML content.
	 */
	public function draw_arrows(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth): string {
		// draw the arrows for the questions
		$l_strContent = $this->getQuestionsArrows($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
		
		return $l_strContent;
	}
	
	/**
	 * Get the arrows of the questions as HTML content.
	 * @param float $i_fltXScale Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float $i_fltYScale Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer $i_intBorderWidth Thickness of the borderlines of the box
	 * @return string The HTML content of the arrows.
	 */
	protected function getQuestionsArrows(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		
		// the HTML content
		$l_strContent = '';
		/* @var $l_objContentQuestion GlcrosswordContentQuestion */
		$l_objContentQuestion = null;
		
		for ($i = 0; $i <= self::C_INT_DIR_MAX; $i++) {
			
			// read the content object
			$l_objContentQuestion = $this->getContentObject($i); 
			
			// if the question of direction $i exists
			if (isset($l_objContentQuestion)) {
				// get the HTML Content of the arrow of this direction
				$l_strContent .= $this->getArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $i);
			}
		}
		
		return $l_strContent;
	}
	
	/**
	 * Get the arrow of a certain direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @param integer 	$i_intDirection 	Direction of the arrow.
	 * @return string 						The HTML content of the arrows.
	 */
	protected function getArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth, int $i_intDirection) {
		// the HTML content
		$l_strContent = '';
		
		switch ($i_intDirection) {
			// if direction top to the left
			case self::C_INT_DIR_TOP_LEFT:
				// get the HTML content for a top to the left arrow
				$l_strContent = $this->getTopLeftArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
			
			// if direction top 
			case self::C_INT_DIR_TOP:
				// get the HTML content for a top arrow
				$l_strContent = $this->getTopArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;

			// if direction top to the right
			case self::C_INT_DIR_TOP_RIGHT:
				// get the HTML content for a top to the right arrow
				$l_strContent = $this->getTopRightArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
				
			// if direction right to the top
			case self::C_INT_DIR_RIGHT_TOP:
				// get the HTML content for a right to the top arrow
				$l_strContent = $this->getRightTopArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
					
				// if direction right
			case self::C_INT_DIR_RIGHT:
				// get the HTML content for right arrow
				$l_strContent = $this->getRightArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
			
			// if direction right to the bottom
			case self::C_INT_DIR_RIGHT_DOWN:
				// get the HTML content for a right to the bottom arrow
				$l_strContent = $this->getRightBottomArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
				
			// if direction down to the right
			case self::C_INT_DIR_DOWN_RIGTH:
				// get the HTML content for a down to the right arrow
				$l_strContent = $this->getDownRightArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
					
			// if direction down
			case self::C_INT_DIR_DOWN:
				// get the HTML content for down arrow
				$l_strContent = $this->getDownArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
			
			// if direction down to left
			case self::C_INT_DIR_DOWN_LEFT:
				// get the HTML content for a down to the left arrow
				$l_strContent = $this->getDownLeftArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
				
			// if direction left to the bottom
			case self::C_INT_DIR_LEFT_DOWN:
				// get the HTML content for a left to the bottom arrow
				$l_strContent = $this->getLeftBottomArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
					
			// if direction left
			case self::C_INT_DIR_LEFT:
				// get the HTML content for left arrow
				$l_strContent = $this->getLeftArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
			
			// if direction left to the top
			case self::C_INT_DIR_LEFT_TOP:
				// get the HTML content for a left to the top arrow
				$l_strContent = $this->getLeftTopArrow($i_fltXScale, $i_fltYScale, $i_intBorderWidth);
				break;
				
		}
		return $l_strContent;
	}
	
	/**
	 * Get the arrow of the left to the bottom direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getLeftBottomArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow left to the bottom -->' . "\n";
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow line properties
		$l_intLeft = (int)($l_intLeft - round(10 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(40 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(5 * $i_fltYScale);
	
		// set the arrow line HTML content
		$l_strContent .= $this->getHtmlLineOfArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
				'glcrossword_line_left_top glcrossword_line_left_top_default', GlcrosswordBoxQuestions::C_INT_DIR_LEFT_DOWN);
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft - round(20 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(45 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlDownArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
												 GlcrosswordBoxQuestions::C_INT_DIR_LEFT_DOWN);
	
		return $l_strContent;
	}
	
	/**
	 * Get the arrow of the left direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getLeftArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow left -->' . "\n";
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft - round(10 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(20 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlLeftArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
												 GlcrosswordBoxQuestions::C_INT_DIR_LEFT);
	
		return $l_strContent;
	}

	/**
	 * Get the arrow of the left to the top direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getLeftTopArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow left to the top -->' . "\n";
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow line properties
		$l_intLeft = (int)($l_intLeft - round(10 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(15 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(5 * $i_fltYScale);
	
		// set the arrow line HTML content
		$l_strContent .= $this->getHtmlLineOfArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
				'glcrossword_line_left_bottom glcrossword_line_left_bottom_default', GlcrosswordBoxQuestions::C_INT_DIR_LEFT_TOP);
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft - round(20 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(5 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlTopArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize, GlcrosswordBoxQuestions::C_INT_DIR_LEFT_TOP);
	
		return $l_strContent;
	}

	/**
	 * Get the arrow of the down to the right direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getDownRightArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow down to the right -->' . "\n";
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow line properties
		$l_intLeft = (int)($l_intLeft + round(40 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(60 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(5 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow line HTML content
		$l_strContent .= $this->getHtmlLineOfArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize, 
												   'glcrossword_line_left_bottom glcrossword_line_left_bottom_default', 
												   GlcrosswordBoxQuestions::C_INT_DIR_DOWN_RIGTH);
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft + round(45 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(61 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlRightArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
												  GlcrosswordBoxQuestions::C_INT_DIR_DOWN_RIGTH);
	
		return $l_strContent;
	}
	
	/**
	 * Get the down arrow.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getDownArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow down -->' . "\n";
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft + round(20 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(60 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlDownArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
												 GlcrosswordBoxQuestions::C_INT_DIR_DOWN);
	
		return $l_strContent;
	}
	
	/**
	 * Get the arrow of the down to the left direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getDownLeftArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow down to the left -->' . "\n";
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow line properties
		$l_intLeft = (int)($l_intLeft + round(15 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(60 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(5 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow line HTML content
		$l_strContent .= $this->getHtmlLineOfArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize, 
												   'glcrossword_line_right_bottom glcrossword_line_right_bottom_default',
													GlcrosswordBoxQuestions::C_INT_DIR_DOWN_LEFT);
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft + round(5 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(61 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlLeftArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
						 GlcrosswordBoxQuestions::C_INT_DIR_DOWN_LEFT);
	
		return $l_strContent;
	}

	/**
	 * Get the arrow of the right to the top direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getRightTopArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow right to the top -->' . "\n";
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow line properties
		$l_intLeft = (int)($l_intLeft + round(60 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(15 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(5 * $i_fltYScale);
	
		// set the arrow line HTML content
		$l_strContent .= $this->getHtmlLineOfArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize, 
												   'glcrossword_line_right_bottom glcrossword_line_right_bottom_default',
													GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_TOP);
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft + round(60 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(5 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlTopArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize, GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_TOP);
	
		return $l_strContent;
	}
	
	/**
	 * Get the right arrow
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getRightArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow right -->' . "\n";
				
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
		
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft + round(60 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(20 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
		
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlRightArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
												  GlcrosswordBoxQuestions::C_INT_DIR_RIGHT);
		
		return $l_strContent;
	}
	
	/**
	 * Get the arrow of the right to the bottom direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getRightBottomArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow right to the bottom -->' . "\n";
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow line properties
		$l_intLeft = (int)($l_intLeft + round(60 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(40 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(5 * $i_fltYScale);
	
		// set the arrow line HTML content
		$l_strContent .= $this->getHtmlLineOfArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize, 
												   'glcrossword_line_right_top glcrossword_line_right_top_default',
													GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_DOWN);
	
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
	
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft + round(60 * $i_fltXScale));
		$l_intTop = (int)($l_intTop + round(45 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
	
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlDownArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
												 GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_DOWN);
	
		return $l_strContent;
	}
	
	/**
	 * Get the arrow of the top to the left direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getTopLeftArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow top to the left -->' . "\n";
		
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
		
		// compute the arrow line properties
		$l_intLeft = (int)($l_intLeft + round(15 * $i_fltXScale));
		$l_intTop = (int)($l_intTop - round(10 * $i_fltYScale)); 
		$l_intGeneralXSize = (int)round(5 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
		
		// set the arrow line HTML content
		$l_strContent .= $this->getHtmlLineOfArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize, 
												   'glcrossword_line_right_top glcrossword_line_right_top_default',
													GlcrosswordBoxQuestions::C_INT_DIR_TOP_LEFT);
		
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
		
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft + round(5 * $i_fltXScale));
		$l_intTop = (int)($l_intTop - round(20 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
		
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlLeftArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
												 GlcrosswordBoxQuestions::C_INT_DIR_TOP_LEFT);
		
		return $l_strContent;
	}
	
	/**
	 * Get the top arrow
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getTopArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow top -->' . "\n";
				
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
		
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft + round(20 * $i_fltXScale));
		$l_intTop = (int)($l_intTop - round(10 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
		
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlTopArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize, GlcrosswordBoxQuestions::C_INT_DIR_TOP);
		
		return $l_strContent;
	}
	
	/**
	 * Get the arrow of the top to the right direction.
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getTopRightArrow(float $i_fltXScale, float $i_fltYScale, int $i_intBorderWidth) {
		// the HTML content
		$l_strContent = '';
		// Box Properties
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
		
		$l_strContent = "\n\t\t\t" . '<!-- Cell ' . $this->m_intX . 'x' . $this->m_intY . ' arrow top to the right -->' . "\n";
		
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
		
		// compute the arrow line properties
		$l_intLeft = (int)($l_intLeft + round(40 * $i_fltXScale));
		$l_intTop = (int)($l_intTop - round(10 * $i_fltYScale)); 
		$l_intGeneralXSize = (int)round(5 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
		
		// set the arrow line HTML content
		$l_strContent .= $this->getHtmlLineOfArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize, 
												   'glcrossword_line_left_top glcrossword_line_left_top_default',
													GlcrosswordBoxQuestions::C_INT_DIR_TOP_RIGHT);
		
		// get the general box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
		
		// compute the arrow properties
		$l_intLeft = (int)($l_intLeft + round(45 * $i_fltXScale));
		$l_intTop = (int)($l_intTop - round(20 * $i_fltYScale));
		$l_intGeneralXSize = (int)round(10 * $i_fltXScale);
		$l_intGeneralYSize = (int)round(10 * $i_fltYScale);
		
		// set the arrow HTML content
		$l_strContent .= $this->getHtmlRightArrow($l_intLeft, $l_intTop, $l_intGeneralXSize, $l_intGeneralYSize,
												  GlcrosswordBoxQuestions::C_INT_DIR_TOP_RIGHT);
		
		return $l_strContent;
	}
	
	/**
	 * Get the plain HTML top arrow
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @param integer	$i_intDirection		Direction of the arrow
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getHtmlTopArrow(int $i_intLeft, int $i_intTop, int $i_intGeneralXSize, int $i_intGeneralYSize, int $i_intDirection) {
		// the HTML content
		$l_strContent = '';
		// create the id of this arrow
		$l_strBoxId = 'arrow' . $this->m_intX . 'x' . $this->m_intY . 'x' . $i_intDirection . 
						'_' . $this->m_objCrossword->get_uniqueId();
		
		// set the arrow HTML content
		$l_strContent = "\t\t\t" . '<div class="glcrossword_triangle" id= "' . $l_strBoxId . 
				'" style="left: ' . $i_intLeft . 'px; top: ' .
				$i_intTop . 'px; border-bottom: ' . $i_intGeneralYSize . 'px; border-right: ' . $i_intGeneralXSize .
				'px; border-left: ' . $i_intGeneralXSize . 'px; border-bottom-style: solid; border-bottom-color: ' .
				'black; border-right-style: solid; border-right-color: transparent; border-left-style: solid; ' .
				'border-left-color: transparent;"></div>' . "\n";
	
		return $l_strContent;
	}
	
	/**
	 * Get the plain HTML bottom arrow
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @param integer	$i_intDirection		Direction of the arrow
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getHtmlDownArrow(int $i_intLeft, int $i_intTop, int $i_intGeneralXSize, int $i_intGeneralYSize, int $i_intDirection) {
		// the HTML content
		$l_strContent = '';
		// create the id of this arrow
		$l_strBoxId = 'arrow' . $this->m_intX . 'x' . $this->m_intY . 'x' . $i_intDirection . 
						'_' . $this->m_objCrossword->get_uniqueId();
				
		// set the arrow HTML content
		$l_strContent = "\t\t\t" . '<div class="glcrossword_triangle" id= "' . $l_strBoxId . 
				'" style="left: ' . $i_intLeft . 'px; top: ' .
				$i_intTop . 'px; border-top: ' . $i_intGeneralYSize . 'px; border-right: ' . $i_intGeneralXSize .
				'px; border-left: ' . $i_intGeneralXSize . 'px; border-top-style: solid; border-top-color: ' .
				'black; border-right-style: solid; border-right-color: transparent; border-left-style: solid; ' .
				'border-left-color: transparent;"></div>' . "\n";
	
		return $l_strContent;
	}
	
	/**
	 * Get the plain HTML right arrow
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @param integer	$i_intDirection		Direction of the arrow
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getHtmlRightArrow(int $i_intLeft, int $i_intTop, int $i_intGeneralXSize, int $i_intGeneralYSize, int $i_intDirection) {
		// the HTML content
		$l_strContent = '';
		// create the id of this arrow
		$l_strBoxId = 'arrow' . $this->m_intX . 'x' . $this->m_intY . 'x' . $i_intDirection . 
						'_' . $this->m_objCrossword->get_uniqueId();
		
		// set the arrow HTML content
		$l_strContent = "\t\t\t" . '<div class="glcrossword_triangle" id= "' . $l_strBoxId . 
						'" style="left: ' . $i_intLeft . 'px; top: ' . $i_intTop . 
						'px; border-top: ' . $i_intGeneralYSize . 'px; border-left: ' . $i_intGeneralXSize . 
						'px; border-bottom: ' . $i_intGeneralYSize . 'px; border-top-style: solid; border-top-color: ' . 
						'transparent; border-left-style: solid; border-left-color: black; border-bottom-style: solid; ' . 
						'border-bottom-color: transparent;"></div>' . "\n";
			
		return $l_strContent;
	}
	
	/**
	 * Get the plain HTML left arrow
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @param integer	$i_intDirection		Direction of the arrow
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getHtmlLeftArrow(int $i_intLeft, int $i_intTop, int $i_intGeneralXSize, int $i_intGeneralYSize, int $i_intDirection) {
		// the HTML content
		$l_strContent = '';
		// create the id of this arrow
		$l_strBoxId = 'arrow' . $this->m_intX . 'x' . $this->m_intY . 'x' . $i_intDirection . 
						'_' . $this->m_objCrossword->get_uniqueId();
		
		// set the arrow HTML content
		$l_strContent = "\t\t\t" . '<div class="glcrossword_triangle" id= "' . $l_strBoxId . 
						'" style="left: ' . $i_intLeft . 'px; top: ' . $i_intTop . 
						'px; border-top: ' . $i_intGeneralYSize . 'px; border-right: ' . $i_intGeneralXSize . 
						'px; border-bottom: ' . $i_intGeneralYSize . 'px; border-top-style: solid; border-top-color: ' . 
						'transparent; border-right-style: solid; border-right-color: black; border-bottom-style: solid; ' . 
						'border-bottom-color: transparent;"></div>' . "\n";
					
		return $l_strContent;
	}
	
	/**
	 * Get the HTML of the line of a arrow
	 * @param float 	$i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 	Thickness of the borderlines of the box
	 * @param integer	$i_intDirection		Direction of the arrowline
	 * @return string 						The HTML content of the arrow.
	 */
	protected function getHtmlLineOfArrow(int $i_intLeft, int $i_intTop, int $i_intGeneralXSize, int $i_intGeneralYSize, string $i_strLineType, int $i_intDirection) {
		// the HTML content
		$l_strContent = '';
		// create the id of this arrowline
		$l_strBoxId = 'arrowline' . $this->m_intX . 'x' . $this->m_intY . 'x' . $i_intDirection . 
						'_' . $this->m_objCrossword->get_uniqueId();
		
		// set the arrow line HTML content
		$l_strContent = "\t\t\t" . '<div class="' . $i_strLineType . '" id="' . $l_strBoxId . '"' . 
						'style="left: ' . $i_intLeft . 'px; top: ' . $i_intTop . 'px; width: ' . $i_intGeneralXSize . 
						'px; height: ' . $i_intGeneralYSize . 'px;"></div>' . "\n";
							
		return $l_strContent;
	}
}
?>