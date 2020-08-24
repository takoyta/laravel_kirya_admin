<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use KiryaDev\Admin\Traits\HasDisabled;
use KiryaDev\Admin\Traits\HasRules;

abstract class FieldElement extends Element
{
    use HasRules, HasDisabled;

    /**
     * @var Closure|string
     */
    public $name;

    public bool $computed = false;

    protected ?Closure $displayCallback = null;
    protected ?Closure $resolveCallback = null;
    protected ?Closure $fillCallback = null;

    public bool $sortable = false;

    public string $help = '';

    protected function __construct($title, $name = null)
    {
        parent::__construct($title);

        if ($name instanceof Closure) {
            $this->computed = true;
        } else {
            $name = $name ?? Str::snake($title);
        }

        $this->name = $name;
    }

    final public function rules(...$rules)
    {
        return $this
            ->creationRules(...$rules)
            ->updateRules(...$rules);
    }

    /**
     * Callback arguments: Model $object, mixed $resolvedValue
     */
    final public function displayUsing(Closure $callback)
    {
        $this->displayCallback = $callback;

        return $this;
    }

    /**
     * Callback arguments: Model $object
     */
    final public function resolveUsing(Closure $callback)
    {
        $this->resolveCallback = $callback;

        return $this;
    }

    /**
     * Callback arguments: Model $object, mixed $requestValue
     */
    final public function fillUsing(Closure $callback)
    {
        $this->fillCallback = $callback;

        return $this;
    }

    final public function sortable()
    {
        $this->sortable = true;

        return $this;
    }

    final public function help(string $text)
    {
        $this->help = $text;

        return $this;
    }

    public function display(Model $object)
    {
        if ($this->computed) {
            return ($this->name)($object);
        }

        $value = $this->resolve($object);
        if (null !== $value && null !== $this->displayCallback) {
            $value = ($this->displayCallback)($object, $value);
        }

        return $value;
    }

    public function formInputView(Model $object)
    {
        [$field, $value] = [$this, old($this->name, $this->resolve($object))];

        return view($this->resolveFormView(), compact('object', 'field', 'value'));
    }

    protected function resolve(Model $object)
    {
        if (null !== $this->resolveCallback) {
            return ($this->resolveCallback)($object);
        }

        // Support json
        $value = $object;
        foreach (explode('->', $this->name) as $key) {
            $value = $value->{$key};
        }

        return $value;
    }

    public function fill(Model $object, $value): void
    {
        if ($this->fillCallback) {
            ($this->fillCallback)($object, $value);
            return;
        }

        $object->{$this->name} = $value;
    }
}
