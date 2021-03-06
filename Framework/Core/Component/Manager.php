<?php
namespace X\Core\Component;
/**
 * 资源管理器基础类
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class Manager {
    /** 
     * 管理器实例列表
     * @var Manager[]
     * */
    protected static $managers = null;
    
    /** 
     * 在全局配置文件中， 每个管理器使用一个顶级的元素来存储配置信息。
     * 其键名在这里配置。
     * @example 
     * <pre>
     * array(
     *  'manager-001' => array(), # 管理器001的配置
     *  'manager-002' => array(), # 管理器002的配置
     * );
     * </pre>
     * @var string
     * */
    protected $configurationKey = null;
    
    /**
     * 管理器配置实例
     * @var ConfigurationArray 
     * */
    private $configuration = null;
    
    /**
     * 获取Management的实例。
     * @return \X\Core\Component\Manager
     */
    public static function getManager() {
        $manager = get_called_class();
        if ( !isset(self::$managers[$manager]) ) {
            self::$managers[$manager] = new $manager();
        }
        return self::$managers[$manager];
    }
    
    /**
     * 将构造函数不公开， 以防止框架内存在第二个管理实例。
     * @return void
     */
    protected function __construct() {
        $this->init();
    }
    
    /**
     * 初始化该管理器
     * @return void
     */
    protected function init() {}
    
    /**
     * 当前管理器的状态
     * @var integer
     */
    private $status = self::STATUS_STOPED;
    
    /**
     * 管理器状态 ：已停止
     * @var integer
     */
    const STATUS_STOPED = 0;
    
    /**
     * 管理器状态 ：已启动
     * @var integer
     */
    const STATUS_RUNNING = 1;
    
    /**
     * 获取当前管理器的状态
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * 启动该管理器
     * @return void
     */
    public function start() {
        $this->status = self::STATUS_RUNNING;
    }
    
    /**
     * 结束该管理器
     * @return void
     */
    public function stop() {
        $this->status = self::STATUS_STOPED;
    }
    
    /**
     * 销毁当前管理器
     * @return void
     */
    public function destroy() {
        self::$managers[get_class($this)] = null;
        unset(self::$managers[get_class($this)]);
    }
    
    /**
     * 获取当前管理器配置
     * @return ConfigurationArray
     */
    public function getConfiguration() {
        if ( null === $this->configuration ) {
            $config = \X\Core\X::system()->getConfiguration()->get($this->configurationKey, array());
            $this->configuration = new ConfigurationArray();
            $this->configuration->setValues($config);
        }
        return $this->configuration;
    }
}