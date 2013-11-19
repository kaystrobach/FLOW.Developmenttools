<?php
namespace KayStrobach\DevelopmentTools\Controller;

use KayStrobach\DevelopmentTools\Utility\ClassParser;
use KayStrobach\DevelopmentTools\Utility\ClassToDot;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;

/**
 * Der Startcontroller
 *
 * Class StandardController
 */
class ModelController extends \TYPO3\Flow\Mvc\Controller\ActionController {

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
	public function showUmlAction() {
		$this->redirect('index');
	}

	/**
	 * Generates a list of controllers and actions
	 *
	 * @return void
	 */
	public function indexAction() {
		$entitiesFromReflectionService = $this->reflectionService->getClassNamesByAnnotation('TYPO3\\Flow\\Annotations\\Entity');
		$dotParser = new ClassToDot();
		$buffer    = $dotParser->makeDotFile($entitiesFromReflectionService);

		$this->view->assign('graphdata', $buffer);
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