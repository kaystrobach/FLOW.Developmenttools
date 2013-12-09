<?php
namespace KayStrobach\DevelopmentTools\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "KayStrobach.DevelopmentTools".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\QueryInterface;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class TranslationLabelRepository extends Repository {
	/**
	 * If set in an implementation overrides automatic detection of the
	 * entity class name being managed by the repository.
	 *
	 * @var string
	 * @api
	 */
	const ENTITY_CLASSNAME = '\KayStrobach\DevelopmentTools\Domain\Model\TranslationLabel';

	/**
	 * @var array
	 */
	protected $defaultOrderings = array(
		'label' => QueryInterface::ORDER_ASCENDING,
	);

	/**
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 * @Flow\Inject
	 */
	protected $logger;

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

		if(array_key_exists('originalLabel', $demands)) {
			$demandConstraints[] = $query->equals('label', $demands['originalLabel']);
		}

		$query->matching(
			$query->logicalAnd(
				$demandConstraints
			)
		);

		return $query->execute();
	}
	public function flushByCacheHash($hash) {
		$this->localizationCache->remove($hash);
	}

}
?>