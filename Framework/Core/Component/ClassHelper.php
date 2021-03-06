<?php
namespace X\Core\Component;
/**
 * 操作 / 获取类信息的帮助类
 * @author Michael Luthor <michaelluthor@163.com>
 */
class ClassHelper {
    /**
     * 根据类和相对路径获取绝对路径。
     * @param string|object $class 类名或对象实例
     * @param string $path 相对路径
     * @return string
     */
    public static function getPathRelatedClass( $class, $path ){
        $classInfo = new \ReflectionClass(is_string($class) ? $class : get_class($class));
        $classPath = dirname($classInfo->getFileName());
        $path = (null===$path) ? $classPath : $classPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
        return $path;
    }
}