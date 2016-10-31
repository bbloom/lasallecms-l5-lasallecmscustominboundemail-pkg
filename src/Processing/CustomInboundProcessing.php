<?php

namespace Lasallecms\Lasallecmscustominboundemail\Processing;

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
use Lasallecms\Lasallecmsapi\Repositories\UserRepository;

// Laravel facades
use Illuminate\Support\Facades\DB;

// Third party classes
use Carbon\Carbon;


/**
 * Class CustomInboundProcessing
 * @package Lasallecms\Lasallecmscustominboundemail\Processing
 */
class CustomInboundProcessing
{
    /**
     * @var Lasallecms\Lasallecmsapi\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * CustomInboundProcessing constructor
     *
     * @param Lasallecms\Lasallecmsapi\Repositories\UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }


    /**
     * Parse the subject field.
     *
     * The subject field is structured in a specific way so it can be parsed:
     *  "123456,654321", where 123456 = userID and 654321 = order number
     *
     * @param  string  $subject    The inbound email's subject line
     * @return array
     */
    public function parseSubjectLine($subject) {

        $subjectLine = explode(',', $subject);

        $data = [];
        $data['userID']                 = $subjectLine[0];
        $data['orderNumber']            = $subjectLine[1];
        $data['alternate_sort_string1'] = $data['orderNumber'];

        return $data;
    }

    /**
     * Is a given user ID in the "users" table?
     *
     * @param  int   $userID
     * @return bool
     */
    public function isUserIDInUsersTable($userID) {
        if ($this->userRepository->getFind($userID)) {
            return true;
        }

        return false;
    }

    /**
     * Is the order number in the specially created "custom_order_number" db table?
     *      *
     * @param  string   $orderNumber     The order number parsed from the inbound email's subject line
     * @return bool
     */
    public function isOrdernumberInCustomordernumberTable($orderNumber) {
        $result =  DB::table('custom_order_number')
            ->where('order_number', $orderNumber)
            ->value('order_number');
        ;

        if (count($result) == 0) {
            return false;
        }

        return true;
    }


    /**
     * Is the order number in the specially created "custom_order_number" db table?
     *      *
     * @param  string   $orderNumber     The order number parsed from the inbound email's subject line
     * @param  int      $user_id         The customer's LaSalle user_id
     * @return bool
     */
    public function isOrdernumberAssociatedWithCustomer($orderNumber, $user_id) {

        $result =  DB::table('custom_order_number')
            ->where('order_number', $orderNumber)
            ->where('user_id', $user_id)
            ->first()
        ;

        if (count($result) == 0) {
            return false;
        }

        return true;
    }


    /**
     * Parse the comments in the inbound email's body.
     *
     * The body is structured to parse the comments.
     * What is between the word "comments", in the body, is the actual comments.
     *
     * INSERT comments INTO the "email_attachments" db table
     *
     * @param  string  $body      The inbound email's body
     * @return array
     */
    public function parseComments($body) {

        $comments = explode("comments", $body);

        // Preface comments with the date
        $comments = "(".Carbon::now()->toDateTimeString().") ".trim($comments[1]);

        return ['comments' => $comments];
    }
}