<?php

namespace KayStrobach\DevelopmentTools\Http;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Http\Component\ComponentContext;
use TYPO3\Flow\Http\Component\ComponentInterface;
use TYPO3\Flow\Object\ObjectManagerInterface;

class ClockworkComponent implements ComponentInterface
{

    /**
     * @var array
     */
    protected $options;

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
        $response = $componentContext->getHttpResponse();
        $response->setHeader(
            'X-Clockwork-Id',
            '123'
        );
        $response->setHeader(
            'X-Clockwork-Version',
            '2.0'
        );
    }
}