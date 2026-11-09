<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Entity;

use DateTime;

/**
 * Marker for entities that can be soft-archived via archivedAt.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
interface Archivable
{
    public function isArchived(): bool;

    public function getArchivedAt(): ?DateTime;

    public function setArchivedAt(?DateTime $archivedAt): static;
}
