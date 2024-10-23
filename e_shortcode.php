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

class econtact_shortcodes extends e_shortcode
{
    static $contactPrefs = array();

    public function __construct() {

        self::$contactPrefs =  e107::pref('econtact');
    }

    /**
     * Shortcode to display contact email with various formatting options.
     *
     * Parameters:
     * - source (optional): Specifies which email to use. Default is "email1".
     * - type (optional): Determines the format of the output. Options include:
     *     - "text" (default): Displays the obfuscated email as plain text.
     *     - "link": Displays the email as a clickable, obfuscated mailto link.
     *     - "custom": Displays a custom-styled mailto link with optional CSS classes.
     * - class (optional): CSS class to apply when using the "custom" type.
     *
     * Usage examples:
     * - {CONTACT_EMAIL: type=text}
     * - {CONTACT_EMAIL: source=email2&type=link}
     * - {CONTACT_EMAIL: type=custom&class=btn btn-primary}
     *
     * @param array|null $parm Parameters for the shortcode.
     * @return string Obfuscated email address based on the provided parameters.
     */


    public function sc_contact_email($parm = null)
    {
        $tp = e107::getParser();

        // Set default values for parameters
        $source = $parm['source'] ?? "email1";
        $type   = $parm['type'] ?? "text";
        $class  = isset($parm['class']) ? "class='" . e107::getParser()->toAttribute($parm['class']) . "'" : "";

        // Get email from preferences
        $email = self::$contactPrefs["contact_{$source}"] ?? null;

        // Return empty if email is not set
        if (empty($email))
        {
            return '';
        }

        // Generate the output based on the 'type' parameter
        switch ($type)
        {
            case "link":
                return $tp->emailObfuscate($email);

            case "custom":
                $obfuscatedEmail = $tp->obfuscate($email);
                return "<a {$class} href='mailto:{$email}'>{$obfuscatedEmail}</a>";

            case "text":
            default:
                return $tp->obfuscate($email);
        }
    }



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
