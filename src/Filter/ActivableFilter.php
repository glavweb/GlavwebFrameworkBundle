<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Glavweb\FrameworkBundle\Entity\Activable;

/**
 * Class ActiveFilter.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ActivableFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        $className = $targetEntity->reflClass->getName();

        if (!\in_array(Activable::class, class_implements($className) ?: [], true)) {
            return '';
        }

        return $targetTableAlias.'.active = true';
    }
}
