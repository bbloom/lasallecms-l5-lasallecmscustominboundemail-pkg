<?php
namespace Lasallecms\Lasallecmscustominboundemail\Models;

/**
 *
 * Custom Inbound Email Processing package for the LaSalle Content Management System, based on the Laravel 5 Framework
 * Copyright (C) 2015 - 2016  The South LaSalle Trading Corporation
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package    Custom Inbound Email Processing package for the LaSalle Content Management System
 * @link       http://LaSalleCMS.com
 * @copyright  (c) 2015 - 2016, The South LaSalle Trading Corporation
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 * @author     The South LaSalle Trading Corporation
 * @email      info@southlasalle.com
 *
 */

// LaSalle Software
use Lasallecms\Lasallecmsapi\Models\BaseModel;

class CustomOrderNumber extends BaseModel
{
    ///////////////////////////////////////////////////////////////////
    ///////////     MANDATORY USER DEFINED PROPERTIES      ////////////
    ///////////              MODIFY THESE!                /////////////
    ///////////////////////////////////////////////////////////////////


    // LARAVEL MODEL CLASS PROPERTIES

    /**
     * The database table used by the model.
     *
     * The convention is plural -- and plural is assumed.
     *
     * Lowercase.
     *
     * @var string
     */
    public $table = "custom_order_number";

    /**
     * Which fields may be mass assigned
     * @var array
     */
    protected $fillable = [
        'order_number', 'user_id'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * LaSalle Software handles the created_at and updated_at fields, so false.
     *
     * @var bool
     */
    public $timestamps = false;


    // PACKAGE PROPERTIES

    /*
     * Name of this package
     *
     * @var string
     */
    public $package_title = "LaSalleCMS";


    // MODEL PROPERTIES

    /*
     * Model class namespace.
     *
     * Do *NOT* specify the model's class.
     *
     * @var string
     */
    public $model_namespace = "Lasallecms\Lasallecmscustominboundemail\Models";

    /*
     * Model's class.
     *
     * Convention is capitalized and singular -- which is assumed.
     *
     * @var string
     */
    public $model_class = "CustomOrderNumber";


    // RESOURCE ROUTE PROPERTIES

    /*
     * The base URL of the resource routes.
     *
     * Frequently is the same as the table name.
     *
     * By convention, plural.
     *
     * Lowercase.
     *
     * @var string
     */
    public $resource_route_name   = "customordernumber";


    // FORM PROCESSORS PROPERTIES.
    // THESE ARE THE ADMIN CRUD COMMAND HANDLERS.
    // THE ONLY REASON YOU HAVE TO CREATE THESE COMMAND HANDLERS AT ALL IS THAT
    // THE EVENTS DIFFER. EVERYTHING THAT HAPPENS UP TO THE "PERSIST" IS PRETTY STANDARD.

    /*
     * Namespace of the Form Processors
     *
     * MUST *NOT* have a slash at the end of the string
     *
     * @var string
     */
    public $namespace_formprocessor = 'Lasallecms\Lasallecmscustominboundemail\Formprocessing';

    /*
     * Class name of the CREATE Form Processor command
     *
     * @var string
     */
    public $classname_formprocessor_create = 'CreateCustomOrderNumberFormProcessing';

    /*
     * Namespace and class name of the UPDATE Form Processor command
     *
     * @var string
     */
    public $classname_formprocessor_update = 'UpdateCustomOrderNumberFormProcessing';

    /*
     * Namespace and class name of the DELETE (DESTROY) Form Processor command
     *
     * @var string
     */
    public $classname_formprocessor_delete = 'DeleteCustomOrderNumberFormProcessing';


    // SANITATION RULES PROPERTIES

    /**
     * Sanitation rules for Create (INSERT)
     *
     * @var array
     */
    public $sanitationRulesForCreate = [
        'order_number'  => 'trim',
    ];

    /**
     * Sanitation rules for UPDATE
     *
     * @var array
     */
    public $sanitationRulesForUpdate = [
        'order_number'  => 'trim',
    ];


    // VALIDATION RULES PROPERTIES

    /**
     * Validation rules for Create (INSERT)
     *
     * @var array
     */
    public $validationRulesForCreate = [
        'order_number' => 'required|integer|unique:custom_order_number,order_number',
        'user_id'      => 'required',
    ];

    /**
     * Validation rules for UPDATE
     *
     * @var array
     */
    public $validationRulesForUpdate = [
        'order_number' => 'required|integer|unique:custom_order_number,order_number',
        'user_id'      => 'required',
    ];


    // USER GROUPS & ROLES PROPERTIES

    /*
     * User groups that are allowed to execute each controller action
     *
     * @var array
     */
    public $allowed_user_groups = [
        ['index'   => ['Super Administrator']],
        ['create'  => ['Super Administrator']],
        ['store'   => ['Super Administrator']],
        ['edit'    => ['Super Administrator']],
        ['update'  => ['Super Administrator']],
        ['destroy' => ['Super Administrator']],
    ];


    // FIELD LIST PROPERTIES

    /*
     * Field list
     *
     * ID and TITLE must go first.
     *
     * Forms will list fields in the order fields are listed in this array.
     *
     * @var array
     */
    public $field_list = [
        [
            /*  "Composite Title" field. Database tables that are related to each other need a "Title" field.
                 There is no natural field that can serve as the "Title" field. The "Composite Title" field
                 concatenates other fields during create and updates automatically.
                 * not for lookup tables
                 * include  'index_skip' => true,  so existing code will exclude from index listing
                 * MySQL field type "text" so not run out of space concatenating multiple varchar(255) fields
             */
            'name'                  => 'composite_title',
            'type'                  => 'composite_title',
            'fields_to_concatenate' => ['order_number', 'user_id'],
            'index_skip'            => true,
        ],
        [
            'name'        => 'id',
            'type'        => 'int',
            'info'        => false,
            'index_skip'  => false,
            'index_align' => 'center',
        ],
        [
            'name'        => 'order_number',
            'type'        => 'int',
            'info'        => false,
            'index_skip'  => false,
            'index_align' => 'center',
        ],
        [
            'name'                  => 'user_id',
            'alternate_form_name'   => 'LaSalle User',
            'type'                  => 'related_table',
            'related_table_name'    => 'users',
            'related_namespace'     => 'Lasallecms\Usermanagement\Models',
            'related_model_class'   => 'User',
            'related_fk_constraint' => false,
            'related_pivot_table'   => false,
            'nullable'              => true,
            'info'                  => 'Required!',
            'index_skip'            => false,
            'index_align'           => 'center',
        ],
        [
            'name'                  => 'title',
            'type'                  => 'varchar',
            'info'                  => 'For internal use only..',
            'index_skip'            => true,
            'persist_wash'          => 'title',
        ],
    ];


    // MISC PROPERTIES

    /*
     * Suppress the delete button when just one record to list, in the listings (index) page
     *
     * true  = suppress the delete button when just one record to list
     * false = display the delete button when just one record to list
     *
     * @var bool
     */
    public $suppress_delete_button_when_one_record = false;

    /*
     * DO NOT DELETE THESE CORE RECORDS.
     *
     * Specify the TITLE of these records
     *
     * Assumed that this database table has a "title" field
     *
     * @var array
     */
    public $do_not_delete_these_core_records = [
        'Blog'
    ];



    ///////////////////////////////////////////////////////////////////
    //////////////        RELATIONSHIPS             ///////////////////
    ///////////////////////////////////////////////////////////////////

    /*
     * One to one relationship with user_id.
     *
     * Method name must be:
     *    * the model name,
     *    * NOT the table name,
     *    * singular;
     *    * lowercase.
     *
     * @return Eloquent
     */
    public function user()
    {
        return $this->belongsTo('Lasallecms\Lasallecmsapi\Models\User');
    }
}