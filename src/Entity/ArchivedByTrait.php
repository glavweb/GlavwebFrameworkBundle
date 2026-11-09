<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Optional archivedBy association for Archivable entities.
 *
 * Use together with ArchivableTrait. UserEntityInterface → project User is resolved via
 * `doctrine.orm.resolve_target_entities` (configured by `glavweb_framework.user_class`).
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
trait ArchivedByTrait
{
    #[ORM\JoinColumn(name: 'archived_by_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: UserEntityInterface::class)]
    private ?UserEntityInterface $archivedBy = null;

    public function getArchivedBy(): ?UserEntityInterface
    {
        return $this->archivedBy;
    }

    public function setArchivedBy(?UserEntityInterface $archivedBy): static
    {
        $this->archivedBy = $archivedBy;

        return $this;
    }
}
