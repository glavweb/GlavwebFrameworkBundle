<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

/**
 * Entity validation failure (managers → admin/API).
 *
 * @author nilov <nilov@glavweb.ru>
 */
class InvalidEntityException extends Exception
{
    protected object $entity;

    protected ConstraintViolationList $violations;

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->violations = new ConstraintViolationList();
    }

    public static function onEntity(object $entity, ConstraintViolationListInterface $violations): static
    {
        $exception = new static(
            'Validation failed for '.$entity::class.
            ' with '.$violations->count().' violation(s).'.
            (string) $violations
        );

        $exception->entity = $entity;
        $exception->violations->addAll($violations);

        return $exception;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
