<?php
/**
 * 分页类
 *
 * @author tommy <tommy@doitphp.com>
 * @copyright Copyright (c) 2010 Tommy Software Studio
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Pagination.php 2.6 2012-12-29 11:40:01Z tommy $
 * @package library
 * @since 1.0
 */

if (!defined('IN_DOIT')) {
    exit();
}

class Pagination {

    /**
     * 分页链接网址
     *
     * @var string
     */
    protected $_baseUrl = null;

    /**
     * 当前页
     *
     * @var integer
     */
    protected $_currentPage = 1;

    /**
     * list总数
     *
     * @var integer
     */
    protected $_totalItems = 0;

    /**
     * 分页总数
     *
     * @var integer
     */
    protected $_totalPages = 0;

    /**
     * 每个页面显示的list数目
     *
     * @var integer
     */
    protected $_perPageItems = 10;

    /**
     * list允许放页码数量,如:1.2.3.4就这4个数字,则$_perCircleLimit为4
     *
     * @var integer
     */
    protected $_perCircleLimit = 10;

    /**
     * list中的坐标. 如:7,8,九,10,11这里的九为当前页,在list中排第三位,则$_circleCenterCount为3
     *
     * @var integer
     */
    protected $_circleCenterCount = 3;

    /**
     * 分页隐藏开关
     *
     * @var boolean
     */
    protected $_hiddenStatus = false;

    /**
     * 第一页
     *
     * @var string
     */
    public $firstPage = '第一页';

    /**
     * 上一页
     *
     * @var string
     */
    public $prePage = '上一页';

    /**
     * 下一页
     *
     * @var string
     */
    public $nextPage = '下一页';

    /**
     * 最后一页
     *
     * @var string
     */
    public $lastPage = '最末页';

    /**
     * 获取总页数
     *
     * @access protected
     * @return integer
     */
    protected function _getTotalPage() {

        return ceil($this->_totalItems / $this->_perPageItems);
    }

    /**
     * 获取当前页数
     *
     * @access protected
     * @return integer
     */
    protected function _parseCurrentPage() {

        //当URL中?page=5的page参数大于总页数时
        return ($this->_currentPage > $this->_totalPages) ? $this->_totalPages : $this->_currentPage;
    }

    /**
     * 设置每页显示的列表数
     *
     * @access public
     *
     * @param integer $num 每页显示的列表数
     *
     * @return object
     */
    public function num($num = null) {

        //参数分析
        if ($num) {
            $this->_perPageItems = $num;
        }

        return $this;
    }

    /**
     * 设置总列表数
     *
     * @access public
     *
     * @param integer $totalNum 总列表数
     *
     * @return object
     */
    public function total($totalNum = null) {

        //参数分析
        if ($totalNum) {
            $this->_totalItems = $totalNum;
        }

        return $this;
    }

    /**
     * 开启分页的隐藏功能
     *
     * @access public
     *
     * @param boolean $item 隐藏开关 , 默认为true
     *
     * @return object
     */
    public function hide($item = true) {

        if ($item === true) {
            $this->_hiddenStatus = true;
        }

        return $this;
    }

    /**
     * 设置分页跳转的网址
     *
     * @access public
     *
     * @param string $baseUrl 分页跳转的网址
     *
     * @return object
     */
    public function url($baseUrl = null) {

        //当网址不存在时
        if ($baseUrl) {
            $this->_baseUrl = trim($baseUrl);
        }

        return $this;
    }

    /**
     * 设置当前的页数
     *
     * @access public
     *
     * @param integer $onPage 当前的页数
     *
     * @return object
     */
    public function page($onPage = null) {

        //参数分析
        if($onPage) {
            $this->_currentPage = $onPage;
        }

        return $this;
    }

    /**
     * 设置分页列表的重心
     *
     * @access public
     *
     * @param integer $countNum 分页列表重心(即:页数)
     *
     * @return object
     */
    public function center($countNum) {

        //参数分析
        if ($countNum && is_int($countNum)) {
            $this->_circleCenterCount = $countNum;
        }

        return $this;
    }

    /**
     * 设置分页列表的列表数
     *
     * @access public
     *
     * @param integer $num 分页列表的列表数
     *
     * @return object
     */
    public function circle($limitNum) {

        //参数分析
        if ($limitNum && is_int($limitNum)) {
            $this->_perCircleLimit = $limitNum;
        }

        return $this;
    }

    /**
     * 获取分页数组
     *
     * @access public
     * @return array
     */
    public function getArray() {

        //支持长的url.
        $this->_baseUrl     = trim(str_replace(array("\n","\r"), '', $this->_baseUrl));
        //获取总页数.
        $this->_totalPages  = $this->_getTotalPage();
        //获取当前页.
        $this->_currentPage = $this->_parseCurrentPage();

        $data = array();

        //当未有分页数据时
        if (!$this->_totalPages) {
            return $data;
        }

        //当分页隐藏功能开启时
        if (($this->_hiddenStatus === true) && ($this->_totalItems <= $this->_perPageItems)) {
            return $data;
        }

        $data['num_items'] = $this->_totalItems;
        $data['per_page']  = $this->_perPageItems;
        $data['num_pages'] = $this->_totalPages;
        $data['page']      = $this->_currentPage;

        //分析上一页
        $data['is_previous'] = 0;
        if ($this->_currentPage != 1 && $this->_totalPages > 1) {
            $data['first_page']    = array('page_number' => $this->firstPage, 'page_url' => $this->_baseUrl . 1);
            $data['previous_page'] = array('page_number' => $this->prePage, 'page_url' => $this->_baseUrl . ($this->_currentPage - 1));
            $data['is_previous']   = 1;
        }

        //分析下一页
        $data['is_next'] = 0;
        if ($this->_currentPage != $this->_totalPages && $this->_totalPages > 1) {
            $data['next_page'] = array('page_number' => $this->nextPage, 'page_url' => $this->_baseUrl . ($this->_currentPage + 1));
            $data['last_page'] = array('page_number' => $this->lastPage, 'page_url' => $this->_baseUrl . $this->_totalPages);
            $data['is_next']   = 1;
        }

        //分析分页列表
        if ($this->_totalPages > $this->_perCircleLimit) {
            if ($this->_currentPage + $this->_perCircleLimit >= $this->_totalPages + $this->_circleCenterCount) {
                $listStart   = $this->_totalPages - $this->_perCircleLimit + 1;
                $listEnd     = $this->_totalPages;
            } else {
                $listStart   = ($this->_currentPage > $this->_circleCenterCount) ? $this->_currentPage - $this->_circleCenterCount + 1 : 1;
                $listEnd     = ($this->_currentPage > $this->_circleCenterCount) ? $this->_currentPage + $this->_perCircleLimit - $this->_circleCenterCount : $this->_perCircleLimit;
            }
        } else {
            $listStart       = 1;
            $listEnd         = $this->_totalPages;
        }

        for($i = $listStart; $i <= $listEnd; $i ++) {
            $data['page_list'][$i] = array('page_number' => $i, 'is_current' => 0, 'page_url' => $this->_baseUrl . $i);
            //分析当前页
            if ($i == $this->_currentPage) {
                $data['page_list'][$i]['is_current'] = 1;
            }
        }

        return $data;
    }
}