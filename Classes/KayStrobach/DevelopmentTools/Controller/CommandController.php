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
class CommandController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\Flow\Cli\CommandManager
	 * @Flow\Inject
	 */
	protected $commandManager;


	/**
	 * Basic action
	 */
	public function indexAction() {
		/**
		 * @var $command \TYPO3\Flow\Cli\Command
		 */
		$commands    = $this->commandManager->getAvailableCommands();

		$availableCommands = array();

		foreach($commands as $command) {
			$availableCommands[] = $this->getCommandInformation($command);
		}

		$this->view->assign('commands', $availableCommands);
	}

	/**
	 * @param Command $command
	 * @return array
	 */
	protected function getCommandInformation(Command $command) {
		$commandInformation = array(
			'identifier'      => $command->getCommandIdentifier(),
			'shortIdentifier' => $this->commandManager->getShortestIdentifierForCommand($command),
			'description'     => $command->getShortDescription(),
			'options'         => array(),
			'arguments'       => array(),
			'seealso'         => array(),
		);

		$commandArgumentDefinitions = $command->getArgumentDefinitions();
		$usage = '';
		$hasOptions = FALSE;
		foreach ($commandArgumentDefinitions as $commandArgumentDefinition) {
			if (!$commandArgumentDefinition->isRequired()) {
				$hasOptions = TRUE;
				$commandInformation['options'][] = array(
					'name'        => $commandArgumentDefinition->getDashedName(),
					'description' => $commandArgumentDefinition->getDescription(),
				);
			} else {
				$commandInformation['arguments'][] = array(
					'name'        => $commandArgumentDefinition->getDashedName(),
					'description' => $commandArgumentDefinition->getDescription(),
				);
				$usage .= sprintf(' <%s>', strtolower(preg_replace('/([A-Z])/', ' $1', $commandArgumentDefinition->getName())));
			}
		}

		// usage
		$helpCommand = new HelpCommandController();
		$commandInformation['usage'] = $helpCommand->getFlowInvocationString() . ' ' . $this->commandManager->getShortestIdentifierForCommand($command) . ($hasOptions ? ' [<options>]' : '') . $usage;

		foreach($command->getRelatedCommandIdentifiers() as $relatedCommandIdentifier) {
			$relatedCommand = $this->commandManager->getCommandByIdentifier($relatedCommandIdentifier);
			$commandInformation['seealso'][] = array(
				'identifier'      => $relatedCommand->getCommandIdentifier(),
				'shortIdentifier' => $this->commandManager->getShortestIdentifierForCommand($command),
				'description'     => $relatedCommand->getShortDescription(),
			);
		}

		return $commandInformation;
	}
}