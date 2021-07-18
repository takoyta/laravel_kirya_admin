<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use KiryaDev\Admin\Resource\AbstractResource;

abstract class Element
{
    public $title;

    /** @internal */
    public bool $showOnIndex = true;
    /** @internal */
    public bool $showOnDetail = true;
    /** @internal */
    public bool $showOnCreation = true;
    /** @internal */
    public bool $showOnUpdate = true;

    protected ?string $formView = null;
    protected AbstractResource $resource;


    final public static function make(...$name)
    {
        return new static(...$name);
    }

    protected function __construct($title)
    {
        $this->title = __($title);

        $this->boot();
    }

    protected function boot(): void
    {
    }

    final public function onlyOnIndex()
    {
        $this->showOnIndex = true;
        $this->showOnDetail = false;
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    final public function onlyOnDetail()
    {
        $this->showOnIndex = false;
        $this->showOnDetail = true;
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    final public function onlyOnForms()
    {
        $this->showOnIndex = false;
        $this->showOnDetail = false;
        $this->showOnCreation = true;
        $this->showOnUpdate = true;

        return $this;
    }

    final public function hideFromIndex()
    {
        $this->showOnIndex = false;

        return $this;
    }

    final public function hideFromDetail()
    {
        $this->showOnDetail = false;

        return $this;
    }

    final public function exceptOnForms()
    {
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    final public function hideWhenCreating()
    {
        $this->showOnCreation = false;

        return $this;
    }

    final public function hideWhenUpdating()
    {
        $this->showOnUpdate = false;

        return $this;
    }

    final public function formView($view)
    {
        $this->formView = $view;

        return $this;
    }

    /**
     * @internal
     */
    final public function setResource(AbstractResource $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * There is some logic about field on index & detail.
     *
     * @internal
     */
    abstract public function displayValue(Model $object);

    /**
     * There is some logic about field on forms.
     *
     * @internal
     */
    abstract public function displayForm(Model $object);

    /**
     * @internal
     */
    final protected function resolveFormView()
    {
        if (null !== $this->formView) {
            return $this->formView;
        }

        $prefix = Str::startsWith(static::class, 'KiryaDev') ? 'admin::' : '';

        return $prefix.'resource.form-partials.' . Str::kebab(class_basename(static::class));
    }
}
