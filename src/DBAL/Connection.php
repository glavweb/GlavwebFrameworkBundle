<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\DBAL;

use Exception;
use Throwable;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Result;

/**
 * Class Connection.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
class Connection extends \Doctrine\DBAL\Connection
{
    public const int RETRY_COUNT = 60;

    public const int RETRY_DELAY = 10 * 10 ** 6;

    public function executeQuery(string $sql, array $params = [], array $types = [], ?QueryCacheProfile $qcp = null): Result
    {
        return $this->reconnectIfFail(
            function () use ($sql, $params, $types, $qcp): Result {
                return parent::executeQuery($sql, $params, $types, $qcp);
            }
        );
    }

    public function executeStatement(string $sql, array $params = [], array $types = []): int|string
    {
        return $this->reconnectIfFail(
            function () use ($sql, $params, $types): int|string {
                return parent::executeStatement($sql, $params, $types);
            }
        );
    }

    /**
     * Does an action and reconnects if connection is lost.
     */
    public function reconnectIfFail(callable $action): mixed
    {
        $attempt = 1;
        while (true) {
            try {
                return $action();
            } catch (Exception $e) {
                if ($attempt >= self::RETRY_COUNT || !$this->isConnectionFailure($e->getPrevious())) {
                    throw $e;
                }
                $this->close();
                if ($attempt > 1) {
                    usleep(self::RETRY_DELAY);
                }
                ++$attempt;
            }
        }
    }

    private function isConnectionFailure(?Throwable $e): bool
    {
        if ($e instanceof \Doctrine\DBAL\Driver\PDO\Exception && 'HY000' === $e->getCode()) {
            return true;
        }

        return false;
    }
}
