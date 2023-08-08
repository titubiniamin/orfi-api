<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\HomePageSettingRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class HomePageSettingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class HomePageSettingCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Setting::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/home-page-setting');
        CRUD::setEntityNameStrings('home page setting', 'home page settings');
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
            'name' => 'logo', // The db column name
            'label' => "Logo", // Table column heading
            'type' => 'image',

            // OPTIONALS
            'value' => function ($value) {
                return $value->logo ? asset('storage') . '/' . $value->logo : asset('images/default-image.jpg');
            },

            // optional width/height if 25px is not ok with you
            'height' => '80px',
            'width' => '80px',
        ]);

//        CRUD::column('logo');
        CRUD::column('title');
        CRUD::column('short_description');
        CRUD::column('header_color');
        CRUD::addColumn([
            'name' => 'background_image', // The db column name
            'label' => "Background Image", // Table column heading
            'type' => 'image',

            // OPTIONALS
            'value' => function ($value) {
                return $value->background_image ? asset('storage') . '/' . $value->background_image : asset('images/default-image.jpg');
            },

            // optional width/height if 25px is not ok with you
            'height' => '80px',
            'width' => '80px',
        ]);


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
        CRUD::setValidation(HomePageSettingRequest::class);

        CRUD::field('title');
        $this->crud->addField([   // Textarea
            'name' => 'title',
            'label' => 'Main Body Title',
            'type' => 'text'
        ]);
        CRUD::addField([   // Upload
            'name'      => 'logo',
            'label'     => 'Logo',
            'type'      => 'upload',
            'upload'    => true,
            'disk'      => 'uploads', // if you store files in the /public folder, please omit this; if you store them in /storage or S3, please specify it;
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        CRUD::addField([   // Upload
            'name'      => 'background_image',
            'label'     => 'Background Image',
            'type'      => 'upload',
            'upload'    => true,
            'disk'      => 'uploads', // if you store files in the /public folder, please omit this; if you store them in /storage or S3, please specify it;
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        $this->crud->addField([   // Color
            'name' => 'header_color',
            'label' => 'Navbar Color',
            'type' => 'color',
            'default' => '#000000',
        ]);
        $this->crud->addField([   // Textarea
            'name' => 'short_description',
            'label' => 'Main Body Description',
            'type' => 'textarea'
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
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-show
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);
        $this->crud->addColumn([
            'name' => 'title',
            'label' => 'Title',
            'limit' => 100000,
        ]);
        $this->crud->addColumn([
            'name' => 'short_description',
            'label' => 'Descriptions',
            'limit' => 1000000,
        ]);
        $this->crud->addColumn([
            'name' => 'background_image', // The db column name
            'label' => "Background Image", // Table column heading
            'type' => 'image',

            // OPTIONALS
            'value' => function ($value) {
                return $value->background_image ? asset('storage') . '/' . $value->background_image : asset('images/default-image.jpg');
            },

            // optional width/height if 25px is not ok with you
            'height' => '120px',
            'width' => '150px',

        ]);
        $this->crud->addColumn([
            'name' => 'logo', // The db column name
            'label' => "Logo", // Table column heading
            'type' => 'image',

            // OPTIONALS
            'value' => function ($value) {
                return $value->logo ? asset('storage') . '/' . $value->logo : asset('images/default-image.jpg');
            },

            // optional width/height if 25px is not ok with you
            'height' => '120px',
            'width' => '150px',

        ]);
        $this->crud->addColumn('header_color');

    }
}
