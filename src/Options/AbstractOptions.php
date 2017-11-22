<?php

/**
 * Copyright 2017 American Express Travel Related Services Company, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
 * or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

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
