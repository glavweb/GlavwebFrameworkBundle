# glavweb/framework-bundle

Верхний слой Glavweb-приложения: DQL, DBAL, utils, маркеры сущностей, `make:manager` / `make:repository`.

Нижние бандлы (`admin`, `extra`, `uploader`, `datagrid`, `data-schema`) **не зависят** от этого пакета; `api-bundle` — зависит.

## User

Сущность `User`, токены, менеджеры — в проекте. Бандл даёт только контракт:

```php
use Glavweb\FrameworkBundle\Entity\UserEntityInterface; // getId(), isEnabled() + Symfony UserInterface
```

`UserEntityInterface` — цель `doctrine.orm.resolve_target_entities`, поэтому общие traits могут ссылаться на пользователя,
не зная проектного класса:

```yaml
# config/packages/glavweb_framework.yaml
glavweb_framework:
    user_class: App\Entity\User\User   # default
    check_user_listener: true          # default; разлогин заблокированного (isEnabled() === false) на kernel.response
```

## Archivable + archivedBy

```php
use Glavweb\FrameworkBundle\Entity\Archivable;
use Glavweb\FrameworkBundle\Entity\ArchivableTrait;
use Glavweb\FrameworkBundle\Entity\ArchivedByTrait;

class Article implements Archivable
{
    use ArchivableTrait;
    use ArchivedByTrait; // ManyToOne → UserEntityInterface → user_class
}
```

`ArchiveManager::archive()` проставляет `archivedBy` текущим пользователем, если у сущности есть `setArchivedBy()`.
