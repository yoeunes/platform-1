<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Role;

use Orchid\Screen\TD;
use Orchid\Screen\Layouts\Table;

class RoleListLayout extends Table
{
    /**
     * @var string
     */
    public $data = 'roles';

    /**
     * @return array
     */
    public function fields() : array
    {
        return [
            TD::set('id', 'ID')
                ->align('center')
                ->width('100px')
                ->sort()
                ->link('platform.systems.roles.edit', 'slug'),

            TD::set('name', __('Name'))
                ->sort()
                ->link('platform.systems.roles.edit', 'slug', 'name'),

            TD::set('slug', __('Slug'))
                ->sort(),

            TD::set('created_at', __('Created'))
                ->sort(),
        ];
    }
}
