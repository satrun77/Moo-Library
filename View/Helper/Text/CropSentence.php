<?php
/**
 *
 * @copyright  2009 Mohammed Alsharaf
 * @author     Mohamed Alsharaf (mohamed.alsharaf@gmail.com)
 * @category   Moo
 * @package    Moo_View
 * @copyright  Copyright (c) 2009-2010 Mohammed Alsharaf.
 * @version    Release: 1.0.0
 * @link       http://jamandcheese-on-phptoast.com/
 */
class Moo_View_Helper_Text_CropSentence extends Zend_View_Helper_Abstract
{
    public function cropSentence($string = null, $length = null, $trail = null)
    {
        if (null === $string) {
            return $this;
        }
        return $this->byWords($string, $length, $trail);
    }

    public function byCharacters($string, $length, $trail)
    {
        $stringLength = strlen($string);
        if ($stringLength <= $length) {
            return $string;
        }

        return substr($string, 0, $length) . $trail;
    }

    public function byWords($string, $length, $trail)
    {
        $arrayString = explode(" ", $string);
        if (count($arrayString) <= $length) {
            return $string;
        }
        $summary = array_slice($arrayString, 0, $length);

        return join(' ', $arrayString) . $trail;
    }
}
