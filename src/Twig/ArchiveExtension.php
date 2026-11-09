<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Twig;

use Glavweb\FrameworkBundle\Model\ArchiveManager;
use InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Twig helpers/tests for Archivable entities.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ArchiveExtension extends AbstractExtension
{
    public function __construct(
        private readonly ArchiveManager $archiveManager,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('canArchive', $this->archiveManager->canArchive(...)),
            new TwigFunction('isArchived', $this->archiveManager->isArchived(...)),
            new TwigFunction('isArchivable', $this->archiveManager->isArchivable(...)),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('archivable', [$this->archiveManager, 'isArchivable']),
            new TwigTest('archived', [$this->archiveManager, 'isArchived']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('notArchived', $this->filterIsNotArchived(...)),
        ];
    }

    /**
     * @param array<mixed> $object
     *
     * @return array<mixed>
     */
    public function filterIsNotArchived(mixed $object): array
    {
        if (is_array($object)) {
            return array_filter($object, [$this->archiveManager, 'isNotArchived']);
        }

        throw new InvalidArgumentException('notArchived filter expects an array.');
    }
}
