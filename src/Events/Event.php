<?php

namespace Ecopro\Base\Events;

use Ecopro\Base\Concerns\Instance;
use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use Instance;
    use SerializesModels;

    /**
     * Fire this event and call the listeners.
     * @return static
     */
    public function fire($payload = [], $halt = false)
    {
        event($this, $payload, $halt);

        return $this;
    }
}
