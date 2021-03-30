<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use KiryaDev\Admin\Resource\AbstractResource;

abstract class Element
{
    public $title;

    public $showOnIndex = true;
    public $showOnDetail = true;
    public $showOnCreation = true;
    public $showOnUpdate = true;

    protected $formView;
    protected AbstractResource $resource;


    public static function make(...$name)
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

    public function onlyOnIndex()
    {
        $this->showOnIndex = true;
        $this->showOnDetail = false;
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    public function onlyOnDetail()
    {
        $this->showOnIndex = false;
        $this->showOnDetail = true;
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    public function onlyOnForms()
    {
        $this->showOnIndex = false;
        $this->showOnDetail = false;
        $this->showOnCreation = true;
        $this->showOnUpdate = true;

        return $this;
    }

    public function hideFromIndex()
    {
        $this->showOnIndex = false;

        return $this;
    }

    public function hideFromDetail()
    {
        $this->showOnDetail = false;

        return $this;
    }

    public function exceptOnForms()
    {
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    public function hideWhenCreating()
    {
        $this->showOnCreation = false;

        return $this;
    }

    public function hideWhenUpdating()
    {
        $this->showOnUpdate = false;

        return $this;
    }

    public function formView($view)
    {
        $this->formView = $view;

        return $this;
    }

    /**
     * @internal
     */
    public function setResource(AbstractResource $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * @internal
     */
    public function displayValue(Model $object)
    {
        // There is some logic about field on index & detail
    }

    /**
     * @internal
     */
    public function displayForm(Model $object)
    {
        // There is some logic about field on forms
    }

    /**
     * @internal
     */
    protected function resolveFormView()
    {
        if (null !== $this->formView) {
            return $this->formView;
        }

        $prefix = Str::startsWith(static::class, 'KiryaDev') ? 'admin::' : '';

        return $prefix.'resource.form-partials.' . Str::kebab(class_basename(static::class));
    }
}
