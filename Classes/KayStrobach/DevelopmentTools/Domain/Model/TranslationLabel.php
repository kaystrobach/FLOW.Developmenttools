<?php
namespace KayStrobach\DevelopmentTools\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "KayStrobach.DevelopmentTools".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\I18n\Locale;

/**
 * @Flow\Entity
 */
class TranslationLabel {

	/**
	 * @var string
	 */
	protected $labelId = '';

	/**
	 * @var string
	 */
	protected $arguments = '';

	/**
	 * @var string
	 */
	protected $label = '';

	/**
	 * @var string
	 */
	protected $packageKey = '';

	/**
	 * @var string
	 */
	protected $sourceName = '';

	/**
	 * @var \TYPO3\Flow\I18n\TranslationProvider\TranslationProviderInterface
	 */
	protected $translationProvider;

	/**
	 * @param \TYPO3\Flow\I18n\TranslationProvider\TranslationProviderInterface $translationProvider
	 * @return void
	 */
	public function injectTranslationProvider(\TYPO3\Flow\I18n\TranslationProvider\TranslationProviderInterface $translationProvider) {
		$this->translationProvider = $translationProvider;
	}

	/**
	 * @return string
	 */
	public function getLabelId() {
		return $this->labelId;
	}

	/**
	 * @param string $labelId
	 * @return void
	 */
	public function setLabelId($labelId) {
		$this->labelId = $labelId;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 * @return void
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getPackageKey() {
		return $this->packageKey;
	}

	/**
	 * @param string $packageKey
	 */
	public function setPackageKey($packageKey) {
		$this->packageKey = $packageKey;
	}

	/**
	 * @return string
	 */
	public function getArguments() {
		return $this->arguments;
	}

	/**
	 * @param string $arguments
	 */
	public function setArguments($arguments) {
		$this->arguments = $arguments;
	}

	/**
	 * @return string
	 */
	public function getSourceName() {
		return $this->sourceName;
	}

	/**
	 * @param string $sourceName
	 */
	public function setSourceName($sourceName) {
		$this->sourceName = $sourceName;
	}

	public function isTranslatedByLabel() {
		if($this->label !== '') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function isTranslatedByLabelId() {
		if($this->labelId !== '') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function isAvailableinXliffForLocaleEn() {
		if($this->isTranslatedByLabel()) {
			$translation = $this->translationProvider->getTranslationByOriginalLabel(
				$this->label,
				new Locale('en'),
				'one',
				$this->sourceName,
				$this->packageKey
			);
			if($translation !== FALSE) {
				return TRUE;
			}
		}
		if($this->isTranslatedByLabelId()) {
			$translation =  $this->translationProvider->getTranslationById(
				$this->labelId,
				new Locale('en'),
				'one',
				$this->sourceName,
				$this->packageKey
			);
			if($translation !== FALSE) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
?>