<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Command;

use Manala\Process\Build as BuildProcess;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Setups a full stack environment on top of Manala' ansible roles.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Build extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Builds a virtual machine from the configured environment')
            ->addArgument('cwd', InputArgument::OPTIONAL, 'The path of the application for which to build the vm', getcwd());
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = realpath($input->getArgument('cwd'));

        if (!is_dir($cwd)) {
            throw new \RuntimeException(sprintf('The working directory "%s" doesn\'t exist.', $cwd));
        }

        // TODO: In {@link Setup}, generates a .manala.yml with the env type then check guess storage dirs
        $this->emptyStorageDirectories($cwd);

        $io = new SymfonyStyle($input, $output);
        $io->comment('<info>Building your environment</info>');

        $process = new BuildProcess($cwd);
        $process->run(function ($type, $buffer) use ($io) {
            $io->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $io->warning(['An error occured during the process execution:', $process->getErrorOutput()]);

            return $process->getExitCode();
        }

        $io->success('Environment successfully built');

        return $process->getExitCode();
    }

    /**
     * Ensures storage directories are empty, otherwise remove them.
     *
     * @param string $cwd
     */
    protected function emptyStorageDirectories($cwd)
    {
        $fs = new Filesystem();
        $rootStorageDir = is_readable($cwd.'/var') ? $cwd.'/var' : $cwd.'/app';

        foreach (['/cache', '/logs'] as $dir) {
            if ($fs->exists($rootStorageDir.$dir)) {
                $fs->remove($rootStorageDir.$dir);
            }
        }
    }

    /**
     * Checks that a given configuration value is properly formatted.
     *
     * @param string $value The value to assert
     *
     * @return string The validated value
     *
     * @throws \InvalidArgumentException If the value is incorrect
     */
    public function validateVar($value)
    {
        if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
            throw new \RuntimeException(sprintf('This value must contain only alphanumeric characters and hyphens.', $value));
        }

        return $value;
    }
}
