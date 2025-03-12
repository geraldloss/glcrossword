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

/**
 * Class to define the content of an answer field
 *
 * @author	Gerald Loß <gerald.loss@gmx.de>
 * @package	glcrossword
 */
class GlcrosswordContentAnswerfield {

	/**
	 * The answer text of this field
	 * @var string
	 */
	protected string $m_strAnswerLetter;
	
	/**
	 * Length of the answertletter
	 * @var integer
	 */
	protected int $m_intLength;

	/**
	 * Constructor of the answer content object
	 * @param string $i_strAnswerLetter The answer letter.
	 */
	public function __construct(string $i_strAnswerLetter) {
		$this->m_strAnswerLetter = $i_strAnswerLetter;
		$this->m_intLength = mb_strlen($this->m_strAnswerLetter, 'UTF-8');
	}
	
	/**
	 * Getter of the answer letter.
	 * @return string Answer letter.
	 */
	public function get_strAnswerLetter(): string {
		return $this->m_strAnswerLetter;
	}
	
	/**
	 * Get the length of the answer letter
	 * @return integer The length of the answer letter
	 */
	public function get_intLength(): int {
		return $this->m_intLength;
	}
}
?>