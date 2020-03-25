<?php

namespace Ecopro\Base\Events;

use Illuminate\Http\Request;

class RequestEvent extends Event
{
    /**
     * @var Request
     */
    public $request;

    /**
     * @return static
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
