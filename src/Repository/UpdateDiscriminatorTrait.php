<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Repository;

use RuntimeException;
use Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Trait UpdateDiscriminatorTrait.
 *
 * The solution from stackoverflow: https://stackoverflow.com/a/54750475
 */
trait UpdateDiscriminatorTrait
{
    /**
     * @return ClassMetadata
     */
    abstract public function getClassMetadata();

    /**
     * @return EntityManager
     */
    abstract public function getEntityManager();

    /**
     * @throws Exception
     */
    protected function updateDiscriminatorColumn(mixed $id, string $class): void
    {
        $classMetadata = $this->getClassMetadata();

        if (!in_array($class, $classMetadata->discriminatorMap)) {
            throw new RuntimeException('invalid discriminator class: '.$class);
        }

        $identifier = $classMetadata->fieldMappings[$classMetadata->identifier[0]]['columnName'];
        $column = $classMetadata->discriminatorColumn['fieldName'];
        $value = array_search($class, $classMetadata->discriminatorMap);

        $connection = $this->getEntityManager()->getConnection();
        $connection->update($classMetadata->table['name'], [
            $column => $value,
        ], [
            $identifier => $id,
        ]);
    }
}
