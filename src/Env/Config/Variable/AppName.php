<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config\Variable;

/**
 * The "APP" var.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class AppName extends Variable
{
    public function __construct(string $value)
    {
        parent::__construct('app_name', $value);
    }

    /**
     * {@inheritdoc}
     */
    public static function validate($value, $throwException = true)
    {
        $isValid = preg_match('/^([-.A-Z0-9])*$/i', $value);

        if (!$isValid && $throwException) {
            throw new \InvalidArgumentException(sprintf(
                'This value must contain only alphanumeric characters, dots and hyphens.',
                $value
            ));
        }

        return $isValid ? strtolower($value) : false;
    }
}
