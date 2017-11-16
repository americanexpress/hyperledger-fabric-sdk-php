<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Options;

use AmericanExpress\HyperledgerFabricClient\Exception\BadMethodCallException;
use Assert\Assertion;
use Assert\AssertionFailedException;

abstract class AbstractOptions
{
    /**
     * AbstractOptions constructor.
     * @param iterable $options
     */
    public function __construct(iterable $options = [])
    {
        foreach ($options as $key => $value) {
            $setter = 'set' . str_replace('_', '', $key);

            $callable = [$this, $setter];

            try {
                Assertion::isCallable($callable);
            } catch (AssertionFailedException $e) {
                throw BadMethodCallException::fromException($e, sprintf(
                    '%s::%s is not callable.',
                    get_called_class(),
                    $setter
                ));
            }

            call_user_func($callable, $value);
        }
    }
}
