<?php

namespace SpaceSpell\ElasticApmBundle\ContextProvider;

use UserContextProviderInterface;

trait UserContextProviderAwareTrait
{
    /**
     * @var UserContextProviderInterface
     */
    protected $userContextProvider;

    public function setUserContextProvider(UserContextProviderInterface $userContextProvider)
    {
        $this->userContextProvider = $userContextProvider;
    }
}
