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
 * Class with the question data
 *
 * @author	Gerald Loﬂ <gerald.loss@gmx.de>
 * @package	glcrossword
 */
class GlcrosswordContentQuestion {

	/**
	 * UID of the question
	 * @var integer
	 * @access protected
	 */
	protected $m_intUid;
	
	/**
	 * Question text 
	 * @var string
	 * @access protected
	 */
	protected $m_strQuestion;
	
	/**
	 * Answert of the question
	 * @var string
	 * @access protected
	 */
	protected $m_strAnswer;

	/**
	 * Edit mask of the answer. 1121 means 2 letters in the third box and
	 * one letter in box one, two and for.
	 * @var integer
	 * @access protected
	 */
	protected $m_intEditMask;
	
	/**
	 * Actual length of the answer considering the edit mask.
	 * @var integer
	 * @access public
	 */
	protected $m_intActualLength;
	
	
	/**
	 * Constructor of this class
	 * @param integer $i_intUID 		UID of the question in the database
	 * @param string  $i_strQuestion 	Question text
	 * @param string  $i_strAnswer 		Answer text
	 * @param integer $i_intEditMask	Edit mask for the answer text
	 */
	public function __construct($i_intUID, $i_strQuestion, $i_strAnswer, $i_intEditMask) {
		
		$this->m_intUid = $i_intUID;
		$this->m_strQuestion = $i_strQuestion;
		$this->m_strAnswer = $i_strAnswer;
		$this->m_intEditMask = $i_intEditMask;		

		// calculate the actual lenght of the answer considering the edit mask
		$this->m_intActualLength = $this->calculateLenght($i_strAnswer, $i_intEditMask);
	}
	
		
	/**
	 * Calculate the lenght of the ansert with the dependencies of the edit mask
	 * @param string $i_strAnswer The answer text of the question
	 * @param integer $i_intEditMask The edit mask 
	 * @return integer The length of the answer. 
	 */
	protected function calculateLenght($i_strAnswer, $i_intEditMask) {
		
		$intAnswerLength = strlen(utf8_decode($i_strAnswer));
		$intEditMaskLength = strlen($i_intEditMask);
		
		// if edit mask is empty
		if ($intEditMaskLength == 1 && $i_intEditMask == 0) {
			// return without changings the anwer length
			return $intAnswerLength;
		}
		
		// examine every letter in the edit mask
		for ($i = 0; $i < $intEditMaskLength; $i++) {
			// read length of letter on this position in answer
			$intCurrentLetterLength = substr($i_intEditMask, $i, 1);
			// one less
			$intCurrentLetterLength -= 1; 
			// substract the remaining from the whole length
			$intAnswerLength -= $intCurrentLetterLength;
		}
		
		return $intAnswerLength;
	}
	
	/**
	 * Getter for the question text.
	 * @return string
	 */
	public function get_strQuestion() {
		return $this->m_strQuestion;
	}
	
	/**
	 * Getter for the answer text.
	 * @return string
	 */
	public function get_strAnswer() {
		return $this->m_strAnswer;
	}

	/**
	 * Getter for the UID.
	 * @return integer
	 */
	public function get_intUid() {
		return $this->m_intUid;
	}

	/**
	 * Getter for the edit mask.
	 * @return integer
	 */
	public function get_intEditMask() {
		return $this->m_intEditMask;
	}
	
	/**
	 * Getter for the actual length.
	 * @return integer
	 */
	public function get_intActualLength() {
		return $this->m_intActualLength;
	}
}
?>