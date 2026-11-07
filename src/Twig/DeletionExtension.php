<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Twig;

use Glavweb\FrameworkBundle\Model\DeletionManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Class DeletionExtension.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
class DeletionExtension extends AbstractExtension
{
    public function __construct(
        private readonly DeletionManager $deletionManager,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('canDelete', $this->deletionManager->canDelete(...)),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('deletable', [$this->deletionManager, 'canDelete']),
        ];
    }
}
