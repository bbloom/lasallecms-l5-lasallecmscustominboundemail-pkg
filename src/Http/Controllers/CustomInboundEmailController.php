<?php

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
use Lasallecms\Lasallecmsemail\Processing\BaseInboundProcessing;
use Lasallecms\Lasallecmsemail\Validation\Validation;
use Lasallecms\Lasallecmscustominboundemail\Processing\CustomInboundProcessing;
use Lasallecms\Lasallecmsmailgun\Processing\MapMailgunPostVariables;
use Lasallecms\Lasallecmsmailgun\Processing\Validation as MailgunValidation;

// Laravel classes
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


/**
 * This custom inbound email process has an email coming in from an employee, but the email pertains to another user
 * (a customer). So the employee and customer both need to be users (in the "users" table).
 *
 * Also, the attachments relate to an order number, which is stored in the "email_attachments" table's
 * "alternate_sort_string1" field.
 *
 * Class customInboundEmailMailgunController
 * @package Lasallecrm\Lasallecrmemail\Http\Controllers
 */
class CustomInboundEmailController extends Controller
{
    /**
     * Handle the standard Mailgun inbound webhook.
     *
     * -----------------------------------------------------------------------------------------------------------------
     * Flow:
     *
     * (i)   get an inbound email into the "email_messages" database table
     * (ii)  get inbound attachments into the "email_attachments" database table
     * (iii) save the inbound attachments
     *
     *
     * -----------------------------------------------------------------------------------------------------------------
     * Rules:
     *
     * (i)  the email must be addressed to someone in the "users" database table, since they must
     *      log in to see their emails
     *
     * (ii) one inbound email address maps to one Mailgun inbound route maps to one "users" table ID
     *
     *
     * -----------------------------------------------------------------------------------------------------------------
     * Map database fields with Mailgun's parsed variables:
     *
     *  email_messages fields       Mailgun parsed post var
     *  ---------------------      ------------------------
     *    user_id                  the user_id associated with "recipient"
     *    from_email_address       sender
     *    from_name                from
     *    to_email_address         To
     *    to_name
     *    subject                  subject
     *    body                     stripped-html / body-plain
     *    message_headers          message-headers
     *
     *
     *  email_attachments field       Mailgun parsed post var
     *  -----------------------      ------------------------
     *   email_messages_id            "email_messages" db table's ID
     *   attachment_path              config('lasallecrmemail.attachment_path')
     *   attachment_filename          getClientOriginalName(attachment-1)
     *
     * $request->file('photo')->move(public_path().'/'.$attachment_path, $fileName);
     *
     *
     * -----------------------------------------------------------------------------------------------------------------
     * Links:
     *
     * securing Mailgun webhooks: https://documentation.mailgun.com/user_manual.html#webhooks
     * Symfony file uploads:      http://api.symfony.com/2.7/Symfony/Component/HttpFoundation/File/UploadedFile.html
     *
     */

    /**
     * @var int
     */
    protected $failResponseCode = 406;

    /**
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var Lasallecms\Lasallecmsmailgun\Processing\MapMailgunPostVariables
     */
    protected $mapMailgunPostVariables;

    /**
     * @var Lasallecrm\Lasallecrmemail\Validation\Validation
     */
    protected $validation;

    /**
     * @var Lasallecms\Lasallecmsmailgun\Processing\Validation
     */
    protected $mailgunValidation;

    /**
     * @var Lasallecms\Lasallecmsemail\Processing\BaseInboundProcessing
     */
    protected $baseInboundProcessing;

    /**
     * @var Lasallecms\Lasallecmscustominboundemail\Processing\CustomInboundProcessing
     */
    protected $customInboundProcessing;


    /**
     * inboundEmailMailgunController constructor.
     *
     * @param Illuminate\Http\Request                                                    $request
     * @param Lasallecms\Lasallecmsmailgun\Processing\MapMailgunPostVariables            $mapMailgunPostVariables
     * @param Lasallecms\Lasallecmsmailgun\Processing\Validation                         $mailgunValidation
     * @param Lasallecrm\Lasallecrmemail\Validation\Validation                           $validation
     * @param Lasallecms\Lasallecmsemail\Processing\BaseInboundProcessing                $baseInboundProcessing
     * @param Lasallecms\Lasallecmscustominboundemail\Processing\CustomInboundProcessing $customInboundProcessing
     */
    public function __construct(
        Request                  $request,
        MapMailgunPostVariables  $mapMailgunPostVariables,
        MailgunValidation        $mailgunValidation,
        Validation               $validation,
        BaseInboundProcessing    $baseInboundProcessing,
        CustomInboundProcessing  $customInboundProcessing
    ) {
        $this->request                 = $request;
        $this->mapMailgunPostVariables = $mapMailgunPostVariables;
        $this->mailgunValidation       = $mailgunValidation;
        $this->validation              = $validation;
        $this->baseInboundProcessing   = $baseInboundProcessing;
        $this->customInboundProcessing = $customInboundProcessing;
    }


    /**
     * Handle a standard inbound webhook POST request from Mailgun
     *
     * If Mailgun receives a 200 (Success) code it will determine the
     * webhook POST is successful and not retry.
     *
     * If Mailgun receives a 406 (Not Acceptable) code, Mailgun will
     * determine the POST is rejected and not retry.
     *
     * https://documentation.mailgun.com/user_manual.html#webhooks
     *
     * @param Request $request
     * @return mixed
     */
    public function inboundCustomHandling() {

        ////////////////////////////////////////////////////////////////////////////////////////////////
        //                  Map the inbound email POST vars to database fields                        //
        ////////////////////////////////////////////////////////////////////////////////////////////////


        //-------------------------------------------------------------------------------------------
        // I originally bound this processing to the third party email API. Specifically, I used the
        // Mailgun POST vars directly via $this->request->input('var'). Better to unbound 'em!
        // Even better to use an interface, but still feeling my way through this one.
        //-------------------------------------------------------------------------------------------

        $data = $this->mapMailgunPostVariables->mapAlllInboundPostVars();



        ////////////////////////////////////////////////////////////////////////////////////////////////
        //                           PRE-PROCESSING VALIDATION                                        //
        ////////////////////////////////////////////////////////////////////////////////////////////////

        //-------------------------------------------------------------
        // Is Mailgun's inbound POST request authentic?
        //-------------------------------------------------------------
        if (!$this->mailgunValidation->verifyWebhookSignature()) {
            return response('Invalid signature.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // There MUST be attachments!
        //-------------------------------------------------------------
        if  ($data['number_of_attachments'] == 0)  {

            // Send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because there are no attachments.";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response('No attachments.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // The atachments must be pre-authorized extensions
        //-------------------------------------------------------------
        if (!$this->validation->attachmentsHaveApprovedFileExtensions($data)) {

            // send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because at least one attachment has an unapproved file extension.";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response('At least one attachment has an unapproved file extension.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // Inbound email is from a pre-approved sender
        //-------------------------------------------------------------
        if (!$this->validation->emailsComeFromListOfApprovedSenders($data['from_email_address'])) {

            // sender is not on the list of pre-approved senders
            // send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because you are not a pre-approved sender";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response('Person who sent email is not an approved sender.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // If there are attachments, did the upload to the /tmp/ folder succeed?
        //-------------------------------------------------------------
        if  ($data['number_of_attachments'] > 0)  {

            if (!$this->validation->verifyAttachmentUploadToTmpFolder($data)) {

                // Send an email back to sender that this email is rejected
                $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because your attachment(s) did not successfully upload to the local /tmp/ folder.";
                $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

                // send response to Mailgun
                return response('Attachment(s) did not successfully upload to the local /tmp/ folder.', $this->failResponseCode);
            }
        }

        //-------------------------------------------------------------
        // Does the Mailgun route map to a user?
        // Let's do this check on the employee sending the inbound email
        //-------------------------------------------------------------
        if (!$this->mailgunValidation->isInboundEmailToEmailAddressMapToUser()) {

            // "To" is not mapped to a user
            // send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because the email address you used is not approved.";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response('The email address you used is not approved.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // Does the mapped user actually exist in the "users" db table?
        // Let's do this check on the employee sending the inbound email.
        //-------------------------------------------------------------
        if (!$this->mailgunValidation->isMappedUserExistInUsersTable()) {

            // send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because you do not exist as a web application user.";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response('Person who sent email does not exist as a web application user.', $this->failResponseCode);
        }



        ////////////////////////////////////////////////////////////////////////////////////////////////
        //    The email is sent by an employee to update a customer's order. Associate the email      //
        //                    with the customer, not with the employee.                               //
        ////////////////////////////////////////////////////////////////////////////////////////////////

        //-------------------------------------------------------------
        // Is the subject line empty?
        //-------------------------------------------------------------
        if (empty($data['subject'])) {

            // send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because the subject line is empty.";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response('Empty subject line.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // Parse the subject line
        //-------------------------------------------------------------
        $parseSubject = $this->customInboundProcessing->parseSubjectLine($data['subject']);

        //-------------------------------------------------------------
        // Is the parsed user ID empty?
        // (http://php.net/manual/en/function.is-int.php)
        //-------------------------------------------------------------
        if (empty($parseSubject['userID'])) {

            // send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because the customer number in the subject line is not specified.";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response('The customer number in the subject line is not specified.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // Does the customer actually exist in the "users" db table?
        //-------------------------------------------------------------
        if (!$this->customInboundProcessing->isUserIDInUsersTable($parseSubject['userID'])) {

            // send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because the customer assigned as ".$parseSubject['userID']." in the subject line is *not* set up as a web application user.";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response("The customer assigned as ".$data['user_id']." in the subject line is *not* set up as a web application user.", $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // Is the parsed order number empty?
        //-------------------------------------------------------------
        if (empty($parseSubject['orderNumber'])) {

            // send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because the order number in the subject line is not specified.";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response('The order number in the subject line is not specified.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // Does the parsed order number exist in the special custom "custom_order_number" db table?
        //-------------------------------------------------------------
        if (!$this->customInboundProcessing->isOrdernumberInCustomordernumberTable($parseSubject['orderNumber'])) {

            // send an email back to sender that this email is rejected
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email has been rejected because the order number specified in the subject line does not exist in the web application.";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

            // send response to Mailgun
            return response('The order number specified in the subject line does not exist in the web application.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // Merge the kosher parsed subject line with the $data array
        //-------------------------------------------------------------
        $data = array_merge($data, $parseSubject);


        //-------------------------------------------------------------
        // Parse the comments. Merge 'em into the $data array
        //-------------------------------------------------------------
        $data = array_merge($data, $this->customInboundProcessing->parseComments($data['body']));




        ////////////////////////////////////////////////////////////////////////////////////////////////
        //          Whew! All the validations are ok. So, proceed with the actual custom              //
        //                               inbound email processing.                                    //
        ////////////////////////////////////////////////////////////////////////////////////////////////

        //-------------------------------------------------------------
        // Build the data for INSERT into email_messages
        //-------------------------------------------------------------
        $data = array_merge($data, $this->baseInboundProcessing->buildDataForDatabaseInsert($data));

        //-------------------------------------------------------------
        // INSERT into the "email_messages" db table
        // $savedOk is the new email_messages.id  *OR*  false
        //-------------------------------------------------------------
        $savedOk = $this->baseInboundProcessing->insertEmail_message($data);

        if (!$savedOk) {
            $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email to ".$data['recipient']." was not successfully processed. Something wrong happened when saving to the database (to email_messages). Please resend!";
            $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);
            return response('Invalid processing.', $this->failResponseCode);
        }

        //-------------------------------------------------------------
        // Process attachments
        // $savedOk is the new email_messages.id because the save succeeded
        //-------------------------------------------------------------
        $this->baseInboundProcessing->processAttachments($savedOk, $data);


        //-------------------------------------------------------------
        // Create a Login Token so customer login bypasses login form
        // and Send Login Token email to the customer
        //-------------------------------------------------------------
        $email['subject'] = "Updates are now available";
        $email['body']    = "New pictures are now available for you on our website.";
        $this->baseInboundProcessing->manageTokenBasedLogin($data['userID'], $email);


        //-------------------------------------------------------------
        // Notification email to inbound email's sender (employee)
        //-------------------------------------------------------------
        $message = "RE: ".$this->baseInboundProcessing->modifiedSubjectLine($data['subject']).".  Your email to ".$data['recipient']." was successfully processed";
        $this->baseInboundProcessing->sendEmailNotificationToSender($message, $data);

        //-------------------------------------------------------------
        // All done! Tell Mailgun that all is well!
        //-------------------------------------------------------------
        return response('Success!', 200);
    }
}