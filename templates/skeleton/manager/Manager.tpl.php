<?php echo "<?php\n\n"; ?>
declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use <?php echo $modelClass->getFullName(); ?>;
use <?php echo $repositoryClass->getFullName(); ?>;
use App\Entity\User\User;
use Glavweb\FrameworkBundle\Exception\DeleteEntityException;
use Glavweb\FrameworkBundle\Exception\InvalidEntityException;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class <?php echo $class_name."\n"; ?>
<?php
if ($author) {
    $authorFull = $author;
    if ($authorEmail) {
        $authorFull .= ' <'.$authorEmail.'>';
    }

    echo " *\n";
    echo " * @author $authorFull\n";
}
?>
 */
class <?php echo $class_name."\n"; ?>
{
    private EntityManagerInterface $entityManager;
    private <?php echo $repositoryClass->getShortName(); ?> $repository;
    private ValidatorInterface $validator;

    public function __construct(ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        /** @var EntityManagerInterface $em */
        $em = $doctrine->getManager();

        $this->entityManager = $em;
        $this->repository = $em->getRepository(<?php echo $modelClass->getShortName(); ?>::class);
        $this->validator = $validator;
    }

    /**
     * @throws InvalidEntityException
     */
    public function create(<?php echo $modelClass->getShortName(); ?> $<?php echo lcfirst($modelClass->getShortName()); ?>, User $user): void
    {
        $violations = $this->validator->validate($<?php echo lcfirst($modelClass->getShortName()); ?>);
        if ($violations->count()) {
            throw InvalidEntityException::onEntity($<?php echo lcfirst($modelClass->getShortName()); ?>, $violations);
        }

        $this->entityManager->persist($<?php echo lcfirst($modelClass->getShortName()); ?>);
        $this->entityManager->flush();
    }

    /**
     * @throws InvalidEntityException
     */
    public function update(<?php echo $modelClass->getShortName(); ?> $<?php echo lcfirst($modelClass->getShortName()); ?>, User $user): void
    {
        $violations = $this->validator->validate($<?php echo lcfirst($modelClass->getShortName()); ?>);
        if ($violations->count()) {
            throw InvalidEntityException::onEntity($<?php echo lcfirst($modelClass->getShortName()); ?>, $violations);
        }

        $this->entityManager->flush();
    }

    /**
     * @throws DeleteEntityException
     */
    public function delete(<?php echo $modelClass->getShortName(); ?> $<?php echo lcfirst($modelClass->getShortName()); ?>): void
    {
        try {
            $this->entityManager->remove($<?php echo lcfirst($modelClass->getShortName()); ?>);
            $this->entityManager->flush();
        } catch (\PDOException|DBALException $e) {
            throw new DeleteEntityException(
                sprintf('Failed to delete the entity "%s" with ID "%s"', <?php echo $modelClass->getShortName(); ?>::class, $<?php echo lcfirst($modelClass->getShortName()); ?>->getId()),
                $e->getCode(),
                $e
            );
        }
    }
}
