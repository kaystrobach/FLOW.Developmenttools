<?php

namespace KayStrobach\DevelopmentTools\Command;


use TYPO3\Flow\Annotations as Flow;



/**
 * @Flow\Scope("singleton")
 */
class DocumentationCommandController extends \TYPO3\Flow\Cli\CommandController {
	public function runDoxygenCommand($title = '', $folder = NULL, $command = 'doxygen') {

	}
	public function runSchemaSpyCommand() {

	}
}