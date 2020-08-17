<?php declare(strict_types=1);

namespace KiryaDev\Admin\Resource;

use Illuminate\Database\Eloquent\Model;

class ActionLink
{
    protected string $route;
    protected string $objectKey = 'id';
    protected string $ability;
    protected string $altTitle;
    protected AbstractResource $resource;
    protected bool $asLink = false;

    /** FontAwesome icon  */
    protected ?string $icon = null;

    /** CSS classes */
    protected array $classes = [];

    protected array $params = [];

    public function __construct(AbstractResource $resource, string $route, string $ability = null, string $title = null)
    {
        $this->resource = $resource;

        $this->route = $route;
        $this->ability = $ability ?? $route;
        $this->altTitle = $title ?? $this->resource->actionLabel(ucfirst($this->ability));
    }

    /**
     * @param null|mixed $object
     * @return null|string
     */
    public function display($object = null)
    {
        if ($object instanceof Model) {
            $this->param($this->objectKey, $object->getKey());
        }

        $url = $this->resource->authorizedTo($this->ability, $object)
            ? $this->resource->makeUrl($this->route, $this->params)
            : null;

        $title = $this->icon ?? $this->altTitle;

        // As Link
        if ($this->asLink) {
            return $url
                ? sprintf(
                    '<a href="%s" class="%s" title="%s">%s</a>',
                    e($url),
                    ($this->icon ? 'a text-muted ' : '') . implode(' ', $this->classes),
                    $this->altTitle,
                    $title
                )
                : $title;
        }

        // As Button
        return $url
            ? sprintf('<a href="%s" class="panel-button %s" title="%s">%s</a>', e($url), implode(' ', $this->classes), $this->altTitle, $title)
            : sprintf('<a class="panel-button %s" disabled>%s</a>', implode(' ', $this->classes), $title);
    }

    public function objectKey($key)
    {
        $this->objectKey = $key;

        return $this;
    }

    public function displayAsLink()
    {
        $this->asLink = true;

        return $this;
    }

    public function icon($class)
    {
        $this->icon = sprintf('<i class="fa fa-%s"></i>', $class);

        return $this;
    }

    public function param()
    {
        if (2 === func_num_args()) {
            [$key, $value] = func_get_args();

            $this->params[$key] = $value;
        } else {
            $this->params = array_merge($this->params, func_get_arg(0));
        }

        return $this;
    }

    public function addClass()
    {
        $this->classes = array_merge($this->classes, func_get_args());

        return $this;
    }
}
