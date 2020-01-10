<?php

namespace SpaceSpell\ElasticApmBundle\ContextProvider;

interface SharedContextProviderInterface
{
    public function getUser() : array;

    public function getCustom() : array;

    public function getTags() : array;
}
