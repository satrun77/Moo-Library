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
class Moo_Mail_Resource_Default implements Moo_Mail_Resource_Resource
{
    private $app;

    public function __construct()
    {
        $this->app = Moo_Application::getInstance();
    }

    public function getSmtpUser()
    {
        return $this->app->siteSetting('mail/user');
    }

    public function getSmtpPassword()
    {
        return $this->app->siteSetting('mail/pass');
    }

    public function getSmtpHost()
    {
        return $this->app->siteSetting('mail/host');
    }

    public function getSmtpExtraParams()
    {
        $params = $this->app->siteSetting('mail/params');
        if (!is_array($params)) {
            return array();
        }
        return $params;
    }

    public function isSmtpEnabled()
    {
        return $this->app->siteSetting('mail/smtp/enable');
    }

    public function renderTemplate(array $data = array(), $template = null, $useLayout = false)
    {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $view = $bootstrap->getResource('View');

        // send data to view
        $view->data = $data;

        // view path
        $view->addScriptPath($this->app->siteSetting('mail/path'));

        // email body
        $body = $view->render($template . '.phtml');
        if (!$useLayout) {
            return $body;
        }
        $view->body = $body;
        // main site template
        return $view->render('index.phtml');
    }

}
