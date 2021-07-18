<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use KiryaDev\Admin\Filters\FilterProvider;
use KiryaDev\Admin\Resource\AbstractResource;
use KiryaDev\Admin\Resource\ActionLink;
use KiryaDev\Admin\Resource\Paginator;

class HasMany extends Element implements Panelable
{
    public string $name;
    public AbstractResource $relatedResource;

    protected int $perPage = 5;
    protected bool $addAction = false;
    protected bool $indexActions = false;

    protected function __construct($title, $name, $resource)
    {
        parent::__construct($title);

        $this->name = $name;
        $this->relatedResource = $resource::instance();
    }

    public function displayValue(Model $object)
    {
        $title = $this->title;
        $resource = $this->resource;
        $fields = $this->fields($object);

        // For isolate filter if more than one filter on page.
        $prefix = $this->name . '_';
        $filterProvider = $this->relatedResource->newFilterProvider($prefix);
        $paginator = new Paginator($this->getRelationQuery($object, $filterProvider), $this->perPage, $prefix, $filterProvider->getValues());

        return view($this->resolveFormView(), compact(
            'resource',
            'title',
            'fields',
            'object',
            'filterProvider',
            'paginator'
        ))
            ->with('actions', $this->actions($object));
    }

    public function displayForm(Model $object)
    {
        return '';
    }

    private function getRelationQuery(Model $object, FilterProvider $filter)
    {
        $query = $object->{$this->name}()->getQuery();
        $filter->apply($query);

        return $query;
    }

    final protected function actions(Model $object): Collection
    {
        $actions = new Collection();
        $abilitySuffix = $this->relatedResource->modelName();

        if ($this->indexActions) {
            $actions = $this->relatedResource
                ->getActionLinksForHandleMany($abilitySuffix, [
                    'resource' => $this->relatedResource->uriKey(),
                    'from' => $this->resource->uriKey(),
                    'relation' => $this->name,
                ]);
        }

        if ($this->addAction) {
            $actions->add($this->buildAddAction());
        }

        return $this->wrapActions($actions, $object);
    }

    protected function buildAddAction(): ActionLink
    {
        $ability = 'addAny' . $this->relatedResource->modelName();
        $title = $this->relatedResource->actionLabel('Add');

        return $this->resource
            ->makeActionLink('addRelated', $ability, $title)
            ->param('related_resource', $this->relatedResource->uriKey());
    }

    /**
     * Wrap action into anonymous class for displaying from template.
     *
     * @param Collection $actions
     * @param Model $object
     * @return Collection
     */
    protected function wrapActions(Collection $actions, Model $object)
    {
        return $actions->map(function ($action) use ($object) {
            return tap(new class {
                public ActionLink $action;
                public Model $object;

                public function display()
                {
                    return $this->action->display($this->object);
                }
            }, static function ($wrapper) use ($action, $object) {
                $wrapper->action = $action;
                $wrapper->object = $object;
            });
        });
    }

    /**
     * Get fields for Panel.
     *  For HasMany requires exclude BelongsTo,
     *  For MorphMany requires exclude MorphTo,
     *  For BelongsToMany requires add actions "Attach" & "Detach".
     *
     * @param Model $object
     * @return Collection
     */
    protected function fields(Model $object): Collection
    {
        return $this
            ->relatedResource
            ->getIndexFields()
            ->filter(function ($field) {
                return !($field instanceof BelongsTo && $field->relatedResource === $this->resource); //exclude reverse relation
            })
            ->add($this->relatedResource->getIndexActionsField());
    }

    public function withAddAction(bool $flag = true)
    {
        $this->addAction = $flag;

        return $this;
    }

    public function withIndexActions(bool $flag = true)
    {
        $this->indexActions = $flag;

        return $this;
    }

    public function perPage(int $count)
    {
        $this->perPage = $count;

        return $this;
    }
}
