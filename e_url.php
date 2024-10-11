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

if (!defined('e107_INIT'))
{
	exit;
}

// v2.x Standard - Simple mod-rewrite module

class contact_url // plugin-folder + '_url'
{
	/**
	 * URL configuration for the contact plugin
	 * @return array URL rewrite configuration
	 */
	public function config()
	{

		// URL configuration for the 'index' route
		$config['report'] = [
				'alias'     => 'report/', // URL alias
				'regex'     => '^{alias}\/?([\?].*)?\/?$',  // Regex for URL matching
				'sef'       => '{alias}', // SEF (Search Engine Friendly) URL
				'redirect'  => '{e_PLUGIN}contact/report.php$1', // Redirection target
			];

		// URL configuration for the 'index' route
		$config['index'] = [
				'alias'     => 'contact/', // URL alias
				'regex'     => '^{alias}/?$', // Regex for URL matching
				'sef'       => '{alias}', // SEF (Search Engine Friendly) URL
				'redirect'  => '{e_PLUGIN}contact/contact.php', // Redirection target
		]; 

		return $config;
	}
}
