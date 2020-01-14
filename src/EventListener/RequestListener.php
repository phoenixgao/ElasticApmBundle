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
    use LoggerAwareTrait, AgentAwareTrait;

    protected $enabled = false;

    protected $exclude = [];

    protected $include = [];

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
            $name = RequestConverter::getTransactionName($event->getRequest());

            $match = true;
            if ($this->include) {
                $match = false;
                foreach ($this->include as $pattern) {
                    $this->logger->info($pattern);
                    if (fnmatch($pattern, $name, FNM_NOESCAPE)) {
                        $match = true;
                        break;
                    }
                }
            } else if ($this->exclude) {
                foreach ($this->exclude as $pattern) {
                    if (fnmatch($pattern, $name, FNM_NOESCAPE)) {
                        $match = false;
                        break;
                    }
                }
            }

            if (!$match) {
                return;
            }

            $this->agent->startTransaction($name);
        } catch (DuplicateTransactionNameException $e) {
            return;
        }

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->info(sprintf('APM transaction registered: "%s"', $name));
        }
    }

    public function setExclude(array $exclude)
    {
        $this->exclude = $exclude;
    }

    public function setInclude(array $include)
    {
        $this->include = $include;
    }
}
