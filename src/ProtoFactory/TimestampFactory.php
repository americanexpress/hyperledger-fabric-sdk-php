<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use Google\Protobuf\Timestamp;

class TimestampFactory
{
    /**
     * @param \DateTime|null $dateTime
     * @return Timestamp
     */
    public static function fromDateTime(\DateTime $dateTime = null): Timestamp
    {
        $dateTime = $dateTime ?: new \DateTime('now', timezone_open('UTC'));

        $timestamp = new Timestamp();
        $timestamp->setSeconds($dateTime->format('U'));
        $timestamp->setNanos($dateTime->format('u') * 1000);

        return $timestamp;
    }
}
