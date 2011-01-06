<?php

/**
 *
 * @copyright  2009 Mohammed Alsharaf
 * @author     Mohamed Alsharaf (mohamed.alsharaf@gmail.com)
 * @category   Moo
 * @package    Moo_Mail
 * @copyright  Copyright (c) 2009-2010 Mohammed Alsharaf.
 * @version    Release: 0.1.0
 * @link       http://jamandcheese-on-phptoast.com/
 */
interface Moo_Mail_Resource
{
    /**
     * Get SMTP username
     *
     * @return string
     */
    public function getSmtpUser();

    /**
     * Get SMTP password
     *
     * @return string
     */
    public function getSmtpPassword();

    /**
     * Get SMTP Host
     *
     * @return string
     */
    public function getSmtpHost();

    /**
     * Get any extra parameters for SMTP configuration
     *
     * @return array
     */
    public function getSmtpExtraParams();

    /**
     *  True is SMTP is enabled
     *
     * @return boolean
     */
    public function isSmtpEnabled();

    /**
     * Render email content
     *
     * @return string/html
     */
    public function renderTemplate(array $data = array(), $template = null, $useLayout = false);
}
