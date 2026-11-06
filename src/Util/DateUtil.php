<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Util;

use DateTime;

/**
 * Class DateUtil.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class DateUtil
{
    public static function getBeginHourTimestamp(int $time): int
    {
        $date = date('H.m.d.Y', $time);
        $dateParts = explode('.', $date);

        $timestamp = mktime((int) $dateParts[0], 0, 0, (int) $dateParts[1], (int) $dateParts[2], (int) $dateParts[3]);
        if (false === $timestamp) {
            throw new \LogicException('Некорректная дата');
        }

        return $timestamp;
    }

    public static function getBeginDayTimestamp(int $time): int
    {
        $date = date('d.m.Y', $time);
        $dateParts = explode('.', $date);

        $timestamp = mktime(0, 0, 0, (int) $dateParts[1], (int) $dateParts[0], (int) $dateParts[2]);
        if (false === $timestamp) {
            throw new \LogicException('Некорректная дата');
        }

        return $timestamp;
    }

    public static function getBeginMonthTimestamp(int $time): int
    {
        $date = date('m.Y', $time);
        $dateParts = explode('.', $date);

        $timestamp = mktime(0, 0, 0, (int) $dateParts[0], 1, (int) $dateParts[1]);
        if (false === $timestamp) {
            throw new \LogicException('Некорректная дата');
        }

        return $timestamp;
    }

    public static function getBeginYearTimestamp(int $time): int
    {
        $timestamp = mktime(0, 0, 0, 1, 1, (int) date('Y', $time));
        if (false === $timestamp) {
            throw new \LogicException('Некорректная дата');
        }

        return $timestamp;
    }

    public static function getBeginDayDateTime(DateTime $dateTime): DateTime
    {
        $beginDayTimestamp = self::getBeginDayTimestamp($dateTime->getTimestamp());

        $date = new DateTime();
        $date->setTimestamp($beginDayTimestamp);

        return $date;
    }

    public static function getEndDayDateTime(DateTime $dateTime): DateTime
    {
        $dateTime = self::getBeginDayDateTime($dateTime);
        $dateTime->modify('+1 day')->modify('-1 second');

        return $dateTime;
    }

    public static function getBeginMonthDateTime(DateTime $dateTime): DateTime
    {
        $beginMonthTimestamp = self::getBeginMonthTimestamp($dateTime->getTimestamp());

        $date = new DateTime();
        $date->setTimestamp($beginMonthTimestamp);

        return $date;
    }

    public static function getLastDayMonthDateTime(DateTime $dateTime): DateTime
    {
        return self::getBeginMonthDateTime($dateTime)->modify('+1 month')->modify('-1 day');
    }

    public static function getMonthNumberFromBegin(DateTime $dateTime): int
    {
        list($year, $month) = explode('|', $dateTime->format('Y|n'));

        return (int) $year * 12 + (int) $month;
    }
}
