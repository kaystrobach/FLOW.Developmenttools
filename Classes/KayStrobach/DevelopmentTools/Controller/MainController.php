<?php
namespace KayStrobach\DevelopmentTools\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SBS.LaPo".              *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;

/**
 * Der Startcontroller
 *
 * Class StandardController
 * @package SBS\LaPo\Controller
 */
class MainController extends \TYPO3\Flow\Mvc\Controller\ActionController {


	public function indexAction() {
		$this->redirect('listOfControllersAndActions');
	}

	/**
	 * @return void
	 */
	public function listOfControllersAndActionsAction() {
		$directory = FLOW_PATH_PACKAGES . 'Application/SBS.LaPo/Classes/SBS/LaPo/Controller/';
		$files     = scandir($directory);

		$controllerFiles = array();

		foreach($files as $file) {
			if(is_file($directory . $file) && strpos($file, 'Controller.php')) {
				$controllerFiles[$file] = $this->getClassesAndMethods($file);
			}
		}
		$this->view->assign('controllers', $controllerFiles);
	}

	/**
	 * @param $className
	 * @return null|\SBS\LaPo\Utility\ClassReflection
	 * @throws \Exception
	 */
	protected function getClassesAndMethods($className) {
		$className = str_replace('.php', '', $className);
		try {
			return new \KayStrobach\DevelopmentTools\Reflection\ClassReflection('SBS\\LaPo\\Controller\\' . $className);
		} catch(\Exception $e) {
			throw new \Exception('Failed to build Reflection Class ' . $className);
			return NULL;
		}
	}
}