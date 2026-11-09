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
use Glavweb\FrameworkBundle\Entity\ArchivedByAware;
use Glavweb\FrameworkBundle\Entity\ArchivedByAwareTrait;

class Article implements ArchivedByAware // extends Archivable
{
    use ArchivedByAwareTrait; // archivedAt + archivedBy → UserEntityInterface → user_class
}
```

Только дата без автора: `Archivable` + `ArchivableTrait`.

`ArchiveManager::archive()` проставляет `archivedBy`, если сущность `instanceof ArchivedByAware`.
