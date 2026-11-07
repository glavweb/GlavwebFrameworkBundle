<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Provides a `published` column for entities implementing Publishable.
 */
trait PublishableTrait
{
    #[ORM\Column(name: 'published', type: Types::BOOLEAN, options: ['comment' => 'Опубликовано'])]
    private bool $published = false;

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }
}
