<?php
/**
 * Boot
 * @author	ExSystem
 * @version	$Id: Boot.php 10 2010-05-19 03:47:02Z exsystemchina@gmail.com $
 * @since	separate file since reversion 23
 */

/**
 * Boot
 * @author  ExSystem
 */
final class Boot {
    /**
     *
     * @var    integer
     */
    private static $FStartAt = null;
    /**
     *
     * @var    array
     */
    private static $FDeclaredClasses = null;

    /**
     *
     */
    static public function PreBoot() {
        if (isset(self::$FDeclaredClasses)) {
            return;
        }
        
        self::$FDeclaredClasses = get_declared_classes();
        self::$FStartAt = count(self::$FDeclaredClasses);
    }

    /**
     *
     * @param  array   $StaticTable
     * @param  array   $ClassInfo
     * @param  string  $ClassDelimiter
     */
    static public function WriteStaticTable(&$StaticTable, &$ClassInfo, $ClassDelimiter) {
        if (!isset(self::$FDeclaredClasses)) {
            throw new Exception('FATAL ERROR!');
        }
        
        self::$FDeclaredClasses = array_slice(get_declared_classes(), self::$FStartAt);
        foreach (self::$FDeclaredClasses as $mClass) {
            if (/*$mClass[0] == 'T' &&*/ is_subclass_of($mClass, 'System\TObject')) {
                $mReflaction = new ReflectionClass($mClass);
                $ClassInfo[$mClass] = $mReflaction->getFileName();
                
                foreach ($mClass::ClassSleep() as $mFieldName) {
                    $mProperty = $mReflaction->getProperty($mFieldName);
                    $mProperty->setAccessible(true);
                    $StaticTable[$mClass . $ClassDelimiter . $mFieldName] = $mProperty->getValue();
                }
                
            //see the PHP BUG #49074 (http://bugs.php.net/bug.php?id=49074).
            //pay attention at the comment of [1 Aug 9:10pm UTC] felipe@php.net.
            }
        }
    }
}

Boot::PreBoot(); //pre-booting.