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
 * Class with the content for the fluid template
 *
 * @author	Gerald Loß <gerald.loss@gmx.de>
 * @package	glcrossword
 */
class GlcrosswordData  {
    /**
     * The content of the crossword 
     * @var GlcrosswordCrossword
     * @access protected
     */
    protected string $htmlContent;
    
    /**
     * Returns the content
     *
     * @return string $content
     */
    public function getHtmlContent(): string {
        return $this->htmlContent;
    }
    
    /**
     * Sets the content
     *
     * @param string $content
     * @return void
     */
    public function setHtmlContent(string $htmlContent): void {
        $this->htmlContent = $htmlContent;
    }
}