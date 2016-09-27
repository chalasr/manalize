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

use Manala\Config\Requirement\Common\RequirementLevel;
use Manala\Config\Requirement\Factory\HandlerFactoryResolver;
use Manala\Config\Requirement\Requirement;
use Manala\Config\Requirement\RequirementChecker;
use Manala\Config\Requirement\Violation\RequirementViolation;
use Manala\Config\Requirement\Violation\RequirementViolationLabelBuilder;
use Manala\Config\Requirement\Violation\RequirementViolationList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Checks if the host's environment meets Manala's requirements (Vagrant, Ansible, etc.).
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class CheckRequirements extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('check:requirements')
            ->setDescription("Checks if your host's environment meets Manala's requirements (Vagrant, Ansible, etc.)")
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addFormatter($output);

        $violationList = new RequirementViolationList();
        $requirementChecker = new RequirementChecker(
            new HandlerFactoryResolver(),
            new RequirementViolationLabelBuilder()
        );

        foreach($this->getRequirements() as $requirement) {
            $output->writeln('Checking ' . $requirement->getName());
            $requirementChecker->check($requirement, $violationList);
        }

        if (count($violationList) > 0) {
            foreach ($violationList as $violation) {
                $description = $this->getFormattedViolationDescription($violation);
                $output->writeln($description);
            }
        }

        if (!$violationList->containsRequiredViolations()) {
            $output->writeln('Congratulations ! Everything seems OK.');
            if ($violationList->containsRecommendedViolations()) {
                $output->writeln('Yet, some recommendations have been emitted (see above).');
            }
        }

    }

    /**
     * @return Requirement[]
     */
    private function getRequirements()
    {
        return [
            new Requirement(
                'vagrant',
                Requirement::TYPE_BINARY,
                RequirementLevel::REQUIRED,
                '>= 1.8.0 < 1.8.5 || ^1.8.6', // /!\ Exclude vagrant 1.8.5 (Manala incompatible)
                'See https://www.vagrantup.com/downloads.html'
            ),
            new Requirement(
                'landrush',
                Requirement::TYPE_VAGRANT_PLUGIN,
                RequirementLevel::REQUIRED,
                '^0.18',
                'See https://github.com/vagrant-landrush/landrush'
            ),
            new Requirement(
                'ansible',
                Requirement::TYPE_BINARY,
                RequirementLevel::RECOMMENDED,
                '^2.0.0',
                'Required only if you intend to use the deploy role. See http://docs.ansible.com/ansible/intro_installation.html'
            ),
        ];
    }

    /**
     * @param OutputInterface $output
     */
    private function addFormatter(OutputInterface $output)
    {
        $errorStyle = new OutputFormatterStyle('red');
        $output->getFormatter()->setStyle('error', $errorStyle);

        $warningStyle = new OutputFormatterStyle('yellow');
        $output->getFormatter()->setStyle('warning', $warningStyle);
    }

    /**
     * @param RequirementViolation $violation
     *
     * @return string Formatted violation description displayed to user.
     */
    private function getFormattedViolationDescription(RequirementViolation $violation)
    {
        $resultPattern = $violation->isRequired() ? '<error>%s</error>' : '<warning>%s</warning>';
        $displayedText = $violation->getLabel();
        $displayedText .= $violation->getHelp() ? ' ' . $violation->getHelp() : '';

        return sprintf($resultPattern, $displayedText);
    }

}
