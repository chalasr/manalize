<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement;

/**
 * Class that represents a host's requirement: name of the required binary (eg. Ansible, vagrant, etc.), required version, etc.
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class Requirement implements Common\RequirementLevelHolderInterface
{
    use Common\RequirementLevelHolderTrait;

    const TYPE_BINARY = 'binary';
    const TYPE_VAGRANT_PLUGIN = 'vagrant_plugin';

    /**
     * Name of the required executable. Eg. Ansible, vagrant, landrush etc.
     *
     * @var string
     */
    private $name;

    /** @var string */
    private $requiredVersion;

    /** @var string */
    private $versionComparator;

    /** @var string */
    private $type;

    /**
     * An optional help to display to the user if the binary is missing or if its version is inferior to the
     * required one. It can be for instance a link to the binary's download page.
     *
     * @var string|null
     */
    private $help;

    /**
     * @param string      $name
     * @param string      $type
     * @param int         $level
     * @param string      $requiredVersion
     * @param string      $versionComparator
     * @param string|null $help
     */
    public function __construct(
        $name,
        $type,
        $level,
        $requiredVersion = '0.0.0',
        $versionComparator = '>=',
        $help = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->level = $level;
        $this->requiredVersion = $requiredVersion;
        $this->versionComparator = $versionComparator;
        $this->help = $help;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRequiredVersion()
    {
        return $this->requiredVersion;
    }

    /**
     * @return string
     */
    public function getVersionComparator()
    {
        return $this->versionComparator;
    }

    /**
     * @return string|null
     */
    public function getHelp()
    {
        return $this->help;
    }
}
