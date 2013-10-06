<?php
namespace KayStrobach\DevelopmentTools\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;

/**
 * Der Startcontroller
 *
 * Class StandardController
 * @package SBS\LaPo\Controller
 */
class ControllerController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @FLOW\Inject
	 * @var \TYPO3\Flow\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * @FLOW\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @var array
	 */
	protected $ignoredPackages = array(
		'TYPO3.Flow',
		'TYPO3.Fluid',
	);

	/**
	 * Basic action
	 */
	public function indexAction() {
		$this->redirect('listOfControllersAndActions');
	}

	/**
	 * Generates a list of controllers and actions
	 *
	 * @return void
	 */
	public function listOfControllersAndActionsAction() {
		$controllersFromReflectionService = $this->reflectionService->getAllSubClassNamesForClass('\TYPO3\Flow\Mvc\Controller\ActionController');

		$controllersForOutput = array();

		foreach($controllersFromReflectionService as $controller) {
			if(!in_array($this->objectManager->getPackageKeyByObjectName($controller), $this->ignoredPackages)) {
				$controllersForOutput[$controller] = $this->getClassesAndMethods($controller);
			}

		}
		$this->view->assign('controllers', $controllersForOutput);
	}

	/**
	 * @param $className
	 * @return null|\SBS\LaPo\Utility\ClassReflection
	 * @throws \Exception
	 */
	protected function getClassesAndMethods($className) {
		$className = str_replace('.php', '', $className);
		try {
			return new \KayStrobach\DevelopmentTools\Reflection\ClassReflection($className);
		} catch(\Exception $e) {
			throw new \Exception('Failed to build Reflection Class ' . $className);
			return NULL;
		}
	}
}