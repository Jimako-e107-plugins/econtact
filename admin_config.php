<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2025 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * #######################################
 * #     e107 contact plugin             #
 * #     by Jimako                       #
 * #     https://www.e107sk.com          #
 * #######################################
 */

require_once('../../class2.php');

if (!getperms('P'))
{
	e107::redirect('admin');
	exit;
}

class contact_adminArea extends e_admin_dispatcher
{
	// Define modes
	protected $modes = [
		'main' => [
			'controller' => 'contact_ui',
			'path'       => null,
			'ui'         => 'contact_form_ui',
			'uipath'     => null
		],
	];

	// Define admin menu
	protected $adminMenu = [
		'main/prefs' => ['caption' => LAN_PREFS, 'perm' => 'P'],
	];

	// Define admin menu aliases
	protected $adminMenuAliases = [
		'main/edit' => 'main/list'
	];

	// Set menu title
	protected $menuTitle = 'Contact';
}

class contact_ui extends e_admin_ui
{
	// Plugin configurations
	protected $pluginTitle = 'Contact';
	protected $pluginName = 'contact';
	protected $table = ''; // No table defined
	protected $pid = '';
	protected $perPage = 10;
	protected $batchDelete = true;
	protected $batchExport = true;
	protected $batchCopy = true;

	// Define preferences
	protected $prefs = [
		'contact_email1'      	=> ['title' => 'Contact Info', 'tab' => 0, 'type' => 'text', 'data' => 'str', 'writeParms' => []],
		'contact_email2' 		=> ['title' => 'Contact Email Copy', 'tab' => 0, 'type' => 'text', 'data' => 'str', 'writeParms' => []],
	];

	// Initialization method
	public function init()
	{
		$contactInfo = e107::getPref('contact_info');
		$this->prefs['contact_email1']['writeParms']['default'] = $contactInfo['email1'];
		$this->prefs['contact_email2']['writeParms']['default'] = $contactInfo['email2'];

	}

	public function afterPrefsSave()
	{
		// Clear existing messages to avoid displaying outdated information
		e107::getMessage()->reset();

		// Retrieve the updated preferences from the plugin
		$contactPrefs = e107::pref('contact');

		// Get new email values from plugin preferences
		$newEmail1 = $contactPrefs['contact_email1'] ?? '';
		$newEmail2 = $contactPrefs['contact_email2'] ?? '';

		// Fetch current core preferences related to contact information
		$coreContactInfo = e107::getPref('contact_info');

		// Update core preference values with new email information
		$coreContactInfo['email1'] = $newEmail1;
		$coreContactInfo['email2'] = $newEmail2;

		// Save the updated core preferences
		$config = e107::getConfig();
		$config->set('contact_info', $coreContactInfo);

		// Attempt to save changes and provide feedback
		if ($config->save())
		{
			// Clear the cache to reflect the latest changes
			e107::getCache()->clear('core_prefs');

			// Inform the user about the successful update
			e107::getMessage()->addSuccess('Core preferences updated successfully.');
		}
		else
		{
			// Inform the user about the failure to update preferences
			e107::getMessage()->addError('Failed to update core preferences.');
		}

		// Return true to continue with the normal plugin preference saving process
		return true;
	}

	// Render help section
	public function renderHelp()
	{
		$caption = LAN_HELP;
		$text = 'Some help text';
		return ['caption' => $caption, 'text' => $text];
	}
}

class contact_form_ui extends e_admin_form_ui
{
	// Custom form-related logic can go here
}

// Create an instance of the admin area
new contact_adminArea();

// Include authentication and footer files
require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;
