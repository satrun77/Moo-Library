<?php

/**
 *
 * @copyright  2010 Mohammed Alsharaf
 * @author     Mohamed Alsharaf (mohamed.alsharaf@gmail.com)
 * @category   Moo
 * @package    Moo_Utility
 * @copyright  Copyright (c) 2010-2011 Mohammed Alsharaf.
 * @version    Release: 0.2.1
 * @link       http://jamandcheese-on-phptoast.com/
 */
class Moo_Utility
{

    /**
     * Logger
     * 
     * @param string $message
     * @param string $type
     */
    public static function log($message, $type = 'firebug')
    {
        $logger = new Zend_Log();
        if ($type == 'firebug') {
            $fbWriter = new Zend_Log_Writer_Firebug();
            $logger->addWriter($fbWriter);
        } else if ($type == 'log') {
            $stream = @fopen(PUBLIC_PATH . "/log.txt", 'a');
            if ($stream) {
                $stdWritter = new Zend_Log_Writer_Stream($stream);
                $stdWritter->addFilter(Zend_Log::DEBUG);
                $stdWritter->addFilter(Zend_Log::INFO);
                $logger->addWriter($stdWritter);
            }
        }
        $logger->log($message, 6);
    }

    /**
     * print debug message and backtrace
     * 
     * @param mix $object
     * @param string $label
     * @param boolean $die
     * @return void
     */
    public static function debug($object, $label= '', $die= true)
    {
        $debug_backtrace = debug_backtrace();

        // log to a file
        if ($die === 'file') {
            $h = fopen(APPLICATION_PATH . '/../data/logs/debuglog.txt', 'a');
            $string = 'label: ' . $label . "\n"
                    . print_r($object, true)
                    . "\n-----------------------------------------------\n";
            fwrite($h, $string);
            fclose($h);
            return;
        }

        // if the request is ajax then print debug trace as text only
        $iframe = isset($_GET['iframe']) ? (int) $_GET['iframe'] : '';
        $request = new Zend_Controller_Request_Http;
        if ($request->isXmlHttpRequest() || !empty($iframe)) {
            $debug = $label . "\n" . $debug_backtrace[1]['file'] . " (line " . $debug_backtrace[1]['line'] . ")\n";
            $debug .= '==========================================================================================\n';
            $debug .= print_r($object, true);
            $debug .= '==========================================================================================\n';
            die($debug);
        }

        // print html debug trace
        $hoverColor = '#ffff99'; //#BFDFFF';
        $bg = '#fff';
        $styles1 = 'color: #101010;white-space:nowrap;clear: both; text-align: left; width: 98%; margin:10px auto; background: #F4F2FF; border: 1px solid #808000; font-family: Tahoma;  font-size: 12px;';
        $styles2 = 'padding: 10px 10px 0px 10px;';
        $styles3 = 'background:#fff;font-weight: bold; color: #101010; border-bottom: 1px dotted #aaa; padding:8px;';
        $styles4 = 'color:#FFF; background-color:#BFDFFF;padding:0px 10px;margin:0px 10px 0px 0px; border:1px solid #BFDFFF; -moz-border-radius: 5px; -webkit-border-radius: 5px;';
        $styles5 = 'background:#fff;overflow:hidden;font-weight: bold; color: #000099; border-bottom: 1px dotted #aaa; padding-bottom: 10px;';
        $styles6 = 'float:right;color:#434343;';
        $styles7 = 'display:none;overflow:hidden;width:100%;clear:both;';
        $styles8 = 'border-bottom:0px solid #008200;padding:5px 0;float:left;width:100%;line-height:19px;';
        $styleFile = 'float:left;clear:both;width:100%;';
        $styleFn = 'float:left;margin-right:5px;color:#666666;font-style:italic;';
        $styleLine = 'float:left;margin-right:5px';
        $debug = '<div id="debug_wrapper" style="' . $styles1 . '">';
        $debug .= '<div id="debug_content" style="' . $styles2 . '">';
        $debug .= '<div id="debug_location" style="' . $styles3 . '">';
        $debug .= '<span style="' . $styles4 . '">' . strtoupper($label) . '</span>Debug called from ' . $debug_backtrace[1]['file'] . ' (line ' . $debug_backtrace[1]['line'] . ')';
        $debug .= '</div>';

        $debug .= '<div id="debug_content" style="' . $styles5 . '">'
                . "<a style=\"" . $styles6 . "\" href=\"javascript:;\" onclick=\"var d = this.nextSibling;if(d.style.display == 'block') {d.style.display = 'none';} else {d.style.display = 'block';}\" onmouseover=\"this.style.color='#808000'\" onmouseout=\"this.style.color='#434343'\">Show Backtrace</a>"
                . '<ul style="' . $styles7 . '">';

        foreach ($debug_backtrace as $aDebug) {
            $debug .= '<li onmouseover="this.style.backgroundColor=\'' . $hoverColor . '\'" onmouseout="this.style.backgroundColor=\'' . $bg . '\'" style="' . $styles8 . '">';
            $debug .= '<div style="' . $styleLine . '">Line ' . (isset($aDebug['line']) ? $aDebug['line'] : '') . ' -</div>';
            $debug .= '<div style="' . $styleFn . '">';
            if (isset($aDebug['class'])) {
                $debug .= $aDebug['class'] . (isset($aDebug['type']) ? $aDebug['type'] : '') . $aDebug['function'] . '();';
            } else if (isset($aDebug['function'])) {
                $debug .= $aDebug['function'] . '();';
            }
            $debug .= '</div>';
            $debug .= '<div style="' . $styleFile . '">' . $aDebug['file'] . '</div>';
            $debug .= '</li>';
        }
        $debug .= '</ul>'
                . '</div>';

        $debug .= '<pre>';
        $debug .= Zend_Debug::dump($object, '', false);
        $debug .= '</pre>';
        $debug .= '</div>';
        $debug .= '</div>';

        echo $debug;

        if ($die === true) {
            die;
        }
    }

    /**
     * Helper method to remove prefix from module resources
     * e.g. remove 'Core_Model_Mapper_' from product model class Core_Model_Mapper_Product
     *
     * @param string $className
     * @return string
     */
    public static function removeClassPrefix($className)
    {
        $resourceTypes = array(
            'Model_DbTable' => 'models/DbTable',
            'Model_Mapper' => 'models/mappers',
            'Model_Helper' => 'models/helpers',
            'Form' => 'forms',
            'Model' => 'models',
            'Plugin' => 'plugins',
            'Service' => 'services',
            'ViewHelper' => 'views/helpers',
            'ViewFilter' => 'views/filters',
        );
        $pattern = "/^(Core|Custom)_(\w+_)?(" . join('|', array_keys($resourceTypes)) . ")_(\w+)/";
        $status = preg_match($pattern, $className, $matchs);

        // check if the class name found or valid
        if ($status === false) {
            return '';
        }
        if (!isset($matchs[4])) {
            return '';
        }

        return $matchs[4];
    }

    /**
     * Helper method to convert Dashed string into CamelCase
     *
     * @param string $module
     * @return string
     */
    public static function getModuleClass($module)
    {
        $filter = new Zend_Filter_Word_DashToCamelCase();
        return $filter->filter($module);
    }

}