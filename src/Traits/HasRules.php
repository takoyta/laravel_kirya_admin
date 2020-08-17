<?php declare(strict_types=1);

namespace KiryaDev\Admin\Traits;


trait HasRules
{
    public $creationRules = [];

    public $updateRules = [];


    /**
     * @param  string  ...$rules
     * @return static
     */
    public function creationRules(...$rules)
    {
        $this->creationRules = array_merge($this->creationRules, $rules);

        return $this;
    }

    /**
     * @param  string  ...$rules
     * @return static
     */
    public function updateRules(...$rules)
    {
        $this->updateRules = array_merge($this->updateRules, $rules);

        return $this;
    }
}