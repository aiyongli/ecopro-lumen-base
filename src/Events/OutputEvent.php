<?php

namespace Ecopro\Base\Events;

class OutputEvent extends Event
{
    public $result;

    /**
     * @return static
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }
}
