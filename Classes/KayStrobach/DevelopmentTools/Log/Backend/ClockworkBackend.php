<?php

namespace KayStrobach\DevelopmentTools\Log\Backend;

use Clockwork\Clockwork;
use Clockwork\DataSource\PhpDataSource;
use Clockwork\Storage\FileStorage;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\Backend\AbstractBackend;
use TYPO3\Flow\Utility\Files;

class ClockworkBackend extends AbstractBackend {

    protected $log = array();

    /**
     * @var Clockwork
     */
    protected $clockwork = null;

    /**
     * Carries out all actions necessary to prepare the logging backend, such as opening
     * the log file or opening a database connection.
     *
     * @return void
     * @api
    */
    public function open()
    {
        $this->clockwork = new Clockwork();
        $this->clockwork->addDataSource(new PhpDataSource());
        Files::createDirectoryRecursively(FLOW_PATH_DATA . '/Clockwork');
        $this->clockwork->setStorage(new FileStorage(FLOW_PATH_DATA . '/Clockwork'));
    }

	/**
	 * Appends the given message along with the additional information into the log.
	 *
	 * @param string $message The message to log
	 * @param integer $severity One of the LOG_* constants
	 * @param mixed $additionalData A variable containing more information about the event to be logged
	 * @param string $packageKey Key of the package triggering the log (determined automatically if not specified)
	 * @param string $className Name of the class triggering the log (determined automatically if not specified)
	 * @param string $methodName Name of the method triggering the log (determined automatically if not specified)
	 * @return void
	 * @api
	 */
	public function append($message, $severity = LOG_INFO, $additionalData = null, $packageKey = null, $className = null, $methodName = null)
	{
        if($additionalData === null) {
            $additionalData = array();
        }
        $this->clockwork->log($severity, $message, $additionalData);
	}

	/**
	 * Carries out all actions necessary to cleanly close the logging backend, such as
	 * closing the log file or disconnecting from a database.
	 *
	 * @return void
	 * @api
	 */
	public function close()
	{

	}

    public function __destruct()
    {
        $this->clockwork->resolveRequest();
        $this->clockwork->storeRequest();
    }

    /**
     * @return Clockwork
     */
    public function getClockwork()
    {
        return $this->clockwork;
    }
}