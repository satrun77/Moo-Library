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
class Moo_Mail
{
    private $body = null;
    protected $_mail = null;

    const HTML_BODY = 1;
    const TEXT_BODY = 2;

    protected $_resource = null;

    public function __construct(array $options = null)
    {
        if (isset($options['resource'])) {
            $this->setResource($options['resource']);
        } else {
            $this->setResource();
        }
        $this->_setSmtp();
    }

    /**
     * Set mail resource or use the default one
     *
     * @param Moo_Mail_Resource $resource
     * @return Moo_Mail
     */
    public function setResource(Moo_Mail_Resource $resource = null)
    {
        if (null === $resource) {
            $this->_resource = new Moo_Mail_Resource_Default();
        } else {
            $this->_resource = $resource;
        }
        return $this;
    }

    /**
     * Retrieve mail object instance
     *
     * @return Zend_Mail
     */
    public function getMail()
    {
        if (is_null($this->_mail)) {
            $this->_mail = new Zend_Mail('utf-8');
        }
        return $this->_mail;
    }

    /**
     * Set the email body content
     * 
     * @param array $data
     * @param string/mix $template
     * @param boolean $useLayout
     * @return Moo_Mail
     */
    public function setBody(array $data, $template, $useLayout = false)
    {
        $this->body = $this->_resource->renderTemplate($data, $template, $useLayout);
        return $this;
    }

    /**
     * Set email subject
     *
     * @param string $subject
     * @return Zend_Mail
     */
    public function setSubject($subject)
    {
        return $this->getMail()->setSubject($subject);
    }

    /**
     * Add a recipient details
     * 
     * @param string $email
     * @param string $name
     * @return Zend_Mail
     */
    public function addTo($email, $name)
    {
        return $this->getMail()->addTo($email, $name);
    }

    /**
     * Set the sender details
     *
     * @param string $email
     * @param string $name
     * @return Zend_Mail
     */
    public function setFrom($email, $name)
    {
        return $this->getMail()->setFrom($email, $name);
    }

    /**
     * Setup SMTP details if there are any
     *
     * @return void
     */
    protected function _setSmtp()
    {
        if (!$this->_resource->isSmtpEnabled()) {
            return;
        }

        $config = array(
            'auth' => 'login',
            'username' => $this->_resource->getSmtpUser(),
            'password' => $this->_resource->getSmtpPassword());
        $config += $this->_resource->getExtraParams();

        $this->transport = new Zend_Mail_Transport_Smtp($this->_resource->getSmtpHost(), $config);
        Zend_Mail::setDefaultTransport($this->transport);
    }

    /**
     * Helper method to send an email
     * 
     * @param array $from
     * @param array $to
     * @param string $subject
     * @param int $type
     * @return boolean
     */
    public function send($from = null, $to = null, $subject = null, $type = My_Mail::HTML_BODY)
    {
        if (null !== $subject) {
            $this->getMail()->setSubject($subject);
        }
        if (null !== $from && is_array($from)) {
            $this->getMail()->setFrom($from[0], $from[1]); //setFrom($email, $name = null)
        }
        if (null !== $to && is_array($to)) {
            foreach ($to as $email => $name) {
                $this->getMail()->addTo($email, $name); //addTo($email, $name = null)
            }
        }
        if ($type == self::HTML_BODY) {
            $this->getMail()->setBodyHtml($this->body);
        } else {
            $this->getMail()->setBodyText($this->body);
        }

        return $this->getMail()->send();
    }

    /**
     * Add Reply-To header
     *
     * @param string $email
     * @return Moo_Mail
     */
    public function setReplyTo($email)
    {
        $this->getMail()->addHeader('Reply-To', $email);
        return $this;
    }

    /**
     * Proxy for undefined methods.  Default behavior is to throw an
     * exception on undefined methods.
     *
     * @param string $method
     * @param array $args
     * @return mix
     */
    public function __call($method, array $args)
    {
        if (method_exists($this->getMail(), $method)) {
            $result = call_user_func_array(array($this->getMail(), $method), $args);
            return $result;
        }
        throw new Zend_Mail_Exception('Method ' . (string) $method . 'does not exists.');
    }
}
