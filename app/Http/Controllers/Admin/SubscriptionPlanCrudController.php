<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SubcriptionPlanRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SubscriptionPlanCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SubscriptionPlanCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\SubscriptionPlan::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/subscription-plan');
        CRUD::setEntityNameStrings('subscription plan', 'subscription plans');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('title');
        CRUD::column('type');
        CRUD::addColumn(['name' => 'id', 'type' => 'select',
            'label' => 'Feature List', 'model' => "App\Models\SubscriptionPlanContent", 'attribute' => 'feature_title',
            'entity' => 'contents.feature_title',
        ]);
        CRUD::addColumn([
            'label' => 'Duration',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->duration.' '.$entry->duration_type;
            }
        ]);
        CRUD::addColumn([
            'name' => 'is_active',
            'label' => 'Status',
            'type' => 'boolean',
            'options' => [0 => 'Inactive', 1 => 'Active'], // optional
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($column['text'] == 'Active') {
                        return 'badge badge-success';
                    }

                    return 'badge badge-danger';
                },
            ],
        ]);
        CRUD::column('description');
        CRUD::orderBy('id','desc');



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
        CRUD::setValidation(SubcriptionPlanRequest::class);

        CRUD::field('title');
        CRUD::field('type');
        $this->crud->addField([   // Number
            'name' => 'amount',
            'label' => 'Price',
            'type' => 'number',

            // optionals
//             'attributes' => ["step" => "any"], // allow decimals
//             'prefix'     => "$",
            'suffix' => "BDT",
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        $this->crud->addField([   // Number
            'name' => 'discount_amount',
            'label' => 'Discount Price',
            'type' => 'number',
            'suffix' => "BDT",
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        $this->crud->addField([   // Color
            'name' => 'color',
            'label' => 'Color',
            'type' => 'color',
            'default' => '#000000',
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        $this->crud->addField([   // Color
            'name' => 'active_color',
            'label' => 'Active Color',
            'type' => 'color',
            'default' => '#000000',
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);

        $this->crud->addField([   // Textarea
            'name' => 'description',
            'label' => 'Description',
            'type' => 'textarea'
        ]);
        $this->crud->addField([   // Number
            'name' => 'duration',
            'label' => 'Duration',
            'type' => 'number',
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        $this->crud->addField([   // select_from_array
            'name' => 'duration_type',
            'label' => "Duration Type",
            'type' => 'select_from_array',
            'options' => ['hour' => 'Hour', 'day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'],
            'allows_null' => false,
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
        ]);
        $this->crud->addField([   // select_from_array
            'name' => 'is_active',
            'label' => "Status",
            'type' => 'select_from_array',
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'default' => 1,

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
            'limit' => 100000
        ]);
        $this->crud->addColumn([
            'name' => 'type',
            'label' => 'Type',
            'wrapper' => [
                'class' => 'text-uppercase'
            ],
        ]);
        $this->crud->addColumn([
            'name' => 'id',
            'type' => 'select',
            'label' => 'Feature List',
            'model' => "App\Models\SubscriptionPlanContent",
            'attribute' => 'feature_title',
            'entity' => 'contents.feature_title',
            'limit' => 100000
        ]);
        $this->crud->addColumn([
            'name' => 'description',
            'label' => 'Descriptions',
            'limit' => 1000000,
        ]);
        $this->crud->addColumn([
            'label' => 'Duration',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->duration.' '.$entry->duration_type;
            }
        ]);
        $this->crud->column('color');
        $this->crud->column('active_color');
        $this->crud->addColumn([
            'label' => 'Price',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->amount.' BDT';
            }
        ]);
        $this->crud->addColumn([
            'label' => 'Discount Price',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->discount_amount.' BDT';
            }
        ]);
        $this->crud->addColumn([
            'name' => 'is_active',
            'label' => 'Status',
            'type' => 'boolean',
            'options' => [0 => 'Inactive', 1 => 'Active'], // optional
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($column['text'] == 'Active') {
                        return 'badge badge-success';
                    }

                    return 'badge badge-danger';
                },
            ],
        ]);
        $this->crud->column('created_at');
    }

}
