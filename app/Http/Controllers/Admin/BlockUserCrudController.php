<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BlockUserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BlockUserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BlockUserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\BlockUser::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/block-user');
        CRUD::setEntityNameStrings('block user', 'block users');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'model' => "App\Models\User", // The path to the model
            'label' => "Profile image", // Table column heading
            'type' => 'image',
            'entity' => 'user', // the method that defines the relationship in your Model
            // OPTIONALS
            'value' => function ($value) {
                return ($value->user && $value->user->avatar) ? env('AWS_CLOUD_FRONT_URL') . $value->user->avatar : asset('images/nophoto.png');
            },
            // optional width/height if 25px is not ok with you
            'height' => '80px',
            'width' => '80px',
        ]);

        CRUD::addColumn([
            'model' => "App\Models\User", // The path to the model
            'type' => 'closure',
            'label' => 'User Name',
            'function' => function ($entry) {
                return $entry->user ? $entry->user->first_name : '' . $entry->user ? ' ' . $entry->user->last_name : '';
            }
        ]);
        CRUD::column('platform');
        CRUD::column('ip_address');
        CRUD::column('location');
        CRUD::column('reason');

    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }


    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(BlockUserRequest::class);
        CRUD::field('user_id');
        CRUD::field('platform');
        CRUD::field('ip_address');
        CRUD::field('location');
        CRUD::field('reason');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
