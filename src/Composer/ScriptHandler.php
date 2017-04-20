<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Composer;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Manala\Manalize\Command\Setup;

/**
 * Manalize composer script handler.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class ScriptHandler
{
    public static function manalizeProject()
    {
        self::bootstrap();

        return (new Setup())->run(new ArrayInput(['--env' => 'symfony']), new ConsoleOutput());
    }

    private static function bootstrap()
    {
        if (!defined('MANALIZE_DIR')) {
            require __DIR__.'/../bootstrap.php';
        }
    }
}
