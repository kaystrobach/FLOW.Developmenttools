<?php

namespace KayStrobach\DevelopmentTools\Aspects;

use KayStrobach\DevelopmentTools\Domain\Model\TranslationLabel;
use TYPO3\Flow\Annotations as Flow;

/**
 * Wraps around the \TYPO3\Flow\Cli\Response
 *
 * @Flow\Aspect
 */
class TranslatorAspect {
	/**
	 * @var \KayStrobach\DevelopmentTools\Domain\Repository\TranslationLabelRepository
	 * @Flow\Inject
	 */
	protected $translationLabelRepository = NULL;

	/**
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 * @Flow\Inject
	 */
	protected $logger;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Log each translation by label
	 *
	 * @param  \TYPO3\Flow\AOP\JoinPointInterface $joinPoint The current join point
	 * @return mixed Result of the target method
	 * @Flow\Around("method(TYPO3\Flow\I18n\Translator->translateByOriginalLabel())")
	 */
	public function translateByOriginalLabel(\TYPO3\Flow\AOP\JoinPointInterface $joinPoint) {
		$arguments = $joinPoint->getMethodArguments();
		$this->logger->log(
			'Translation: ' . $arguments['packageKey'] . ' (' . $arguments['sourceName'] . ') >> ' . $arguments['originalLabel'],
			LOG_DEBUG
		);
		$this->storeTranslation($joinPoint->getMethodArguments());
		return $joinPoint->getAdviceChain()->proceed($joinPoint);
	}

	/**
	 * log each translation by ID
	 *
	 * @param  \TYPO3\Flow\AOP\JoinPointInterface $joinPoint The current join point
	 * @return mixed Result of the target method
	 * @Flow\Around("method(TYPO3\Flow\I18n\Translator->translateById())")
	 */
	public function translateById(\TYPO3\Flow\AOP\JoinPointInterface $joinPoint) {
		$arguments = $joinPoint->getMethodArguments();
		$this->logger->log(
			'Translation: ' . $arguments['packageKey'] . ' (' . $arguments['sourceName'] . ') -- ' . $arguments['labelId'],
			LOG_DEBUG
		);
		$this->storeTranslation($joinPoint->getMethodArguments());
		return $joinPoint->getAdviceChain()->proceed($joinPoint);
	}

	/**
	 * @param $arguments
	 */
	protected function storeTranslation($arguments) {
		$translationLabel = $this->translationLabelRepository->findByDemands($arguments);

		if($translationLabel->count() === 0) {
			$translationLabel = new TranslationLabel();

			$translationLabel->setPackageKey($arguments['packageKey']);
			#$translationLabel->setPluralForm($arguments['pluralForm']);
			$translationLabel->setSourceName($arguments['sourceName']);

			if(array_key_exists('labelId', $arguments)) {
				$translationLabel->setLabelId($arguments['labelId']);
			}

			if(array_key_exists('originalLabel', $arguments)) {
				$translationLabel->setLabel($arguments['originalLabel']);
			}
			$this->translationLabelRepository->add($translationLabel);
			$this->persistenceManager->persistAll();
			$this->logger->log('Translation was not known', LOG_DEBUG);
		} else {
			$this->logger->log('Translation is already known', LOG_DEBUG);
		}
	}
}