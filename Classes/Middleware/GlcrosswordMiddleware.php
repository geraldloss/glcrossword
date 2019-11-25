<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Gerald Loß <gerald.loss@gmx.de>
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
namespace Loss\Glcrossword\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Loss\Glcrossword\Ajax\GlcrosswordAjax;

/**
 * PSR-15 Middleware for glcrossword
 *
 * @internal
 */
class GlcrosswordMiddleware implements MiddlewareInterface{
    
    /**
     * Provides all informations about the crossword content
     * 
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        
        $psr15eID = $request->getParsedBody()['PSR-15-eID'] ?? $request->getQueryParams()['PSR-15-eID'] ?? null;
        
        if ($psr15eID !== 'glcrossword') {
            return $handler->handle($request);
        }
        
        // Remove any output produced until now
        ob_clean();
        
        /** @var Response $response */
        $response = GeneralUtility::makeInstance(Response::class);
        
        /** @var GlcrosswordAjax $glcrosswordAjax */
        $glcrosswordAjax = GeneralUtility::makeInstance(GlcrosswordAjax::class);
        
        // call the actual ajax handler and write the result in the standard output
        echo $glcrosswordAjax->handleAjaxRequest();
        
        // no further middleware processing
        return new NullResponse();
    }
}
?>