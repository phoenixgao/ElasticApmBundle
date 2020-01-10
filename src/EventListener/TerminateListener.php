<?php

namespace SpaceSpell\ElasticApmBundle\EventListener;

use PhilKra\Exception\Transaction\UnknownTransactionException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use SpaceSpell\ElasticApmBundle\Agent\AgentAwareInterface;
use SpaceSpell\ElasticApmBundle\Agent\AgentAwareTrait;
use SpaceSpell\ElasticApmBundle\ContextProvider\UserContextProviderAwareInterface;
use SpaceSpell\ElasticApmBundle\ContextProvider\UserContextProviderAwareTrait;
use SpaceSpell\ElasticApmBundle\ContextProvider\UserContextProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use SpaceSpell\ElasticApmBundle\Support\RequestConverter;

class TerminateListener implements LoggerAwareInterface, AgentAwareInterface, UserContextProviderAwareInterface
{
    use LoggerAwareTrait, AgentAwareTrait, UserContextProviderAwareTrait;

    protected $enabled = false;

    public function __construct($enabled)
    {
        $this->enabled = $enabled;
    }

    public function onKernelTerminate(PostResponseEvent $event)
    {
        if (!$this->enabled || !$event->isMasterRequest()) {
            return;
        }

        try {
            $transaction = $this->agent->getTransaction($name = RequestConverter::getTransactionName($event->getRequest()));
        } catch (UnknownTransactionException $e) {
            return;
        }

        $transaction->stop();

        $meta = $this->getMeta($event->getResponse());

        $transaction->setMeta($meta);

        if (null !== $this->logger) {
            $this->logger->info(sprintf('APM transaction stopped for "%s"', $name));
        }

        if ($this->userContextProvider instanceof UserContextProviderInterface) {
            $transaction->setUserContext($this->userContextProvider->getUserContext());
        }

        try {
            $sent = $this->agent->send();
        } catch (\Exception $e) {
            $sent = false;
        }

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->info(sprintf('APM transaction %s: "%s"', $sent ? 'sent' : 'not sent', $name));
        }
    }

    private function getMeta(Response $response): array
    {
        $meta = [
            'result' => $response->getStatusCode(),
        ];

        return $meta;
    }
}
