<?php
namespace KayStrobach\DevelopmentTools\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Command\HelpCommandController;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Cli\Command;
use TYPO3\Flow\Cli\CommandManager;

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
	protected $translationLabelRepository = NULL;

	public function indexAction() {
		$this->view->assign('translationLabels', $this->translationLabelRepository->findAll());
	}

	public function clearAllAction() {
		$this->translationLabelRepository->removeAll();
		$this->redirect('index');
	}
}