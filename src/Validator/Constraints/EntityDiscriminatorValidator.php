<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class EntityDiscriminatorValidator.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
class EntityDiscriminatorValidator extends ChoiceValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntityDiscriminator) {
            throw new UnexpectedTypeException($constraint, EntityDiscriminator::class);
        }

        /** @var class-string $rootClass */
        $rootClass = $constraint->rootClass;
        $classMetadata = $this->entityManager->getClassMetadata($rootClass);

        $constraint->choices = array_keys($classMetadata->discriminatorMap);

        parent::validate($value, $constraint);
    }
}
