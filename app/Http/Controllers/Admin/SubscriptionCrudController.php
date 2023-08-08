<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SubscriptionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SubscriptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SubscriptionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Subscription::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/subscription');
        CRUD::setEntityNameStrings('subscription', 'subscriptions');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
//        CRUD::column('id');
        CRUD::addColumn([
            'type' => 'closure',
            'label' => 'User Name',
            'function' => function ($entry) {
                return $entry->user ? $entry->user->first_name : '' . $entry->user ? ' ' . $entry->user->last_name : '';
            }
        ]);
        CRUD::addColumn([
            'type' => 'closure',
            'label' => 'Transaction ID',
            'function' => function ($entry) {
                return $entry->payment->bank_transaction_id;
            }
        ]);
        CRUD::column('subscription_plan_id');
        CRUD::addColumn([
            'name' => 'expired_at',
            'label' => 'Expired Date',
            'type' => 'date',
        ]);

        CRUD::addColumn([
            'name'=>'status',
            'label' => 'Status',
            'type' => 'enum',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->status == 'active') {
                        return 'badge badge-success text-capitalize';
                    }else if ($entry->status == 'inactive') {
                        return 'badge badge-warning text-capitalize';
                    }else if ($entry->status == 'canceled') {
                        return 'badge badge-danger text-capitalize';
                    }
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
        CRUD::setValidation(SubscriptionRequest::class);

        CRUD::field('id');
        CRUD::field('user_id');
        CRUD::field('payment_id');
        CRUD::field('subscription_plan_id');
        CRUD::field('is_canceled');
        CRUD::field('is_active');
        CRUD::field('expired_at');
        CRUD::field('created_at');
        CRUD::field('updated_at');

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
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupShowOperation()
    {
        CRUD::addColumn([
            'type' => 'closure',
            'label' => 'User Information',
            'function' => function ($entry) {
                echo  $entry->user ? $entry->user->first_name : '' . $entry->user ? ' ' . $entry->user->last_name : '';
                echo "</br>";
                echo $entry->user->email;
                echo "</br>";
                return $entry->user->phone;
            }
        ]);
        CRUD::addColumn([
            'type' => 'closure',
            'label' => 'Transaction ID',
            'function' => function ($entry) {
                return $entry->payment->bank_transaction_id;
            }
        ]);
        CRUD::column('subscription_plan_id');
        CRUD::addColumn([
            'type' => 'closure',
            'label' => 'Feature List',
            'function' => function ($entry) {
                for($i=0; $i<count($entry->subscription_plan->contents); $i++){
                    echo $entry->subscription_plan->contents[$i]->feature_title;
                    echo "</br>";
                }
            }
        ]);

        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Started At',
            'type' => 'datetime',
            'format' => 'd MMM Y - H:mm A',
        ]);
        CRUD::addColumn([
            'name' => 'expired_at',
            'label' => 'Expired Date',
            'type'  => 'datetime',
            'format' => 'd MMM Y - H:mm A',
        ]);
        CRUD::addColumn([
            'name'=>'status',
            'label' => 'Status',
            'type' => 'enum',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->status == 'active') {
                        return 'badge badge-success text-capitalize';
                    }else if ($entry->status == 'inactive') {
                        return 'badge badge-warning text-capitalize';
                    }else if ($entry->status == 'canceled') {
                        return 'badge badge-danger text-capitalize';
                    }
                },
            ],
        ]);
    }
}
