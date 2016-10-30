<?php
//CustomOrderNumberAdminController.php

namespace Lasallecms\Lasallecmscustominboundemail\Http\Controllers;

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
use Lasallecms\Formhandling\AdminFormhandling\AdminFormBaseController;
use Lasallecms\Lasallecmsapi\Repositories\BaseRepository;


///////////////////////////////////////////////////////////////////
///////     MODIFY THE MODEL NAMESPACE & CLASS "as Model"     /////
///////          THIS IS THE ONLY THING YOU HAVE TO           /////
///////              SPECIFY IN THIS CONTROLLER               /////
///////////////////////////////////////////////////////////////////
use Lasallecms\Lasallecmscustominboundemail\Models\CustomOrderNumber as Model;

// Laravel facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

// Laravel classes
use Illuminate\Http\Request;
// Third party classes
use Carbon\Carbon;


/*
 * Resource controller for administration of posts
 */
class CustomOrderNumberAdminController extends AdminFormBaseController
{
    /*
     * @param  Model, as specified above
     * @param  Lasallecms\Lasallecmsapi\Repositories\BaseRepository
     * @return void
     */
    public function __construct(Model $model, BaseRepository $repository)
    {
        // execute AdminController's construct method first in order to run the middleware
        parent::__construct();

        // Inject the model
        $this->model = $model;

        // Inject repository
        $this->repository = $repository;

        // Inject the relevant model into the repository
        $this->repository->injectModelIntoRepository($this->model->model_namespace."\\".$this->model->model_class);
    }

    /**
     * Form to create a new user
     * GET /users/create
     *
     * @return Response
     */
    public function create() {

        $field = [
            'name'                => 'groups',
            'related_table_name'  => 'groups',
            'related_model_class' => 'Group',
        ];

        return view('lasallecmscustominboundemail::admin/order_number/create',[
            'repository'  => $this->repository,
            'field'       => $field,
            'pagetitle'   => 'Order Numbers',
            'DatesHelper' => DatesHelper::class,
            'Form'        => Form::class,
            'HTMLHelper'  => HTMLHelper::class,
        ]);
    }


    /**
     * Show the form for editing a specific user
     * GET /users/{id}/edit
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        // Is this record locked?
        if ($this->repository->isLocked($id))
        {
            $response = 'This order number is not available for editing, as someone else is currently editing this order number';
            Session::flash('message', $response);
            Session::flash('status_code', 400 );
            return Redirect::route('admin.customordernumber.index');
        }

        $orderNumber = $this->repository->getFind($id);

        // Lock the record
        $this->repository->populateLockFields($id);
        $field = [
            'name'                => 'groups',
            'related_table_name'  => 'groups',
            'related_model_class' => 'Group',
        ];
        return view('lasallecmsadmin::'.config('lasallecmsadmin.admin_template_name').'/users/create',[
            'repository'  => $this->repository,
            'field'       => $field,
            'pagetitle'   => 'Order Numbers',
            'DatesHelper' => DatesHelper::class,
            'Form'        => Form::class,
            'HTMLHelper'  => HTMLHelper::class,
            'orderNumber' => $orderNumber,
        ]);
    }
}