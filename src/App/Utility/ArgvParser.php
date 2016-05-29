<?php

namespace App\Utility;

// stolen from http://stackoverflow.com/a/18229461
class ArgvParser extends StringIterator
{
    const TOKEN_DOUBLE_QUOTE = '"';
    const TOKEN_SINGLE_QUOTE = "'";
    const TOKEN_SPACE = ' ';
    const TOKEN_ESCAPE = '\\';

    public function parse()
    {
        $this->rewind();

        $args = [];

        while ($this->valid()) {
            switch ($this->current()) {
                case self::TOKEN_DOUBLE_QUOTE:
                case self::TOKEN_SINGLE_QUOTE:
                    $args[] = $this->QUOTED($this->current());
                    break;

                case self::TOKEN_SPACE:
                    $this->next();
                    break;

                default:
                    $args[] = $this->UNQUOTED();
            }
        }

        return $args;
    }

    private function QUOTED($enclosure)
    {
        $this->next();
        $result = '';

        while ($this->valid()) {
            if ($this->current() == self::TOKEN_ESCAPE) {
                $this->next();
                if ($this->valid() && $this->current() == $enclosure) {
                    $result .= $enclosure;
                } elseif ($this->valid()) {
                    $result .= self::TOKEN_ESCAPE;
                    if ($this->current() != self::TOKEN_ESCAPE) {
                        $result .= $this->current();
                    }
                }
            } elseif ($this->current() == $enclosure) {
                $this->next();
                break;
            } else {
                $result .= $this->current();
            }
            $this->next();
        }

        return $result;
    }

    private function UNQUOTED()
    {
        $result = '';

        while ($this->valid()) {
            if ($this->current() == self::TOKEN_SPACE) {
                $this->next();
                break;
            } else {
                $result .= $this->current();
            }
            $this->next();
        }

        return $result;
    }

    public static function parseString($input)
    {
        $parser = new self($input);

        return $parser->parse();
    }
}
