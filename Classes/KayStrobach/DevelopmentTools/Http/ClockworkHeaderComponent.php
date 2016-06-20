<?php

namespace KayStrobach\DevelopmentTools\Http;

use Clockwork\Clockwork;
use Clockwork\DataSource\PhpDataSource;
use Clockwork\Storage\FileStorage;
use KayStrobach\DevelopmentTools\Log\Backend\ClockworkBackend;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Http\Component\ComponentContext;
use TYPO3\Flow\Http\Component\ComponentInterface;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Flow\Utility\Files;

class ClockworkHeaderComponent implements ComponentInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \TYPO3\Flow\Log\SystemLoggerInterface
     * @Flow\Inject
     */
    protected $logger;
    
    /**
     * @param ComponentContext $componentContext
     * @return void
     * @api
     */
    public function handle(ComponentContext $componentContext)
    {
        $loggerBackends = ObjectAccess::getProperty($this->logger, 'backends', TRUE);

        foreach($loggerBackends as $loggerBackend) {
            if($loggerBackend instanceof ClockworkBackend) {
                $response = $componentContext->getHttpResponse();
                $response->setHeader(
                    'X-Clockwork-Id',
                    $loggerBackend->getClockwork()->getRequest()->id
                );
                $response->setHeader(
                    'X-Clockwork-Version',
                    Clockwork::VERSION
                );
                return;
            }
        }
    }
}