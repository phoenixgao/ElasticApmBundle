<?php

namespace SpaceSpell\ElasticApmBundle\Agent;

use PhilKra\Agent;

trait AgentAwareTrait
{
    /**
     * @var Agent
     */
    protected $agent;

    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;

        return $this;
    }
}
