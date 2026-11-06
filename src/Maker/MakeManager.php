<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Maker;

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
 * Class MakeManager.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MakeManager extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:manager';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new manager class')
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

        $managerClass = $generator->createClassNameDetails(
            $modelArgument,
            'Model\\',
            'Manager'
        );

        $repositoryClass = $generator->createClassNameDetails(
            $modelArgument,
            'Repository\\',
            'Repository'
        );

        $generator->generateClass(
            $managerClass->getFullName(),
            __DIR__.'/../../templates/skeleton/manager/Manager.tpl.php',
            [
                'modelClass' => $modelClass,
                'repositoryClass' => $repositoryClass,
                'author' => $input->getOption('author'),
                'authorEmail' => $input->getOption('author-email'),
            ]
        );

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
    }
}
