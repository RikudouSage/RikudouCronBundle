<?php

namespace Rikudou\CronBundle\Cron;

class CronJobsList implements \Countable, \ArrayAccess, \Iterator
{

    /**
     * @var int The variable for iterator
     */
    private $i = 0;

    /**
     * @var string[]
     */
    private $classes;

    /**
     * CronJobsList constructor.
     * @param string[] $classes
     */
    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->classes);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     * @internal
     */
    public function offsetExists($offset)
    {
        return isset($this->classes[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     * @internal
     */
    public function offsetGet($offset)
    {
        return $this->classes[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     * @internal
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException("This is a read-only collection");
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @internal
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException("This is a read-only collection");
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return string Can return any type.
     * @since 5.0.0
     * @internal
     */
    public function current()
    {
        return $this->offsetGet($this->i);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     * @internal
     */
    public function next()
    {
        $this->i++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     * @internal
     */
    public function key()
    {
        return $this->i;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     * @internal
     */
    public function valid()
    {
        return $this->offsetExists($this->i);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     * @internal
     */
    public function rewind()
    {
        $this->i = 0;
    }
}