<?php
namespace KayStrobach\DevelopmentTools\Controller;

use KayStrobach\DevelopmentTools\Utility\ClassParser;
use KayStrobach\DevelopmentTools\Utility\ClassToDot;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Mvc\Exception\StopActionException;

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
	 * @param string $centralClass
	 * @return void
	 */
	public function indexAction($centralClass = '') {
		$entitiesFromReflectionService = $this->reflectionService->getClassNamesByAnnotation('TYPO3\\Flow\\Annotations\\Entity');
		$dotParser = new ClassToDot();
		$dotParser->setUriBuilder($this->uriBuilder);

		if($centralClass !== '') {
			$entitiesFromReflectionService = $this->filterEntities($centralClass, $entitiesFromReflectionService, $dotParser);
			$dotParser->setCentralClass($centralClass);

		}

		$buffer    = $dotParser->makeDotFile($entitiesFromReflectionService);
		$this->view->assign('graphdata', $buffer);
	}

	/**
	 * @param string $centralClass
	 * @param array $allClasses
	 * @param ClassToDot $dotParser
	 * @return array
	 */
	public function filterEntities($centralClass, $allClasses, $dotParser) {
		$filteredClasses = array(
			$centralClass
		);
		$classParser = new ClassParser();

		$parsedClassResult = $classParser->parseClass($centralClass);
		foreach($parsedClassResult['properties'] as $property) {
			if(!($dotParser->isIgnoredProperty($property['name']) || $dotParser->isAttributeTypeWithoutConnection($property['model']))) {
				$filteredClasses[] = $property['model'];
			}
		}
		return $filteredClasses;
	}
}