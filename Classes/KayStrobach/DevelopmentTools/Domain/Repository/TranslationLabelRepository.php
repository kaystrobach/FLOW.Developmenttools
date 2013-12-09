<?php
namespace KayStrobach\DevelopmentTools\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "KayStrobach.DevelopmentTools".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class TranslationLabelRepository {

	/**
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 * @Flow\Inject
	 */
	protected $logger;

	/**
	 * localization cache
	 *
	 * @var \TYPO3\Flow\Cache\Frontend\VariableFrontend
	 * @return void
	 */
	protected $localizationCache;

	/**
	 * Sets the foo cache
	 * @param \TYPO3\Flow\Cache\Frontend\VariableFrontend $localizationCache
	 */
	public function setLocalizationCache(\TYPO3\Flow\Cache\Frontend\VariableFrontend $localizationCache) {
		$this->localizationCache = $localizationCache;
	}

	/**
	 *
	 */
	public function findAll() {
		return $this->localizationCache->getIterator();
	}

	/**
	 * @param array $demands
	 * @return array
	 */
	public function findByDemands(array $demands) {
		if(array_key_exists('labelId', $demands)) {
			return $this->localizationCache->getByTag(sha1($demands['labelId']));
		}
		if(array_key_exists('originalLabel', $demands)) {
			return $this->localizationCache->getByTag(sha1($demands['originalLabel']));
		}
		return NULL;
	}
	/**
	 * @param $translationLabel
	 */
	public function addOrUpdate($translationLabel) {
		$this->localizationCache->set($translationLabel->getCacheHash(), $translationLabel, $translationLabel->getCacheTags());
	}

	/**
	 * @param $tag
	 */
	public function flushByTag($tag) {
		$this->localizationCache->flushByTag(sha1($tag));
	}

	public function flush() {
		$this->localizationCache->flush();
	}
	public function flushByCacheHash($hash) {
		$this->localizationCache->remove($hash);
	}

}
?>