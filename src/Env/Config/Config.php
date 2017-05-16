<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config;

use Manala\Manalize\Env\Config\Variable\Variable;
use Manala\Manalize\Env\EnvName;

/**
 * Represents a config part of a Manala environment.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
abstract class Config
{
    /**
     * @var EnvName
     */
    protected $envName;

    /**
     * @var Variable[]
     */
    protected $vars;

    public static function create(EnvName $envName, array $vars)
    {
        return new static($envName, ...$vars);
    }

    public function __construct(EnvName $envName, Variable ...$vars)
    {
        $this->envName = $envName;
        $this->vars = $vars;
    }

    public function getFiles(): \Traversable
    {
        $origin = $this->getOrigin();

        yield ($origin instanceof \SplFileInfo) ? $origin : new \SplFileInfo($origin);
    }

    /**
     * Gets the configuration template(s).
     *
     * @return \SplFileInfo
     */
    public function getOrigin(): \SplFileInfo
    {
        return new \SplFileInfo(MANALIZE_HOME.'/templates/'.$this->envName->getValue());
    }

    /**
     * Returns the template to render.
     *
     * @return \SplFileInfo
     */
    public function getTemplate()
    {
        return new \SplFileInfo((string) $this->getOrigin().'.twig');
    }

    /**
     * Returns the variables to be used for rendering the template.
     *
     * @return Variable[]
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @return null|string A file to find in the "dist" directory of
     *                     the corresponding template and to place at
     *                     the root directory of the workspace, e.g.
     *                     'Makefile' => dump from "TEMPLATE-DIR/dist/Makefile" to "Makefile"
     */
    public function getDist()
    {
    }
}
