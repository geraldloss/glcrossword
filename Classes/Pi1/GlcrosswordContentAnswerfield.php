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

/**
 * Class with the one field of the answert
 *
 * @author	Gerald Loß <gerald.loss@gmx.de>
 * @package	glcrossword
 */
class GlcrosswordContentAnswerfield {

	/**
	 * Text of the answerletter in this single box 
	 * @var string
	 * @access protected
	 */
	protected $m_strAnswerLetter;
	
	/**
	 * Length of the answertletter
	 * @var integer
	 * @access protected
	 */
	protected $m_intLength;

	/**
	 * Constructor of the answer content object
	 * @param string $i_strAnswerLetter The answer letter.
	 */
	public function __construct($i_strAnswerLetter) {
		$this->m_strAnswerLetter = $i_strAnswerLetter;
		$this->m_intLength = strlen(utf8_decode($this->m_strAnswerLetter));
	}
	
	/**
	 * Getter of the answer letter.
	 * @return string Answer letter.
	 */
	public function get_strAnswerLetter() {
		return $this->m_strAnswerLetter;
	}
	
	public function get_intLength() {
		return $this->m_intLength;
	}
}
?>