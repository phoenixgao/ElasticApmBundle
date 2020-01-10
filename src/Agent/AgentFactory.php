<?php

namespace SpaceSpell\ElasticApmBundle\Agent;

use PhilKra\Agent;

class AgentFactory
{
    public static function createAgent(array $config)
    {
        $config['active'] = true;

        // Check PHP SAPI, if it's cli deactivate APM agent
        if (PHP_SAPI === 'cli') {
            $config['active'] = false;
        }

        return new Agent($config);
    }
}
