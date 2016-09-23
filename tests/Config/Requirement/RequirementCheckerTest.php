<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Config\Requirement;

use Manala\Config\Requirement\Exception\MissingRequirementException;
use Manala\Config\Requirement\Factory\HandlerFactoryInterface;
use Manala\Config\Requirement\Factory\HandlerFactoryResolver;
use Manala\Config\Requirement\Processor\AbstractProcessor;
use Manala\Config\Requirement\Requirement;
use Manala\Config\Requirement\Common\RequirementLevel;
use Manala\Config\Requirement\RequirementChecker;
use Manala\Config\Requirement\SemVer\BinaryVersionParser;
use Manala\Config\Requirement\Violation\RequirementViolationLabelBuilder;
use Manala\Config\Requirement\Violation\RequirementViolationList;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class RequirementCheckerTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequirementChecker */
    private $requirementChecker;

    /** @var HandlerFactoryInterface|ObjectProphecy */
    private $handlerFactory;

    /** @var AbstractProcessor|ObjectProphecy */
    private $processor;

    public function setUp()
    {
        $this->handlerFactory = $this->prophesize(HandlerFactoryInterface::class);
        $this->processor = $this->prophesize(AbstractProcessor::class);
        $this->handlerFactory->getProcessor()->willReturn($this->processor->reveal());
        $handlerFactoryResolver = $this->prophesize(HandlerFactoryResolver::class);
        $handlerFactoryResolver->getHandlerFactory(Argument::any())->willReturn($this->handlerFactory);

        $this->requirementChecker = new RequirementChecker(
            $handlerFactoryResolver->reveal(),
            new RequirementViolationLabelBuilder()
        );
    }

    public function testCheckerShouldCreateAViolationIfRequiredBinaryIsMissing()
    {
        $violationList = new RequirementViolationList();
        $this->assertEquals(0, count($violationList));

        $requirement = new Requirement(
            'vagrant',
            Requirement::TYPE_BINARY,
            RequirementLevel::REQUIRED
        );

        $this->processor->process('vagrant')->willThrow(new MissingRequirementException());

        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEquals(1, count($violationList));
        $this->assertTrue($violationList->containsRequiredViolations());
        $this->assertFalse($violationList->containsRecommendedViolations());
    }

    public function testCheckerShouldCreateAViolationIfRecommendedBinaryIsMissing()
    {
        $violationList = new RequirementViolationList();

        $requirement = new Requirement(
            'vagrant',
            Requirement::TYPE_BINARY,
            RequirementLevel::RECOMMENDED
        );

        $this->processor->process('vagrant')->willThrow(new MissingRequirementException());

        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEquals(1, count($violationList));
        $this->assertFalse($violationList->containsRequiredViolations());
        $this->assertTrue($violationList->containsRecommendedViolations());
    }

    public function testCheckerShouldCreateAViolationIfRequiredBinaryHasInsufficientVersion()
    {
        $violationList = new RequirementViolationList();

        $requirement = new Requirement(
            'vagrant',
            Requirement::TYPE_BINARY,
            RequirementLevel::REQUIRED,
            '1.8.2'
        );

        $this->processor->process('vagrant')->willReturn('vagrant 1.7.2');
        $this->handlerFactory->getVersionParser()->willReturn(new BinaryVersionParser());
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEquals(1, count($violationList));
    }

    public function testCheckerShouldNotCreateAViolationIfBinaryIsOK()
    {
        $violationList = new RequirementViolationList();

        $requirement = new Requirement(
            'vagrant',
            Requirement::TYPE_BINARY,
            RequirementLevel::REQUIRED,
            '1.8.2'
        );

        $this->processor->process('vagrant')->willReturn('vagrant 1.8.4');
        $this->handlerFactory->getVersionParser()->willReturn(new BinaryVersionParser());
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEquals(0, count($violationList));
        $this->assertFalse($violationList->containsRecommendedViolations());
        $this->assertFalse($violationList->containsRequiredViolations());
    }
}
