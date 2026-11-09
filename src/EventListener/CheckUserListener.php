<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\EventListener;

use Glavweb\FrameworkBundle\Entity\UserEntityInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Invalidates the session when the authenticated user is disabled.
 *
 * Registered on kernel.response when `glavweb_framework.check_user_listener` is true (default).
 *
 * @author nilov <nilov@glavweb.ru>
 */
class CheckUserListener
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if (
            $event->isMainRequest()
            && !$event->getResponse()->isClientError()
            && !$event->getResponse()->isServerError()
            && $this->tokenStorage->getToken() instanceof TokenInterface
        ) {
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user instanceof UserEntityInterface && !$user->isEnabled()) {
                $this->tokenStorage->setToken(new NullToken());

                $request = $event->getRequest();
                if ($request->hasSession() && $request->getSession()->isStarted()) {
                    $request->getSession()->invalidate();
                }

                throw new AccessDeniedException();
            }
        }
    }
}
