<?php

namespace KayStrobach\DevelopmentTools\Http;

use Clockwork\Clockwork;
use Clockwork\DataSource\PhpDataSource;
use Clockwork\Storage\FileStorage;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Http\Component\ComponentContext;
use TYPO3\Flow\Http\Component\ComponentInterface;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Utility\Files;

class ClockworkDataComponent implements ComponentInterface
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
     * @param array $options
     */
    public function __construct(array $options = array()) {
        $this->options = $options;
    }

    /**
     * @param ComponentContext $componentContext
     * @return void
     * @api
     */
    public function handle(ComponentContext $componentContext)
    {
        $path = $componentContext->getHttpRequest()->getRelativePath();

        $this->logger->log('Plimm: ' . substr($path, 12));

        if(substr($path, 0, 11) === '__clockwork') {
            $storage = new FileStorage(FLOW_PATH_DATA . '/Clockwork');
            $data = $storage->retrieve(substr($path, 12));
            if($data !== null) {
                $componentContext->getHttpResponse()->setContent($data->toJson());
                $componentContext->setParameter('TYPO3\Flow\Http\Component\ComponentChain', 'cancel', TRUE);
            }
        }
    }
}