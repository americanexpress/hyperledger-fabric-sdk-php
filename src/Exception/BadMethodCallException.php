<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Exception;

class BadMethodCallException extends \BadMethodCallException implements ExceptionInterface
{
    /**
     * @param \Exception $exception
     * @param string $message
     * @return self
     */
    public static function fromException(\Exception $exception, string $message = '')
    {
        return new self($message ?: $exception->getMessage(), $exception->getCode(), $exception);
    }
}
