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
	require_once(__DIR__ . '/../../class2.php');
}

define('PLUGIN_NAME', 'econtact');

if (!e107::isInstalled(PLUGIN_NAME))
{
	e107::redirect();
}

define('e_PAGE_KEY', 'contact');  //route is too late?
$contactSingleton =  e107::getSingleton(e_CURRENT_PLUGIN, e_PLUGIN .   'econtact/contact_class.php');
 
$contactSingleton->setRedirection(e_PAGE_KEY);
$contactSingleton->setContactLayouts(e_PAGE_KEY);
print_a($contactSingleton);
e107::lan(PLUGIN_NAME, 'lan_contact');  // Loads e_PLUGIN.'contact/languages/'.e_LANGUAGE.'/lan_contact.php'
$pagetitle = "LAN_". strtoupper(e_PAGE_KEY) . "_PAGE_TITLE";
e107::title(constant($pagetitle));

e107::canonical(e_PAGE_KEY);
e107::route(PLUGIN_NAME . '/' . e_PAGE_KEY);

require_once(HEADERF);


if (isset($_POST['send-contactus']))
{
	processFormSubmit();
}
else
{
	$form = $contactSingleton->renderContactForm(e_PAGE_KEY);
	$info = $contactSingleton->renderContactInfo();
}

$CONTACT_LAYOUT = $contactSingleton->getPageLayout(e_PAGE_KEY);

$CONTACT_LAYOUT = str_replace(
	['{---CONTACT-FORM---}', '{---CONTACT-INFO---}'],
	[$form, $info],
	$CONTACT_LAYOUT
);

// $text = e107::getParser()->parseTemplate($CONTACT_LAYOUT, true  );

e107::getRender()->tablerender("", $CONTACT_LAYOUT, e_PAGE_KEY . "-page");



/**
 * @param $sql
 * @return array
 */
function processFormSubmit()
{
	$sql = e107::getDb();
	$sec_img = e107::getSecureImg();
	$tp = e107::getParser();
	$ns = e107::getRender();
	$pref = e107::getPref();

	$error = "";
	$ignore = false;


	// Contact Form Filter -----

	$contact_filter = e107::pref('core', 'contact_filter', '');

	if (!empty($contact_filter))
	{
		$tmp = explode("\n", $contact_filter);

		if (!empty($tmp))
		{
			foreach ($tmp as $filterItem)
			{
				if (strpos($_POST['body'], $filterItem) !== false)
				{
					$ignore = true;
					break;
				}
			}
		}
	}

	// ---------

	$sender_name = $tp->toEmail($_POST['author_name'], true, 'RAWTEXT');
	$sender = check_email($_POST['email_send']);
	$subject = $tp->toEmail($_POST['subject'], true, 'RAWTEXT');
	$body = nl2br($tp->toEmail(strip_tags($_POST['body']), true, 'RAWTEXT'));

	$email_copy = !empty($_POST['email_copy']) ? 1 : 0;

	// Check Image-Code
	if (isset($_POST['rand_num']) && ($sec_img->invalidCode($_POST['rand_num'], $_POST['code_verify'])))
	{
		$error .= LAN_CONTACT_15 . "\n";
	}

	// Check message body.
	if (strlen(trim($body)) < 15)
	{
		$error .= LAN_CONTACT_12 . "\n";
	}

	// Check subject line.
	if (isset($_POST['subject']) && strlen(trim($subject)) < 2)
	{
		$error .= LAN_CONTACT_13 . "\n";
	}

	if (!strpos(trim($sender), "@"))
	{
		$error .= LAN_CONTACT_11 . "\n";
	}

	// No errors - so proceed to email the admin and the user (if selected).
	if ($ignore === true)
	{
		$ns->tablerender('', "<div class='alert alert-success'>" . LAN_CONTACT_09 . "</div>"); // ignore and leave them none the wiser.
		e107::getDebug()->log("Contact form post ignored");
		require_once(FOOTERF);
		exit;
	}
	elseif (empty($error))
	{
		$body .= "<br /><br />
				<table class='table'>
				<tr>
				<td>IP:</td><td>" . e107::getIPHandler()->getIP(true) . "</td></tr>";

		if (USER)
		{
			$body .= "<tr><td>User:</td><td>#" . USERID . " " . USERNAME . "</td></tr>";
		}

		if (empty($_POST['contact_person']) && !empty($pref['sitecontacts'])) // only 1 person, so contact_person not posted.
		{
			if ($pref['sitecontacts'] == e_UC_MAINADMIN)
			{
				$query = "user_perms = '0' OR user_perms = '0.' ";
			}
			elseif ($pref['sitecontacts'] == e_UC_ADMIN)
			{
				$query = "user_admin = 1 ";
			}
			else
			{
				$query = "FIND_IN_SET(" . $pref['sitecontacts'] . ",user_class) ";
			}
		}
		else
		{
			$query = "user_id = " . intval($_POST['contact_person']);
		}

		if ($sql->gen("SELECT user_name,user_email FROM `#user` WHERE " . $query . " LIMIT 1"))
		{
			$row = $sql->fetch();
			$send_to = $row['user_email'];
			$send_to_name = $row['user_name'];
		}
		else
		{
			$send_to = SITEADMINEMAIL;
			$send_to_name = ADMIN;
		}


		// ----------------------

		$CONTACT_EMAIL = e107::getTemplate('econtact', 'contact', 'email');

		unset($_POST['contact_person'], $_POST['author_name'], $_POST['email_send'], $_POST['subject'], $_POST['body'], $_POST['rand_num'], $_POST['code_verify'], $_POST['send-contactus']);

		if (!empty($_POST)) // support for custom fields in contact template.
		{
			foreach ($_POST as $k => $v)
			{
				$body .= "<tr><td>" . $k . ":</td><td>" . $tp->toEmail($v, true, 'RAWTEXT') . "</td></tr>";
			}
		}

		$body .= "</table>";

		if (!empty($CONTACT_EMAIL['subject']))
		{
			$vars = array('CONTACT_SUBJECT' => $subject, 'CONTACT_PERSON' => $send_to_name);

			if (!empty($_POST)) // support for custom fields in contact template.
			{
				foreach ($_POST as $k => $v)
				{
					$scKey = strtoupper($k);
					$vars[$scKey] = $tp->toEmail($v, true, 'RAWTEXT');
				}
			}

			$subject = $tp->simpleParse($CONTACT_EMAIL['subject'], $vars);
		}

		// -----------------------

		// Send as default sender to avoid spam issues. Use 'replyto' instead.
		$eml = array(
			'subject'      => $subject,
			'sender_name'  => $sender_name,
			'body'         => $body,
			'replyto'      => $sender,
			'replytonames' => $sender_name,
			'template'     => 'default'
		);


		$message = e107::getEmail()->sendEmail($send_to, $send_to_name, $eml) ? LAN_CONTACT_09 : LAN_CONTACT_10;

		//	$message =  (sendemail($send_to,"[".SITENAME."] ".$subject, $body,$send_to_name,$sender,$sender_name)) ? LANCONTACT_09 : LANCONTACT_10;

		if (isset($pref['contact_emailcopy']) && $pref['contact_emailcopy'] && $email_copy == 1)
		{
			require_once(e_HANDLER . "mail.php");
			sendemail($sender, "[" . SITENAME . "] " . $subject, $body, ADMIN, $sender, $sender_name);
		}


		$ns->tablerender('', "<div class='alert alert-success'>" . $message . "</div>");
	}
	else
	{
		message_handler("P_ALERT", $error);
	}
}



require_once(FOOTERF);
