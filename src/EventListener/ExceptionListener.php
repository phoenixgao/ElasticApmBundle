<?php

namespace SpaceSpell\ElasticApmBundle\EventListener;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener implements LoggerAwareInterface, AgentAwareInterface
{
    use LoggerAwareTrait, AgentAwareTrait;

    protected $enabled = false;

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
}
