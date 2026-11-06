<?php echo "<?php\n\n"; ?>
declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use <?php echo $modelClass->getFullName(); ?>;
use Doctrine\ORM\EntityRepository;

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
 *
 * @extends EntityRepository<<?php echo $modelClass->getShortName(); ?>>
 */
class <?php echo $class_name; ?> extends EntityRepository
{
}
