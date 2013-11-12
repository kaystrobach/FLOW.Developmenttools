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
class ViewHelperController extends \TYPO3\Flow\Mvc\Controller\ActionController {

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
	 * Basic action
	 */
	public function indexAction() {
		$viewHelpersFromReflectionService = $this->reflectionService->getAllSubClassNamesForClass('\\TYPO3\\Fluid\\Core\\ViewHelper\\AbstractViewHelper');

		$viewHelpersForOutput = array();

		foreach($viewHelpersFromReflectionService as $viewHelper) {
			$helper = $this->getClassesAndMethods($viewHelper);
			if(!$helper->isAbstract()) {
				$viewHelpersForOutput[$viewHelper] = $helper;
			}
		}
		$this->view->assign('viewHelpers', $viewHelpersForOutput);
	}

	/**
	 * @param $className
	 * @return null|\KayStrobach\DevelopmentTools\Reflection\ClassReflection
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