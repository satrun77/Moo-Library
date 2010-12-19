<?php
/**
 *
 * @copyright  2009 Mohammed Alsharaf
 * @author     Mohamed Alsharaf (mohamed.alsharaf@gmail.com)
 * @category   Moo
 * @package    Moo_View
 * @copyright  Copyright (c) 2009-2010 Mohammed Alsharaf.
 * @version    Release: 0.0.2
 * @link       http://jamandcheese-on-phptoast.com/
 */
class Moo_View_Helper_Messenger extends Zend_View_Helper_Abstract
{
    const DEFAULT_MSG = 'info';

    protected $_messageKeys = array(
        'msg_message',
        'error_message',
        'info_message',
        'success_message',
        'warning_message',
    );

    protected $_flashMessenger = null;

    public function messenger($messages = null)
    {
        if ($messages !== null) {
            return $this->_renderInjectedMessages($messages);
        }
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

        foreach ($this->_messageKeys as $messageKey) {
            $messages = $this->_getMessages($messageKey);
            if ($messages) {
                echo $this->_renderMessage($messages,$messageKey);
            }
            unset($messages);
        }
    }

    protected function _renderInjectedMessages($messages)
    {
        if (!is_array($messages)) {
            return $this->_renderMessage((string) $messages,self::DEFAULT_MSG);
        }

        $return = '';
        foreach ($messages as $messageKey => $message) {
            $return  .= $this->_renderMessage($message, $messageKey . '_message');
        }
        return $return;
    }
    protected function _getMessages($messageKey)
    {
        $result = array();
        $this->_flashMessenger->setNamespace($messageKey);

        if ($this->_flashMessenger->hasMessages()) {
            $result = $this->_flashMessenger->getMessages();
        }

        // check view object
        if (isset($this->view->$messageKey)) {
            array_push($result, $this->view->$messageKey);
        }

        //add any messages from this request
        if ($this->_flashMessenger->hasCurrentMessages()) {
            $result = array_merge( $result, $this->_flashMessenger->getCurrentMessages());
            //we don�t need to display them twice.
            $this->_flashMessenger->clearCurrentMessages();
        }
        return $result;
    }

    protected function _renderMessage($message, $name)
    {
        if (!is_array($message)) {
            $message = array($message);
        }
        return $this->view->htmlList($message, false, array('class'=>$name), false);
    }
}
