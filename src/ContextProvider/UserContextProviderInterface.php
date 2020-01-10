<?php

namespace SpaceSpell\ElasticApmBundle\ContextProvider;

interface UserContextProviderInterface
{
    public function getUserContext() : array;
}
