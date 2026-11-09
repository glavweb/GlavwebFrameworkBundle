<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Entity;

/**
 * Archivable entity that also tracks who archived it (`archivedBy`).
 *
 * Use with {@see Archivable} / {@see ArchivableTrait} and {@see ArchivedByAwareTrait}.
 * UserEntityInterface is resolved to the project User via `glavweb_framework.user_class`.
 *
 * @author nilov <nilov@glavweb.ru>
 */
interface ArchivedByAware extends Archivable
{
    public function getArchivedBy(): ?UserEntityInterface;

    public function setArchivedBy(?UserEntityInterface $archivedBy): static;
}
