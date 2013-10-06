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
class ActionNameViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * Counts the items of a given property.
	 *
	 * @param array $subject The array or \Countable to be counted
	 * @return integer The number of elements
	 * @throws \TYPO3\Fluid\Core\ViewHelper\Exception
	 * @api
	 */
	public function render($subject = NULL) {
		if ($subject === NULL) {
			$subject = $this->renderChildren();
		}
		if(substr($subject, 0, -6) !== 'Action') {
			return substr($subject, 0, -6);
		} else {
			return $subject;
		}

	}
}

?>