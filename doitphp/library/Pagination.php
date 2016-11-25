<?php
/**
 * 分页类
 *
 * @author tommy <tommy@doitphp.com>
 * @copyright Copyright (c) 2010 Tommy Software Studio
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Pagination.php 2.0 2012-12-29 11:40:01Z tommy $
 * @package library
 * @since 1.0
 */

if (!defined('IN_DOIT')) {
    exit();
}

class Pagination {

    /**
     * 连接网址
     *
     * @var string
     */
    protected $_url = null;

    /**
     * 当前页
     *
     * @var integer
     */
    protected $_page = 1;

    /**
     * list总数
     *
     * @var integer
     */
    protected $_total = 0;

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
    protected $_num = 10;

    /**
     * list允许放页码数量,如:1.2.3.4就这4个数字,则$perCircle为4
     *
     * @var integer
     */
    protected $_perCircle = 10;

    /**
     * list中的坐标. 如:7,8,九,10,11这里的九为当前页,在list中排第三位,则$center为3
     *
     * @var integer
     */
    protected $_center = 3;

    /**
     * 分页css名
     *
     * @var string
     */
    protected $_styleFile = null;

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

        return ceil($this->_total / $this->_num);
    }

    /**
     * 获取当前页数
     *
     * @access protected
     * @return integer
     */
    protected function _getPageNum() {

        //当URL中?page=5的page参数大于总页数时
        return ($this->_page > $this->_totalPages) ? $this->_totalPages : $this->_page;
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
            $this->_num = $num;
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
            $this->_total = $totalNum;
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
     * @param string $url 分页跳转的网址
     *
     * @return object
     */
    public function url($url = null) {

        //当网址不存在时
        if ($url) {
            $this->_url = trim($url);
        }

        return $this;
    }

    /**
     * 设置当前的页数
     *
     * @access public
     *
     * @param integer $page 当前的页数
     *
     * @return object
     */
    public function page($page = null) {

        //参数分析
        if($page) {
            $this->_page = $page;
        }

        return $this;
    }

    /**
     * 设置分页列表的重心
     *
     * @access public
     *
     * @param integer $num 分页列表重心(即：页数)
     *
     * @return object
     */
    public function center($num) {

        //参数分析
        if ($num && is_int($num)) {
            $this->_center = $num;
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
    public function circle($num) {

        //参数分析
        if ($num && is_int($num)) {
            $this->_perCircle = $num;
        }

        return $this;
    }

    /**
     * 输出分页数组
     *
     * @access public
     * @return array
     */
    public function render() {

        return $this->_processData();
    }

    /**
     * 处理分页数组
     *
     * @access protected
     * @return array
     */
    protected function _processData() {

        //支持长的url.
        $this->_url        = trim(str_replace(array("\n","\r"), '', $this->_url));

        //获取总页数.
        $this->_totalPages = $this->_getTotalPage();

        //获取当前页.
        $this->_page       = $this->_getPageNum();

        $data = array();

        //当未有分页数据时
        if (!$this->_totalPages) {
            return $data;
        }

        //当分页隐藏功能开启时
        if (($this->_hiddenStatus === true) && ($this->_total <= $this->_num)) {
            return $data;
        }

        $data['listTotalNum']   = $this->_total;
        $data['perPageListNum'] = $this->_num;
        $data['pageTotalNum']   = $this->_totalPages;
        $data['page']           = $this->_page;

        //分析上一页
        if ($this->_page != 1 && $this->_totalPages > 1) {
            $data['firstPage'] = array('text'=>$this->firstPage, 'url'=>$this->_url . 1);
            $data['prePage']   = array('text'=>$this->prePage, 'url'=>$this->_url . ($this->_page - 1));
        }

        //分析下一页
        if ($this->_page != $this->_totalPages && $this->_totalPages > 1) {
            $data['nextPage'] = array('text'=>$this->nextPage, 'url'=>$this->_url . ($this->_page + 1));
            $data['lastPage'] = array('text'=>$this->lastPage, 'url'=>$this->_url . $this->_totalPages);
        }

        //分析分页列表
        if ($this->_totalPages > $this->_perCircle) {
            if ($this->_page + $this->_perCircle >= $this->_totalPages + $this->_center) {
                $listStart   = $this->_totalPages - $this->_perCircle + 1;
                $listEnd     = $this->_totalPages;
            } else {
                $listStart   = ($this->_page>$this->_center) ? $this->_page - $this->_center + 1 : 1;
                $listEnd     = ($this->_page>$this->_center) ? $this->_page + $this->_perCircle-$this->_center : $this->_perCircle;
            }
        } else {
            $listStart       = 1;
            $listEnd         = $this->_totalPages;
        }

        for($i = $listStart; $i <= $listEnd; $i ++) {
            $data['pageList'][$i] = array('text'=>$i, 'active'=>0, 'url'=> $this->_url . $i);
            //分析当前页
            if ($i == $this->_page) {
                $data['pageList'][$i]['active'] = 1;
            }
        }

        return $data;
    }
}