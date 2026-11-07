<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Glavweb\FrameworkBundle\Entity\ConditionDeletable;

/**
 * Class DeletionManager.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
class DeletionManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function canDelete(object $object): bool
    {
        if ($object instanceof ConditionDeletable) {
            $useDefaultCondition = true;
            $result = $object->canBeDeleted($useDefaultCondition);

            if (!$result && !$useDefaultCondition) {
                return false;
            }
        }

        return $this->defaultCanDelete($object);
    }

    private function defaultCanDelete(object $object): bool
    {
        if (!method_exists($object, 'getId') || !$object->getId()) {
            return false;
        }

        $metadata = $this->entityManager->getClassMetadata($object::class);
        $dependencies = $this->getDependencies($metadata);

        foreach ($dependencies as ['class' => $class, 'association' => $association]) {
            /** @var AssociationMapping $association */
            if ($association->type() & ClassMetadata::TO_MANY) {
                $queryPart = "JOIN t.{$association->fieldName} a WHERE a = (:id)";
            } else {
                $queryPart = "WHERE t.{$association->fieldName} = :id";
            }

            $result = $this->entityManager
                ->createQuery("SELECT 1 FROM $class t $queryPart")
                ->setParameters([
                    'id' => $object->getId(),
                ])
                ->setMaxResults(1)
                ->getScalarResult();

            if (!empty($result)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<array{class: class-string, association: AssociationMapping}>
     */
    private function getDependencies(ClassMetadata $metadata): array
    {
        $allClassesMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $classes = [$metadata->getName(), ...$metadata->parentClasses];

        return array_reduce($allClassesMetadata, static function (array $result, ClassMetadata $classMetadata) use ($classes): array {
            $associations = array_filter(
                $classMetadata->associationMappings,
                static function (AssociationMapping $associationMapping) use ($classes): bool {
                    return \in_array($associationMapping->targetEntity, $classes, true)
                        && $associationMapping->isOwningSide();
                }
            );

            foreach ($associations as $association) {
                $result[] = [
                    'class' => $classMetadata->getName(),
                    'association' => $association,
                ];
            }

            return $result;
        }, []);
    }
}
