<?php
namespace App\Traits;

trait Savable
{
    /**
     * @param object|array $data
     */
    abstract public function packSaveAttributtes($data);

    /**
     * @return static
     */
    public function newSavable($data)
    {
        return $this->newInstance($this->packSaveAttributtes($data));
    }
}
