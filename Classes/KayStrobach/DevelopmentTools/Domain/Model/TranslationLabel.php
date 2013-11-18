<?php
namespace KayStrobach\DevelopmentTools\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "KayStrobach.DevelopmentTools".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

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

	function __construct() {
		$this->labelId   = '';
		$this->label     = '';
		$this->arguments = '';
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
}
?>