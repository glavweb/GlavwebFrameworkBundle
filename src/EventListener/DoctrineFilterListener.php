<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Persistence\ManagerRegistry;
use Glavweb\FrameworkBundle\Controller\EnableActivableFilter;
use Glavweb\FrameworkBundle\Controller\EnablePublishableFilter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Enables/disables Doctrine SQL filters based on the current controller and user.
 *
 * Sonata CRUD controllers disable publishable/activable filters so admins see all rows.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
#[AsEventListener(event: 'kernel.controller')]
class DoctrineFilterListener
{
    private const string SONATA_CRUD_CONTROLLER = 'Sonata\\AdminBundle\\Controller\\CRUDController';

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function __invoke(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (
            is_object($controller)
            && class_exists(self::SONATA_CRUD_CONTROLLER)
            && is_a($controller, self::SONATA_CRUD_CONTROLLER)
        ) {
            $this->disableFilter('publishable_filter');
            $this->disableFilter('activable_filter');
        }

        if ($controller instanceof EnablePublishableFilter) {
            if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $this->enableFilter('publishable_filter');
            } else {
                $this->disableFilter('publishable_filter');
            }
        }

        if ($controller instanceof EnableActivableFilter) {
            if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $this->enableFilter('activable_filter');
            } else {
                $this->disableFilter('activable_filter');
            }
        }
    }

    private function enableFilter(string $filterName): SQLFilter
    {
        /** @var EntityManagerInterface $em */
        $em = $this->doctrine->getManager();

        if (!$em->getFilters()->isEnabled($filterName)) {
            $em->getFilters()->enable($filterName);
        }

        return $em->getFilters()->getFilter($filterName);
    }

    private function disableFilter(string $filterName): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->doctrine->getManager();

        if ($em->getFilters()->isEnabled($filterName)) {
            $em->getFilters()->disable($filterName);
        }
    }
}
