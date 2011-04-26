<?php

/**
 *
 * @copyright  2010 Mohammed Alsharaf
 * @author     Mohamed Alsharaf (mohamed.alsharaf@gmail.com)
 * @category   Moo
 * @package    Moo_View
 * @copyright  Copyright (c) 2009-2010 Mohammed Alsharaf.
 * @version    Release: 1.2.0
 * @link       http://jamandcheese-on-phptoast.com/
 */
class Moo_View_Helper_Html_Table extends Zend_View_Helper_Abstract
{
    /**
     * Table default attributes
     *
     * @var array
     */
    protected $_attribs = array(
        'class' => 'table',
        'cellpadding' => '0',
        'cellspacing' => '0',
        'border' => '0',
        'summary' => '',
    );
    /**
     * Columns options
     * 
     * @var array
     */
    protected $_columns = null;
    /**
     * List of allowed attributes for td tag
     * 
     * @var array
     */
    protected $_tdAttribs = array(
        'class', 'align', 'id', 'valign', 'style', 'width'
    );
    /**
     * List of allowed attributes for table tag
     * 
     * @var array
     */
    protected $_tableAttribs = array(
        'class', 'cellpadding', 'cellspacing', 'border',
        'summary', 'style', 'id', 'width'
    );
    /**
     * Caption text
     *
     * @var string
     */
    protected $_caption = null;
    /**
     * Attributes for th tag
     * 
     * @var array
     */
    protected $_headAttribs = array();
    /**
     * Callbak name
     * @var string
     */
    protected $_columnCallback = 'getData';
    protected $_columnHelper = null;
    /**
     * Array contain table data
     *
     * @var array
     */
    protected $_data = null;
    /**
     * Footer output
     *
     * @var string
     */
    protected $_foot = null;
    /**
     * Table output
     *
     * @var html
     */
    protected $_output = '';
    /**
     * Text when no data available
     *
     * @var string
     */
    protected $_notFoundText = null;
    /**
     * Default link parameters
     *
     * @var array
     */
    protected $_params = array(
        'sortBy' => array(),
        'sort' => '',
        'page' => 1,
    );

    public function table($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
        $this->_setParameters();
        return $this;
    }

    protected function setOptions($options = null)
    {
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                }
            }
        }
        return $this;
    }

    /**
     * Set table caption
     * 
     * @param string $caption
     * @return Moo_View_Helper_Html_Table
     */
    public function setCaption($caption)
    {
        $this->_caption = $caption;
        return $this;
    }

    /**
     * Set columns options
     *
     * <code>
     * array(
     *     'col_name' => array(
     *          'class' => 'class_name',
     *          'text'  => 'display text',
     *          'link'  => true, // if set this column is a link
     *          ... any other attributes
     *     ),
     * );
     * </code>
     *
     * @param array $columns
     * @return Moo_View_Helper_Html_Table
     */
    public function setColumns($columns)
    {
        $this->_columns = $columns;
        $this->_countColumns = count($columns);
        return $this;
    }

    /**
     * Set th tag attributes
     * 
     * @param array $attribs
     * @return Moo_View_Helper_Html_Table
     */
    public function setHeadAttribs($attribs)
    {
        $this->_headAttribs = $attribs;
        return $this;
    }

    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    public function setNotFoundText($text)
    {
        $this->_notFoundText = $text;
        return $this;
    }

    /**
     * Set table attributes
     *
     * @param string $options
     * @return Moo_View_Helper_Html_Table
     */
    public function setAttribs($options = null)
    {
        if (is_array($options)) {
            $this->_attribs = array_merge($this->_attribs, $options);
        }
        return $this;
    }

    /**
     * Modify table attribute
     *
     * @param string $name
     * @param string $value
     * @return Moo_View_Helper_Html_Table
     */
    public function setAttrib($name, $value = null)
    {
        if (!empty($name)) {
            $this->_attribs[$name] = $value;
        }
        return $this;
    }

    /**
     * Add any content to the footer of the table
     *
     * @param string|html $foot
     * @return Moo_View_Helper_Html_Table
     */
    public function setFoot($foot)
    {
        $this->_foot = $foot;
        return $this;
    }

    public function setColumnCallback($callback)
    {
        $this->_columnCallback = $callback;
        return $this;
    }

    public function setColumnHelper($helper)
    {
        $this->_columnHelper = $helper;
        return $this;
    }

    public function render()
    {
        $this->_output = '<table ';
        $this->_output .= $this->_renderAttribs($this->_attribs, $this->_tableAttribs);
        $this->_output .= '>';

        if (!is_null($this->_caption)) {
            $this->_output .= "<caption>" . $this->_caption . "</caption>";
        }

        $this->_renderHead();
        $this->_renderBody();
        $this->_renderFoot();

        $this->_output .= '</table>';

        return $this;
    }

    private function _setParameters()
    {
        // request params
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->_params['module'] = $request->getModuleName();
        $this->_params['action'] = $request->getActionName();
        $this->_params['controller'] = $request->getControllerName();
        $sort = $request->getParam('sort');
        if (!empty($sort)) {
            if (!is_array($sort)) {
                $sort = array($sort);
            }
            foreach ($sort as $aSort) {
                $value = explode('_', $aSort);
                $this->_params['sort'][] = $value[1];
                $this->_params['sortBy'][] = $value[0];
            }
        }
        $this->_params['page'] = $request->getParam('page');
        return $this;
    }

    private function _renderHead()
    {
        if (null === $this->_columns) {
            return $this;
        }

        $this->_output .= '<thead><tr>';
        foreach ($this->_columns as $key => $head) {
            $attribs = array_merge($this->_headAttribs, $head);
            $text = $this->_renderColumnText($key, $head);
            $this->_output .= '<th ' . $this->_renderAttribs($attribs, $this->_tdAttribs) . '>';
            $this->_output .= $text . '</th>';
            unset($text);
        }
        $this->_output .= '</tr></thead>';
    }

    private function _renderColumnText($name, $head)
    {
        if (!isset($head['link'])) {
            return $head['text'];
        }

        $class = '';
        $name = strtolower($name);
        $key = array_search($name, $this->_params['sortBy']);
        if ($key !== false) {
            $hsort = strtoupper($this->_params['sort'][$key]) == "DESC" ? "ASC" : "DESC";
            if (isset($this->_tdAttribs['class'])) {
                $class .= $this->_tdAttribs['class'] . ' ';
            }
            $class .= 'sortingBy-' . $hsort;
        } else {
            $hsort = "DESC";
            if (isset($this->_tdAttribs['class'])) {
                $class .= $this->_tdAttribs['class'] . ' ';
            }
            $class .= 'DESC';
        }
        $this->_tdAttribs['class'] = $class;

        $xhtml = "<a class='sortable'
		     href='" . $this->view->url(array(
                        'sort' => $this->view->escape($name) . '_' . $hsort,
                        'page' => $this->_params['page'])) . "'>";
        $xhtml .= $this->view->escape($head['text']);
        $xhtml .= "</a>";
        return $xhtml;
    }

    private function _renderAttribs($attribs = null, $attribsFor = null)
    {
        $output = '';
        if (is_array($attribs)) {
            foreach ($attribs as $name => $value) {
                if (!in_array($name, $attribsFor)) {
                    continue;
                }
                $output .= $this->_attribName($name) . '="' . $this->_attribValue($value) . '" ';
            }
        }
        return $output;
    }

    private function _attribName($name)
    {
        return strip_tags($name);
    }

    private function _attribValue($name)
    {
        return strip_tags($name);
    }

    private function _renderBody()
    {
        $this->_output .= '<tbody>';

        if (null !== $this->_data && $this->_data !== false && count($this->_data) > 0) {
            $i = 0;
            foreach ($this->_data as $key => $row) {
                $this->_output .= '<tr id="row_' . $i . '" class="' . ($i % 2 ? 'even' : 'odd') . '">';
                foreach ($this->_columns as $key => $column) {
                    $this->_output .= '<td>';
                    $this->_output .= $this->_getData($row, $key, $column);
                    $this->_output .= '</td>';
                }
                $this->_output .= '</tr>';
                $i++;
            }
        } else {
            $this->_noRecords();
        }

        $this->_output .= '</tbody>';
    }

    /**
     *
     * @param <type> $row
     * @param <type> $key
     * @param <type> $column
     * @return <type>
     */
    private function _getData($row, $key, $column)
    {
        if (isset($column['callback']) && is_callable($column['callback'])) {
            $output = $column['callback']($row, $key, $column);
        } elseif (isset($column['callback']) && method_exists($row, $column['callback'])) {
            $output = $row->{$column['callback']}();
        } elseif (method_exists($row, $this->_columnCallback)) {
            $output = $row->{$this->_columnCallback}($key);
        } elseif(isset($column['callback'][0]) && is_object($column['callback'][0])) {
            $output = $column['callback'][0]->$column['callback'][1]($row, $key, $column);
        } else {
            $output = '';
        }

        $helper = isset($column['helper'])? $column['helper'] : $this->_columnHelper;
        if (null !== $helper) {
            $helper = $this->view->getHelper($helper);            
            if ($helper instanceof Moo_View_Helper_Html_Table_RowInterface) {
                $output = $helper->init(array(
                    'output' => $output,
                    'row'    => $row,
                    'key'    => $key,
                    'column' => $column,
                ))
                ->render();
            }
        }
        return $output;
    }

    private function _noRecords()
    {
        if (!is_null($this->_notFoundText)) {
            $this->_output .= '<tr><td class="td" colspan="' . $this->_countColumns . '">';
            $this->_output .= '<div class="noRecords">' . $this->view->escape($this->_notFoundText) . '</div>';
            $this->_output .= '</td></tr>';
        }
    }

    private function _renderFoot()
    {
        if (null !== $this->_foot) {
            $this->_output .= '<tfoot>';
            $this->_output .= '<tr><td class="foot" colspan="' . $this->_countColumns . '">';
            $this->_output .= is_string($this->_foot)? $this->_foot : $this->_foot[0]->{$this->_foot[1]}();
            $this->_output .= '</td></tr>';
            $this->_output .= '</tfoot>';
        }
    }

    /**
     * Render table
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->_output == null) {
            $this->render();
        }
        return $this->_output;
    }
}
     