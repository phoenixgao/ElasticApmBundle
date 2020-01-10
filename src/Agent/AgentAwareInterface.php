<?php

namespace SpaceSpell\ElasticApmBundle\Agent;

use PhilKra\Agent;

interface AgentAwareInterface
{
    public function setAgent(Agent $agent);
}
