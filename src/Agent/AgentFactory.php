<?php

namespace SpaceSpell\ElasticApmBundle\Agent;

use PhilKra\Agent;
use SpaceSpell\ElasticApmBundle\ContextProvider\SharedContextProviderInterface;

class AgentFactory
{
    public static function createAgent(array $config, SharedContextProviderInterface $sharedContextProvider = null)
    {
        $config['active'] = true;

        // Check PHP SAPI, if it's cli deactivate APM agent
        if (PHP_SAPI === 'cli') {
            $config['active'] = false;
        }

        $sharedContext = [];

        if ($sharedContextProvider instanceof SharedContextProviderInterface) {
            $sharedContext['user'] = $sharedContextProvider->getUser();
            $sharedContext['custom'] = $sharedContextProvider->getCustom();
            $sharedContext['tags'] = $sharedContextProvider->getTags();
        }

        return new Agent($config, $sharedContext);
    }
}
