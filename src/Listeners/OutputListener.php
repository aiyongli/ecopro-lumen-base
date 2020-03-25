<?php

namespace Ecopro\Base\Listeners;

use Ecopro\Base\Concerns\Helper;
use Ecopro\Base\Events\OutputEvent;

class OutputListener
{
    use Helper;

    /**
     * Handle the event.
     *
     * @param  OutputEvent  $event
     * @return void
     */
    public function handle(OutputEvent $event)
    {
        $log = json_encode($event->result);

        $this->logHelper()->debug($log, 'OUTPUT');
    }
}
