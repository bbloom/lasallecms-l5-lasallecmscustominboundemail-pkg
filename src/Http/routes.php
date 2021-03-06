<?php

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


// Custom inbound processing
Route::post('email/inboundemailcustomhandling', [
    'as'   => 'inboundEmailCustomHandling',
    'uses' => 'CustomInboundEmailController@inboundCustomHandling'
]);


// Custom front-end routes for customers
Route::group(array('prefix' => 'customercare'), function() {
    Route::get('displayorders', [
        'as'   => 'FrontendCustomerCareDashboard',
        'uses' => 'FrontendCustomerCareDashboardController@displayAllAlternatesortstring1Links'
    ]);

    Route::get('displayorderupdates/{alternatesortstring1}',
        'FrontendCustomerCareDashboardController@displaySingleAlternatesortstring1');
});

Route::group(array('prefix' => 'admin'), function()
{
    Route::resource('customordernumber', 'CustomOrderNumberAdminController');
    Route::post('customordernumber/confirmDeletion/{id}', 'CustomOrderNumberAdminController@confirmDeletion');
    Route::post('customordernumber/confirmDeletionMultipleRows', 'CustomOrderNumberAdminController@confirmDeletionMultipleRows');
    Route::post('customordernumber/destroyMultipleRecords', 'CustomOrderNumberAdminController@destroyMultipleRecords');

    Route::get('bob', 'QuicktestController@bob');

});
