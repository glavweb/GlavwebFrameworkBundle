<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Model;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Glavweb\FrameworkBundle\Entity\Archivable;
use Glavweb\FrameworkBundle\Entity\ArchivedByAware;
use Glavweb\FrameworkBundle\Entity\UserEntityInterface;
use InvalidArgumentException;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Soft-archive helper for Archivable entities.
 *
 * If the entity implements {@see ArchivedByAware}, the current security user is stored as archivedBy.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ArchiveManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    public function isArchivable(mixed $objectOrClass): bool
    {
        return is_subclass_of($objectOrClass, Archivable::class);
    }

    public function canArchive(object $object): bool
    {
        if ($this->isArchivable($object)) {
            /** @var Archivable $object */
            return !$object->isArchived();
        }

        return false;
    }

    public function isArchived(object $object): bool
    {
        if ($this->isArchivable($object)) {
            /** @var Archivable $object */
            return $object->isArchived();
        }

        return false;
    }

    public function isNotArchived(object $object): bool
    {
        return !$this->isArchived($object);
    }

    public function archive(object $object, bool $andFlush = true): void
    {
        if (!$object instanceof Archivable) {
            throw new InvalidArgumentException('$object must be an instance of Archivable');
        }

        $object->setArchivedAt(new DateTime());
        $this->setArchivedByIfSupported($object, $this->security->getUser());

        if ($andFlush) {
            $this->entityManager->flush();
        }
    }

    public function unarchive(object $object, bool $andFlush = true): void
    {
        if (!$object instanceof Archivable) {
            throw new InvalidArgumentException('$object must be an instance of Archivable');
        }

        $object->setArchivedAt(null);
        $this->setArchivedByIfSupported($object, null);

        if ($andFlush) {
            $this->entityManager->flush();
        }
    }

    private function setArchivedByIfSupported(object $object, mixed $user): void
    {
        if (!$object instanceof ArchivedByAware) {
            return;
        }

        $object->setArchivedBy($user instanceof UserEntityInterface ? $user : null);
    }
}
