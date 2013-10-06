<?php
namespace KayStrobach\DevelopmentTools\Reflection;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;

/**
 * Der Startcontroller
 *
 * Class StandardController
 * @package SBS\LaPo\Controller
 */
class ClassReflection extends \TYPO3\Flow\Reflection\ClassReflection {
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	protected $hiddenMethods = array(
		'initializeActionMethodArguments',
		'getActionMethodParameters',
		'initializeActionMethodValidators',
		'getActionValidationGroups',
		'getActionValidateAnnotationData',
		'initializeAction',
		'callActionMethod',
		'getActionIgnoredValidationArguments',
		'errorAction',
		'resolveActionMethodName'
	);

	public function getControllerForFluid() {
		$classname = $this->getName();
		return substr($classname, strpos($classname, '\\Controller\\') + 12, -10);
	}
	public function getPackageForFluid() {
		$classname = $this->getName();
		return $this->objectManager->getPackageKeyByObjectName($classname);
	}
	public function getPackageForPath() {
		return str_replace('.', '/', $this->getPackageForFluid());
	}
	public function getMethods($filter = NULL) {
		$methods = parent::getMethods($filter);
		$returnedMethods = array();
		foreach($methods as $method) {
			if((substr($method->getName(), -6, 6) === 'Action') && (!in_array($method->getName(), $this->hiddenMethods))) {
				$returnedMethods[] = $method;
			}
		}
		return $returnedMethods;
	}
}