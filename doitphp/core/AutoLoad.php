<?php
/**
 * DoitPHP自动加载引导类
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) 2015 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: AutoLoad.php 2.0 2012-12-01 11:52:00Z tommy <tommy@doitphp.com> $
 * @package core
 * @since 1.0
 */

if (!defined('IN_DOIT')) {
    exit();
}

abstract class AutoLoad {

    /**
     * DoitPHP核心类引导数组
     *
     * 用于自动加载文件运行时,引导路径
     * @var array
     */
    private static $_coreClassArray = array(
    'Request'           => 'core/Request.php',
    'Response'          => 'core/Response.php',
    'Model'             => 'core/Model.php',
    'DbCommand'         => 'core/DbCommand.php',
    'DbPdo'             => 'core/DbPdo.php',
    'Log'               => 'core/Log.php',
    'DoitException'     => 'core/DoitException.php',
    'Widget'            => 'core/Widget.php',
    'View'              => 'core/View.php',
    'Template'          => 'core/Template.php',
    'WidgetTemplate'    => 'core/WidgetTemplate.php',
    'Extension'         => 'core/Extension.php',
    'Pagination'        => 'library/Pagination.php',
    'File'              => 'library/File.php',
    'Html'              => 'library/Html.php',
    'Cookie'            => 'library/Cookie.php',
    'Session'           => 'library/Session.php',
    'Image'             => 'library/Image.php',
    'Captcha'           => 'library/Captcha.php',
    'Curl'              => 'library/Curl.php',
    'Client'            => 'library/Client.php',
    'Validation'        => 'library/Validation.php',
    'FileDownload'      => 'library/FileDownload.php',
    'FileUpload'        => 'library/FileUpload.php',
    'Excel'             => 'library/Excel.php',
    'Csv'               => 'library/Csv.php',
    'Security'          => 'library/Security.php',
    'Text'              => 'library/Text.php',
    'Encrypt'           => 'library/Encrypt.php',
    'Tree'              => 'library/Tree.php',
    'MongoDb'           => 'library/MongoDb.php',
    'Language'          => 'library/Language.php',
    'Cache_Memcached'   => 'library/cache/Memcached.php',
    'Cache_Memcache'    => 'library/cache/Memcache.php',
    'Cache_Redis'       => 'library/cache/Redis.php',
    'Cache_File'        => 'library/cache/File.php',
    'Pinyin'            => 'library/Pinyin.php',
    'Calendar'          => 'library/Calendar.php',
    'Benchmark'         => 'library/Benchmark.php',
    'HttpResponse'      => 'library/HttpResponse.php',
    'Ftp'               => 'library/Ftp.php',
    );

    /**
     * 项目文件的自动加载
     *
     * doitPHP系统自动加载核心类库文件(core目录内的文件)及运行所需的controller文件、model文件、widget文件等
     *
     * 注:并非程序初始化时将所有的controller,model等文件都统统加载完,再执行其它。
     * 理解本函数前一定要先理解AutoLoad的作用。
     * 当程序运行时发现所需的文件没有找到时,AutoLoad才会被激发,按照loadClass()的程序设计来完成对该文件的加载
     *
     * @access public
     *
     * @param string $className 所需要加载的类的名称,注:不含后缀名
     *
     * @return void
     */
    public static function loadClass($className) {

        //doitPHP核心类文件的加载分析
        if (isset(self::$_coreClassArray[$className])) {
            //当$className在核心类引导数组中存在时, 加载核心类文件
            Doit::loadFile(DOIT_ROOT . DS . self::$_coreClassArray[$className]);
        } elseif (substr($className, -10) == 'Controller') {
            //controller文件自动载分析
            self::_loadTagFile($className, 'Controller');
        } elseif (substr($className, -5) == 'Model') {
            //modlel文件自动加载分析
            self::_loadTagFile($className, 'Model');
        } elseif (substr($className, -6) == 'Widget') {
            //加载所要运行的widget文件
            self::_loadTagFile($className, 'Widget');
        } else {
            //加载所使用命名空间的类文件
            if (strpos($className, '\\') !== false) {
                $filePath = BASE_PATH . DS . str_replace('\\', DS, $className) . '.php';
                if (!is_file($filePath)) {
                    //当使用命名空间的文件不存在时，提示错误信息
                    Response::halt('The File: ' . $filePath .' is not found !');
                }
                Doit::loadFile($filePath);
                return true;
            }
            //分析加载扩展类文件目录(library)的文件
            if (!self::_loadTagFile($className, 'Library')) {
                //根据配置文件improt的引导设置，自动加载文件
                if (!self::_loadImportConfigFile($className)) {
                    //最后，当运行上述自动加载规则，均没有加载所需要的文件时，提示错误信息
                    Response::halt('The Class: ' . $className .' is not found !');
                }
            }
        }

        return true;
    }

    /**
     * 自动加载标签文件
     *
     * @access private
     *
     * @param string $className 所需要加载的类的名称,注:不含后缀名
     * @param string $tagName 类文件的行为标签。如Controller, Widget, Model
     *
     * @return void
     */
    private static function _loadTagFile($className, $tagName) {

        //分析标签文件目录路径
        switch ($tagName) {

            case 'Controller':
                $dirName   = 'controllers';
                //当controller文件存放于子目录时
                if (strpos($className, '_') !== false) {
                    $childDirArray = explode('_', strtolower($className));
                    $className     = ucfirst(array_pop($childDirArray));
                    $className     = implode(DS, $childDirArray) . DS . $className;
                }
                break;

            case 'Model':
                $dirName   = 'models';
                break;

            case 'Widget':
                $dirName   = 'widgets';
                break;

            case 'Library':
                $dirName   = 'library';
                break;

            default:
                $dirName = 'library';
        }

        //分析标签文件的实际路径
        $tagFilePath = BASE_PATH . DS . $dirName . DS . str_replace('_', DS, $className) . '.php';

        //当标签文件存在时
        if (!is_file($tagFilePath)) {
            if ($tagName == 'Library') {
                return false;
            }
            //当所要加载的标签文件不存在时,显示错误提示信息
            Response::halt('The File: ' . $tagFilePath . ' is not found!');
        }

        //加载标签文件
        Doit::loadFile($tagFilePath);
        return true;
    }

    /**
     * 加载自定义配置文件所引导的文件
     *
     * @access private
     *
     * @param string $className 所需要加载的类的名称,注:不含后缀名
     *
     * @return void
     */
    private static function _loadImportConfigFile($className) {

        //定义自动加载状态。(true:已加载/false:未加载)
        $atuoLoadStatus = false;

        //分析配置文件import引导信息
        $importRules = Configure::get('import');

        //当配置文件引导信息合法时
        if ($importRules && is_array($importRules)) {
            foreach ($importRules as $rules) {
                if (!$rules) {
                    continue;
                }

                //当配置文件引导信息中含有*'时，将设置的规则中的*替换为所要加载的文件类名
                if (strpos($rules, '*') !== false) {
                    $filePath = str_replace('*', $className, $rules);
                } else {
                    $filePath = $rules . DS . str_replace('_', DS, $className) . '.php';
                }

                //当自定义自动加载的文件存在时
                if (is_file($filePath)) {
                    //加载文件
                    Doit::loadFile($filePath);
                    $atuoLoadStatus = true;
                    break;
                }
            }
        }

        return $atuoLoadStatus;
    }
}