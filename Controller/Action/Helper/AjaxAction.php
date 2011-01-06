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
class Moo_Controller_Action_Helper_AjaxAction extends Zend_Controller_Action_Helper_Abstract
{
    protected $_output = '';

    public function ajaxAction($ajaxTask = null)
    {
        try {
            if (null === $ajaxTask) {
                return $this;
            }
            $this->getHelper('layout')->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender(true);

            // method name
            $method = $ajaxTask . 'Ajax';

            // only ajax request allowed
            if (!$this->_isAjax()) {
                throw new Exception('You don\'t have the right to access this page.');
            }

            if (empty($ajaxTask) || !method_exists($this->getActionController() , $method)) {
                throw new Exception('Incorrect request.');
            }

            /*if(!$this->getHelper('acl')->isAllowAjaxAccess($ajaxTask, $this->getRequest()->getControllerName())) {
             throw new Exception('You don\'t have access right.');
             }*/

            // call method our ajax method it should return an array
            $this->_output = $this->getActionController()->$method();

            // echo the result from the ajax method
            $this->echoResult();

        } catch (Exception $exp) {
            $result = array('faild' => '2','message'=>'');

            if ($this->view->env != 'production') {
                $output = '<div style="width:600px">An unexpected error occurred.';
                $output .= '<h2>Unexpected Exception: ' . $exp->getMessage() . ' (' . $method . ')</h2><br /><pre style="overflow:scroll;width:600px;">';
                $output .= $exp->getTraceAsString();
                $output .= '</pre></div>';
            } else {
                if ($exp->getCode() == 777) {
                    $output = $exp->getMessage();
                } else {
                    $output ='An unexpected error occurred. Please try again later.';
                }
            }

            $result['content'] = $output;
            $result['message'] = $output;
            $this->_output =  $result;
            $this->echoResult();
        }
    }

    /**
     * Proxy for undefined properties.  Default behavior is to throw an
     * exception on undefined properties.
     *
     * @param  string $property
     * @return void
     * @throws Zend_Controller_Action_Exception
     */
    public function __get($property)
    {
        switch ($property) {
            case '_request':
                return $this->getActionController()->getRequest();
                break;
            case 'view':
                return $this->getActionController()->view;
                break;
            case '_invokeArgs':
                return $this->getActionController()->getInvokeArgs();
                break;
        }
        throw new Zend_Controller_Action_Exception(sprintf('Property "%s" does not exist in __get()', $property), 500);
    }

    /**
     * Proxy for undefined methods.  Default behavior is to throw an
     * exception on undefined methods.
     *
     * @param  string $property
     * @return void
     * @throws Zend_Controller_Action_Exception
     */
    public function __call($methodName, $args)
    {
        if (method_exists($this->getActionController(), $methodName)) {
            return call_user_func_array( array($this->getActionController(), $methodName) , $args);
        }

        throw new Zend_Controller_Action_Exception(sprintf('Method "%s" does not exist and was not trapped in __call()', $methodName), 500);
    }

    /**
     * Print out the ajax response. Incase of a HTTP request to an iframe,
     * we assume it is an ajax request and wrap the response with <textarea>
     *
     * @return void
     */
    public function echoResult()
    {
        $iframe = $this->_getParam('iframe');

        if (!empty($iframe)) {
            echo '<textarea>';
        }
        echo Zend_Json::encode($this->_output);
        if (!empty($iframe)) {
            echo '</textarea>';
        }
    }

    /**
     * Proxy for getParam() in Zend_Controller_Request_Abstract
     *
     * @param string $paramName
     * @param mix $default
     * @return string/mix
     */
    protected function _getParam($paramName, $default = null)
    {
        return $this->_request->getParam($paramName, $default);
    }

    /**
     * Check if the current request is ajax or not
     * 
     * @return boolean
     */
    protected function _isAjax()
    {
        $iframe = $this->_getParam('iframe');

        if ($this->getRequest()->isXmlHttpRequest()) {
            return true;
        }

        if (!empty($iframe)) {
            return true;
        }
        return false;
    }

    /**
     * Perform helper when called as $this->_helper->ajaxAction() from an action controller
     *
     * Proxies to {@link simple()}
     *
     * @param  string $method
     * @return void
     */
    public function direct($method = null)
    {
        return $this->ajaxAction($method);
    }
}
