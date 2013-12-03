<?php
namespace KayStrobach\DevelopmentTools\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "KayStrobach.DevelopmentTools".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Exception;
use TYPO3\Flow\I18n\Locale;


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

	public function __construct() {
	}
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

	public function setLabelFromFramework() {
		$label = $this->getLabelFromFramework();
		if($label !== FALSE) {
			$this->setLabel($label);
		}
	}

	public function getLabelFromFramework() {
		$label = $this->translationProvider->getTranslationById(
			$this->labelId,
			new Locale('en'),
			'one',
			$this->sourceName,
			$this->packageKey
		);
		return $label;
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
		return FALSE;
	}

	/**
	 *
	 */
	public function getCacheHash() {
		if($this->getLabelId() !== '') {
			return sha1($this->getLabelId());
		} elseif($this->getLabel() !== '') {
			return sha1($this->getLabel());
		} else {
			throw new \Exception('Label and LabelId are empty');
		}
	}

	public function getCacheTags() {
		$buffer = array();
		if($this->getLabelId() !== '') {
			$buffer[] = sha1($this->getLabelId());
		}
		if($this->getLabel() !== '') {
			$buffer[] = sha1($this->getLabel());
		}
		if($this->getLabelFromFramework() !== FALSE)
		if($this->getPackageKey() !== '') {
			$buffer[] = sha1($this->getLabelFromFramework());
		}
		if($this->getSourceName() !== '') {
			$buffer[] = sha1($this->getSourceName());
		}
		return $buffer;
	}
}
?>