<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Exception;

class BadMethodCallException extends \BadMethodCallException implements ExceptionInterface
{
    /**
     * @param \Exception $exception
     * @return self
     */
    public static function fromException(\Exception $exception)
    {
        return new self($exception->getMessage(), $exception->getCode(), $exception);
    }
}
