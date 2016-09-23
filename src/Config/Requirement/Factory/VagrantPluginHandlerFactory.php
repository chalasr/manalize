<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement\Factory;

use Manala\Config\Requirement\Processor\VagrantPluginProcessor;
use Manala\Config\Requirement\SemVer\VagrantPluginVersionParser;

/**
 * Factory that instantiates the concrete processor and version parser to handle vagrant plugin requirements.
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class VagrantPluginHandlerFactory implements HandlerFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return VagrantPluginProcessor
     */
    public function getProcessor()
    {
        return new VagrantPluginProcessor();
    }

    /**
     * {@inheritdoc}
     *
     * @return VagrantPluginVersionParser
     */
    public function getVersionParser()
    {
        return new VagrantPluginVersionParser();
    }
}
