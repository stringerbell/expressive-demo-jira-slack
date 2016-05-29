<?php

namespace App\Utility;

class StringIterator implements \Iterator
{
    private $string;

    private $current;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function current()
    {
        return $this->string[$this->current];
    }

    public function next()
    {
        ++$this->current;
    }

    public function key()
    {
        return $this->current;
    }

    public function valid()
    {
        return $this->current < strlen($this->string);
    }

    public function rewind()
    {
        $this->current = 0;
    }
}
