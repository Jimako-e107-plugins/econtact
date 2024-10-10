<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2017 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
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

class contact_shortcodes  extends e_shortcode
{
    function sc_contact_info($parm = null)
    {
        $ipref = e107::getPref('contact_info');
        $type = varset($parm['type']);

        if (empty($type) || empty($ipref[$type]))
        {
            return null;
        }

        $tp = e107::getParser();
        $ret = '';

        switch ($type)
        {
            case "organization":
                $ret = $tp->toHTML($ipref[$type], true, 'TITLE');
                break;

            case 'email1':
            case 'email2':
            case 'phone1':
            case 'phone2':
            case 'phone3':
            case 'fax':
                $ret = $tp->obfuscate($ipref[$type]);
                break;

            default:
                $ret = $tp->toHTML($ipref[$type], true, 'BODY');
                // code to be executed if n is different from all labels;
        }

        return $ret;
    }

}
