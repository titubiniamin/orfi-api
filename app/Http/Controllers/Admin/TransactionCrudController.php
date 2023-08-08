<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TransctionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TransactionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TransactionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Payment\Payment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/transaction');
        CRUD::setEntityNameStrings('transaction', 'transactions');
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
            'name' => 'bank_transaction_id',
            'label' => 'Transaction ID',
            'type' => 'text',
        ]);
        CRUD::addColumn([
            'type' => 'closure',
            'label' => 'User Name',
            'function' => function ($entry) {
                return $entry->user->first_name . ' ' . $entry->user->last_name;
            }
        ]);
        CRUD::addColumn([
            'name' => 'card_issuer',
            'label' => 'Account',
            'type' => 'text',
        ]);
        CRUD::addColumn([
            'name' => 'card_no',
            'label' => 'Account No',
            'type' => 'text',
        ]);
        CRUD::column('amount');
        CRUD::addColumn([
            'name' => 'transaction_date',
            'label' => 'Transaction Date',
            'type' => 'datetime',
        ]);
        CRUD::addColumn([
            'name'=>'status',
            'label' => 'Status',
            'type' => 'enum',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->status == 'processing') {
                        return 'badge badge-info text-capitalize';
                    }else if ($entry->status == 'pending') {
                        return 'badge badge-warning text-capitalize';
                    }else if ($entry->status == 'success') {
                        return 'badge badge-success text-capitalize';
                    }else if ($entry->status == 'failed') {
                        return 'badge badge-danger text-capitalize';
                    }
                },
            ],
        ]);

        $this->crud->disableResponsiveTable();

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
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
            'name' => 'transaction_id',
            'label' => 'ID',
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'name' => 'store_id',
            'label' => 'Store ID',
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'type' => 'closure',
            'label' => 'User Name',
            'function' => function ($entry) {
                return $entry->user->first_name . ' ' . $entry->user->last_name;
            }
        ]);
        $this->crud->addColumn([
            'type' => 'closure',
            'label' => 'Email Address',
            'function' => function ($entry) {
                return $entry->user->email;
            }
        ]);
        $this->crud->addColumn([
            'type' => 'closure',
            'label' => 'Contact No',
            'function' => function ($entry) {
                return $entry->user->phone ;
            }
        ]);
        $this->crud->addColumn([
            'name' => 'card_issuer',
            'label' => 'Account',
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'name' => 'card_no',
            'label' => 'Account No',
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'name' => 'bank_transaction_id',
            'label' => 'Transaction ID',
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'type' => 'closure',
            'label' => 'Total Amount',
            'function' => function ($entry) {
                return $entry->amount . ' ' . $entry->currency;
            }
        ]);
        $this->crud->addColumn([
            'type' => 'closure',
            'label' => 'Store Amount',
            'function' => function ($entry) {
                return $entry->store_amount . ' ' . $entry->currency;
            }
        ]);
        $this->crud->addColumn([
            'name' => 'transaction_date',
            'label' => 'Transaction Date',
            'type' => 'datetime',
        ]);
        $this->crud->addColumn([
            'name'=>'status',
            'label' => 'Status',
            'type' => 'enum',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->status == 'processing') {
                        return 'badge badge-info text-capitalize';
                    }else if ($entry->status == 'pending') {
                        return 'badge badge-warning text-capitalize';
                    }else if ($entry->status == 'success') {
                        return 'badge badge-success text-capitalize';
                    }else if ($entry->status == 'failed') {
                        return 'badge badge-danger text-capitalize';
                    }
                },
            ],
        ]);
        $this->crud->column('created_at');
    }
}
