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

use Manala\Config\Vars;
use Manala\Env\EnvEnum;
use Manala\Env\EnvFactory;
use Manala\Process\Setup as SetupProcess;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Setups a full stack environment on top of Manala' ansible roles.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Setup extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Configures your environment on top of Manala ansible roles')
            ->addArgument('cwd', InputArgument::OPTIONAL, 'The path of the application for which to setup the environment', getcwd())
            ->addOption('env', null, InputOption::VALUE_OPTIONAL, 'One of the supported environment types', 'symfony');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = realpath($input->getArgument('cwd'));
        $envType = EnvEnum::create($input->getOption('env'));

        if (!is_dir($cwd)) {
            throw new \RuntimeException(sprintf('The working directory "%s" doesn\'t exist.', $cwd));
        }

        $io = new SymfonyStyle($input, $output);
        $io->setDecorated(true);
        $io->comment(sprintf('Composing your <info>%s</info> environment', (string) $envType));

        $vars = new Vars($io->ask('Vendor name', null, [$this, 'validateVar']), $io->ask('App name', null, [$this, 'validateVar']));
        $process = new SetupProcess($cwd);

        foreach ($this->doSetup(EnvFactory::createEnv($envType), $vars, $cwd) as $targetFile) {
            $io->writeln(sprintf('- %s', str_replace($cwd.DIRECTORY_SEPARATOR, '', $targetFile)));
        }

        $io->success('Environment successfully created');

        return 0;
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
            throw new RuntimeException(sprintf('This value must contain only alphanumeric characters and hyphens.', $value));
        }

        return $value;
    }

    /**
     * Prepare configuration dumps for the given process.
     *
     * @param Env  $env
     * @param Vars $vars
     *
     * @return \Generator The dumped file path
     */
    private function doSetup(Env $env, Vars $vars, $cwd)
    {
        $fs = new Filesystem();

        foreach ($env->getConfigs() as $config) {
            $baseTarget = $cwd.DIRECTORY_SEPARATOR.$config->getPath();
            $template = $config->getTemplate();

            foreach ($config->getFiles() as $file) {
                $target = str_replace($config->getOrigin(), $baseTarget, $file->getPathName());
                $dump = ((string) $template === $file->getRealPath()) ? Dumper::dump($config, $vars) : file_get_contents($file);
                $fs->dumpFile($target, $dump);

                yield $target;
            }
        }
    }
}
