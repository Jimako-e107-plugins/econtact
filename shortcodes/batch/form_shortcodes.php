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

// Load language file for the contact plugine
// e107::lan('econtact', 'lan_contact');

class plugin_econtact_form_shortcodes extends e_shortcode
{

	function sc_contact_email_copy($parm = '')
	{
		$contact_emailcopy = e107::getPref('contact_emailcopy');

		// If the 'contact_emailcopy' preference is not set or is false, return nothing
		if (empty($contact_emailcopy))
		{
			return '';
		}

		// Use the form handler to generate the checkbox
		$frm = e107::getForm();
		return $frm->checkbox('email_copy', 1, false, ['label'=> LAN_CONTACT_07]);
	}

	function sc_contact_person($parm = '')
	{
 
		$sql = e107::getDb();
 

		// Determine the query based on the sitecontacts preference
		$siteContactsPref = e107::getPref('sitecontacts');
		if ($siteContactsPref == e_UC_ADMIN)
		{
			$query = "user_admin = 1 AND user_ban = 0";
		}
		elseif ($siteContactsPref == e_UC_MAINADMIN)
		{
			$query = "user_admin = 1 AND (user_perms = '0' OR user_perms = '0.') ";
		}
		else
		{
			$query = "FIND_IN_SET(" . intval($siteContactsPref) . ", user_class) AND user_ban = 0";
		}

		// Fetch users based on the query
		$count = $sql->select("user", "user_id, user_name", $query . " ORDER BY user_name");

		// If more than one user is found, generate the dropdown
		if ($count > 1)
		{
			$frm = e107::getForm();
			$options = [];

			while ($row = $sql->fetch())
			{
				$options[$row['user_id']] = $row['user_name'];
			}

			// Generate the dropdown using e107::getForm()
			return $frm->select('contact_person', $options, '', ['class' => 'form-select form-control']);
		}

		return ''; // Return empty if no users found or only one
	}
 

	function sc_contact_imagecode($parm = '')
	{
		if (!USER)
		{
			return e107::getSecureImg()->renderImage();
		}
	}

	function sc_contact_imagecode_label($parm = '')
	{
		if (!USER)
		{
			return e107::getSecureImg()->renderLabel();
		}
	}

	function sc_contact_imagecode_input($parm = '')
	{
		if (!USER)
		{
			return e107::getSecureImg()->renderInput();
		}
	}


	/* example {CONTACT_NAME} */
	/* example {CONTACT_NAME: class=form-control} */
	/* example {CONTACT_NAME: class=col-md-12&placeholder=".LAN_CONTACT_03." *} */

	function sc_contact_name($parm = null)
	{
		$userName = deftrue('USERNAME');
		$class = (!empty($parm['class'])) ? $parm['class'] : 'tbox form-control';
		$placeholder = (!empty($parm['placeholder'])) ? " placeholder= '" . $parm['placeholder'] . "'" : '';
		$value      = 	!empty($_POST['author_name']) ?  e107::getParser()->filter($_POST['author_name']) : $userName;
		return "<input type='text'   id='contactName' title='" . LAN_CONTACT_17 . "' aria-label='" . LAN_CONTACT_17 . "' aria-labelledby='contactName' name='author_name' required='required' size='30' " . $placeholder . "  class='" . $class . "' value=\"" . $value . "\" />";
	}

	function sc_contact_map($parm = null)
	{
		$pref = e107::getPref('contact_info');

		if (empty($pref['address']) && empty($pref['coordinates']))
		{
			return null;
		}

		$address = !empty($pref['coordinates']) ? $pref['coordinates'] : $pref['address'];
		$address = trim($address);
		$address = str_replace("\n", " ", $address);

		$zoom = varset($parm['zoom'], 'street');

		$zoomOpts = [
			'street' => 17,
			'district'  => 14,
			'city'  => 12,

		];

		$zoom = (int) varset($zoomOpts[$zoom], $zoom);

		// &z='.$zoom.'

		return '<iframe class="sc-contact-map" src="https://www.google.com/maps?q=' . $address . '&output=embed&z=' . $zoom . '"></iframe>';
	}






	/* example {CONTACT_EMAIL} */
	/* example {CONTACT_EMAIL: class=form-control} */
	/* example {CONTACT_EMAIL: class=col-md-12&placeholder=".LAN_CONTACT_04." *} */

	function sc_contact_email($parm = null)
	{
		$userEmail = deftrue('USEREMAIL');
		$disabled = (!empty($userEmail)) ? 'readonly' : ''; // don't allow change from a verified email address.

		$class = (!empty($parm['class'])) ? $parm['class'] : 'tbox form-control';
		$placeholder = (!empty($parm['placeholder'])) ? " placeholder= '" . $parm['placeholder'] . "'" : '';
		$value = !empty($_POST['email_send']) ? e107::getParser()->filter($_POST['email_send'], 'email') : USEREMAIL;
		return "<input type='email'   " . $disabled . " id='contactEmail' title='" . LAN_CONTACT_18 . "' aria-label='" . LAN_CONTACT_18 . "'  aria-labelledby='contactEmail' name='email_send' required='required' size='30' " . $placeholder . " class='" . $class . "' value='" . $value . "' />";
	}



	/* example {CONTACT_SUBJECT} */
	/* example {CONTACT_SUBJECT: class=form-control} */
	/* example {CONTACT_SUBJECT: class=col-md-12&placeholder=".LAN_CONTACT_05." *} */

	function sc_contact_subject($parm = null)
	{
		$class = (!empty($parm['class'])) ? $parm['class'] : 'tbox form-control';
		$placeholder = (!empty($parm['placeholder'])) ? " placeholder= '" . $parm['placeholder'] . "'" : '';
		$value = !empty($_POST['subject']) ? e107::getParser()->filter($_POST['subject']) : '';
		return "<input type='text' id='contactSubject' title='" . LAN_CONTACT_19 . "' aria-label='" . LAN_CONTACT_19 . "' aria-labelledby='contactSubject' name='subject' required='required' size='30' " . $placeholder . " class='" . $class . "' value=\"" . $value . "\" />";
	}


	function sc_contact_body($parm = null)
	{
		if (is_string($parm))
		{
			parse_str($parm, $parm);
		}

		$rows = vartrue($parm['rows'], 10);
		$cols = vartrue($parm['cols'], 70);
		$placeholder = !empty($parm['placeholder']) ? "placeholder=\"" . $parm['placeholder'] . "\"" : "";

		$size = ($cols > 60) ? 'input-xxlarge' : '';

		$class = (!empty($parm['class'])) ? $parm['class'] : 'tbox ' . $size . ' form-control';


		$value = !empty($_POST['body']) ? stripslashes($_POST['body']) : '';

		return "<textarea cols='{$cols}'  id='contactBody' rows='{$rows}' title='" . LAN_CONTACT_20 . "' aria-label='" . LAN_CONTACT_20 . "' aria-labelledby='contactBody' name='body' " . $placeholder . " required='required' class='" . $class . "'>" . $value . "</textarea>";
	}


	/* example {CONTACT_SUBMIT_BUTTON} */
	/* example {CONTACT_SUBMIT_BUTTON: class=contact submit btn btn-minimal} */
	function sc_contact_submit_button($parm = null)
	{
		$class = (!empty($parm['class'])) ? $parm['class'] : 'btn btn-primary button';

		return "<input type='submit' name='send-contactus' value=\"" . LAN_CONTACT_08 . "\" class='" . $class . "' />";
	}

	function sc_contact_gdpr_check($parm = null)
	{
		$parm['class'] = (!empty($parm['class'])) ? $parm['class'] : '';
		$parm = array_merge(array('required' => 1), $parm);
 
		$parm['label'] =  LAN_CONTACT_21;
		return e107::getForm()->checkbox('gdpr', 1, false, $parm );
	}

	/* {CONTACT_GDPR_LINK} */
	function sc_contact_gdpr_link($parm = null)
	{
		$pp = e107::getPref('gdpr_privacypolicy', '');
		if (!$pp)
		{
			return '';
		}
		$pp = e107::getParser()->replaceConstants($pp, 'full');
		$class = (!empty($parm['class'])) ? $parm['class'] : '';
		$link = sprintf('<span class="%s"><a href="%s" target="_blank">%s</a></span>', $class, $pp, LAN_CONTACT_22);

		return e107::getParser()->lanVars(LAN_CONTACT_23, $link);
	}

	/* {REPORT_SUBJECT} */
	/* form-select is needed for bootswatch themes */
	function sc_report_subject($parm = null)
	{

		$options = [LAN_RULESVIOLATION =>LAN_RULESVIOLATION, LAN_BUGREPORT=> LAN_BUGREPORT, LAN_MISSING=>LAN_MISSING];

		return e107::getForm()->select('subject', $options, false, array('class' => 'form-select form-control'));
	}


	/* {REPORTPAGE} */
	function sc_reportpage($parm = null)
	{
		//<input type='hidden' name='reportpage' value='".descript($_GET['url'])."'><br />";
		return e107::getForm()->hidden('reportpage', e107::getParser()->filter($_GET['url'], 'url'));
	}
}
