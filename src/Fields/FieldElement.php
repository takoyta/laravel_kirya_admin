<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;


use Illuminate\Support\Str;
use KiryaDev\Admin\Traits\HasRules;
use KiryaDev\Admin\Traits\HasDisabled;

abstract class FieldElement extends Element
{
    use HasRules, HasDisabled;

    /**
     * @var \Closure|string
     */
    public $name;

    public $computed = false;

    /**
     * Callback not call when value is null.
     *
     * @var \Closure|null
     */
    protected $displayCallback;
    protected $resolveCallback;
    public $fillCallback;

    public $sortable = false;

    public $help;


    protected function __construct($title, $name = null)
    {
        parent::__construct($title);

        if ($name instanceof \Closure) {
            $this->computed = true;
        } else {
            $name = $name ?? Str::snake($title);
        }

        $this->name = $name;
    }


    /**
     * @param  string  ...$rules
     * @return static
     */
    public function rules(...$rules)
    {
        return $this
            ->creationRules(...$rules)
            ->updateRules(...$rules);
    }

    /**
     * Null value not display.
     *
     * @param  \Closure  $callback
     * @return static
     */
    public function displayUsing(\Closure $callback)
    {
        $this->displayCallback = $callback;

        return $this;
    }

    /**
     * Null value not resolvings.
     *
     * @param  \Closure  $callback
     * @return static
     */
    public function resolveUsing(\Closure $callback)
    {
        $this->resolveCallback = $callback;

        return $this;
    }

    /**
     * @param  \Closure  $callback
     * @return static
     */
    public function fillUsing(\Closure $callback)
    {
        $this->fillCallback = $callback;

        return $this;
    }

    /**
     * @return static
     */
    public function sortable()
    {
        $this->sortable = true;

        return $this;
    }

    /**
     * @param  string  $text
     * @return static
     */
    public function help($text)
    {
        $this->help = $text;

        return $this;
    }

    /**
     * @param  mixed  $object
     * @return mixed
     */
    public function display($object)
    {
        if ($this->computed)
            return call_user_func($this->name, $object);

        if (is_null($value = $object->{$this->name}))
            return null;

        return is_null($fn = $this->displayCallback) ? $value : $fn($value);
    }

    /**
     * @param  mixed  $object
     * @return mixed
     */
    public function formInputView($object)
    {
        [$field, $value] = [$this, old($this->name, $this->resolve($object))];

        return view($this->resolveFormView(), compact('object', 'field', 'value'));
    }

    /**
     * @param  mixed  $object
     * @return mixed
     */
    protected function resolve($object)
    {
        if (is_null($value = $object->{$this->name})) {
            return null;
        }

        return is_null($fn = $this->resolveCallback) ? $value: $fn($value);
    }
}