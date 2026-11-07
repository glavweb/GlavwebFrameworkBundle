<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Glavweb\FrameworkBundle\Entity\Publishable;

/**
 * Class PublishableFilter.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class PublishableFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        $className = $targetEntity->reflClass->getName();

        if (!\in_array(Publishable::class, class_implements($className) ?: [], true)) {
            return '';
        }

        if (!$targetEntity->hasField('published')) {
            return '';
        }

        return $targetTableAlias.'.published';
    }
}
