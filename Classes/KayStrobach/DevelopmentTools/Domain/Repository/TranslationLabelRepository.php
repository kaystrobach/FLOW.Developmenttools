<?php
namespace KayStrobach\DevelopmentTools\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "KayStrobach.DevelopmentTools".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class TranslationLabelRepository extends Repository {
	/**
	 * @var array
	 */
	protected $defaultOrderings = array();

	/**
	 * @param array $demands
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface
	 */
	public function findByDemands(array $demands) {
		$query = $this->createQuery();
		$demandConstraints = array(
			$query->equals('packageKey', $demands['packageKey']),
			$query->equals('sourceName', $demands['sourceName'])
		);

		if(array_key_exists('labelId', $demands)) {
			$demandConstraints[] = $query->equals('labelId', $demands['labelId']);
		}

		if(array_key_exists('label', $demands)) {
			$demandConstraints[] = $query->equals('label', $demands['label']);
		}

		$query->matching(
			$query->logicalAnd(
				$demandConstraints
			)
		);

		return $query->execute();
	}

}
?>