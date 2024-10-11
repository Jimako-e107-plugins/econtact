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
			'controller' => 'pages_ui',
			'path'       => null,
			'ui'         => 'pages_form_ui',
			'uipath'     => null
		],
		'contact' => [
			'controller' => 'contact_ui',
			'path'       => null,
			'ui'         => 'contact_form_ui',
			'uipath'     => null
		],
	];

	// Define admin menu
	protected $adminMenu = [
		'main/prefs'  => array('caption' => "Pages settings", 'perm' => 'P'),
		'contact/prefs'  => ['caption' => LAN_PREFS, 'perm' => 'P'],
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

class pages_ui extends e_admin_ui
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
		'contact_pages' => array(
			'title' => '',
			'tab' => 0,
			'type' => 'method',
			'data' => 'json',
			'width' => '38%',
			'help' => '',
			'readParms' => '',
			'writeParms' => array("nolabel" => 1),
			'class' => 'left',
			'thclass' => 'left'
		),
 
	];

	// Initialization method
	public function init()
	{ 

	}


	public function afterPrefsSave()
	{
		// Clear existing messages to avoid displaying outdated information
		e107::getMessage()->reset();

		// Retrieve the updated preferences from the plugin
		$contactPrefs = e107::pref('contact', 'contact_pages');
		$contactPagesPrefs = e107::unserialize($contactPrefs);
 
		// Get new email values from plugin preferences
		$newCoreContactVisibility = $contactPagesPrefs['contactform']['contact'] ?? '';
 
		// Save the updated core preferences
		$config = e107::getConfig();
		$config->set('contact_visibility', $newCoreContactVisibility);

		// Attempt to save changes and provide feedback
		$config->save();
	    e107::getCache()->clear('core_prefs');
 
		// Return true to continue with the normal plugin preference saving process
		return true;
	}

	// Render help section
	public function renderHelp()
	{
		$caption = LAN_HELP;
		$text = 'If page is not available, URL is redirected to homepage<br>';
		return ['caption' => $caption, 'text' => $text];
	}
}

class main_form_ui extends e_admin_form_ui
{
 
 
}

class pages_form_ui extends e_admin_form_ui
{

	public function contact_pages($curVal, $mode)
	{
		switch ($mode)
		{
			case 'read': // Edit Page
				return "Are you cheating?"; // Simplified return statement

			case 'write': // Edit Page
				// Fetch current values
				$value = $curVal;
				$contact_pages = self::contactPages();
				$theme_layouts = self::themeLayouts();
				$contact_layouts = self::contactLayouts();

				// Start building the output
				$text = "<div class='e-container'>";
				$text .= "<table class='table table-striped table-bordered' style='margin-bottom:40px'>";
				$text .= "<colgroup>
                          <col style='min-width:220px' />
                          <col style='width:15%' />
                          <col style='width:15%' />
                      </colgroup>";
				$text .= "<thead>
                          <tr>
                              <th>" . LAN_PAGE . "</th>
                              <th>Is page available?</th>
                              <th>Theme Page Layout</th>
							  <th>Form Visibility</th>
							  <th>Contact Info Visibility</th>
							  <th>Contact Layout</th>
                          </tr>
                      </thead><tbody>";

				// Loop through contact pages to generate rows
				foreach ($contact_pages as $page => $val)
				{
					$text .= "<tr>";
					$text .= "<td>" . htmlspecialchars($val) . "</td>";

					// Activate checkbox
					$nameitem = 'activate';
					$field = ['type' => 'boolean'];
					$actual_value = $value[$nameitem][$page] ?? ''; // Use null coalescing operator for better readability
					$text .= "<td>" . $this->renderElement("contact_pages[$nameitem][$page]", $actual_value, $field) . "</td>";

					// Layout dropdown
					$nameitem = 'themelayout';
					$field = ['type' => 'dropdown', 'writeParms' => $theme_layouts];
					$actual_value = $value[$nameitem][$page] ?? ''; // Use null coalescing operator
					$text .= "<td>" . $this->renderElement("contact_pages[$nameitem][$page]", $actual_value, $field) . "</td>";

					// form visibility
					$nameitem = 'contactform';
					$field['contact'] = ['type' => 'userclass', 'writeParms' => ['default' => e_UC_PUBLIC] ];
					$field['report'] = ['type' => 'userclass', 'writeParms' => ['default' => e_UC_MEMBER]];
					$actual_value = $value[$nameitem][$page] ?? ''; // Use null coalescing operator
					$text .= "<td>" . $this->renderElement("contact_pages[$nameitem][$page]", $actual_value, $field[$page]) . "</td>";

					// form visibility
					$nameitem = 'contactinfo';
					$field['contact'] = ['type' => 'userclass', 'writeParms' => ['default' => e_UC_PUBLIC]];
					$field['report'] = ['type' => 'userclass', 'writeParms' => ['default' => e_UC_MEMBER]];
					$actual_value = $value[$nameitem][$page] ?? ''; // Use null coalescing operator
					$text .= "<td>" . $this->renderElement("contact_pages[$nameitem][$page]", $actual_value, $field[$page]) . "</td>";

					// contact layout
					$nameitem = 'contactlayout';
					$field = ['type' => 'dropdown', 'writeParms' => $contact_layouts];
					$actual_value = $value[$nameitem][$page] ?? ''; // Use null coalescing operator
					$text .= "<td>" . $this->renderElement("contact_pages[$nameitem][$page]", $actual_value, $field) . "</td>";

					$text .= "</tr>"; // Close table row
				}

				$text .= "</tbody></table></div>"; // Close table and container
				return $text; // Return the complete HTML output
		}

		return null; // Default return for unsupported modes
	}


	/**
	 * Set supported pages by this plugin
	 *
	 * @return array
	 */
	public static function contactPages()
	{

		$array = [
			'contact' => 'Contact Page',
			'report' => 'Report Page',
		];

		return $array;
	}

	public static function themeLayouts()
	{
		$pref = e107::getPref();

		$layouts[0] = '--- Theme Manager ---';
		foreach ($pref['sitetheme_layouts'] as $key => $val)
		{

			$layoutName = $val['@attributes']['title'];

			$layouts[$key] =  $layoutName;
		}

		return $layouts;
	}

	public static function contactLayouts()
	{
		$templates = e107::getLayouts('contact', 'contact_layout', 'front', null, true, false);
 
		return $templates;
	}

}

// Create an instance of the admin area
new contact_adminArea();

// Include authentication and footer files
require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;
