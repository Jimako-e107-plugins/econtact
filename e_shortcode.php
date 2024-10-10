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

class contact_shortcodes extends e_shortcode
{
    /**
     * Shortcode to display contact information based on the type passed in $parm.
     * @param array|null $parm Parameters that specify which type of contact info to display.
     * @return string|null Parsed HTML output or obfuscated email/phone.
     */
    public function sc_contact_info($parm = null)
    {
        // Fetch contact information from preferences
        $contactInfo = e107::getPref('contact_info');

        // Get the 'type' parameter from $parm
        $type = $parm['type'] ?? null;

        // If no type is specified or the contact info for that type is not set, return null
        if (empty($type) || empty($contactInfo[$type]))
        {
            return null;
        }

        // Get the parser object for HTML rendering and obfuscation
        $tp = e107::getParser();

        // Variable to hold the return value
        $ret = '';

        // Handle different types of contact information
        switch ($type)
        {
            case "organization":
                // Render the organization name as a title
                $ret = $tp->toHTML($contactInfo[$type], true, 'TITLE');
                break;

            case 'email1':
            case 'email2':
            case 'phone1':
            case 'phone2':
            case 'phone3':
            case 'fax':
                // Obfuscate the contact information (email or phone numbers)
                $ret = $tp->obfuscate($contactInfo[$type]);
                break;

            default:
                // Render other contact information as a body element
                $ret = $tp->toHTML($contactInfo[$type], true, 'BODY');
                break;
        }

        return $ret;
    }
}
