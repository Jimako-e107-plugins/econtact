<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2016 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Contact Template
 */
 // $Id$

if (!defined('e107_INIT')) { exit; }

 


 

// Shortcode wrappers.
$REPORT_WRAPPER['form']['CONTACT_IMAGECODE'] 			= "<div class='control-group form-group'><label for='code-verify'>{CONTACT_IMAGECODE_LABEL}</label> {---}";
$REPORT_WRAPPER['form']['CONTACT_IMAGECODE_INPUT'] 	= "<span class='m-2'>{---}</span></div>";
$REPORT_WRAPPER['form']['CONTACT_EMAIL_COPY'] 			= "<div class='control-group form-group mb-2'>{---}</div>";
$REPORT_WRAPPER['form']['CONTACT_PERSON']				= "<div class='control-group form-group mb-2'><label class='form-label' for='contactPerson'>{LAN=CONTACT_14}</label>{---}</div>";




$REPORT_TEMPLATE['form'] = "
	<form action='".e_SELF. "' method='post' id='contactForm' class='mt-5' >
	{CONTACT_PERSON}
	<div class='control-group form-group mb-2'><label class='form-label' for='contactName'>{LAN=CONTACT_03}</label>
		{CONTACT_NAME}
	</div>
	<div class='control-group form-group mb-2'><label class='form-label' for='contactEmail'>{LAN=CONTACT_04}</label>
		{CONTACT_EMAIL}
	</div>
	<div class='control-group form-group mb-2'><label class='form-label' for='contactSubject'>{LAN=REPORT}</label>
		{REPORT_SUBJECT}
	</div>

		{CONTACT_EMAIL_COPY}

	<div class='control-group form-group mb-2'><label class='form-label' for='contactBody'>{LAN=CONTACT_06}</label>
		{CONTACT_BODY}
	</div>

	{CONTACT_IMAGECODE}
	{CONTACT_IMAGECODE_INPUT}

	<div class='form-group mb-2'>
 		{CONTACT_GDPR_CHECK}
		{CONTACT_GDPR_LINK}
	</div>
 
 	{REPORTPAGE}
	<div class='form-group mb-2'>
	{CONTACT_SUBMIT_BUTTON}
	</div>
	</form>";
 

	// Customize the email subject
	// Variables:  CONTACT_SUBJECT and CONTACT_PERSON as well as any custom fields set in the form. )
$REPORT_TEMPLATE['email']['subject'] = "{CONTACT_SUBJECT}";
