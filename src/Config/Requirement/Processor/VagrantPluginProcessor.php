<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement\Processor;

use Manala\Config\Requirement\Exception\MissingRequirementException;

class VagrantPluginProcessor extends AbstractProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process($name)
    {
        $output = parent::process($name);

        // If output is empty, it means that the vagrant plugin identified by $name is not installed:
        if ($output == '') {
            throw new MissingRequirementException();
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand($name)
    {
        return sprintf('vagrant plugin list | grep %s', $name);
    }
}
