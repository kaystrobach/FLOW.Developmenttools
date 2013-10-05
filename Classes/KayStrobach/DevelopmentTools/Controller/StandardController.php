<?php
namespace KayStrobach\DevelopmentTools\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SBS.LaPo".              *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;

/**
 * Der StandardController
 *
 *
 * @package SBS\LaPo\Controller
 */
class StandardController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 * Basic action
	 */
	public function indexAction() {
		$this->redirect('listOfControllersAndActions', 'Controller');
	}
}