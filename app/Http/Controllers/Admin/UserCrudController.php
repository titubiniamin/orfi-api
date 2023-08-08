<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use App\Models\BlockUser;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\User;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
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
            'name' => 'avatar', // The db column name
            'label' => "Profile image", // Table column heading
            'type' => 'image',

            // OPTIONALS
            'value' => function ($value) {
                return $value->avatar ? env('AWS_CLOUD_FRONT_URL') . $value->avatar : asset('images/nophoto.png');
            },

            // optional width/height if 25px is not ok with you
            'height' => '80px',
            'width' => '80px',
        ]);
        CRUD::addColumn([
            'type' => 'closure',
            'label' => 'User Name',
            'function' => function ($entry) {
                return $entry->first_name . ' ' . $entry->last_name;
            }
        ]);
        CRUD::column('email');
        CRUD::addColumn([
            'name' => 'date_of_birth',
            'label' => 'Date of Birth',
            'type' => 'date',
        ]);
        CRUD::column('phone');
        CRUD::column('address');
        CRUD::addButtonFromModelFunction('line', 'open_google', 'openGoogle');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {

        CRUD::setValidation(UserRequest::class);
        CRUD::addField([   // Number
            'name' => 'first_name',
            'label' => 'First Name',
            'type' => 'text',
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        CRUD::addField([   // Number
            'name' => 'last_name',
            'label' => 'Last Name',
            'type' => 'text',
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);

        CRUD::addField([   // Upload
            'name' => 'date_of_birth',
            'label' => 'Date of Birth',
            'type' => 'date',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        CRUD::addField([   // Upload
            'name' => 'phone',
            'label' => 'Mobile number',
            'type' => 'text',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        CRUD::addField([   // Upload
            'name' => 'avatar',
            'label' => 'Profile Image',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'uploads', // if you store files in the /public folder, please omit this; if you store them in /storage or S3, please specify it;
        ]);
        CRUD::field('address');
        CRUD::addField([   // Upload
            'name' => 'email',
            'label' => 'Email Address',
            'type' => 'email',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
            ],
        ]);CRUD::addField([   // Upload
        'name'  => 'password',
        'label' => 'Password',
        'type'  => 'password',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
            ],
        ]);

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

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function userBan($id)
    {
        $user = BlockUser::query()->where('user_id', $id);
        if (!$user->first()) {
            $user->create(['user_id' => $id, 'reason' => 'Block from admin',]);
            \Alert::error(trans('The user has been banned successfully'))->flash();

        } else {
            $user->delete();
            \Alert::success(trans('The user has been unbanned successfully'))->flash();
        }
        return back();
    }
}
