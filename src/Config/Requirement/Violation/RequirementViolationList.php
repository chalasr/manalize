<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement\Violation;

use Manala\Config\Requirement\Common\RequirementLevel;

/**
 * Iterable collection of requirement violations.
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class RequirementViolationList implements \Iterator, \ArrayAccess, \Countable
{
    /** @var int */
    private $position;

    /** @var RequirementViolation[] */
    private $violations = [];

    public function __construct()
    {
        $this->rewind();
        $this->violations = [];
    }

    public function addViolation(RequirementViolation $violation)
    {
        $this->violations[] = $violation;
    }

    /**
     * @return RequirementViolation[]
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * Does the violation list contain at least one violation with the given level ?
     *
     * @return bool
     */
    private function containsViolations($level)
    {
        foreach ($this->violations as $violation) {
            if ($violation->getLevel() === $level) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function containsRequiredViolations()
    {
        return $this->containsViolations(RequirementLevel::REQUIRED);
    }

    /**
     * @return bool
     */
    public function containsRecommendedViolations()
    {
        return $this->containsViolations(RequirementLevel::RECOMMENDED);
    }

    /**
     * {@inheritdoc}
     *
     * @return RequirementViolation
     */
    public function current()
    {
        return $this->violations[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->violations[$this->position]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->violations);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->violations[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->violations[$offset]) ? $this->violations[$offset] : null;
    }

    /**
     * {@inheritdoc}
     *
     * @param int                  $offset
     * @param RequirementViolation $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof RequirementViolation) {
            throw new \InvalidArgumentException();
        }

        if ($offset === null) {
            $this->violations[] = $value;

            return;
        }

        $this->violations[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->violations[$offset]);
    }


}
