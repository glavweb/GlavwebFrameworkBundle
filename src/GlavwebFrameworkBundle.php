<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle;

use Glavweb\FrameworkBundle\Entity\UserEntityInterface;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Class GlavwebFrameworkBundle.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
class GlavwebFrameworkBundle extends AbstractBundle
{
    public const string DEFAULT_USER_CLASS = 'App\\Entity\\User\\User';

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/packages/*.yaml');

        if (!$builder->hasExtension('doctrine')) {
            return;
        }

        $userClass = self::DEFAULT_USER_CLASS;
        foreach ($builder->getExtensionConfig('glavweb_framework') as $config) {
            if (isset($config['user_class']) && \is_string($config['user_class']) && '' !== $config['user_class']) {
                $userClass = $config['user_class'];
            }
        }

        $builder->prependExtensionConfig('doctrine', [
            'orm' => [
                'resolve_target_entities' => [
                    UserEntityInterface::class => $userClass,
                ],
            ],
        ]);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->setParameter('glavweb_framework.user_class', $config['user_class']);
        $builder->setParameter('glavweb_framework.maker.entity_namespace', $config['maker']['entity_namespace']);

        $container->import('../config/services.yaml');

        if ($config['check_user_listener']) {
            $container->import('../config/services_check_user.yaml');
        }

        if (class_exists(AbstractMaker::class)) {
            $container->import('../config/services_maker.yaml');
        }
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('user_class')
                    ->info('Project User entity implementing Glavweb\FrameworkBundle\Entity\UserEntityInterface (target of doctrine resolve_target_entities).')
                    ->defaultValue(self::DEFAULT_USER_CLASS)
                    ->cannotBeEmpty()
                ->end()
                ->booleanNode('check_user_listener')
                    ->info('Register CheckUserListener: drop the session / token of a disabled user on kernel.response.')
                    ->defaultTrue()
                ->end()
                ->arrayNode('maker')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('entity_namespace')->defaultValue('App\\Entity\\')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
