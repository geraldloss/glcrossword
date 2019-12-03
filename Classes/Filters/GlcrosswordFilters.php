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

namespace Loss\Glcrossword\Filters;

/**
 * Filter methods for the select in group boxes in a TCA 
 *
 * @author	Gerald Loß <gerald.loss@gmx.de>
 * @package	glcrossword
 */
class GlcrosswordFilters {
	/**
	 * Filter the question for selection in the flexform. It should only parent
	 * values be selected.
	 * Is deactivated and not testet yet. Is still avail at typo3 version 6.x 
	 *
	 * @param array $i_arrParameters The parameters
	 * @param object $i_objParentObject The parent object
	 */ 
	public function filterQuestions($i_arrParameters, $i_objParentObject) {
		// get the current field values
	    $l_arrFieldValues = $i_arrParameters['values'];
		
		// if this is no parent
		if ($l_arrFieldValues[l10n_parent] <> 0) {
			// delete the value array
			$l_arrFieldValues = array();
		}
		
		return $l_arrFieldValues;
	}
}
?>