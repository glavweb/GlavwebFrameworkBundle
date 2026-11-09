<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Provides an `archived_at` column for entities implementing Archivable.
 *
 * For archive + author use {@see ArchivedByAware} / {@see ArchivedByAwareTrait} (includes this trait).
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
trait ArchivableTrait
{
    #[ORM\Column(name: 'archived_at', type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => 'Время архивирования'])]
    private ?DateTime $archivedAt = null;

    public function isArchived(): bool
    {
        return null !== $this->archivedAt;
    }

    public function getArchivedAt(): ?DateTime
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?DateTime $archivedAt): static
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }
}
