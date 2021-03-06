<?php
namespace X\Core\Environment\Util;
/**
 * 运行环境基础类
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class Handler {
    /**
     * 运行参数
     * @var array
     */
    protected $parameters = array();
    
    /**
     * 初始化当前环境
     * @return void
     */
    public function __construct() {
        $this->parameters = $this->initParameters();
    }
    
    /**
     * 初始化运行参数
     * @return array
     */
    abstract protected function initParameters();
    
    /**
     * 获取当前运行环境名称
     * @return string
     */
    abstract public function getName();
    
    /**
     * 获取运行环境参数列表
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }
    
    /**
     * 初始化当前环境
     * @return void
     */
    public function init() {}
}