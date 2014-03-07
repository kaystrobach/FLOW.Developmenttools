<?php

namespace KayStrobach\DevelopmentTools\Domain\Model;

use TYPO3\Flow\Annotations as Flow;

class MenuItem implements \KayStrobach\Menu\Domain\Model\MenuItemInterface {
	/**
	 * @FLOW\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @param array $parentItem
	 * @return array
	 */
	public function getItems(array $parentItem) {
		$entitiesFromReflectionService = $this->reflectionService->getClassNamesByAnnotation('TYPO3\\Flow\\Annotations\\Entity');
		$menuItems                     = array();
		foreach($entitiesFromReflectionService as $entity) {
			$menuItems[] = array(
				'label'      => $entity,
				'action'     => 'index',
				'controller' => 'Model',
				'package'    => 'KayStrobach.DevelopmentTools',
				'url'        => 'heise.de',
				'arguments'  => array(
					'centralClass' => '\\' . $entity
				)
			);
		}
		return $menuItems;
	}
}