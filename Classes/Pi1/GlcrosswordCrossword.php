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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Loss\Glcrossword\Controller\GlcrosswordController;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;

/**
 * The main crossword class. It contains all boxes with the questions and the answers.
 *
 * @author	Gerald Loß <gerald.loss@gmx.de>
 * @package	glcrossword
 */
class GlcrosswordCrossword  {
	
	/**
	 * Static global array with all instantiated crosswords.
	 * @var array
	 * @access public
	 */
	static public $m_arrCrosswords;
	
	/**
	 * Title of the crossword.
	 * @var string
	 * @access protected
	 */
	protected $m_strCrosswordTitle;
	
	/**
	 * The unique ID of this Crossword.
	 * @var integer
	 * @access protected
	 */
	protected $m_intUniqueId;
	
	/**
	 * Width of the crossword.
	 * @var integer
	 * @access protected
	 */
	protected $m_intWidthOfCrossword;
	
	/**
	 * Height of the crossword.
	 * @var integer
	 * @access protected
	 */
	protected $m_intHeighthOfCrossword;
	
	/**
	 * Scale factor for the x range. 0.5 means 50%.
	 * @var float
	 * @access protected
	 */
	protected $m_fltXScale;
	
	/**
	 * Scale factor for the y range. 0.5 means 50%.
	 * @var float
	 * @access protected
	 */
	protected $m_fltYScale;
	
	/**
	 * The width of the borders of the crossword.
	 * @var integer
	 * @access protected
	 */
	protected $m_intBorderWidth;
	
	/**
	 * Array with the already instantiated objects of the boxes
	 * @var array
	 * @access protected
	 */
	protected $m_arrBoxes;
	
	/**
	 * Array with boxes, which are out of bound or with similair failures.
	 * @var array
	 * @access protected
	 */
	protected $m_arrErrorBoxes;
	
	/**
	 * If this flag is true, then is in the whole crossword at least one box with error.
	 * @var boolean
	 * @access protected;
	 */
	protected  $m_blnIsError;
	
	/**
	 * The content of the error dialog widget on the frontend
	 * @var string
	 */
	protected  $m_strDialogError;
	
	/**
	 * Dialog local lang dependend for a wrong answer.
	 * @var string
	 */
	protected $m_strDialogWrongAnswer;
	
	/**
	 * Dialog for the description of the hint mode 
	 * @var string
	 */
	protected $m_strDialogHintDescription;
	
	/**
	 * Waiting data dialog for ajax hourglass
	 * @var string
	 */
	protected $m_strDialogWaiting; 
	
	//	***************************************************************************************************
	//	Class constants part
	//	***************************************************************************************************
	
	/**
	 * Name of the direction field of the edit causing question array.
	 * @var string
	 */
	const C_STR_ECQ_KEY_NAME_DIR = 'dir';
	
	/**
	 * Name of the field with the content of the question of the edit causing question array.
	 * @var string
	 */
	const C_STR_ECQ_KEY_NAME_TEXT = 'text';
	
	/**
	 * The height of the buttons in the crossword.
	 * @var integer
	 */
	const C_INT_BUTTON_HEIGHT = 20;
	
	/**
	 * Gets a crossword with a unique ID.
	 * @param 	integer 					$i_intUniqueId 	The unique ID of the requested crossword.
	 * @return 	GlcrosswordCrossword					Object with the requestet crossword.
	 */
	static public function get_Crossword($i_intUniqueId) {
		return self::$m_arrCrosswords[$i_intUniqueId];
	}
	
	/**
	 * Constructor of this class
	 *
	 * @param string		$i_strCrosswordTitle 	Title of the crossword.
	 * @param integer 		$i_intUniqueId			Unique ID of the crossword. 
	 * @param integer 		$i_intHeight			Height of the crossword
	 * @param integer 		$i_intWidth				Width of the crossword
	 * @param float			$i_fltXScale			Scale factor of the x range. 0.5 means 50%.
	 * @param float			$i_fltYScale			Scale factor of the y range. 0.5 means 50%.
	 * @param integer		$i_intBorderWidth		Border width of the crossword
	 * @param string		$i_strRelatedQuestions	Related questions of the crossword.
	 */
	public function __construct($i_strCrosswordTitle, 
								$i_intUniqueId, 
								$i_intHeight, 
								$i_intWidth,
								$i_fltXScale, 
								$i_fltYScale, 
								$i_intBorderWidth,
								$i_strRelatedQuestions) {
		
		// one row of the crossword
		$arrayRows = array();
		// initialise error flag
		$this->m_blnIsError = false;
		
		// set title of the crossword
		$this->m_strCrosswordTitle = $i_strCrosswordTitle;
		
		// store the unique ID for this unique crossword object
		$this->m_intUniqueId = $i_intUniqueId;
		
		// store this crossword globally with its own unique ID
		self::$m_arrCrosswords[$i_intUniqueId] = $this;
		
		$this->m_intHeighthOfCrossword = $i_intHeight;
		$this->m_intWidthOfCrossword = $i_intWidth;
		$this->m_fltXScale = $i_fltXScale;
		$this->m_fltYScale = $i_fltYScale;
		$this->m_intBorderWidth = $i_intBorderWidth;
		$this->m_arrErrorBoxes = array();
		
		// store the dialog for wrong answer local lang dependend
		$this->m_strDialogWrongAnswer = LocalizationUtility::translate('code.error.wrong.answer',
		                                                                GlcrosswordController::c_strExtensionName );
		// store the dialog for the hint description
		$this->m_strDialogHintDescription = LocalizationUtility::translate('text.button.hint.description',
		                                                                   GlcrosswordController::c_strExtensionName );
		
		// store the waiting dialog message local lang dependend
		$this->m_strDialogWaiting = LocalizationUtility::translate('text.hourglass.waiting',
                                                                  GlcrosswordController::c_strExtensionName );
		
		// initialisation of the array with the boxes of the crossword
		$arrayRows = array_fill(1, $this->m_intHeighthOfCrossword, NULL);
		$this->m_arrBoxes = array_fill(1, $this->m_intWidthOfCrossword, $arrayRows);
		
	    // build the box array for the crossword with the questions from the database
	    $this->buildBoxesArray($i_strRelatedQuestions);
	}
	
	/**
	 * Returns box from global array concerning the X and Y coordinates.
	 * @param integer $i_intX	X value of the coordinate.
	 * @param integer $i_intY	Y value of the coordinate.
	 * @return GlcrosswordBox Box on this coordinetes.
	 */
	public function getBox($i_intX, $i_intY) {
		// if the object already exist on this coordinates
		if (isset($this->m_arrBoxes[$i_intX][$i_intY])) {
			return $this->m_arrBoxes[$i_intX][$i_intY];
	
		// if there is no object
		} else {
			return NULL;
		}
	}
	
	/**
	 * Adds a box to the crossword.
	 * @param GlcrosswordBox $i_objBox The box which is to add to the crossword.
	 */
	public function addBox($i_objBox) {
		
		// The causing question object
		/* @var $l_objCausingQuestion GlcrosswordBoxQuestions */
		$l_objCausingQuestion = NULL;
		// the error text
		$l_strErrorText = '';
		// the causing question text
		$l_strCausingQuestionText = '';
		// UID of the causing question
		$l_intCausingQuestionUid = 0;
		// actual length of the answer of the causing question
		$l_intActualLength = 0;
		// the direction of the causing question
		$l_intCausingDirection = 0;
		
		
		// if box is inside the crossword borders
		if (   $i_objBox->get_intX() <= $this->m_intWidthOfCrossword 
			&& $i_objBox->get_intY() <= $this->m_intHeighthOfCrossword
			&& $i_objBox->get_intX() >= 1
			&& $i_objBox->get_intY() >= 1) {
  			
			// add this box to the regular array
			$this->m_arrBoxes[$i_objBox->get_intX()][$i_objBox->get_intY()] = $i_objBox;
		
		// if this box is out of bounds and it is an answer box
		} elseif ( $i_objBox->get_strType() == GlcrosswordBox::C_STR_TYPE_ANSWER ) {
			
			// get the causing question information
			$i_objBox->getCausingQuestionInformation($i_objBox, $l_strCausingQuestionText, $l_intCausingQuestionUid, 
													 $l_intActualLength, $l_objCausingQuestion, $l_intCausingDirection);

			// The answer of the question "%s" with the UID %u has a length of %u  
			// and is therefore out of the bounds of the crossword.
			$l_strErrorText = LocalizationUtility::translate('code.error.answer.box.out.of.bounds',
			                                                 GlcrosswordController::c_strExtensionName );
			$l_strErrorText = sprintf($l_strErrorText, 
									 filter_var($l_strCausingQuestionText,FILTER_SANITIZE_FULL_SPECIAL_CHARS), 
									 $l_intCausingQuestionUid, 
									 $l_intActualLength);
			// set the error for the causing question
			$l_objCausingQuestion->set_strErrorText($l_strErrorText, $l_intCausingDirection);
			$l_objCausingQuestion->set_blnIsError(true);
		
		// if there is a question box out of bounds
		} elseif ($i_objBox->get_strType() == GlcrosswordBox::C_STR_TYPE_QUESTION) {
			// The question "%s" with the UID %u has the x/y coordiante %d and   
			// %d and is out of the bounds of the crossword.
		    $l_strErrorText = LocalizationUtility::translate('code.error.question.box.out.of.bounds',
		                                                     GlcrosswordController::c_strExtensionName );
			$l_strErrorText = sprintf($l_strErrorText, 
									  filter_var($i_objBox->getFirstQuestion()->get_strQuestion(),FILTER_SANITIZE_FULL_SPECIAL_CHARS), 
									  $i_objBox->getFirstQuestion()->get_intUid(),
									  $i_objBox->get_intX(), 
									  $i_objBox->get_intY());
			$i_objBox->set_strErrorText($l_strErrorText);
			$i_objBox->set_blnIsError(true);
			// add this box to the error array
			array_push($this->m_arrErrorBoxes, $i_objBox);
				
		// if all other box types not inside the borders of the crossword
		} else {
			// The empty box with the x/y coordiante %d and %d is out of the
		  	// bounds of this crossword. This is an error in the source code.
		    // Please contact the developer.
		    $l_strErrorText = LocalizationUtility::translate('code.error.empty.box.out.of.bounds',
		                                                     GlcrosswordController::c_strExtensionName );
			$l_strErrorText = sprintf($l_strErrorText, $i_objBox->get_intX(), $i_objBox->get_intY());
			$i_objBox->set_strErrorText($l_strErrorText);
			$i_objBox->set_blnIsError(true);
			
			// add this box to the error array
			array_push($this->m_arrErrorBoxes, $i_objBox);
		}
	}
	
	/**
	 * Get an box object from the error array. This are all boxes which have cause an error
	 * and are not displayable inside the crossword grid.
	 * @param integer $i_intIndex Index in the array.
	 * @return GlcrosswordBox Box from the array.
	 */
	public function getErrorBox($i_intIndex) {
		// if index exceed the size of the array
		if ($i_intIndex >= sizeof($this->m_arrErrorBoxes )) {
			return NULL;
			// if index is in the size of the array
		} else {
			return $this->m_arrErrorBoxes[$i_intIndex];
		}
	}
	
	/**
	 * Getter of the height of the crossword.
	 * @return integer
	 */
	public function get_HeightOfCrossword() {
		return $this->m_intHeighthOfCrossword;
	}

	/**
	 * Getter of the width of the crossword.
	 * @return integer
	 */
	public function get_WidthOfCrossword() {
		return $this->m_intWidthOfCrossword;
	}
	
	/**
	 * Getter of the unique ID of the crossword.
	 * @return integer
	 */
	public function get_uniqueId() {
		return $this->m_intUniqueId;
	}
	
	/**
	 * Getter of the error flag of the crossword.
	 * @return boolean
	 */
	public function get_blnIsError() {
		return $this->m_blnIsError;
	}
	
	/**
	 * Setter of the error flag of the crossword
	 * @param boolean $i_blnValue
	 */
	public function set_blnIsError($i_blnValue) {
		$this->m_blnIsError = $i_blnValue;
		// set the error dialog text, because it is not available in the ajax request
		$this->m_strDialogError = $this->readDialogErrorText();
	}
	
	/**
	 * Getter for the error dialog
	 * @return string The error dialog text
	 */	
	public function get_strDialogError() {
		return $this->m_strDialogError;
	}
	
	/**
	 * Getter for the dialog of wrong answers.
	 * @return string The dialog content.
	 */
	public function get_strDialogWrongAnswer(){
		return $this->m_strDialogWrongAnswer;
	}
	
	/**
	 * Getter for the dialog of the hint description.
	 * @return string The dialog content.
	 */
	public function get_strDialogHintDescription(){
		return $this->m_strDialogHintDescription;
	}
	
	/**
	 * Getter for the waiting dialog for the hourglass
	 * @return string The waiting dialog
	 */
	public function get_strDialogWaiting(){
		return $this->m_strDialogWaiting;
	}
	
	/**
	 * Getter for all texts in the javascript crossword. All texts are LL dependend.
	 * @return array	An array with all texts.
	 */
	public function getLLTexts() {
		return array( 'errorDialog' => $this->get_strDialogError(),
					  'wrongAnswerDialog' => $this->get_strDialogWrongAnswer(),
					  'hintDescription' => $this->get_strDialogHintDescription() );
	}
	
	
	/**
	 * Getter for the border width of the crossword.
	 * @return integer The border width.
	 */
	public function get_intBorderWidth(){
		return $this->m_intBorderWidth;
	}
	
	/**
	 * Get the unique ID of the crossword
	 * @return	integer	The unique ID of the crossword.
	 */
	public function get_intUniqueId(){
		return $this->m_intUniqueId;
	}
	
	/**
	 * Draw the crossword with the initial HTML content.
	 * @param float $i_fltXScale		The scale factor for the X dimension.
	 * @param float $i_fltYScale		The scale factor for the Y dimension.
	 * @param integer $i_intBorderWidth	The width of the border.
	 * @return string					The HTML content of the crossword.
	 */
	public function draw(){
		
		// the current box
		/* @var $l_objCurrentBox GlcrosswordBox */
		$l_objCurrentBox = NULL;
		// the HTML content
		$l_strContent = '';
		// The width of the whole crossword
		$l_intCrosswordWidth = 0;
		// The height of the whole crossword
		$l_intCrosswordHeight = 0;
		// count of rows in the error array
		$l_intErrorRows = 0;
		// the actual height of the crossword
		$l_intActualHeight = 0;
		// the gap beweent crossword an error boxes
		$l_intGapHeight = 0;
		// space from the top for the out of bounds errors
		$l_intOOBErrorTopSpace = 0;
		// space from the top for the buttons
		$l_intButtonTopSpace = 0;

		// compute the width
		$l_intCrosswordWidth = round(GlcrosswordBox::C_INT_BOX_SIZE * $this->m_intWidthOfCrossword * $this->m_fltXScale);
		// compute the height
		$l_intCrosswordHeight = round(GlcrosswordBox::C_INT_BOX_SIZE * $this->m_intHeighthOfCrossword * $this->m_fltYScale);
		$l_intActualHeight = $l_intCrosswordHeight;
		
		// a little gap between the crossword and the buttons
		$l_intGapHeight = round(GlcrosswordBox::C_INT_BOX_SIZE / 4 * $this->m_fltYScale);
		// exceed the hight of the whole crossword with a little gap an the height of the Butt0ns
		$l_intCrosswordHeight += $l_intGapHeight + GlcrosswordCrossword::C_INT_BUTTON_HEIGHT;
		
		
		// if there are error boxes with out of bound exceptions
		if (count($this->m_arrErrorBoxes) > 0) {
			// exceed the hight with a little gap
			$l_intCrosswordHeight += $l_intGapHeight; 
			// compute the count of rows in the error array
			$l_intErrorRows = ceil(count($this->m_arrErrorBoxes) / $this->m_intWidthOfCrossword);
			// exceed the hight with the count of rows in the error area
			$l_intCrosswordHeight += round($l_intErrorRows * GlcrosswordBox::C_INT_BOX_SIZE * $this->m_fltYScale);
		}
		
		// write the main div tag, with the unique ID in the ID attribute
		$l_strContent = "\t\t" . '<div id="glcrossword_' . $this->m_intUniqueId . '" style="width: ' . $l_intCrosswordWidth . 'px; ';
		$l_strContent .= 'height: ' . $l_intCrosswordHeight . 'px; position: relative;">' . "\n";
		
		
		// go through every box in the crossword for the boxes itself
		for ($x = 1; $x <= $this->m_intWidthOfCrossword; $x++) {
			for ($y = 1; $y <= $this->m_intHeighthOfCrossword; $y++) {
				
				// get the HTML content of this box
				$l_objCurrentBox = $this->m_arrBoxes[$x][$y];
				$l_strContent .= $l_objCurrentBox->draw($this->m_fltXScale, $this->m_fltYScale, $this->m_intBorderWidth);
			}
		}
		
		// go through every box in the crossword for the arrows
		for ($x = 1; $x <= $this->m_intWidthOfCrossword; $x++) {
			for ($y = 1; $y <= $this->m_intHeighthOfCrossword; $y++) {
				
				// get the HTML content of this box
				$l_objCurrentBox = $this->m_arrBoxes[$x][$y];
				// if this box is of type question
				if ($l_objCurrentBox->get_strType() == GlcrosswordBox::C_STR_TYPE_QUESTION) {
					$l_strContent.=  $l_objCurrentBox->draw_arrows($this->m_fltXScale, 
																   $this->m_fltYScale, 
																   $this->m_intBorderWidth );
				}
			}
		}
		
		// compute the top space for the buttons
		$l_intButtonTopSpace = $l_intGapHeight + $l_intActualHeight;
		// draw the buttons under the crossword
		$l_strContent .= $this->getHTMLButtonContent($l_intButtonTopSpace);
		
		// compute the top space for the out of bound error boxes
		$l_intOOBErrorTopSpace = ( 2 * $l_intGapHeight ) + $l_intActualHeight + GlcrosswordCrossword::C_INT_BUTTON_HEIGHT;
		
		// for every error in error boxes array
		foreach ($this->m_arrErrorBoxes as $intErrorCount => $objErrorBox) {
			// write the HTML content of the out of bound errors
			$l_strContent.= $objErrorBox->drawErrorBox( $intErrorCount, $this->m_fltXScale, $this->m_fltYScale, $this->m_intBorderWidth, 
								  						$l_intOOBErrorTopSpace );
		}
		
		// write an invisible hourglass for later use
		$l_strContent .= $this->drawHourGlass();
		
		// write the closing div tag
		$l_strContent .= "\t\t" . '</div>' . "\n"; 
		
		return $l_strContent;
	}
	
	/**
	 * Returns an array with all texts of the questions.
	 * @return array Array with all texts of the questions.
	 */
	public function getQuestionsArray() {
		// the current box
		/* @var $l_objCurrentBox GlcrosswordBox */
		$l_objCurrentBox = NULL;
		// question array of the questions box
		$l_arrQuestions = array();
		
		
		// go through every box in the crossword
		for ($x = 1; $x <= $this->m_intWidthOfCrossword; $x++) {
			for ($y = 1; $y <= $this->m_intHeighthOfCrossword; $y++) {
				
				// read the box
				$l_objCurrentBox = $this->m_arrBoxes[$x][$y];
				// if this box is of type question
				if ($l_objCurrentBox->get_strType() == GlcrosswordBox::C_STR_TYPE_QUESTION) {
					// insert the questions array into this array
					$l_arrQuestions[$x][$y] = $l_objCurrentBox->getQuestionContentArray();
				} else {
					// else insert an empty entry into the array
					$l_arrQuestions[$x][$y] = NULL;
				}
			}
		}
		
		// return the array
		return $l_arrQuestions;
	}
	
	/**
	 * Get a hint for the given anser box.
	 * 
	 * @param 	array 	$i_arrCoordiante	Array with the coordinates of the answer box.
	 * @return	string						Content of the answer box.
	 */
	public function getHintForAnswerBox($i_arrCoordiante){
		/* @var $l_objCurrentBox GlcrosswordBox */
		$l_objCurrentBox = NULL;
		
		// read the box
		$l_objCurrentBox = $this->m_arrBoxes[$i_arrCoordiante['x']][$i_arrCoordiante['y']];
		
		// if this box is of type answer
		if ($l_objCurrentBox->get_strType() == GlcrosswordBox::C_STR_TYPE_ANSWER) {
			// return the content of this answer box
			return $l_objCurrentBox->
						getContentObject(GlcrosswordBoxAnswer::C_INT_CONTENT_INDEX)->
							get_strAnswerLetter();			
		}
		else {
			return '';
		}
	}
	
	/**
	 * Returns an array with all the errors of the crossword.
	 * @return array Array with all errors of the crossword.
	 */
	public function getErrorArray() {
		/* @var $l_objCurrentBox GlcrosswordBox */
		$l_objCurrentBox = NULL;
		// error array of the questions box
		$l_arrErrors = array();
		
		// go through every box in the crossword
		for ($x = 1; $x <= $this->m_intWidthOfCrossword; $x++) {
			for ($y = 1; $y <= $this->m_intHeighthOfCrossword; $y++) {
				
				// read the box
				$l_objCurrentBox = $this->m_arrBoxes[$x][$y];
				// if there is an error in this box
				If ($l_objCurrentBox->get_blnIsError() == true){
					// insert Error text
					$l_arrErrors[$x][$y] = $l_objCurrentBox->get_strErrorText(); 
				
				// if there is no error for this box
				} else {
					// define a null value
					$l_arrErrors[$x][$y] = NULL;
				}
				
			}
		}
		// return the array
		return $l_arrErrors;
	}
	
	/**
	 * Returns an array with all the out of bound errors of the crossword.
	 * @return array Array with all errors of the crossword.
	 */
	public function getErrorArrayOOB() {
		
		// the returning array with the error descriptions
		$l_arrErrorOOB = array();
		
		// go through every error box
		foreach ($this->m_arrErrorBoxes as $key => $objBox) {
			$l_arrErrorOOB[$key] = $objBox->get_strErrorText();
		}
		
		// return the error array
		return $l_arrErrorOOB;
	}
	
	
	/**
	 * getter for the error flag.
	 * @return boolean	 
	 */
	public function isError() {
		// send error flag back
		return $this->m_blnError;
	}
	
	/**
	 * Gets the text for the error dialog widget
	 * @return string	Text for the dialog widget
	 */
	public function readDialogErrorText() {
		// the error message data
		$l_strErrorMessage = "";
		
		// get the error message from locallang
		$l_strErrorMessage = LocalizationUtility::translate('code.error.general.dialog',
		                                                    GlcrosswordController::c_strExtensionName );
		
		// return the text
		return $l_strErrorMessage;
		
	}
	
	
	/**
	 * Gets the edit matrix with all informations of the direction of the text in everey box
	 * and the lenght of the text in every direction.
	 * This matrix has the following dimensions.
	 * First Index: 	Collumns of the crossword.
	 * Second Index: 	Rows of the crossword.
	 * Third Index:		for 'fieldlength' with the length of the current field
	 * 					and for 'directions' with the following directions array
	 * 		First Index: 	Direction of the text.
	 * 						See Constants with the prefix GlcrosswordBox::C_INT_EDIT_DIR_*
	 * 		Second Index: 	Side of the current box where the text is going on.
	 * 						See Constants with the prefix GlcrosswordBox::C_INT_EDIT_SIDE_*
	 * 		Value: 			Length of the text in this direction.
	 * @return array Array with the edit matrix.
	 */
	public function getEditMatrix() {
		
		// The current box in the crossword.
		/* @var $l_objCurrentBox GlcrosswordBox */ 
		$l_objCurrentBox = NULL;
		
		// the returning array with the edit matrix
		$l_arrEditMatrix = array();
		// Array with all causing questions of the answer box
		$l_arrCausingQuestions = array();
		$l_arrCausingQuestion = array();
		// array with the directions for the edit matrix
		$l_arrDirections = array();
		
		// go through every box in the crossword
		for ($x = 1; $x <= $this->m_intWidthOfCrossword; $x++) {
			for ($y = 1; $y <= $this->m_intHeighthOfCrossword; $y++) {

				// read the box
				$l_objCurrentBox = $this->m_arrBoxes[$x][$y];
				
				// if it is a answer box
				if ($l_objCurrentBox->get_strType() == GlcrosswordBox::C_STR_TYPE_ANSWER) {
					
					// get all causing questions auf this answer box
					$l_arrCausingQuestions = $l_objCurrentBox->getAllCausingQuestions();
					
					// initialize the direction array
					$l_arrDirections = array();
					
					// the the initial edit matrix with empty direction array
					$l_arrEditMatrix[$x][$y] = array(
						GlcrosswordBox::C_STR_EDIT_FIELDLENGTH => 
									$l_objCurrentBox->getContentObject(
										GlcrosswordBoxAnswer::C_INT_CONTENT_INDEX)->get_intLength(), 
						GlcrosswordBox::C_STR_EDIT_DIRECTIONS  => $l_arrDirections ); 
					
					// go through every causing question
					foreach ( $l_arrCausingQuestions as $l_arrCausingQuestion ){

						// build the directions array
						// if there is already an existing directions array, then we have to merge both arrays,
						// otherwise we would overwrite the previous array
						$l_arrDirections = $this->mergeEditMatrixArrays(
													$l_arrDirections, 
										   			$this->getCausingQuestionData($l_objCurrentBox, $l_arrCausingQuestion));
						 
						// set new merged direction array
						$l_arrEditMatrix[$x][$y][GlcrosswordBox::C_STR_EDIT_DIRECTIONS] = $l_arrDirections; 
					}
				}
			}
		}
		
		return $l_arrEditMatrix;
	}
	
	/**
	 * For every answer box is stored which question has caused this answer letter,
	 * for controlling the edit mode in the crossword.
	 * 
	 * First Index: Collumns of the crossword.
	 * Second Index: Rows of the crossword.
	 * Third Index: Edit direction of the text. See 
	 * 				See Constants with the prefix GlcrosswordBox::C_INT_EDIT_DIR_*
	 * Value:	x => x coordinate,
	 * 			Y => y coordinate,
	 * 			dir => direction of the question
	 * 					See Constants with the prefix GlcrosswordBoxQuestions::C_INT_DIR_*
	 * 			text => <Text of the question> */
	public function getEditCausingQuestionArray(){
		// The current box in the crossword.
		/* @var $l_objCurrentBox GlcrosswordBox */
		$l_objCurrentBox = NULL;
		// The current box in the crossword.
		/* @var $l_objQuestionBox GlcrosswordBoxQuestions */ 
		$l_objQuestionBox = NULL;
		
		// the edit causing question array
		$l_arrEditCausingQuestions = array(); 
		// array with the question data
		$l_arrQuestionData = array();
		// Array with all causing questions of the answer box
		$l_arrCausingQuestions = array();
		$l_arrCausingQuestion = array();
		
		// the direction of the causing question
		$l_intDirection = -1;

		
		// go through every box in the crossword
		for ($x = 1; $x <= $this->m_intWidthOfCrossword; $x++) {
			for ($y = 1; $y <= $this->m_intHeighthOfCrossword; $y++) {
		
				// read the box
				$l_objCurrentBox = $this->m_arrBoxes[$x][$y];
		
				// if it is an answer box
				if ($l_objCurrentBox->get_strType() == GlcrosswordBox::C_STR_TYPE_ANSWER) {

					// get all causing questions auf this answer box
					$l_arrCausingQuestions = $l_objCurrentBox->getAllCausingQuestions();
						
					// go through every causing question
					foreach ( $l_arrCausingQuestions as $l_arrCausingQuestion ){
						
						// get the direction of the question
						$l_intDirection = $l_arrCausingQuestion[GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_DIRECTION];
						
						// get the question box from the causing question array
						$l_objQuestionBox = $l_arrCausingQuestion[GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_QUESTION];
						
						$l_arrQuestionData = array(
								'x' => $l_objQuestionBox->get_intX(),
								'y' => $l_objQuestionBox->get_intY(),
								// translate the direction of the causing question in the direction of the edit matrix
								GlcrosswordCrossword::C_STR_ECQ_KEY_NAME_DIR => $l_intDirection,
								// set the question text
								GlcrosswordCrossword::C_STR_ECQ_KEY_NAME_TEXT => 
															$l_objQuestionBox->get_objQuestion($l_intDirection)->get_strQuestion()
						);

						$l_arrEditCausingQuestions[$x][$y][$this->convertQDir2EDir($l_intDirection)] = $l_arrQuestionData; 
					}
				}
			}
		}
		
		// return the edit causing question array
		return $l_arrEditCausingQuestions;
	}
	
	/**
	 * Delivers the button HMTL content.
	 * @param integer $i_intTopSpace	Space from the top for the buttons
	 * @return string					The HTML content.
	 */
	public function getHTMLButtonContent($i_intTopSpace) {
		// the HTML content
		$l_strContent = '';

		$l_strContent .= "\t\t\t" . '<!-- The buttons under the crossword -->' . "\n";
		
		$l_strContent .= "\t\t\t" . '<div class = "glcrossword_button_container" id="btnContainer_' . 
										$this->get_uniqueId() . '" style="top: '. 
										$i_intTopSpace .'px;">' . "\n";
		
		// the solution button
		$l_strContent .= "\t\t\t\t" . '<button id="btnSolution_' . $this->get_uniqueId() . 
						  '" title >' . $this->getStrButtonSolution() . '</button>' . "\n";
		
		// the hint button
		$l_strContent .= "\t\t\t\t" . '<button id="btnHint_' . $this->get_uniqueId() . '" title >' . 
			$this->getStrButtonHint() . '</button>' . "\n";

		$l_strContent .= "\t\t\t" . '</div>' . "\n";
		
		return $l_strContent;
	}
	
	/**
	 * Returns an array with all answer content of every answer box in the crossword.
	 * 
	 * @return	array	All data of the solution of the crossword.
	 * 				Index 1: The x coordiante
	 * 				Index 2: The y coordinate
	 * 				value: The answer content of this box.
	 */
	public function getSolutionData() {
		// the current box
		/* @var $l_objCurrentBox GlcrosswordBox */
		$l_objCurrentBox = NULL;
		// solution data array of the crossword
		$l_arrSolutionData = array();
		
		
		// go through every box in the crossword
		for ($x = 1; $x <= $this->m_intWidthOfCrossword; $x++) {
			for ($y = 1; $y <= $this->m_intHeighthOfCrossword; $y++) {
		
				// read the box
				$l_objCurrentBox = $this->m_arrBoxes[$x][$y];
				// if this box is of type answer
				if ($l_objCurrentBox->get_strType() == GlcrosswordBox::C_STR_TYPE_ANSWER) {
					// insert the answer content in this position
					$l_arrSolutionData[$x][$y] = $l_objCurrentBox->
								getContentObject(GlcrosswordBoxAnswer::C_INT_CONTENT_INDEX)->
									get_strAnswerLetter();
				} else {
					// else insert an empty entry into the array
					$l_arrSolutionData[$x][$y] = NULL;
				}
			}
		}
		
		// return the array
		return $l_arrSolutionData;
	}
	
	/**
	 * Returns an array with the size of the HTML element of one box in the crossword.
	 * @return array	Array with two components
	 * 						width: 	The width of the box.
	 * 						height:	The height of the box.
	 */
	public function getHtmlBoxSize(){
		
		// one box of the crossword
		/* @var $l_objCurrentBox GlcrosswordBox */
		$l_objCurrentBox = NULL;
		// the returning array
		$l_arrSize = array();
		
		// read box 1x1 this one must always exists. Otherwise we have no boxes at all
		$l_objCurrentBox = $this->m_arrBoxes[1][1];
		
		// if we have found the box
		if ($l_objCurrentBox != NULL) {
			$l_arrSize = $l_objCurrentBox->getGeneralBoxSize($this->m_fltXScale, $this->m_fltYScale);
		}
		else {
			$l_arrSize[width] = 0;
			$l_arrSize[height] = 0;
		}
		
		// return the array
		return $l_arrSize;
	}
	
	/**
	 * Draws an invisible hourglass for later activating.
	 * @return	string	The HTML content with the hourglass.
	 */
	public function drawHourGlass(){
		
		// the HTML content of the hourglass
		$l_strContent = '';
		// the width and height of the hourglass
		$l_intHourglassHeight = 0;
		$l_intHourglassWidth = 0;
		// the size of a HTML box
		$l_arrBoxSize = array();
		// style for the image
		$l_strStyle = '';
		
		$l_arrBoxSize = $this->getHtmlBoxSize();
		
		$l_intHourglassHeight = $this->m_intHeighthOfCrossword * $l_arrBoxSize['height'];
		$l_intHourglassWidth = $this->m_intWidthOfCrossword * $l_arrBoxSize['width'];
		
		// if only the heigth is to small
		if ($l_intHourglassHeight < 255 && $l_intHourglassWidth >= 300) {
			$l_strStyle = 'style="height: ' . $l_intHourglassHeight . 'px;" ';
		}
		// if only the width is to narrow
		else if ($l_intHourglassHeight >= 255 && $l_intHourglassWidth < 300) {
			$l_strStyle = 'style="width: ' . $l_intHourglassWidth . 'px;" ';
		}
		// if width  and height is to small
		else if ($l_intHourglassHeight < 255 && $l_intHourglassWidth < 300) {
			$l_strStyle = 'style="width: ' . $l_intHourglassWidth . 'px; ';
			$l_strStyle .= 'height: ' . $l_intHourglassHeight . 'px;" ';
		}
		
		$l_strContent = "\n\t\t\t" . '<!-- The main hourglass for the ajax requests -->';
		$l_strContent .= "\n\t\t\t" . '<div id="ajaxHourGlassOuter_' . 
										$this->get_uniqueId() . '" class="glcrossword_ajax_hourglass_off" ';
		$l_strContent .= 'style="width: ' . $l_intHourglassWidth . 'px; ';
		$l_strContent .= 'height: ' . $l_intHourglassHeight . 'px;"> ';
		
		$l_strContent .= "\n\t\t\t\t" . '<div id="ajaxHourGlassMiddle_' . 
										$this->get_uniqueId() . '" class="glcrossword_ajax_hourglass_middle">';
		$l_strContent .= "\n\t\t\t\t\t" . '<div id="ajaxHourGlassInner_' . 
										$this->get_uniqueId() . '" class="glcrossword_ajax_hourglass_inner">';
				
		$l_strContent .= "\n\t\t\t\t\t\t" .'<img ';
		$l_strContent .= 'id="ajaxHourGlassImg_' . 
										$this->get_uniqueId() . '" ' . $l_strStyle;
		$l_strContent .= 'class="glcrossword_ajax_hourglass"';
		
		$l_strContent .= 'src="' . PathUtility::getAbsoluteWebPath(GeneralUtility::getFileAbsFileName(
		      'EXT:glcrossword/Resources/Public/images/Animated_Crossword.gif')) . '" /> ';
		
		$l_strContent .= "\n\t\t\t\t\t\t" . '<p id="ajaxHourGlassText_' . 
										$this->get_uniqueId() . '" class="glcrossword_ajax_hourglass_text">' . 
						 $this->get_strDialogWaiting() . '</p>';
		
		$l_strContent .= "\n\t\t\t\t\t" . '</div>';
		$l_strContent .= "\n\t\t\t\t" . '</div>';
		$l_strContent .= "\n\t\t\t" . '</div>';
		
		// returns the HTML content
		return $l_strContent;  
	}
	
//	*********************************************************************************	
//	Protected methods part
//	*********************************************************************************	
	
	/**
	 * Build the array with all the boxes of the crossword
	 * @param string $i_strRelatedQuestions The UIDs of the related questions
	 */
	protected function buildBoxesArray($i_strRelatedQuestions) {
		
		/* @var $l_objBoxQuestions GlcrosswordBoxQuestions */
		$l_objBoxQuestions = NULL;
		/* @var $l_objQueryBuilder QueryBuilder */
		$l_objQueryBuilder = NULL;
		// Where clausel for select on DB
		$l_arrWhere = array();
		// the related Questions in an array
		$l_arrRelatedQuestions = array();
		// the result of the sql statement
		/* var $l_objResult \Doctrine\DBAL\Driver */
		$l_objResult = Null;
		/* @var LanguageAspect $languageAspect */
		$languageAspect = Null;
		
		// get query builder for questions table
		$l_objQueryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
		->getQueryBuilderForTable(GlcrosswordController::C_STR_DB_TABLE_QUESTIONS);

		// convert related Questions in an array
		$l_arrRelatedQuestions = explode(',', $i_strRelatedQuestions);
		
		$languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
		
		// build where clausel
		/*
		 * uid IN (' . $i_strRelatedQuestions . ')'
		 * And (    sys_language_uid IN (-1,0)'
		 *       OR (sys_language_uid = ' . $GLOBALS['TSFE']->sys_language_uid . ' AND l10n_parent = 0)
		 *     )
		 */
		$l_arrWhere = [ $l_objQueryBuilder->expr()->in('uid', $l_arrRelatedQuestions),
		                $l_objQueryBuilder->expr()->orX(
                            $l_objQueryBuilder->expr()->in('sys_language_uid', array(-1,0)),
                            $l_objQueryBuilder->expr()->andX(
                                $l_objQueryBuilder->expr()->eq('sys_language_uid', $l_objQueryBuilder->createNamedParameter($languageAspect->getId())),
    	                        $l_objQueryBuilder->expr()->eq('l10n_parent', $l_objQueryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
    	                    )
		                )
		              ];
        
		// exec the sql statement
		$l_objQueryBuilder->select('*')
		                  ->from(GlcrosswordController::C_STR_DB_TABLE_QUESTIONS)
                		  ->where(...$l_arrWhere);
		
		// just for debugging
        //$l_TestSql = $l_objQueryBuilder->getSQL();
        //$l_TestParameters = $l_objQueryBuilder->getParameters();
		                  
        $l_objResult = $l_objQueryBuilder->execute(); 
		                  
        // exchange fetch() with fetchAssociative() up to Typo3 v11 -> Method fetch() will be removed
//        while($row = $l_objResult->fetchAssociative() ){
        while($row = $l_objResult->fetch() ){
			// if there is a valid row (should be always the case)
			// and if the current row language is different from the currently needed language
			// and if overlay mode is set
			if (is_array($row) 
				&& $row['sys_language_uid'] != $languageAspect->getContentId() 
			    && $languageAspect->getLegacyOverlayType()) {
				
				// get the overlay language
				$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay(GlcrosswordController::C_STR_DB_TABLE_QUESTIONS, 
																    $row,
				                                                    $languageAspect->getContentId(), 
				                                                    $languageAspect->getLegacyOverlayType());				
			}

			// if row is after the language operations still valid
			if ($row) {
				// create the question and add it to the crossword
				$l_objBoxQuestions = GlcrosswordBoxQuestions::boxQuestionFactory($row['xpos'], 
																					 $row['ypos'], 
																					 $row['uid'], 
																					 $row['direction'], 
																					 $row['question'], 
																					 $row['answer'],
																					 $row['mask'],
																					 $this );
				// add the related answer fields to the crossword
				$this->addRelatedAnswerFields($l_objBoxQuestions, $row['direction']);
			}
		}
		
		// Fill all gaps with empty boxes in the crossword
		$this->fillEmptyBoxes();

		// if there is no error in the crossword
		if ($this->m_blnIsError == false) {
			// set extra border, if after an answer is no other answer, empty box or the bounds of the crossword
			$this->setExtraBorders();
		}
	}
	
	/**
	 * Fill all gaps with empty boxes in the crossword.
	 */
	protected function fillEmptyBoxes() {
		
		// go through every box in the crossword
		for ($x = 1; $x <= $this->m_intWidthOfCrossword; $x++) {
			for ($y = 1; $y <= $this->m_intHeighthOfCrossword; $y++) {
				
				// if in this box is no content
				if (! isset($this->m_arrBoxes[$x][$y])) {
					// create an empty box and add it to this crossword
					GlcrosswordBoxEmpty::boxEmptyFactory($x, $y, $this);
				}
			}
		}
	}
	
	/**
	 * Set extra borders at the end of ansers which have another answer in the next box. 
	 */
	protected function setExtraBorders(){
		
		// the current box
		/* @var $l_objCurrentBox GlcrosswordBox */
		$l_objCurrentBox = null;
		
		// go through every box in the crossword
		for ($x = 1; $x <= $this->m_intWidthOfCrossword; $x++) {
			for ($y = 1; $y <= $this->m_intHeighthOfCrossword; $y++) {
		
				// get the current box
				$l_objCurrentBox = $this->m_arrBoxes[$x][$y];
				
				// if this is a question
				if ($l_objCurrentBox->get_strType() == GlcrosswordBox::C_STR_TYPE_QUESTION) {
					 $this->checkQuestionForExtraBorders($l_objCurrentBox);
				}
			}
		}
	}
	
	/**
	 * Check all questions of a question box, if extra borders are necessary and
	 * set this extra borders to the actual answer boxes.
	 * 
	 * @param GlcrosswordBoxQuestions $i_objQuestionBox
	 */
	protected function checkQuestionForExtraBorders($i_objQuestionBox) {
		// array with all questions of the current box
		$l_arrAllQuestions = array();
		// the last answer box of a question
		/* @var $l_objLastAnswerBox GlcrosswordBoxAnswer */
		$l_objLastAnswerBox = NULL;
		// the next box after the answer
		/* @var $l_objNextBox GlcrosswordBox */
		$l_objNextBox = NULL;
		// the pure direction
		$l_intPureDirection = 0;
		
		// get array with all questions
		$l_arrAllQuestions = $i_objQuestionBox->getContentArray();
		
		// check for every direction
		for ($i = 0; $i <= GlcrosswordBoxQuestions::C_INT_DIR_MAX; $i++) {
			
			// if in this direction is no question
		    if (!array_key_exists($i, $l_arrAllQuestions)) {
				// jump to the next direction
				continue;
			}
			
			// get the last answer box of this question
			$l_objLastAnswerBox = $i_objQuestionBox->getLastAnswerBoxOfQuestion($i);
			
			$l_intPureDirection = $this->convertDirection2PureDir($i);
			
			// get the next box after the answerbox
			$l_objNextBox = $l_objLastAnswerBox->getBoxFromOffset($l_intPureDirection, 1);
			
			// if the next box is not out of the bounds of the crossword
			// AND this is a box of the type answer
			if ($l_objNextBox != NULL
				&& $l_objNextBox->get_strType() == GlcrosswordBox::C_STR_TYPE_ANSWER) {
				
				// if the next box has not already an extra border
				if ($l_objNextBox->getOppositeBorderFlag($i) == false) {
					// set the extra border for the given direction
					$l_objLastAnswerBox->setExtraBorder($i);
				}
			}
		}
	}
	
	/**
	 * Add the related AnswerFieldBoxes of an Question to the crossword
	 * @param GlcrosswordBoxQuestions	$i_objBoxQuestions	Object with the questions
	 * @param integer 						$i_intDirection		Direction of the question
	 */
	protected function addRelatedAnswerFields($i_objBoxQuestions, $i_intDirection) {
		
		// Content of an answer letter
		/* @var $l_objContentAnswer GlcrosswordContentAnswerfield */
		$l_objContentAnswer = NULL;
		// the start vector
		$l_arrStartVector = array();
		// the direction vector
		$l_arrDirectionVector = array();
		// current box for the answer
		$l_arrCurrentBox = array();
		// flag for end of answer
		$l_blnEndOfAnswer = FALSE;
		// the next letter
		$l_strNextLetter = '';
		// the index of the letter in the answer
		$l_intIndex = 0;
		// the index of the letter in the edit mask
		$l_intIndexEditMask = 0;
		
		// determine the direction vectors
		$this->getDirectionVector($i_intDirection, $l_arrStartVector, $l_arrDirectionVector);
		
		// initialise the current box
		$l_arrCurrentBox = array( 'X' => $i_objBoxQuestions->get_intX(),
								  'Y' => $i_objBoxQuestions->get_intY());
		
		// go to the start box of the answer
		$l_arrCurrentBox['X'] += $l_arrStartVector['X'];
		$l_arrCurrentBox['Y'] += $l_arrStartVector['Y'];
		
		// get the next answer letter an shift the index to the next letter
		$l_strNextLetter = $this->getNextAnswerLetter( $i_objBoxQuestions->get_objQuestion($i_intDirection)->get_strAnswer(), 
													   $i_objBoxQuestions->get_objQuestion($i_intDirection)->get_intEditMask(),
													   $l_intIndex,
													   $l_intIndexEditMask);
		// if we are at the end of the answer
		if ($l_strNextLetter == '') {
			$l_blnEndOfAnswer = true;
		}
		
		while (! $l_blnEndOfAnswer) {
			
			// create answer letter content object
			$l_objContentAnswer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(GlcrosswordContentAnswerfield::class,
									 					   $l_strNextLetter );
			
			// create an answer box and add it to this crossword obejct
			GlcrosswordBoxAnswer::boxAnswerFactory($l_arrCurrentBox['X'], 
 													    $l_arrCurrentBox['Y'], 
													    $l_objContentAnswer, 
													    $i_objBoxQuestions, 
													    $i_intDirection, 
													    $this );
			
			// move to the next box in the crossword
			$l_arrCurrentBox['X'] += $l_arrDirectionVector['X'];
			$l_arrCurrentBox['Y'] += $l_arrDirectionVector['Y'];
				
			// get the next answer letter an shift the index to the next letter
			$l_strNextLetter = $this->getNextAnswerLetter($i_objBoxQuestions->get_objQuestion($i_intDirection)->get_strAnswer(), 
														  $i_objBoxQuestions->get_objQuestion($i_intDirection)->get_intEditMask(),
														  $l_intIndex,
													   	  $l_intIndexEditMask);
			// if we are at the end of the answer
			if ($l_strNextLetter == '') {
				$l_blnEndOfAnswer = true;
			}
		}
	}
	
	/**
	 * Returns the next answer letter an shift the index to the next letter.
	 * @param string 	$i_strAnswerText		The answer text.
	 * @param string 	$i_strEditMask			The edit mask.
	 * @param integer 	&$c_intIndex			The index of the position of the letter in the answer.
	 * @param integer	&$c_intIndexEditMask	The index of the position of the letter in the edit mask.
	 * @return string							The next letter.
	 */
	protected function getNextAnswerLetter($i_strAnswerText, $i_strEditMask, &$c_intIndex, &$c_intIndexEditMask) {

		// length of the letter
		$l_intLetterLength = 0;
		// length of answer
		$l_intAnswerLength = 0;
		// the next letter of the answer
		$l_strAnswerLetter = '';
		
		$l_intAnswerLength = strlen(utf8_decode($i_strAnswerText));
		
		// if we are at the end of the answer
		if ($l_intAnswerLength <= $c_intIndex) {
			return '';
		}
		
		// Determine the length of the current letter in the index position
		$l_intLetterLength = $this->getLengthOfCurrentLetter($i_strEditMask, $c_intIndexEditMask);
		
		// cut the letter
		$l_strAnswerLetter = mb_substr($i_strAnswerText, $c_intIndex, $l_intLetterLength, 'UTF-8');
		
		// shift the index to the next letter
		$c_intIndex += $l_intLetterLength;
		// shift the index of the edit mask always 1 letter to the right
		$c_intIndexEditMask += 1;
		
		// return the answer letter
		return $l_strAnswerLetter;
	}
	
	
	/**
	 * Determine the length of the current letter in the index position
	 * @param string $i_strEditMask 	The edit mask.
	 * @param integer $i_intIndex		Index of the position in the answer.
	 * @return integer					The length of the current letter.
	 */	
	protected function getLengthOfCurrentLetter($i_strEditMask, $i_intIndex) {
		
		// length of the edit mask
		$l_intEditMaskLength = 0;
		
		$l_intEditMaskLength = strlen( $i_strEditMask );
		
		// if edit mask shorter then the answer string OR if edit mask is initial
		if ( $l_intEditMaskLength < ($i_intIndex + 1) || $i_strEditMask == 0) {
			// then return the default value 1
			return 1;
		
		// if edit mask greater or equal the answer string
		} else {

			// cut the length of this answer letter
			return substr($i_strEditMask, $i_intIndex, 1);
		}
	}
	
	/**
	 * Determine the vectors for the direction of the answer
	 * @param integer $i_intDirection The direction of the answer.
	 * @param array $e_arrStartVector The vectro where the anser starts.
	 * @param array $e_arrDirectionVector The vector for the direction in the crossword.
	 */
	protected function getDirectionVector($i_intDirection, &$e_arrStartVector, &$e_arrDirectionVector) {
		
		// determine first the start vector
		switch (true) {
			// if there is a direction which starts top
			case (	$i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_TOP 
				 || $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_TOP_LEFT 
				 || $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_TOP_RIGHT ):

				$e_arrStartVector = array('X' => 0,
										  'Y' => -1 );
				break;

			// if there is a direction which starts right
			case (	$i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_RIGHT 
				 || $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_DOWN 
				 || $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_TOP ):

				$e_arrStartVector = array('X' => 1,
										  'Y' => 0 );
				break;
				
			// if there is a direction which starts down
			case (	$i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_DOWN 
				 || $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_DOWN_LEFT 
				 || $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_DOWN_RIGTH ):

				$e_arrStartVector = array('X' => 0,
										  'Y' => 1 );
				break;
				
			// if there is a direction which starts left
			case (	$i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_LEFT
				 || $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_LEFT_DOWN 
				 || $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_LEFT_TOP ):

				$e_arrStartVector = array('X' => -1,
										  'Y' => 0 );
				break;
		}
		
		// determine the direction vector
		switch (true) {
			// if there is a direction to the top
			case ($i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_LEFT_TOP
				|| $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_TOP
				|| $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_TOP):
			
				$e_arrDirectionVector = array('X' => 0,
										  'Y' => -1 );
				break;
			
			
			// if there is a direction to the right
			case (   $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_RIGHT
				|| $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_TOP_RIGHT
				|| $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_DOWN_RIGTH ):
			
				$e_arrDirectionVector = array('X' => 1,
										  'Y' => 0 );
				break;

			// if there is a direction to the bottom
			case (  $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_DOWN
				|| $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_DOWN
				|| $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_LEFT_DOWN ):
			
				$e_arrDirectionVector = array('X' => 0,
										  'Y' => 1 );
				break;
		
			// if there is a direction to the left
			case (  $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_LEFT
				|| $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_TOP_LEFT
				|| $i_intDirection == GlcrosswordBoxQuestions::C_INT_DIR_DOWN_LEFT ):
			
				$e_arrDirectionVector = array('X' => -1,
										  'Y' => 0 );
				break;
		}
	}
	
	/**
	 * Deliver all data from the causing question f�r the edit matrix
	 * @param GlcrosswordBox 	$i_objCurrentBox		The current box, for which one we compute all the data.
	 * @param array 				$i_arrCausingQuestion	The causing question
	 * 														(See constants with GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_*)
	 * @return array 										First Index: 	Direction of the text.
	 * 														Second Index: 	Side of the current box where the text is going on.
	 * 														Value: 			Length of the text in this direction.
	 */
	protected function getCausingQuestionData($i_objCurrentBox, $i_arrCausingQuestion) {
		
		// The current box in the crossword.
		/* @var $l_objQuestionBox GlcrosswordBoxQuestions */ 
		$l_objQuestionBox = NULL;
		// the edit direction of the causing question
		$l_intEditDirection = 0;
		// both affected sides
		$l_intSide1 = 0;
		$l_intSide2 = 0;
		// the returning array
		$l_arrQuestionData = array();
		
		// get the question box from the causing question array
		$l_objQuestionBox = $i_arrCausingQuestion[GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_QUESTION];
		
		// translate the direction of the causing question in the direction of the edit matrix
		$l_intEditDirection = $this->convertQDir2EDir(
								$i_arrCausingQuestion[GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_DIRECTION]);
		
		// get the both affected sides
		$this->getSideForEditMatrix($l_intEditDirection, $l_intSide1, $l_intSide2);
		
		// compute for side 1 the length
		$l_arrQuestionData[$l_intEditDirection][$l_intSide1] = 
				   $this->getLengthForEditMatrix($l_intSide1,
											   $l_intEditDirection, 
											   $i_arrCausingQuestion[GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_DIRECTION], 
											   $i_objCurrentBox, 
											   $l_objQuestionBox);
		
		// compute for side 2 the length
		$l_arrQuestionData[$l_intEditDirection][$l_intSide2] = 
					$this->getLengthForEditMatrix($l_intSide2,
												$l_intEditDirection,
												$i_arrCausingQuestion[GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_DIRECTION],
												$i_objCurrentBox,
												$l_objQuestionBox);
		
		// return the array with the data
		return $l_arrQuestionData; 
	}
	
	/**
	 * Convert question direction in the direction for the edit Matrix
	 * @param 	integer 	$i_intQuestionDirection	Direction from the question box (See constants with GlcrosswordBox::C_INT_EDIT_DIR_*)
	 * @return	integer 							Direction for the edit Matrix (See constant with GlcrosswordBox::C_INT_EDIT_DIR_*)
	 */
	protected function convertQDir2EDir($i_intQuestionDirection) {
		
		// the returning value with the direction of the edit Matrix
		$l_intEditDirection = 0;
		
		switch ( $i_intQuestionDirection) {
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP_LEFT:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_LEFT;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_TOP;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP_RIGHT:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_RIGHT;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_TOP:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_TOP;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_RIGHT;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_DOWN:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_BOTTOM;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_RIGTH:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_RIGHT;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_BOTTOM;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_LEFT:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_LEFT;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_DOWN:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_BOTTOM;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_LEFT;
				break;
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_TOP:
				$l_intEditDirection = GlcrosswordBox::C_INT_EDIT_DIR_TOP;
				break;
		}
		
		// give back the converted direction
		return $l_intEditDirection;
	}
	
	/**
	 * Convert the question direction into the pure direction.
	 * 
	 * @param 	integer $i_intQuestionDirection The question direction.
	 * @return	integer							The pure direction.
	 */
	protected function convertDirection2PureDir($i_intQuestionDirection) {
		switch ($i_intQuestionDirection) {
			// if the question goes to the left
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_LEFT:
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT:
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP_LEFT:
		
				// return the left direction
				return GlcrosswordBoxQuestions::C_INT_DIR_LEFT;
		
			// if the question goes to the top
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_TOP:
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP:
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_TOP:
					
				// return the top direction
				return GlcrosswordBoxQuestions::C_INT_DIR_TOP;
						
			// if the question goes to the right
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP_RIGHT:
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT:
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_RIGTH:
					
				// return the right direction
				return GlcrosswordBoxQuestions::C_INT_DIR_RIGHT;
						
			// if the question goes to the bottom
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_DOWN:
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN:
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_DOWN:
					
				// return the down direction
				return GlcrosswordBoxQuestions::C_INT_DIR_DOWN;
		}
		
	}
	
	/**
	 * Returns the both side wich are affecting with this direction.
	 * @param integer $i_intEditDir	Direction for the edit Matrix. See constants with GlcrosswordBox::C_INT_EDIT_DIR_*
	 * @param integer $e_intSide1	Affected side 1. (See constants with GlcrosswordBox::C_INT_EDIT_SIDE_*)
	 * @param integer $e_intSide2	Affected side 2. (See constants with GlcrosswordBox::C_INT_EDIT_SIDE_*)
	 */
	protected function getSideForEditMatrix($i_intEditDir, &$e_intSide1, &$e_intSide2) {
		
		// which edit direction is it		
		switch ($i_intEditDir) {
			case GlcrosswordBox::C_INT_EDIT_DIR_TOP :
				$e_intSide1 = GlcrosswordBox::C_INT_EDIT_SIDE_BOTTOM;
				$e_intSide2 = GlcrosswordBox::C_INT_EDIT_SIDE_TOP;
				break;
			case GlcrosswordBox::C_INT_EDIT_DIR_RIGHT :
				$e_intSide1 = GlcrosswordBox::C_INT_EDIT_SIDE_LEFT;
				$e_intSide2 = GlcrosswordBox::C_INT_EDIT_SIDE_RIGHT;
				break;
			case GlcrosswordBox::C_INT_EDIT_DIR_BOTTOM :
				$e_intSide1 = GlcrosswordBox::C_INT_EDIT_SIDE_TOP;
				$e_intSide2 = GlcrosswordBox::C_INT_EDIT_SIDE_BOTTOM;
				break;
					case GlcrosswordBox::C_INT_EDIT_DIR_LEFT :
				$e_intSide1 = GlcrosswordBox::C_INT_EDIT_SIDE_RIGHT;
				$e_intSide2 = GlcrosswordBox::C_INT_EDIT_SIDE_LEFT;
				break;
		}
	}
	
	
	
	/**
	 * Returns the length of the equested direction an side for the current box..
	 * @param	integer						$i_intEditSide			Side from the edit Matrix. 
	 * 																(See constants with GlcrosswordBox::C_INT_EDIT_SIDE_*)
	 * @param	integer						$i_intEditDirection		Direction of the edit Matrix.
	 * 																(See constants with GlcrosswordBox::C_INT_EDIT_DIR_*)
	 * @param	integer						$i_intQuestionDirection	Actual direction of the question.
	 * 																(See constants with GlcrosswordBoxQuestions::C_INT_DIR_*)
	 * @param 	GlcrosswordBox			$i_objCurrentBox		The current box.
	 * @param 	GlcrosswordBoxQuestions $i_objCausingQuestion	The causing question which is affection the current box.
	 * @return	integer												Length of the requested side an direction.
	 */
	protected function getLengthForEditMatrix($i_intEditSide, $i_intEditDirection, $i_intQuestionDirection, 
											$i_objCurrentBox, $i_objCausingQuestion) {
		
		// the requested length
		$l_intLength = 0;
		
		// which side is requested
		switch ( $i_intEditSide ) {
			
			// if the top side is requested
			case GlcrosswordBox::C_INT_EDIT_SIDE_TOP :
				
				// if from the frontside
				if ($i_intEditDirection == GlcrosswordBox::C_INT_EDIT_DIR_BOTTOM) {
					// get the length from the frontside
					$l_intLength = $this->getFrontsideLength($i_objCurrentBox->get_intY(), 
															 $i_objCausingQuestion->get_intY(), 
															 $i_objCurrentBox->get_intX(), 
															 $i_objCausingQuestion->get_intX());
				}
				// from the backside
				else {
					// get the length from the backside
					$l_intLength = $this->getBacksideLength($i_objCurrentBox->get_intY(), 
															$i_objCausingQuestion->get_intY(), 
															$i_objCurrentBox->get_intX(), 
															$i_objCausingQuestion->get_intX(),
															$i_objCausingQuestion->get_objQuestion($i_intQuestionDirection)->get_intActualLength() );
				}
				
				break;

			// if the right side is requested
			case GlcrosswordBox::C_INT_EDIT_SIDE_RIGHT :

				// if from the frontside
				if ($i_intEditDirection == GlcrosswordBox::C_INT_EDIT_DIR_LEFT) {
					// get the length from the frontside
					$l_intLength = $this->getFrontsideLength($i_objCurrentBox->get_intX(), 
															 $i_objCausingQuestion->get_intX(), 
															 $i_objCurrentBox->get_intY(), 
															 $i_objCausingQuestion->get_intY());
				}
				// from the backside
				else {
					// get the length from the backside
					$l_intLength = $this->getBacksideLength($i_objCurrentBox->get_intX(), 
															$i_objCausingQuestion->get_intX(), 
															$i_objCurrentBox->get_intY(), 
															$i_objCausingQuestion->get_intY(),
															$i_objCausingQuestion->get_objQuestion($i_intQuestionDirection)->get_intActualLength() );
				}
				
				break;

			// if the bottom side is requested
			case GlcrosswordBox::C_INT_EDIT_SIDE_BOTTOM :

				// if from the frontside
				if ($i_intEditDirection == GlcrosswordBox::C_INT_EDIT_DIR_TOP) {
					// get the length from the frontside
					$l_intLength = $this->getFrontsideLength($i_objCurrentBox->get_intY(), 
                                                             $i_objCausingQuestion->get_intY(),
                                                             $i_objCurrentBox->get_intX(), 
															 $i_objCausingQuestion->get_intX());
				}
				// from the backside
				else {
					// get the length from the backside
					$l_intLength = $this->getBacksideLength($i_objCurrentBox->get_intY(), 
															$i_objCausingQuestion->get_intY(), 
															$i_objCurrentBox->get_intX(), 
															$i_objCausingQuestion->get_intX(),
															$i_objCausingQuestion->get_objQuestion($i_intQuestionDirection)->get_intActualLength() );
				}
				
				break;

			// if the left side is requested
			case GlcrosswordBox::C_INT_EDIT_SIDE_LEFT :

				// if from the frontside
				if ($i_intEditDirection == GlcrosswordBox::C_INT_EDIT_DIR_RIGHT) {
					// get the length from the frontside
					$l_intLength = $this->getFrontsideLength($i_objCurrentBox->get_intX(), 
															 $i_objCausingQuestion->get_intX(), 
															 $i_objCurrentBox->get_intY(), 
															 $i_objCausingQuestion->get_intY());
				}
				// from the backside
				else {
					// get the length from the backside
					$l_intLength = $this->getBacksideLength($i_objCurrentBox->get_intX(), 
															$i_objCausingQuestion->get_intX(), 
															$i_objCurrentBox->get_intY(), 
															$i_objCausingQuestion->get_intY(),
															$i_objCausingQuestion->get_objQuestion($i_intQuestionDirection)->get_intActualLength() );
				}
				
				break;
		}
		
		// return the requested length
		return $l_intLength;
	}
	
	/**
	 * Returns the lengths for the edit Matrix of the frontside. 
	 * @param integer $i_intCurrentLengthCoord	Coordinate value of the length coordinate of the current box.
	 * @param integer $i_intQuestionLengthCoord	Coordinate value of the length coordinate of the question box.
	 * @param integer $i_intCurrentSideCoord	Coordinate value of the side coordinate of the current box.
	 * @param integer $i_intQuestionSideCoord	Coordinate value of the side coordinate of the question box.
	 * @return	integer 						The requested length.
	 */
	protected function getFrontsideLength($i_intCurrentLengthCoord, $i_intQuestionLengthCoord,
										  $i_intCurrentSideCoord, $i_intQuestionSideCoord) {
		
		// the length to compute
		$l_intLength = 0;
		
		$l_intLength = abs($i_intCurrentLengthCoord - $i_intQuestionLengthCoord);
		
		// if the both ar on the same row/column
		if ($i_intCurrentSideCoord == $i_intQuestionSideCoord) {
			// then is the length one shorter
			$l_intLength -= 1;
		}
		
		// return the length
		return $l_intLength;
	}
	
	/**
	 * Returns the lengths for the edit Matrix of the backside.
	 * @param integer $i_intCurrentLengthCoord	Coordinate value of the length coordinate of the current box.
	 * @param integer $i_intQuestionLengthCoord	Coordinate value of the length coordinate of the question box.
	 * @param integer $i_intCurrentSideCoord	Coordinate value of the side coordinate of the current box.
	 * @param integer $i_intQuestionSideCoord	Coordinate value of the side coordinate of the question box.
	 * @param integer $i_intLengthOfAnswer		The length of the whole answer.
	 * @return integer							The requested length.
	 */
	protected function getBacksideLength($i_intCurrentLengthCoord, $i_intQuestionLengthCoord,
										  $i_intCurrentSideCoord, $i_intQuestionSideCoord,
										  $i_intLengthOfAnswer) {
		// the requested length
		$l_intBacksideLength = 0;
		// the frontside length
		$l_intFrontsideLength = 0;
		
		// get the length of the frontside
		$l_intFrontsideLength = $this->getFrontsideLength($i_intCurrentLengthCoord, $i_intQuestionLengthCoord, 
														  $i_intCurrentSideCoord, $i_intQuestionSideCoord);
		
		// compute the backside length
		$l_intBacksideLength = $i_intLengthOfAnswer - $l_intFrontsideLength - 1;

		// return the length
		return $l_intBacksideLength;
	}
	
	/**
	 * Merge both array into one array for the edit Matrix. Both arrays have the folltowing structure
	 * First index:		Direction of the text
	 * Second index: 	Side of the current box
	 * Value:			Length of the text on this side in this direction. 
	 * @param array $i_arrOldArray	The old array with the data.
	 * @param array $i_arrNewArray	The new array with the new data.
	 */
	protected function mergeEditMatrixArrays($i_arrOldArray, $i_arrNewArray) {
		
		// the merged array
		$l_arrMergedData = array(); 
		
		// first assign the old array to the merged array
		$l_arrMergedData = $i_arrOldArray;
		
		// go through every element in the new array
		foreach ($i_arrNewArray as $l_intDirKey => $l_arrDir) {
			foreach ($l_arrDir as $l_intSideKey => $l_intlength) {
				// and transfer it to the merged array
				$l_arrMergedData[$l_intDirKey][$l_intSideKey] = $l_intlength;
			}
		}
		
		// return the merged array
		return $l_arrMergedData;
	}
	
	/**
	 * Read the text for the solution button.
	 * @return string The text of the button.
	 */
	protected function getStrButtonSolution() {
	    return LocalizationUtility::translate('text.button.solution',
	                                           GlcrosswordController::c_strExtensionName );
	}
	
	/**
	 * Read the text for the hint button.
	 * @return string The text of the button.
	 */
	protected function getStrButtonHint() {
	    return LocalizationUtility::translate('text.button.hint',
	                                           GlcrosswordController::c_strExtensionName );
	}
}
?>