<?php

namespace SpaceSpell\ElasticApmBundle\ContextProvider;

interface UserContextProviderAwareInterface
{
    public function setUserContextProvider(UserContextProviderInterface $userContextProvider);
}
