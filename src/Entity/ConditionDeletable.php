<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Entity;

/**
 * Interface ConditionDeletable.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
interface ConditionDeletable
{
    public function canBeDeleted(bool &$useDefaultCondition): bool;
}
