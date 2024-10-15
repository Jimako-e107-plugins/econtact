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

class contact_menu
{
	public function __construct()
	{
		// Load the language file for the contact plugin
		e107::lan('contact', 'lan_contact');
	}

	/**
	 * Returns the configuration fields for the menu.
	 * @param string $menu Menu name (e.g., 'contact')
	 * @return array Configuration fields for the selected menu
	 */
	public function config($menu = '')
	{
		// Get the database object and initialize the fields array
		$sql = e107::getDb();
		$fields = [];

		// Fetch available templates for the contact menu
		$templates = e107::getLayouts('contact', 'contact_menu', 'front', null, true, false);

		// Set a default caption if LAN_CONTACT_00 is defined
		$default_caption = defset('LAN_CONTACT_PAGE_TITLE', 'Contact Us');

		// Switch case to handle different menu types
		switch ($menu)
		{
			case 'contact':
				// Define the configuration fields for the 'contact' menu
				$fields['caption'] = [
					'title'      => LAN_CAPTION,
					'type'       => 'text',
					'multilan'   => true,
					'writeParms' => ['size' => 'xxlarge', 'default' => $default_caption]
				];

				$fields['subtitle'] = [
					'title'      => LAN_TITLE,
					'type'       => 'text',
					'multilan'   => true,
					'writeParms' => ['size' => 'xxlarge']
				];

				$fields['template'] = [
					'title'      => LAN_TEMPLATE,
					'type'       => 'dropdown',
					'writeParms' => ['optArray' => $templates],
					'help'       => ''
				];

				$fields['tablestyle'] = [
					'title'      => 'Tablestyle key',
					'type'       => 'text',
					'writeParms' => ['size' => 'xxlarge']
				];

				// Return the fields configuration
				return $fields;

			default:
				// Return an empty array if no menu is matched
				return [];
		}
	}
}
