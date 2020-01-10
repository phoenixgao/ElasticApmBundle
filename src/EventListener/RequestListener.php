<?php

namespace SpaceSpell\ElasticApmBundle\EventListener;

use PhilKra\Exception\Transaction\DuplicateTransactionNameException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use SpaceSpell\ElasticApmBundle\Agent\AgentAwareInterface;
use SpaceSpell\ElasticApmBundle\Agent\AgentAwareTrait;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use SpaceSpell\ElasticApmBundle\Support\RequestConverter;

class RequestListener implements LoggerAwareInterface, AgentAwareInterface
{
    use LoggerAwareTrait, AgentAwareTrait, SharedContextProviderAwareTrait;

    protected $enabled = false;

    public function __construct($enabled)
    {
        $this->enabled = $enabled;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->enabled || !$event->isMasterRequest()) {
            return;
        }

        try {
            $this->agent->startTransaction($name = RequestConverter::getTransactionName($event->getRequest()));
        } catch (DuplicateTransactionNameException $e) {
            return;
        }

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->info(sprintf('APM transaction registered: "%s"', $name));
        }
    }
}
