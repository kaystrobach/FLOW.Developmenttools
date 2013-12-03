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
	 * @var \TYPO3\Flow\I18n\TranslationProvider\TranslationProviderInterface
	 */
	protected $translationProvider;

	/**
	 * Sets the foo cache
	 * @param \TYPO3\Flow\Cache\Frontend\VariableFrontend $localizationCache
	 */
	public function setLocalizationCache(\TYPO3\Flow\Cache\Frontend\VariableFrontend $localizationCache) {
		$this->localizationCache = $localizationCache;
	}

	/**
	 * @param \TYPO3\Flow\I18n\TranslationProvider\TranslationProviderInterface $translationProvider
	 * @return void
	 */
	public function injectTranslationProvider(\TYPO3\Flow\I18n\TranslationProvider\TranslationProviderInterface $translationProvider) {
		$this->translationProvider = $translationProvider;
	}

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
			'Translation: By Lbl: ' . $arguments['packageKey'] . '/' . $arguments['sourceName'] . '/"' . $arguments['originalLabel'] . '"',
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
			'Translation:  By Id: ' . $arguments['packageKey'] . '/' . $arguments['sourceName'] . '/"' . $arguments['labelId'] . '"',
			LOG_DEBUG
		);
		$this->storeTranslation($joinPoint->getMethodArguments());
		return $joinPoint->getAdviceChain()->proceed($joinPoint);
	}

	/**
	 * @param $arguments
	 */
	protected function storeTranslation($arguments) {
		/**
		 * @var TranslationLabel $translationLabel
		 */
		$translationLabelResult = $this->translationLabelRepository->findByDemands($arguments);

		if(count($translationLabelResult) === 0) {
			$translationLabel = new TranslationLabel();
			$translationLabel->setPackageKey($arguments['packageKey']);
			$translationLabel->setSourceName($arguments['sourceName']);
			if(array_key_exists('labelId', $arguments)) {
				$translationLabel->setLabelId($arguments['labelId']);
			}
			if(array_key_exists('originalLabel', $arguments)) {
				$translationLabel->setLabel($arguments['originalLabel']);
			}
			$this->logger->log('Translation:      +: ' . $translationLabel->getLabelId(), LOG_DEBUG);
		} else {
			// store label from translation file if there is one :D
			$translationLabel = array_shift($translationLabelResult);
			$translationLabel->setLabelFromFramework();

			$this->logger->log('Translation:      .: ' . $translationLabel->getLabelId(), LOG_DEBUG);

			/*if($translationLabel->getLabelId() !== '') {
				$demands = $arguments;
				$demands['labelId'] = '';
				$demands['label']   = $translationLabel->getLabel();
				$translationLabelResult = $this->translationLabelRepository->findByDemands($demands);
				foreach($translationLabelResult as $translationLabel) {
					$this->translationLabelRepository->remove($translationLabel);
				}
			}*/
		}
		$this->translationLabelRepository->addOrUpdate($translationLabel);
	}
}