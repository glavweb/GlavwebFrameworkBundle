<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Logging\Monolog;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * Class RequestIdProcessor.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
class RequestIdProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        if (isset($_SERVER['HTTP_X_REQUEST_ID'])) {
            $record->extra['requestId'] = $_SERVER['HTTP_X_REQUEST_ID'];
        }

        return $record;
    }
}
