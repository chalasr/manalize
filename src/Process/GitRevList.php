<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Process;

use Symfony\Component\Process\Process;

final class GitRevList extends Process
{
    public function __construct($cwd)
    {
        parent::__construct('git rev-list --tags --max-count=1', $cwd);
    }
}
