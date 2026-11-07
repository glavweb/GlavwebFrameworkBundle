<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Gedmo sortable position column.
 */
trait SortablePositionTrait
{
    #[Gedmo\SortablePosition]
    #[ORM\Column(options: ['comment' => 'Позиция'])]
    private int $position = -1;

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
