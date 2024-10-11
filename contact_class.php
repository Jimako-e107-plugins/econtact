<?php

class Contact
{


    /**
     * Plugin Preferences
     * contact::prefs['xy']; 
     */
    static $contactPrefs = array();

    /**
     * Class construcotr
     */
    function __construct()
    {
 
        $this->setProperties();
    }


    /**
     * Define the required plugin properties
     *
     * @return void
     */
    public function setProperties()
    {
        self::$contactPrefs =  e107::pref('contact');
        self::$contactPrefs['contact_pages'] = e107::unserialize(self::$contactPrefs['contact_pages']);
 
    }

    public function setContactLayouts($page = "notset")
    {
        // Check if the specified page layout exists and is greater than zero
        $layoutId = self::$contactPrefs['contact_pages']['themelayout'][$page] ?? null;

        if ($layoutId > 0)
        {
            // Define the layout constant if a valid layout ID is found
            define('THEME_LAYOUT', $layoutId);
        }
    }

    public function setRedirection($page = "notset")
    {
        // Check if the specified page key exists
        if (!array_key_exists($page, self::$contactPrefs['contact_pages']['activate']) || self::$contactPrefs['contact_pages']['activate'][$page] <= 0)
        {
            // Redirect to the homepage if the page is not activated or doesn't exist
            e107::redirect(); // homepage
        }
    }


    public function renderContactForm($page = "notset")  {

        // check if form is allowed to render, don't display form without owner control  
        
        if (!array_key_exists($page, self::$contactPrefs['contact_pages']['contactform']))
        {
            return '';
        }
        $active = self::$contactPrefs['contact_pages']['contactform'][$page];
 
        if(!check_class($active)) {
            if($active == e_UC_MEMBER)  {
                //render message
                return  $this->renderSignupRequired();
            }
        }
        else {
            $CONTACT_FORM = e107::getTemplate('contact', $page, 'form');
            $contact_shortcodes = e107::getScBatch('form', 'contact', false);
            $contact_shortcodes->wrapper($page.'/form');
 
            $text = e107::getParser()->parseTemplate($CONTACT_FORM, true, $contact_shortcodes);

            if (trim($text) !== '')
            {
                return e107::getRender()->tablerender(LAN_CONTACT_02, $text, "contact-form", true);
            }

        }
 
    }


    public function renderContactInfo($page = "notset")
    {

        // check if form is allowed to render, don't display form without owner control  

        if (!array_key_exists($page, self::$contactPrefs['contact_pages']['contactinfo']))
        {
            return '';
        }
        $active = self::$contactPrefs['contact_pages']['contactform'][$page];

        if (!check_class($active))
        {
            if ($active == e_UC_MEMBER)
            {
                //render message
                return  $this->renderSignupRequired();
            }
        }
        else
        {
            $CONTACT_INFO = e107::getTemplate('contact', 'contact', 'info');
            $contact_shortcodes = e107::getScBatch('form', 'contact', false);
            $contact_shortcodes->wrapper('contact/info');

            $text = e107::getParser()->parseTemplate($CONTACT_INFO, true, $contact_shortcodes);
 
            return e107::getRender()->tablerender(LAN_CONTACT_01, $text, "contact-info", true);

        }
    }


    public function renderSignupRequired()
    {

        $srch = array("[", "]");
        $repl = array("<a class='alert-link' href='" . e_SIGNUP . "'>", "</a>");
        $message = LAN_CONTACT_16; // "You must be [registered] and signed-in to use this form.";

        $text = e107::getRender()->tablerender(LAN_CONTACT_02, "<div class='alert alert-info'>" . str_replace($srch, $repl, $message) . "</div>", "contact", true);
        return $text;
    }

    public function getPageLayout($page = "notset") 
    {
        $LAYOUT = '{---CONTACT-INFO---} {---CONTACT-FORM---}  ';
        $layout_key = self::$contactPrefs['contact_pages']['contactlayout'][$page];
 
        $LAYOUT = e107::getTemplate('contact', 'contact_layout' , $layout_key);
 
        return $LAYOUT;

    }


}
