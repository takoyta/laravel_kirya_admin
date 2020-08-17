<?php declare(strict_types=1);

namespace KiryaDev\Admin\Policies;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface Policy
 *
 * Can containts abilitis for relationship`s actions.
 * For example: addUser, attachGroup.
 */
interface Policy
{
    /**
     * Determine whether the user can view any objects.
     *
     * @param  User  $user
     * @return bool
     */
    public function viewAny($user);

    /**
     * Determine whether the user can view an object.
     *
     * @param  User  $user
     * @param  Model $model
     * @return bool
     */
    public function view($user, $model);

    /**
     * Determine whether the user can create objects.
     *
     * @param  User  $user
     * @return bool
     */
    public function create($user);

    /**
     * Determine whether the user can update any object.
     *
     * @param  User  $user
     * @param  Model $model
     * @return bool
     */
    public function update($user, $model);

    /**
     * Determine whether the user can delete any object.
     *
     * @param  User  $user
     * @param  Model $model
     * @return bool
     */
    public function delete($user, $model);
}
