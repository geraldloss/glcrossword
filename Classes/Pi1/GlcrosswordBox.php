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



/**
 * Abstract class to define a box of the crossword
 *
 * @author	Gerald Loﬂ <gerald.loss@gmx.de>
 * @package	glcrossword
 */
abstract class GlcrosswordBox  {
	//	***************************************************************************************************
	//	Class constants part
	//	***************************************************************************************************
	
	/**
	 * Box type of questions
	 * @var string
	 */
	const C_STR_TYPE_QUESTION = 'question';
	/**
	 * Box type of answer
	 * @var string
	 */
	const C_STR_TYPE_ANSWER = 'answer';
	/**
	 * Box type of emtyp box
	 * @var string
	 */
	const C_STR_TYPE_EMPTY = 'empty';
	/**
	 * The general box size.
	 * @var integer
	 */
	const C_INT_BOX_SIZE = 60;
	
	// Constants for the direction of the edit matrix
	/**
	 * To the top.
	 * @var integer
	 */
	const C_INT_EDIT_DIR_TOP = 0;
	/**
	 * To the left.
	 * @var integer
	 */
	const C_INT_EDIT_DIR_RIGHT = 1;
	/**
	 * To the bottom.
	 * @var integer
	 */
	const C_INT_EDIT_DIR_BOTTOM = 2;
	/**
	 * To the left
	 * @var integer
	 */
	const C_INT_EDIT_DIR_LEFT = 3;
	/**
	 * Max value of the direction constants, of the edit matrix.
	 * @var integer
	 */
	const C_INT_EDIT_DIR_MAX = 4;
	
	
	// Constants for the side of the edit matrix
	/**
	 * Top.
	 * @var integer
	 */
	const C_INT_EDIT_SIDE_TOP = 0;
	/**
	 * Right.
	 * @var integer
	 */
	const C_INT_EDIT_SIDE_RIGHT = 1;
	/**
	 * Bottom.
	 * @var integer
	 */
	const C_INT_EDIT_SIDE_BOTTOM = 2;
	/**
	 * Left
	 * @var integer
	 */
	const C_INT_EDIT_SIDE_LEFT = 3;
	/**
	 * Max value of the side constants, of the edit matrix.
	 * @var integer
	 */
	const C_INT_EDIT_SIDE_MAX = 4;
	/**
	 * Identifier for the fieldlength in the edit matrix
	 * @var string
	 */
	const C_STR_EDIT_FIELDLENGTH = 'fieldlength';
	/**
	 * Identifier for the directions array in the edit matrix
	 * @var array
	 */
	const C_STR_EDIT_DIRECTIONS = 'directions';
	
//	***************************************************************************************************
//	Class attributes part
//	***************************************************************************************************
	
	/**
	 * Array with an list of the priorities of the box types
	 * @var array
	 */
	private static  $m_arrTypePriorityList = array(
			GlcrosswordBox::C_STR_TYPE_QUESTION => 0,
			GlcrosswordBox::C_STR_TYPE_ANSWER => 1,
			GlcrosswordBox::C_STR_TYPE_EMPTY => 2 );
	
	/**
	 * The crossword where this box belongs to.
	 * @var GlcrosswordCrossword
	 * @access protected
	 */
	protected $m_objCrossword;
	
	/**
	 * Array with the actual content of the box
	 * @var array 
	 * @access protected
	 */
	protected $m_arrContent;
	
	/**
	 * Array with flags if an out of bound error for the given direction is already set
	 * @var array
	 * @access protected
	 */
	protected $m_arrOutOfBoundErrors;
	
	/**
	 * X position of the box in the crossword
	 * @var integer
	 * @access protected
	 */
	protected $m_intX;
	/**
	 * Y position of the box in the crossword
	 * @var integer
	 * @access protected
	 */
	protected $m_intY;
	
	/**
	 * Is there an error in this box
	 * @var boolean
	 * @access protected
	 */
	protected $m_blnIsError;
	
	/**
	 * Error text
	 * @var string
	 * @access protected 
	 */
	protected $m_strErrorText;
	
	/**
	 * Type of this box -> see C_STR_TYPE* constants
	 * @var string
	 * @access protected
	 */
	protected $m_strType;
	
	/**
	 * The unique ID of the crossword
	 * @var integer
	 * @access protected
	 */
	protected $m_intUniqueId;
	
//	***************************************************************************************************	
//	Static methods part
//	***************************************************************************************************
	
	/**
	 * Returns Informations of the causing question.
	 * @param GlcrosswordBox 			$i_objBox 					Box which causes the question.
	 * @param string 						$e_strQuestionText			Text of the causing question.
	 * @param integer 						$e_intUID 					UID of the causing question.
	 * @param integer						$e_intActualLength 			The actual length of the answer of the causing question
	 * @param GlcrosswordBoxQuestions	$e_objCausingQuestionBox	Object with the causing question
	 * @param integer						$e_intCausingDirection		Direction of the causing question
	 */
	public static function getCausingQuestionInformation($i_objBox, &$e_strQuestionText, &$e_intUID, &$e_intActualLength, 
														 &$e_objCausingQuestionBox, &$e_intCausingDirection) {
		// content of the causing question
		/* @var $l_objContentQuestion GlcrosswordContentQuestion */
		$l_objContentQuestion = NULL;
		// array with the box of the causing question and the direction of the question in this box
		/* @var $l_arrCausingQuestion array() */
		$l_arrCausingQuestion = array();
		// direction of the causing question
	
		switch ($i_objBox->m_strType) {
			// if it is the type question
			case GlcrosswordBox::C_STR_TYPE_QUESTION:
				$l_objContentQuestion = $i_objBox->getFirstQuestion($e_intCausingDirection);
				// return the question text
				$e_strQuestionText = $l_objContentQuestion->get_strQuestion();
				// return the UID
				$e_intUID = $l_objContentQuestion->get_intUid();
				// get actual length
				$e_intActualLength = $l_objContentQuestion->get_intActualLength(); 
				// return this box as the causing question box
				$e_objCausingQuestionBox = $i_objBox;
				break;
					
			// if the type is answer
			case GlcrosswordBox::C_STR_TYPE_ANSWER:
				// get the first causing question in an array
				$l_arrCausingQuestion = $i_objBox->getFirstCausingQuestion();
				// read the causing question object
				$e_objCausingQuestionBox = $l_arrCausingQuestion[GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_QUESTION];
				// read the direction auf the causing question in the question object
				$e_intCausingDirection = $l_arrCausingQuestion[GlcrosswordBoxAnswer::C_STR_KEY_CAUSING_DIRECTION];
				// read the content object
				$l_objContentQuestion = $e_objCausingQuestionBox->get_objQuestion($e_intCausingDirection);
				// get the question text
				$e_strQuestionText = $l_objContentQuestion->get_strQuestion();
				// get the UID
				$e_intUID = $l_objContentQuestion->get_intUid();
				// get the acutal length
				$e_intActualLength = $l_objContentQuestion->get_intActualLength();
				// return the question object 
				$e_objCausingQuestionBox = $e_objCausingQuestionBox; 
				break;

			// if empty box (this should never happen)
			case GlcrosswordBox::C_STR_TYPE_EMPTY:
				$e_strQuestionText = '';
				$e_intUID = 0;
				$e_intActualLength = 0;
				$e_objCausingQuestionBox = NULL;
				break;
				
		}
	}
		
	/**
	 * Returns true, if new type has a higher priority.
	 * @param string 	$i_strOldType	Old type.
	 * @param string 	$i_strNewType	New type.
	 * @return boolean 					True, if new type has higher priority.
	 */
	public static function typeHasHigherPriority($i_strOldType, $i_strNewType) {
		// if the old type has a lower priority
		if (GlcrosswordBox::$m_arrTypePriorityList[$i_strOldType] > GlcrosswordBox::$m_arrTypePriorityList[$i_strNewType]) {
			return true;
		} else {
			return FALSE;
		}
	}
	
	//	***************************************************************************************************
	//	Constructor part
	//	***************************************************************************************************
	
	/**
	 * Constructor of this class
	 * @param integer 					$i_intX 		X position of the box in the crossword
	 * @param integer 					$i_intY 		Y position of the box in the crossword
	 * @param string					$i_strType		Type of the box see C_STR_TYPE* constants
	 * @param GlcrosswordCrossword	$i_objCrossword	Object of Crossword to which this box belongs.
	 */
	protected function __construct($i_intX, $i_intY, $i_strType, $i_objCrossword) {
		$this->m_intX = $i_intX;
		$this->m_intY = $i_intY;
		$this->m_strType = $i_strType;
		$this->m_objCrossword = $i_objCrossword;
		$this->m_blnIsError = FALSE;
		$this->m_strErrorText = '';
		
		// initialize the out of bound error flag array
		for ($i = 0; $i <= GlcrosswordBoxQuestions::C_INT_DIR_MAX; $i++) {
			$this->m_arrOutOfBoundErrors[$i] = false;
		}
		
	}

//	***************************************************************************************************	
//	Public methods part
//	***************************************************************************************************
	
	/**
	 * Adds a content object to the box
	 * @param integer 	$i_intIndex Index for the array
	 * @param mixed 	$i_objContent Content object
	 * @return boolean 	True if content obejct successfully added; False if index
	 * 					already exists.
	 */
	public function addContent($i_intIndex, $i_objContent) {
		
		// if content object already exists
		if (isset($this->m_arrContent[$i_intIndex])) {
			// exit with FALSE
			return FALSE;
		
		// if content object still not exists
		} else {
			// add this object
			$this->m_arrContent[$i_intIndex] = $i_objContent;
			// exit with true
			return true;
		}
	} 
	
	
	/**
	 * Get the Contentobject of a certain index.
	 * @param integer $i_intIndex Index of the Contentobject
	 * @return mixed Contentobject
	 */
	public function getContentObject($i_intIndex) {
		return $this->m_arrContent[$i_intIndex];
	}
	
	/**
	 * Getter of the array with the content of this box.
	 * @return array:
	 */
	public function getContentArray() {
		return  $this->m_arrContent;
	}
	
	/**
	 * Get X position of the box in the crossword
	 * @return integer 
	 */
	public function get_intX() {
		return $this->m_intX;
	}
	
	/**
	 * Get Y position of the box in the crossword
	 * @return integer 
	 */
	public function get_intY() {
		return $this->m_intY;
	}
	
	/**
	 * Get the information if there is an error in this box
	 * @return boolean
	 */
	public function get_blnIsError() {
		return $this->m_blnIsError;
	}
	
	/**
	 * Set the information if there is an error in this box
	 * @param boolean $i_blnValue Value of this property
	 */
	public function set_blnIsError($i_blnValue) {
		$this->m_blnIsError = $i_blnValue;
		
		// if there is an error
		if ($i_blnValue) {
			$this->m_objCrossword->set_blnIsError($i_blnValue);
		}
	}

	/**
	 * Get the error text if there is an error
	 * @return string 
	 */
	public function get_strErrorText() {
		return $this->m_strErrorText;
	}
	
	/**
	 * Set the error text if there is an error.
	 * @param string 	$i_strValue 				Value of the error text
	 * @param integer	$i_intOutOfBoundDirection	Direction of the out of bound error
	 */
	public function set_strErrorText($i_strValue, $i_intOutOfBoundDirection = -1) {
		
		// if hte out of bound direction is set
		if ($i_intOutOfBoundDirection != -1) {
			// check the array if we have already set this error text
			if ($this->m_arrOutOfBoundErrors[$i_intOutOfBoundDirection]) {
				// then leave this method for preventing doublet
				return;
			} else {
				// set the flag for the first time
				$this->m_arrOutOfBoundErrors[$i_intOutOfBoundDirection] = true;
			}
		}
		
		// if error text is empty
		if ($this->m_strErrorText == '') {
			// add the value
			$this->m_strErrorText = $i_strValue;
		
		// if there exist already an error text
		} else {
			// this error text to the existing
			$this->m_strErrorText = $this->m_strErrorText . 
									'<br>--------------------<br>' .
									$i_strValue; 
		}
	} 
	
	/**
	 * Get the type of this box -> see C_STR_TYPE* constants
	 * @return string 
	 */
	public function get_strType() {
		return $this->m_strType;
	}
	
	/**
	 * Getter for the crossword.
	 * @return GlcrosswordCrossword
	 */
	public function get_crossword() {
		return $this->m_objCrossword;
	}

	/**
	 * Draws a  box in HTML, This is only a pattern. Use the methods of the inherited box classes.
	 * @param float $i_fltXScale Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float $i_fltYScale Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer $i_intBorderWidth Thickness of the borderlines of the box
	 * @return string	Returns the generated HTML content.
	 */
	public function draw( $i_fltXScale, $i_fltYScale, $i_intBorderWidth) {
		// the HTML content of the qestion box
		$l_strContent = '';
	
		// get the plain box
		$l_strContent = $this->getSingleBox($i_fltXScale, $i_fltYScale, $i_intBorderWidth, '');
	
		return $l_strContent;
	}
	
	/**
	 * Draws a an error box in HTML for the out of bound errors
	 * @param integer	$i_intErrorCount			Count of the error.
	 * @param float 	$i_fltXScale 				Scale of the boxes 1 means 100%, 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 				Scale of the boxes 1 means 100%, 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth 			Thickness of the borderlines of the box
	 * @param integer 	$i_intTopSpace				The space from the top of the crossword for drawing the 
	 * 												first out of bounds error boxes.
	 * @return string								Returns the generated HTML content.
	 */
	public function drawErrorBox( $i_intErrorCount, $i_fltXScale, $i_fltYScale, $i_intBorderWidth, 
								  $i_intTopSpace ) {
		// the HTML content of the qestion box
		$l_strContent = '';
		// the properties of the box
		$l_intLeft = 0;
		$l_intTop = 0;
		$l_intWidth = 0;
		$l_intHeight = 0;
	
		// get the properties of the box
		$this->getErrorBoxProperties($i_intErrorCount, $i_fltXScale, $i_fltYScale, $i_intBorderWidth, 
									 $i_intTopSpace, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
		
		// get the HTML content of this error box
		$l_strContent = $this->getSingleErrorBox($i_intErrorCount, $l_intLeft, $l_intTop, $l_intWidth, 
												 $l_intHeight, $i_intBorderWidth);
		
		return $l_strContent;
	}
	
	/**
	 * Get the coordinates of the box from this box in the given direction with the given offset.
	 * 
	 * @param integer $i_intDirection	Direction for the search.
	 * 									See constants with the prefix GlcrosswordBoxQuestions::C_INT_DIR_*
	 * @param integer $i_intOffset		Offset from this box.
	 * @return	GlcrosswordBox		Box with the x and y coordinates of the wanted box.
	 */
	public function getBoxFromOffset($i_intDirection, $i_intOffset) {
		
		$l_arrNewCoord = array( "x" => 0,
								"y" => 0);
		
		// set the new coordinates to the coordinates of this box
		$l_arrNewCoord["x"] = $this->m_intX;
		$l_arrNewCoord["y"] = $this->m_intY;
		
		switch ($i_intDirection) {
			// if direction top to the left
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP_LEFT:
				$l_arrNewCoord["x"] -= $i_intOffset - 1;
				$l_arrNewCoord["y"] -= 1;
				break;
			
			// if direction top 
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP:
				$l_arrNewCoord["y"] -= $i_intOffset;
				break;

			// if direction top to the right
			case GlcrosswordBoxQuestions::C_INT_DIR_TOP_RIGHT:
				$l_arrNewCoord["x"] += $i_intOffset - 1;
				$l_arrNewCoord["y"] -= 1;
				break;
				
			// if direction right to the top
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_TOP:
				$l_arrNewCoord["x"] += 1;
				$l_arrNewCoord["y"] -= $i_intOffset - 1;
				break;
					
				// if direction right
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT:
				$l_arrNewCoord["x"] += $i_intOffset;
				break;
			
			// if direction right to the bottom
			case GlcrosswordBoxQuestions::C_INT_DIR_RIGHT_DOWN:
				$l_arrNewCoord["x"] += 1;
				$l_arrNewCoord["y"] += $i_intOffset - 1;
				break;
				
			// if direction down to the right
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_RIGTH:
				$l_arrNewCoord["x"] += $i_intOffset - 1;
				$l_arrNewCoord["y"] += 1;
				break;
					
			// if direction down
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN:
				$l_arrNewCoord["y"] += $i_intOffset;
				break;
			
			// if direction down to left
			case GlcrosswordBoxQuestions::C_INT_DIR_DOWN_LEFT:
				$l_arrNewCoord["x"] -= $i_intOffset - 1;
				$l_arrNewCoord["y"] += 1;
				break;
				
			// if direction left to the bottom
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_DOWN:
				$l_arrNewCoord["x"] -= 1;
				$l_arrNewCoord["y"] += $i_intOffset - 1;
				break;
					
			// if direction left
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT:
				$l_arrNewCoord["x"] -= $i_intOffset;
				break;
			
			// if direction left to the top
			case GlcrosswordBoxQuestions::C_INT_DIR_LEFT_TOP:
				$l_arrNewCoord["x"] -= 1;
				$l_arrNewCoord["y"] -= $i_intOffset - 1;
				break;
		}
		
		return $this->m_objCrossword->getBox($l_arrNewCoord["x"], $l_arrNewCoord["y"]);
	}
	
	/**
	 * Get the size of the html element of this box.
	 * @param 	float $i_fltXScale	Scale factor for the x range. 0.5 means 50%.
	 * @param 	float $i_fltYScale	Scale factor for the y range. 0.5 means 50%.
	 * @param 	bool  $i_blnNoRound	Don't round the result. (default is false)
	 * @return 	array				Array with the width and the heigth of the HTML element of this box.
	 */
	public function getGeneralBoxSize($i_fltXScale, $i_fltYScale, $i_blnNoRound = false){
		
		// the returning array
		$l_arrSize = array();
		
		// compute the size
		$l_arrSize['width'] = (GlcrosswordBox::C_INT_BOX_SIZE * $i_fltXScale);
		$l_arrSize['height'] = (GlcrosswordBox::C_INT_BOX_SIZE * $i_fltYScale);
		
		// if rounding is requestet
		if ($i_blnNoRound == true) {
			// round the result
			$l_arrSize['width'] = round($l_arrSize['width']);
			$l_arrSize['height'] = round($l_arrSize['height']);
		}
		
		// return the size
		return $l_arrSize; 
	}
	
//	***************************************************************************************************	
//	Protected methods part
//	***************************************************************************************************
	
	protected function getSingleErrorBox($i_intErrorCount, $i_intLeft, $i_intTop, $i_intWidth, 
										 $i_intHeight, $i_intBorderWidth) {
		// the ID of the box
		$l_strBoxId = '';
		// the html content of this box
		$l_strHtmlContent = '';
		
		// create the id of this box
		$l_strBoxId = 'error' . $i_intErrorCount . '_' . $this->m_objCrossword->get_intUniqueId();

		// the comment of thies box
		$l_strHtmlContent = "\n\t\t\t" . '<!-- ' . $this->get_strType() . ' error ' . $i_intErrorCount . 
											' ' . $this->m_intX . 'x' . $this->m_intY . ' -->' . "\n";
		
		// create the div element with all parameters
		$l_strHtmlContent .= "\t\t\t" . '<div class="glcrossword_error_oob" ';
		$l_strHtmlContent .= 'id="' . $l_strBoxId . '" title style="left: ';
		$l_strHtmlContent .= $i_intLeft . 'px; top: ' . $i_intTop . 'px; width: ' . $i_intWidth . 'px; ';
		$l_strHtmlContent .= 'height: ' . $i_intHeight . 'px; border-width: ' . $i_intBorderWidth . 'px; ">';
		$l_strHtmlContent .= '</div>';
		$l_strHtmlContent .= "\n";
		
		return $l_strHtmlContent;
	}
	
	/**
	 * Deliver the porperties of the error box for the HTML CSS configuration
	 *
	 * @param integer $i_intErrorCount				Count of the error.
	 * @param float   $i_fltXScale 					Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float   $i_fltYScale 					Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer $i_intBorderWidth 			Thickness of the borderlines of the box
	 * @param integer $i_intTopSpace				The space from the top of the crossword for drawing the 
	 * 												first out of bounds error boxes.
	 * @param integer &$e_intLeft 					coordinates from the left side.
	 * @param integer &$e_intTop 					coordinates from the top.
	 * @param integer &$e_intWidth 					Width of the box.
	 * @param integer &$e_intHeight 				Height of the box.
	 * @return string 								HTML content of a single box
	 */
	protected function getErrorBoxProperties($i_intErrorCount, $i_fltXScale, $i_fltYScale, $i_intBorderWidth,
											 $i_intTopSpace, &$e_intLeft, &$e_intTop, &$e_intWidth, &$e_intHeight){
	
		// the x and y coordinates
		$l_intErrorX = 0;
		$l_intErrorY = 0;

		// compute the x coordinates
		$l_intErrorX = ($i_intErrorCount + 1) % $this->get_crossword()->get_WidthOfCrossword();
		if ($l_intErrorX == 0) {
			$l_intErrorX = $this->get_crossword()->get_WidthOfCrossword();
		}

		// compute the y coordinates
		$l_intErrorY = ceil(($i_intErrorCount + 1) / $this->get_crossword()->get_WidthOfCrossword());
		
		// compute the properties with this coordinates
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, 
								$e_intLeft, $e_intTop, $e_intWidth, $e_intHeight,
								$l_intErrorX, $l_intErrorY);
		
		// add the gap and the actual crossword height to the top value
		$e_intTop += $i_intTopSpace;
	}
	
		
	/**
	 * Deliver the porperties of the box for the HTML CSS configuration
	 * 
	 * @param float $i_fltXScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float $i_fltYScale 		Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer $i_intBorderWidth Thickness of the borderlines of the box
	 * @param integer &$e_intLeft 		coordinates from the left side.
	 * @param integer &$e_intTop 		coordinates from the top.
	 * @param integer &$e_intWidth 		Width of the box.
	 * @param integer &$e_intHeight 	Height of the box.
	 * @param integer $i_intX			Optionaly the x coordinates
	 * @param integer $i_intY			Optionaly the y coordinates
	 * @return string HTML content of a single box
	 */
	protected function getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth,
										&$e_intLeft, &$e_intTop, &$e_intWidth, &$e_intHeight,
										$i_intX = -1, $i_intY = -1){
		
		// the x and y coordinate
		$l_intX = 0;
		$l_intY = 0;
		// array with the size of the HTML element of this box
		$l_arrBoxHtmlSize = array();
		
		// if the x value is send through the interface
		if ($i_intX != -1) {
			$l_intX = $i_intX;
		} else {
			$l_intX = $this->m_intX;
		}
		
		// if the y value is send through the interface
		if ($i_intY != -1) {
			$l_intY = $i_intY;
		} else {
			$l_intY = $this->m_intY;
		}
		
		// compute the size of this box without rounding
		$l_arrBoxHtmlSize = $this->getGeneralBoxSize($i_fltXScale, $i_fltYScale, false);
		// compute the left space
		$e_intLeft = round(($l_intX - 1) * $l_arrBoxHtmlSize['width']);
		// compute the top space
		$e_intTop = round(($l_intY - 1) * $l_arrBoxHtmlSize['height']);
		
		// compute the size of this box with rounding
		$l_arrBoxHtmlSize = $this->getGeneralBoxSize($i_fltXScale, $i_fltYScale);
		// compute the width
		$e_intWidth = $l_arrBoxHtmlSize['width'];// - $i_intBorderWidth;
		// compute the height
		$e_intHeight = $l_arrBoxHtmlSize['height'];// - $i_intBorderWidth;
	}
	
	/**
	 * Deliver a single box of a crossword as a HTML div element
	 *
	 * @param float 	$i_fltXScale 			Scale of the boxes 1 means 100% 0.5 means 50% of the width
	 * @param float 	$i_fltYScale 			Scale of the boxes 1 means 100% 0.5 means 50% of the height
	 * @param integer 	$i_intBorderWidth		Thickness of the borderlines of the box
	 * @param string 	$i_strDivClass			Class of the div element.
	 * @param bool		$i_blnNoClosingStyleTag	True if omit the closing quotes in the style tag. (Default is true)
	 * @param bool		$i_blnNoClosingDivTag 	True if omit the closing div tag. (Default is true)
	 * @return string 						HTML content of a single box
	 */
	protected function getSingleBox($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $i_strDivClass, 
									$i_blnNoClosingStyleTag = true, $i_blnNoClosingDivTag = true){
		
		// the coordinates from the left
		$l_intLeft = 0;
		// the coordinates from the top
		$l_intTop = 0;
		// the width of the box
		$l_intWidth = 0;
		// the height of the box
		$l_intHeight = 0;
		
		// create the id of this box
		$l_strBoxId = 'box' . $this->m_intX . 'x' . $this->m_intY . '_' . $this->m_objCrossword->get_uniqueId();
		
		// get the box properties
		$this->getBoxProperties($i_fltXScale, $i_fltYScale, $i_intBorderWidth, $l_intLeft, $l_intTop, $l_intWidth, $l_intHeight);
		
		$l_strSingleBox = "\n\t\t\t" . '<!-- ' . $this->get_strType() . ' Cell ' . $this->m_intX . 'x' . $this->m_intY . ' -->' . "\n";
		
		// create the div element with all parameters
		$l_strSingleBox .= "\t\t\t" . '<div class="' . $i_strDivClass . ' glcrossword_cell_layout" ';
		$l_strSingleBox .= 'id="' . $l_strBoxId . '" title style="left: ';
		$l_strSingleBox .= $l_intLeft . 'px; top: ' . $l_intTop . 'px; width: ' . $l_intWidth . 'px; ';
		$l_strSingleBox .= 'height: ' . $l_intHeight . 'px; border-width: ' . $i_intBorderWidth . 'px; ';
		
		// if flag for no closing style Tag is not set 
		if (! $i_blnNoClosingStyleTag) {
			$l_strSingleBox .= '" >';
		}
		
		// if flag for no closing div tag is not set
		// this is only allowed if no closing style tag is also not set
		if (! $i_blnNoClosingDivTag && ! $i_blnNoClosingStyleTag) {
			$l_strSingleBox .= '</div>';
		    $l_strSingleBox .= "\n";
		}
		
		return $l_strSingleBox;
	}
}
?>