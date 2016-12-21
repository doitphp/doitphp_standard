<?php
/**
 * 无限分类
 *
 * @author tommy <tommy@doitphp.com>
 * @copyright Copyright (c) 2010 Tommy Software Studio
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Tree.php 3.0 2016-12-22 19:01:01Z tommy $
 * @package library
 * @since 1.0
 */

if (!defined('IN_DOIT')) {
    exit();
}

class Tree {

    /**
     * 分类的父ID的键名(key)
     *
     * @var integer
     */
    private $_parentId = 'pid';

    /**
     * 分类的ID(key)
     *
     * @var integer
     */
    private $_id = 'id';

    /**
     * 分类名
     *
     * @var string
     */
    private $_name = 'name';

    /**
     * 子分类名
     *
     * @var string
     */
    private $_child = 'child';

    /**
     * 设置分类树数组的Key,即节点数组的字段名(初始化配置)
     *
     * @access public
     *
     * @param string $nodeId 分类树数组key的节点Id
     * @param string $nodeName 分类树数组key的节点名称
     * @param string $nodeParentId 分类树数组key的分节点Id
     * @param string $nodeChildName 分类树数组key的子节点Id
     *
     * @return $this
     */
    public function setKeyName($nodeId = 'id', $nodeName = 'name', $nodeParentId = 'pid', $nodeChildName = 'child') {

        $this->_id       = (!$nodeId) ? $this->_id : $nodeId;
        $this->_name     = (!$nodeName) ? $this->_name : $nodeName;
        $this->_parentId = (!$nodeParentId) ? $this->_parentId : $nodeParentId;
        $this->_child    = (!$nodeChildName) ? $this->_child : $nodeChildName;

        return $this;
    }


    /**
     * 获取无限分类树
     *
     * @access public
     *
     * @param array $data 待处理的数组
     * @param integer $parentId 父ID
     *
     * @return array
     */
    public function getTree($data, $parentId = 0) {

        //parse params
        if (!$data || !is_array($data)) {
            return array();
        }

        //get child tree array
        $childArray = $this->_getChildren($data, $parentId);
        //当子分类无元素时,结果递归
        if(!$childArray) {
            return array();
        }

        $treeArray = array();
        foreach ($childArray as $lines) {
            $treeArray[$lines[$this->_id]] = array(
            $this->_id    => $lines[$this->_id],
            $this->_name  => $lines[$this->_name],
            $this->_child => $this->getTree($data, $lines[$this->_id]),
            );
        }

        return $treeArray;
    }

    /**
     * 无限级分类树-获取子类
     *
     * @access public
     *
     * @param array $data 树的数组
     * @param integer $id 父类ID
     *
     * @return array
     */
    protected function _getChildren($data, $id) {

        $childrenArray = array();
        foreach ($data as $value) {
            if ($value[$this->_parentId] == $id) {
                $childrenArray[] = $value;
            }
        }

        return $childrenArray;
    }
}