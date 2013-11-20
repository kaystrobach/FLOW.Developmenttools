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
 * Der Startcontroller
 *
 * Class StandardController
 */
class TranslationLabelController extends \TYPO3\Flow\Mvc\Controller\ActionController {
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

	public function indexAction() {
		$this->view->assign('translationLabels', $this->translationLabelRepository->findAll());
	}

	public function clearAllAction() {
		$this->translationLabelRepository->removeAll();
		$this->redirect('index');
	}

	public function removeAction(TranslationLabel $translationLabel) {
		$this->translationLabelRepository->remove($translationLabel);
		$this->redirect('index');
	}

	public function clearTranslationCacheAction() {
		$this->cacheManager->getCache('Flow_I18n_XmlModelCache')->flush();
		$this->redirect('index');
	}

	/**
	 * @param TranslationLabel $translationLabel
	 */
	public function addToXliffAction(TranslationLabel $translationLabel) {
		$filename = 'resource://' . $translationLabel->getPackageKey() . '/Private/Translations/en/' . $translationLabel->getSourceName() . '.xlf';

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