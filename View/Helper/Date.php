<?php

/**
 *
 * @copyright  2010 Mohammed Alsharaf
 * @author     Mohamed Alsharaf (mohamed.alsharaf@gmail.com)
 * @category   Moo
 * @package    Moo_View_Helper
 * @copyright  Copyright (c) 2009-2010 Mohammed Alsharaf.
 * @version    Release: 0.0.1
 * @link       http://jamandcheese-on-phptoast.com/
 */
class Moo_View_Helper_Date extends Zend_View_Helper_Abstract
{
    protected $_date = null;
    protected $_dateObject = null;
    protected $_format = 'l, Y-m-d';

    public function date($date = '')
    {
        $this->setDate($date);

        return $this;
    }

    /**
     * Set date
     *
     * @param string/int $date
     * @return Moo_View_Helper_Date
     */
    public function setDate($date)
    {
        if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        $this->_date = $date;

        return $this;
    }

    /**
     * Set date format
     *
     * @param string $format
     * @return Moo_View_Helper_Date
     */
    public function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * Get date object. If it's not exists return Zend_Date
     * 
     * @return object
     */
    protected function getDateObject()
    {
        if ($this->_dateObject === null) {
            $this->_dateObject = new Zend_Date();
        }
        return $this->_dateObject;
    }

    /**
     * return formatted date
     *
     * @param string $format
     * @return string
     */
    public function toString($format = null)
    {
        if (empty($this->_date)) {
            return '';
        }

        if (null === $format) {
            $format = $this->_format;
        }

        return $this->getDateObject()
                ->set($this->_date)
                ->toString($format);
    }

    /**
     * Check if $this->_date is equal to a date
     *
     * @param string/int $date
     * @return boolean
     */
    public function isEqualToDate($date)
    {
        $date = is_numeric($date) ? $date : strtotime($date);
        $diff = $date - $this->_date;
        if ($diff <= 24) {
            return true;
        }
        return false;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * return relative time since today
     *
     * @author http://www.zfsnippets.com/snippets/view/id/43
     * @return string
     */
    public function sinceToday($accuracy = 2, $splitter = ', ')
    {
        if (time() > $this->_date) {
            $unixtime = time() - $this->_date;
        } else {
            $unixtime = $this->_date - time();
        }

        $mt = new Zend_Measure_Time($unixtime);
        $units = $mt->getConversionList();

        $translate = Zend_Registry::get('Zend_Translate');

        $chunks = array(
            Zend_Measure_Time::YEAR,
            Zend_Measure_Time::WEEK,
            Zend_Measure_Time::DAY,
            Zend_Measure_Time::HOUR,
            Zend_Measure_Time::MINUTE,
            Zend_Measure_Time::SECOND
        );

        $translations = array(
            'year' => array($translate->_('year'), $translate->_('years')),
            'week' => array($translate->_('week'), $translate->_('weeks')),
            'day' => array($translate->_('day'), $translate->_('days')),
            'h' => array($translate->_('hour'), $translate->_('hours')),
            'min' => array($translate->_('minute'), $translate->_('minutes')),
            's' => array($translate->_('second'), $translate->_('seconds'))
        );

        $measure = array();
        for ($i = 0; $i < count($chunks); $i++) {

            $chunk_seconds = $units[$chunks[$i]][0];
            if ($unixtime >= $chunk_seconds) {
                $measure[$units[$chunks[$i]][1]] = floor($unixtime / $chunk_seconds);
                $unixtime %= $chunk_seconds;
            }
        }

        $measure = array_slice($measure, 0, $accuracy, true);

        $str = '';
        foreach ($measure as $key => $val) {
            $unit = $translations[$key];

            if ($val == 1) {
                $unit = $unit[0];
            } else {
                $unit = $unit[1];
            }

            $str .= $val . ' ' . $unit . $splitter;
        }

        return substr($str, 0, 0 - strlen($splitter));
    }

}
