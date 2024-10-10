<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2025 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * #######################################
 * #     e107 contact plugin    		 #
 * #     by Jimako                       #
 * #     https://www.e107sk.com          #
 * #######################################
 */

if (!defined('e107_INIT'))
{
	exit;
}

//<label for='code-verify'>{CONTACT_IMAGECODE_LABEL}</label>
$CONTACT_MENU_WRAPPER['menu']['CONTACT_IMAGECODE'] 			= '<div class="col-2 my-auto "> {---}</div>';
$CONTACT_MENU_WRAPPER['menu']['CONTACT_IMAGECODE_INPUT'] 	= '<div class="col-2"><span class="m-2">{---}</span></div>';
 
 

$CONTACT_MENU_TEMPLATE['default']['form'] =  '
<div class="row contactMenuForm">
	<div class="mb-3">
		<label for=contactName" class="form-label">{LAN=CONTACT_03}</label>
		{CONTACT_NAME}
	</div>
	<div class="mb-3">
		<label for=contactEmail" class="form-label">{LAN=CONTACT_04}</label>
		{CONTACT_EMAIL}
	</div>
	<div class="mb-3">
		<label for=contactBody" class="form-label">{LAN=CONTACT_06}</label>
		{CONTACT_BODY=rows=5&cols=30}
	</div>	 
 	<div class="mb-3">
		<label for=gdpr" class="form-label">{LAN=CONTACT_24}</label>
			<div class="checkbox form-check">
				<label>{CONTACT_GDPR_CHECK} {LAN=CONTACT_21}</label>
				<div class="help-block">{CONTACT_GDPR_LINK}</div> 
			</div>
	</div>	 
	{CONTACT_IMAGECODE}
	{CONTACT_IMAGECODE_INPUT}
	<div class="mb-3 text-center">
	{CONTACT_SUBMIT_BUTTON: class=btn btn-primary btn-contact-menu}
	</div>
	</div>       
 ';
$CONTACT_MENU_TEMPLATE['default']['caption'] = '<h2 class="display-2">{MENU_TITLE}</h2><p class="lead text-light">{MENU_SUBTITLE}</p>';;
$CONTACT_MENU_TEMPLATE['default']['tablestyle'] = 'contact-menu';
