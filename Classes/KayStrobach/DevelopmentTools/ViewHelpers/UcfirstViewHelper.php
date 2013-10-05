<?php
namespace KayStrobach\DevelopmentTools\ViewHelpers;

	/*                                                                        *
	 * This script belongs to the TYPO3 Flow package "Fluid".                 *
	 *                                                                        *
	 * It is free software; you can redistribute it and/or modify it under    *
	 * the terms of the GNU Lesser General Public License, either version 3   *
	 * of the License, or (at your option) any later version.                 *
	 *                                                                        *
	 * The TYPO3 project - inspiring people to share!                         *
	 *                                                                        */


/**
 *
 * <code title="inline notation">
 * {namespace dev=KayStrobach\DevelopmentTools\Controlle}
 * {name -> dev:actionname()}
 * </code>
 *
 * @package KayStrobach\DevelopmentTools\Controller
 */
class UcfirstViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * Counts the items of a given property.
	 *
	 * @param array $subject the string
	 * @return string with first letter uc
	 * @api
	 */
	public function render($subject = NULL) {
		if ($subject === NULL) {
			$subject = $this->renderChildren();
		}
		return ucfirst($subject);

	}
}

?>