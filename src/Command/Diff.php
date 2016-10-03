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

use Manala\Env\EnvEnum;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
class Diff extends Command
{
    /** @var Filesystem */
    private $fs;

    public function __construct()
    {
        parent::__construct('diff');

        $this->fs = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Computes the diff between the current project and the Manala templates.')
            ->addArgument('cwd', InputArgument::OPTIONAL, 'The path of the application', getcwd())
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

        $originalTemplatePath = MANALA_DIR.'/src/Resources/'.$envType;

        $templatePath = $this->copyTemplateToTmpDir($originalTemplatePath);

        $colorOpt = $output->isDecorated() ? '--color' : '--no-color';

        $process = new Process("git diff --diff-filter=ACMRTUXB --no-index --patch $colorOpt ./ $templatePath", $cwd);

        $process->run(function ($type, $buffer) use ($output, $templatePath) {
            $output->writeln(str_replace($templatePath, '/.', $buffer));
        });

        $this->fs->remove($templatePath);

        return 0;
    }

    private function copyTemplateToTmpDir($templatePath)
    {
        $tmpPath = sys_get_temp_dir().'/Manala';

        if ($this->fs->exists($tmpPath)) {
            $this->fs->remove($tmpPath);
        }

        $this->fs->mkdir($tmpPath);

        $directoryIterator = new \RecursiveDirectoryIterator($templatePath, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $this->fs->mkdir($tmpPath.DIRECTORY_SEPARATOR.$iterator->getSubPathname());
            } else {
                $this->fs->copy($item, $tmpPath.DIRECTORY_SEPARATOR.$iterator->getSubPathname());
            }
        }

        return $tmpPath;
    }
}
