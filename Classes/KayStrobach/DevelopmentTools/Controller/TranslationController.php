<?php
namespace KayStrobach\DevelopmentTools\Controller;

use KayStrobach\DevelopmentTools\Domain\Model\TranslationLabel;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Command\HelpCommandController;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Cli\Command;
use TYPO3\Flow\Cli\CommandManager;
use TYPO3\Flow\Error\Message;


/**
 * Shows all collected translation requests
 *
 * Class StandardController
 */
class TranslationController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 * @var \KayStrobach\DevelopmentTools\Domain\Repository\TranslationLabelRepository
	 * @Flow\Inject
	 */
	protected $translationLabelRepository;

	/**
	 * @var \TYPO3\Flow\Cache\CacheManager
	 * @Flow\Inject
	 */
	protected $cacheManager;

	/**
	 * list all translation label collected
	 */
	public function indexAction() {
		$this->view->assign('translationLabels', $this->translationLabelRepository->findAll());
	}

	/**
	 * remove all translation labels
	 */
	public function clearAllAction() {
		$this->translationLabelRepository->flush();
		$this->redirect('index');
	}

	/**
	 * remove a selected translation label
	 *
	 * @param string $translationLabelCacheHash
	 */
	public function removeAction($translationLabelCacheHash) {
		$this->translationLabelRepository->flushByCacheHash($translationLabelCacheHash);
		$this->redirect('index');
	}

	/**
	 * clear FLOWs translation cache
	 */
	public function clearTranslationCacheAction() {
		$this->cacheManager->getCache('Flow_I18n_XmlModelCache')->flush();
		$this->redirect('index');
	}

	/**
	 * add a stub with the selected id to the related xliff file
	 *
	 * @param TranslationLabel $translationLabel
	 * @param string $label
	 * @param string $labelId
	 */
	public function addToXliffAction(TranslationLabel $translationLabel, $label = NULL, $labelId = NULL) {
		$filename = 'resource://' . $translationLabel->getPackageKey() . '/Private/Translations/en/' . $translationLabel->getSourceName() . '.xlf';

		if($label !== NULL) {
			$translationLabel->setLabel($label);
		}
		if($labelId !== NULL) {
			$translationLabel->setLabelId($labelId);
		}
		if($labelId !== NULL || $label !== NULL) {
			$this->translationLabelRepository->update($translationLabel);
		}

		if(file_exists($filename)) {
			$doc = new \DOMDocument('1.0');
			$doc->preserveWhiteSpace = false;
			$doc->load($filename, LIBXML_COMPACT);
			$doc->formatOutput = true;

			if($doc->getElementById($translationLabel->getLabelId()) !== NULL) {
				$this->addFlashMessage('This id %1s is already present in the Translation file', '', Message::SEVERITY_NOTICE, array($translationLabel->getLabelId()));
				$this->redirect('index');
				return;
			}
			$bodyElement = $doc->getElementsByTagName('body')->item(0);

			$source = $doc->createElement('source', $translationLabel->getLabel());

			$transUnit = $doc->createElement('trans-unit');
			$transUnit->setAttribute('id', $translationLabel->getLabelId());
			$transUnit->setIdAttribute('id', TRUE);
			$transUnit->setAttribute('xml:space', 'preserve');
			$transUnit->appendChild($source);

			$bodyElement->appendChild($transUnit);

			if(is_writable($filename)) {
				$doc->save($filename);
				$this->cacheManager->getCache('Flow_I18n_XmlModelCache')->flush();
				$this->addFlashMessage('File %1s saved', '', Message::SEVERITY_OK, array($filename));
			} else {
				$this->addFlashMessage('The xlf file %1s is not writeable for me', '', Message::SEVERITY_ERROR, array($filename));
			}

		} else {
			$this->addFlashMessage('Can´ find file %1s', '', Message::SEVERITY_ERROR, array($filename));
		}
		$this->redirect('index');
	}
}