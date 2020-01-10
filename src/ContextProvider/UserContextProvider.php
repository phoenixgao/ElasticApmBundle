<?php

namespace SpaceSpell\ElasticApmBundle\ContextProvider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserContextProvider implements UserContextProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var array
     */
    protected $userContext = [];

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->parseToken();
    }

    private function parseToken()
    {
        if ($this->tokenStorage && $token = $this->tokenStorage->getToken()) {
            $user = $token->getUser();
        }

        if ($user instanceof UserInterface) {
            $this->userContext['username'] = $user->getUsername();
            $this->userContext['roles'] = $user->getRoles();
            if (method_exists($user, 'getId')) {
                $this->userContext['id'] = $user->getId();
            }
            if (method_exists($user, 'getEmail')) {
                $this->userContext['email'] = $user->getEmail();
            }
        }
    }

    public function getUserContext() : array
    {
        return $this->userContext;
    }
}
