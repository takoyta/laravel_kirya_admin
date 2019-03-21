<?php

namespace KiryaDev\Admin\Actions;


use KiryaDev\Admin\Traits\HasUriKey;

abstract class Actionable
{
    use HasUriKey;


    /**
     * @var \KiryaDev\Admin\Resource\Resource
     */
    protected $resource;

    /**
     * Actionable constructor.
     *
     * @param  \KiryaDev\Admin\Resource\Resource  $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param  mixed  $object
     * @return mixed
     */
    public function display($object = null)
    {
        return $this->link()->param('action', static::uriKey())->display($object);
    }

    /**
     * @return  \KiryaDev\Admin\Resource\ActionLink
     */
    public abstract function link();

    /**
     * @param  \KiryaDev\Admin\Http\Requests\ActionResourceRequest  $request
     * @return mixed
     */
    public abstract function handle($request);
}