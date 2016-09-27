<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement\SemVer;

interface VersionParserInterface
{
    /**
     * Get the required executable's version from its command output.
     *
     * @param string $name
     * @param string $consoleOutput
     *
     * @return string
     */
    public function getVersion($name, $consoleOutput);
}
