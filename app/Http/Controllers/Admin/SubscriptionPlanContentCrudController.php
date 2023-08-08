<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SubcriptionPlanContentRequest;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanContent;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SubscriptionPlanContentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SubscriptionPlanContentCrudController extends CrudController
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
        CRUD::setModel(\App\Models\SubscriptionPlanContent::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/subscription-plan-content');
        CRUD::setEntityNameStrings('subscription plan content', 'subscription plan contents');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::addColumn(['name' => 'subscription_id', 'type' => 'select',
            'label' => 'Plan Name', 'model' => "App\Models\SubscriptionPlan",'attribute' => 'title',
            'entity' => 'plan.title',
        ]);
        CRUD::column('feature_title');

        CRUD::addColumn([
            'name'    => 'is_active',
            'label'   => 'Status',
            'type'    => 'boolean',
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
        CRUD::setValidation(SubcriptionPlanContentRequest::class);

        CRUD::field('feature_title');
        CRUD::field('subscription_plan_id');
        CRUD::addField([   // select_from_array
            'name' => 'subscription_plan_id',
            'label' => "Subscription Plan",
            'type' => 'select_from_array',
            'options' => SubscriptionPlan::get()->pluck('title','id')->toArray(),
            'default' => 1,
        ]);
        CRUD::addField([   // select_from_array
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
            'name' => 'subscription_id',
            'type' => 'select',
            'label' => 'Plan Name',
            'model' => "App\Models\SubscriptionPlan",
            'attribute' => 'title',
            'entity' => 'plan.title',
            'limit' => 100000
        ]);
        $this->crud->addColumn([
            'name' => 'feature_title',
            'label' => 'Title',
            'limit' => 100000
        ]);
        $this->crud->addColumn([
            'name'    => 'is_active',
            'label'   => 'Status',
            'type'    => 'boolean',
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
