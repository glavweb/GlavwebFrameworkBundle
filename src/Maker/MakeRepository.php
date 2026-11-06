<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Maker;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeRepository.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MakeRepository extends AbstractMaker
{
    private EntityManager $entityManager;

    /**
     * MakeAdmin constructor.
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $doctrine->getManager();
        $this->entityManager = $entityManager;
    }

    public static function getCommandName(): string
    {
        return 'make:repository';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new repository class')
            ->addArgument('model', InputArgument::REQUIRED, 'The name of the model class (e.g. <fg=yellow>Article</>)')
            ->addOption('author', null, InputOption::VALUE_OPTIONAL, 'The author of class')
            ->addOption('author-email', null, InputOption::VALUE_OPTIONAL, 'The author e-mail of class')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $modelArgument = $input->getArgument('model');

        $modelClass = $generator->createClassNameDetails(
            $modelArgument,
            'Entity\\',
            ''
        );

        $repositoryClass = $generator->createClassNameDetails(
            $modelArgument,
            'Repository\\',
            'Repository'
        );

        $generator->generateClass(
            $repositoryClass->getFullName(),
            __DIR__.'/../../templates/skeleton/entity/Repository.tpl.php',
            [
                'modelClass' => $modelClass,
                'author' => $input->getOption('author'),
                'authorEmail' => $input->getOption('author-email'),
            ]
        );

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
    }
}
