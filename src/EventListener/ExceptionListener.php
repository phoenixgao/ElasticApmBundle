<?php

namespace SpaceSpell\ElasticApmBundle\EventListener;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SpaceSpell\ElasticApmBundle\Agent\AgentAwareInterface;
use SpaceSpell\ElasticApmBundle\Agent\AgentAwareTrait;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener implements LoggerAwareInterface, AgentAwareInterface
{
    use LoggerAwareTrait, AgentAwareTrait;

    protected $enabled = false;

    protected $exclude = [];

    protected $include = [];

    public function __construct($enabled)
    {
        $this->enabled = $enabled;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->enabled) {
            return;
        }

        $exception = $event->getException();

        $name = get_class($exception);
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

        $this->agent->captureThrowable($exception);

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->info(sprintf('APM errors captured: "%s"', $exception->getTraceAsString()));
        }

        try {
            $sent = $this->agent->send();
        } catch (\Exception $e) {
            $sent = false;
        }

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->info(
                sprintf('APM errors %s: "%s"', $sent ? 'sent' : 'not sent', $exception->getTraceAsString())
            );
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
