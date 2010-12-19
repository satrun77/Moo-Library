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
class Moo_Controller_Action_Helper_Messenger extends Zend_Controller_Action_Helper_Abstract
{
    protected $_flashMessenger = null;

    public function messenger($name='error', $message=null)
    {
        if ($name == 'error' && $message === null) {
            return $this;
        }
        if (!isset($this->_flashMessenger[$name])) {
            $this->_flashMessenger[$name] = $this->getActionController()
                                                 ->getHelper('FlashMessenger')
                                                 ->setNamespace($name.'_message');
        }
        if ($message !== null) {
            $message = $this->getActionController()->view->translate($message);
            $this->_flashMessenger[$name]->addMessage($message);
        }
        return $this->_flashMessenger[$name];
    }

    public function direct($name='error', $message=null)
    {
        return $this->messenger($name,$message);
    }
}