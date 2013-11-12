<?php
namespace KayStrobach\DevelopmentTools\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;

/**
 * Der StandardController
 *
 *
 */
class StandardController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 * Basic action
	 */
	public function indexAction() {
		$this->redirect('listOfControllersAndActions', 'Controller');
	}
}