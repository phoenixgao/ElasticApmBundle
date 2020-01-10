<?php

namespace SpaceSpell\ElasticApmBundle\ContextProvider;

use UserContextProviderInterface;

interface UserContextProviderAwareInterface
{
    public function setUserContextProvider(UserContextProviderInterface $userContextProvider);
}
