<?php declare(strict_types=1);

namespace KiryaDev\Admin\Traits;

trait HasDisabled
{
    /**
     * @var bool|null
     */
    public $disabled = null;

    /**
     * @var \Closure|bool
     */
    public $disableResolver = false;


    /**
     * @param  \Closure|bool  $bool
     * @return static
     */
    public function disable($bool = true)
    {
        $this->disableResolver = $bool;

        return $this;
    }

    /**
     * @return static
     */
    public function disableOnCreation()
    {
        return $this->disable(function ($object) {
            return ! $object->exists;
        });
    }

    /**
     * @return static
     */
    public function disableOnUpdate()
    {
        return $this->disable(function ($object) {
            return $object->exists;
        });
    }
}
