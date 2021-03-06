<?php
namespace X\Core\Service;
use X\Core\X;
use X\Core\Component\Exception;
use X\Core\Component\Manager as UtilManager;
/**
 * 服务管理器
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Manager extends UtilManager {
    /** 
     * 服务配置键名
     * @var string 
     * */
    protected $configurationKey = 'services';
    
    /**
     * 所有已经加载的服务实例
     * @var array
     */
    private $services = array(
        # 'name' => array(
        #      'isLoaded'  => true,
        #      'service'   => $service)
    );
    
    /**
     * 启动服务管理器
     * @see \X\Core\Component\Manager::start()
     * @return void
     */
    public function start(){
        parent::start();
        foreach ( $this->getConfiguration() as $name => $configuration ) {
            if ( $configuration['enable'] ) {
                $this->load($name);
                if ( isset($configuration['delay']) && false === $configuration['delay'] ) {
                    $this->get($name)->start();
                }
            } else {
                $this->services[$name]['isLoaded']  = false;
                $this->services[$name]['service']   = null;
            }
        }
    }
    
    /**
     * 停止服务管理器
     * @see \X\Core\Component\Manager::stop()
     * @return void
     */
    public function stop() {
        foreach ( $this->services as $name => $service ) {
            if( null === $service['service'] ) {
                continue;
            }
            if ( XService::STATUS_RUNNING === $service['service']->getStatus() ) {
                $service['service']->stop();
                $service['service']->destroy();
            }
        }
        $this->services = array();
        parent::stop();
    }
    
    /**
     * 加载服务
     * @param string $name 服务名称
     * @throws Exception
     */
    public function load($name) {
        if ( !$this->has($name) ) {
            throw new Exception("Service '$name' does not exists.");
        }
        
        $configuration = $this->getConfiguration()->get($name);
        $serviceClass = $configuration['class'];
        if ( !class_exists($serviceClass, true) ) {
            throw new Exception("Service class '$name' does not exists.");
        }
        
        if ( !( is_subclass_of($serviceClass, '\\X\\Core\\Service\\XService') ) ) {
            throw new Exception("Service class '$serviceClass' should be extends from '\\X\\Core\\Service\\XService'.");
        }
        
        $service = new $serviceClass($configuration['params']);
        $this->services[$name]['isLoaded']  = true;
        $this->services[$name]['service']   = $service;
    }
    
    /**
     * 判断服务是否已经加载
     * @param string $name 服务名称
     * @throws Exception
     * @return boolean
     */
    public function isLoaded( $name ) {
        if ( !$this->has($name) ) {
            throw new Exception("Service '{$name}' does not exists.");
        }
        return isset($this->services[$name]) ? $this->services[$name]['isLoaded'] : false;
    }
    
    /**
     * 卸载指定服务
     * @param string $name 服务名称
     * @throws Exception
     */
    public function unload( $name ) {
        if ( !$this->has($name) ) {
            throw new Exception("Service '$name' does not exists.");
        }
        
        if ( !$this->isLoaded($name) ) {
            return;
        }
        
        $this->services[$name]['service'] = null;
        $this->services[$name]['isLoaded'] = false;
    }
    
    /**
     * 根据名称获取服务实例
     * @param string $name 服务名称
     * @throws Exception
     * @return \X\Core\Service\XService
     */
    public function get( $name ) {
        if ( !$this->has($name) ) {
            throw new Exception("Service '$name' does not exists.");
        }
        
        if ( !$this->isLoaded($name) ) {
            $this->load($name, $this->configuration[$name]);
        }
        
        /* @var $service \X\Core\Service\XService */
        $service = $this->services[$name]['service'];
        if ( $this->isEnabled($name) && $this->isLazyLoadEnabled($name) && XService::STATUS_STOPPED===$service->getStatus() ) {
            $service->start();
        }
        return $service;
    }
    
    /**
     * 判断服务是否启用
     * @param string $name 服务名称
     * @return boolean|mixed
     */
    public function isEnabled( $name ) {
        $config = $this->getConfiguration()->get($name);
        return isset($config['enable']) ? $config['enable'] : false;
    }
    
    /**
     * 判断服务是否延迟加载
     * @param string $name 服务名称
     * @return boolean|mixed
     */
    public function isLazyLoadEnabled($name) {
        $config = $this->getConfiguration()->get($name);
        return isset($config['delay']) ? $config['delay'] : false;
    }
    
    /**
     * 判断服务是否存在
     * @param string $name 服务名称
     * @return boolean
     */
    public function has( $name ) {
        return $this->getConfiguration()->has($name);
    }
    
    /**
     * 获取所有服务名称列表
     * @return array
     */
    public function getList() {
        return array_keys($this->getConfiguration()->toArray());
    }
}