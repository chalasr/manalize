<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Functional;

use Manala\Command\Diff;
use Symfony\Component\Console\Tester\CommandTester;

class DiffTest extends \PHPUnit_Framework_TestCase
{
    const ACME_PROJECT_WD = MANALA_DIR.'/tests/fixtures/Command/DiffTest/AcmeProject';
    const EXPECTED_OUTPUT_FILE = MANALA_DIR.'/tests/fixtures/Command/DiffTest/expected_acme.patch';

    public function testExecute()
    {
        $tester = new CommandTester(new Diff());
        $tester
            ->setInputs(['manala', 'symfony'])
            ->execute(['cwd' => static::ACME_PROJECT_WD]);

        if (0 !== $tester->getStatusCode()) {
            echo $tester->getDisplay();
        }

        $this->assertSame(0, $tester->getStatusCode());

        $this->assertStringEqualsFile(static::EXPECTED_OUTPUT_FILE, $tester->getDisplay(true));
    }
}
