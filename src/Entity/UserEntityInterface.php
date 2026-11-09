<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Minimal contract bundles rely on for the project User entity.
 *
 * Target of `doctrine.orm.resolve_target_entities` (see `glavweb_framework.user_class`) — associations
 * in shared traits (e.g. {@see ArchivedByAwareTrait}) resolve to the project User instead of a concrete class.
 * Passwords, tokens and lifecycle stay in the project (`App\Model\User\UserManager`).
 *
 * @author nilov <nilov@glavweb.ru>
 */
interface UserEntityInterface extends UserInterface
{
    public function getId(): ?int;

    /**
     * Disabled users are logged out by CheckUserListener.
     */
    public function isEnabled(): bool;
}
