<?php
namespace KayStrobach\DevelopmentTools\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;

/**
 * Der Startcontroller
 *
 * Class StandardController
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
		'TYPO3.Welcome',
		'KayStrobach.Menu',
		'KayStrobach.DevelopmentTools',
	);

	/**
	 * Basic action
	 */
	public function listOfControllersAndActionsAction() {
		$this->redirect('index');
	}

	/**
	 * Generates a list of controllers and actions
	 *
	 * @return void
	 */
	public function indexAction() {
		$controllersFromReflectionService = $this->reflectionService->getAllSubClassNamesForClass('\TYPO3\Flow\Mvc\Controller\ActionController');
		$uriBuilder = clone $this->controllerContext->getUriBuilder();


		$controllersForOutput = array();

		foreach($controllersFromReflectionService as $controller) {
			if(!in_array($this->objectManager->getPackageKeyByObjectName($controller), $this->ignoredPackages)) {
				$helper = $this->getClassesAndMethods($controller);
				try {
					$uriBuilder->uriFor('index', array(), $controller, $this->objectManager->getPackageKeyByObjectName($controller), NULL);
					if(!$helper->isAbstract()) {
						$controllersForOutput[$controller] = helper;
					}
				} catch(Exception $e) {
					$this->addFlashMessage($e->getMessage());
				}
			}

		}
		$this->view->assign('controllers', $controllersForOutput);
	}

	/**
	 * @param $className
	 * @return \KayStrobach\DevelopmentTools\Reflection\ClassReflection
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