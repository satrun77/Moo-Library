<?php

/**
 *
 * @copyright  2009 Mohammed Alsharaf
 * @author     Mohamed Alsharaf (mohamed.alsharaf@gmail.com)
 * @category   Moo
 * @package    Moo_Controller
 * @copyright  Copyright (c) 2009-2010 Mohammed Alsharaf.
 * @version    Release: 0.0.1
 * @link       http://jamandcheese-on-phptoast.com/
 */
class Moo_Controller_Action_Helper_PageTitle extends Zend_Controller_Action_Helper_Abstract
{

    public function pageTitle($pageTitle, $appendName=false)
    {
        $view = $this->getActionController()->view;
        $view->pageTitle = $pageTitle;
        if (is_string($appendName)) {
            $pageTitle = $pageTitle . ' :: ' . $appendName;
            $view->pageTitle = $appendName;
        }
        $view->headTitle()->enableTranslation()->set($pageTitle . ' :: ' . $view->siteTitle);

        return $this;
    }

    public function direct($pageTitle, $appendName=false)
    {
        return $this->pageTitle($pageTitle, $appendName);
    }

}
