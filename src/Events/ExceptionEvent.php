<?php

namespace Ecopro\Base\Events;

use Throwable;

class ExceptionEvent extends Event
{
    /**
     * @var Throwable
     */
    public $exception;

    /**
     * @return static
     */
    public function setException(Throwable $exception)
    {
        $this->exception = $exception;

        return $this;
    }
}
