<?php

/**
 *
 * @copyright  2009 Mohammed Alsharaf
 * @author     Mohamed Alsharaf (mohamed.alsharaf@gmail.com)
 * @category   Moo
 * @package    Moo_View
 * @copyright  Copyright (c) 2009-2010 Mohammed Alsharaf.
 * @version    Release: 0.0.1
 * @link       http://jamandcheese-on-phptoast.com/
 */
class Moo_View_Helper_Html_RowClass extends Zend_View_Helper_Abstract
{
    protected static $rowClass = null;

    public function rowClass($obj = null)
    {
        if (null === $obj) {
            return $this;
        }

        if (!is_null(self::$rowClass)) {
            self::$rowClass = 1 + self::$rowClass;
        } else {
            self::$rowClass = 1;
        }

        $class = (self::$rowClass % 2) ? 'odd' : 'even';
        return $class;
    }

    public function Id()
    {
        return self::$rowClass;
    }

}
