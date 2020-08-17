<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;


class Boolean extends FieldElement
{
    protected $labelResolver;


    protected function boot()
    {
        $this->rules('boolean');
    }

    public function display($object)
    {
        $value = parent::display($object);

        if (null !== $this->labelResolver && null !== $value) {
            $label = call_user_func($this->labelResolver, $object);
        } elseif (is_bool($value)) {
            $label = __($value ? 'Yes' : 'No');
        } else {
            $label = $value;
        }

        return view('admin::resource.detail-partials.boolean', compact('value', 'label'));
    }

    public function resolveLabelUsing(\Closure $callback)
    {
        $this->labelResolver = $callback;

        return $this;
    }
}