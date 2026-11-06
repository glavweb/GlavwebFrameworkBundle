<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

/**
 * Class FixDefaultSchemaListener.
 *
 * Keep extra PostgreSQL schemas from the database on the ORM-generated schema so
 * `doctrine:migrations:diff` does not emit CREATE SCHEMA for them.
 *
 * The default schema (`public`) is left to Doctrine Migrations: DiffGenerator
 * already calls createNamespace() on the schema name. Adding it here as well
 * throws NamespaceAlreadyExists.
 *
 * @see https://github.com/doctrine/dbal/issues/1110
 * @see https://github.com/doctrine/migrations/pull/1463
 *
 * @author Melkij https://github.com/Melkij
 * @author Valentin Udaltsov https://github.com/vudaltsov
 */
#[AsDoctrineListener(event: ToolEvents::postGenerateSchema)]
class FixDefaultSchemaListener
{
    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schemaManager = $args->getEntityManager()
            ->getConnection()
            ->createSchemaManager();

        if (!$schemaManager instanceof PostgreSQLSchemaManager) {
            return;
        }

        $schema = $args->getSchema();
        $defaultNamespace = $schema->getName();

        foreach ($schemaManager->listSchemaNames() as $namespace) {
            if ($defaultNamespace !== '' && strcasecmp($namespace, $defaultNamespace) === 0) {
                continue;
            }

            if (!$schema->hasNamespace($namespace)) {
                $schema->createNamespace($namespace);
            }
        }
    }
}
