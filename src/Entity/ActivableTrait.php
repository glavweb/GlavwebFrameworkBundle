<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Provides an `active` column for entities implementing Activable.
 */
trait ActivableTrait
{
    #[ORM\Column(name: 'active', type: Types::BOOLEAN, options: ['comment' => 'Активен'])]
    private bool $active = false;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
