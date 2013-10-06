<?php
namespace KayStrobach\DevelopmentTools\ViewHelpers;

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