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

use Manala\Config\Requirement\Requirement;

/**
 * This resolver implements the abstract factory pattern: it returns the proper factory that instantiates the concrete
 * handlers (processor and version parser) expected by a requirement (based on its type, eg binary or vagrant plugin).
 *
 * @see https://en.wikipedia.org/wiki/Abstract_factory_pattern
 */
class HandlerFactoryResolver
{
    /**
     * @param Requirement $requirement
     *
     * @return HandlerFactoryInterface
     */
    public function getHandlerFactory(Requirement $requirement)
    {
        $type = $requirement->getType();

        switch ($type) {
            case Requirement::TYPE_BINARY:
                return new BinaryHandlerFactory();
            case Requirement::TYPE_VAGRANT_PLUGIN:
                return new VagrantPluginHandlerFactory();
            default:
                throw new \InvalidArgumentException(sprintf('No handler factory for type %s', $type));
        }
    }
}
