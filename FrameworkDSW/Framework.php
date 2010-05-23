<?php
/**
 * Framework
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 1
 */
require_once 'FrameworkDSW/Boot.php'; //must be the first line.
require_once 'FrameworkDSW/System.php';

/**
 * Serialization exception.
 * @author  许子健
 */
class ESerializationException extends ESysException {}
/**
 *
 * @author  许子健
 */
class ESerializeResource extends ESerializationException {}
/**
 *
 * @author  许子健
 */
class EBadSerializedData extends ESerializationException {}
/**
 *
 * @author  许子健
 */
class EIllegalClass extends ESerializationException {}

/**
 * Framework class
 *
 * The Framework implements some static methods for framework objects to use.
 * @author  许子健
 */
class Framework extends TObject {
    /**
     * @internal   The array key name for the variable to be serialized.
     * @var        integer
     */
    const CVar = 0;
    /**
     * @internal   The array key name for the StaticTable, for keeping all class
     * static variables.
     * @var        integer
     */
    const CStaticTable = 1;
    /**
     * @internal   The array key name for class information, which keeping class
     * names and their file paths.
     * @var        integer
     */
    const CClassInfo = 0;
    /**
     * @internal   The array key name for Content, and its value represents the
     * variable and the corresponding class static variables in the
     * form of string after being serializing.
     * @var        integer
     */
    const CContent = 1;
    /**
     * @internal   The delimiter used to separate the class name and its field.
     * It should be a string of only exactly one ASCII character.
     * @var        string
     */
    const CClassDelimiter = '.';
    
    /**
     * @var    array
     */
    private static $FStaticTable = array ();
    /**
     * @var    array
     */
    private static $FClassInfo = array ();

    /**
     * Serialize a variable or a class into a string.
     *
     * The variable can be in any type of a PHP variable except the resource.
     * If the variable is a resource or an array containing resource variables,
     * an {@link ESerializeResource} exception will be thrown.<br>
     * Many other variables such as strings, objects, arrays, etc. may be
     * corresponded with the variable given, they will be also serialized. For
     * any object of them extended from TObject, including the requested
     * <var>$Var</var>, its {@link TObject::Sleep() Sleep()} method will be
     * called, and you can do your own custom code before the object is
     * serialized, like dealing with resource variables of the object for these
     * resource variables will not be processed by default.<br>
     * All static variables of the corresponding classes will be serialized,
     * including objects and arrays, only when these classes are extended from
     * {@link TObject}. Before they are serialized, their static method
     * {@link TObject::ClassSleep() ClassSleep()} will be called, where you can
     * do custom code for dealing with non-{@link TObject} extended classes
     * variables.<br>
     * It is no need to record included source files, since these information
     * will be recorded in this method.
     * @param  mixed   $Var        The variable or the name of the class to be
     * serialized.
     * @return string              The result of the serialization.
     */
    public static function Serialize($Var) {
        $mCallback = function (&$mDummy, &$mElement) {
            if ($mElement instanceof TObject) {
                $mElement->Sleep();
            }
        };
        
        self::$FStaticTable = array ();
        self::$FClassInfo = array ();
        
        if (is_resource($Var)) {
            throw new ESerializeResource();
        }
        
        if ($Var instanceof TObject) {
            $Var->Sleep();
        }
        elseif (is_array($Var)) {
            array_walk_recursive($Var, $mCallback);
        }
        
        Boot::WriteStaticTable(self::$FStaticTable, self::$FClassInfo, self::CClassDelimiter);
        
        //These code below illustrates the structure of a serialization string.
        //The Static Table is an array of this structure:
        //    array(
        //        [CLASS_NAME_1.CClassDelimiter.FIELD_NAME_1 => FIELD_VALUE_1,
        //        [CLASS_NAME_2.CClassDelimiter.FIELD_NAME_2 => FIELD_VALUE_2,
        //        [...,
        //        [CLASS_NAME_n.CClassDelimiter.FIELD_NAME_m => FIELD_VALUE_p]]]]
        //    );
        //The Class Info Table is an array of this structure:
        //    array(
        //        [CLASS_NAME_1 => FILE_PATH_OF_CLASS_1,
        //        [CLASS_NAME_2 => FILE_PATH_OF_CLASS_2,
        //        [...,
        //        [CLASS_NAME_n => FILE_PATH_OF_CLASS_n]]]]
        //    );
        $mResult = serialize(array (self::CVar => $Var, self::CStaticTable => self::$FStaticTable));
        $mResult = serialize(array (array (self::CClassInfo => self::$FClassInfo, self::CContent => $mResult)));
        
        self::$FStaticTable = array ();
        self::$FClassInfo = array ();
        
        return $mResult;
    }

    /**
     * Unserialize a string into a variable or a class.
     *
     * This function will unserilize a variable or a class with its
     * corresponding variables and classes together according the given string
     * produced by {@link Framework::Serialize()} before.<br>
     * All the classes will be loaded automatically, and after that, their
     * {@link TObject::ClasWakeUp()} will be invoked, if the class is a subclass
     * of {@link TObject} or it is just a {@link TObject}.<br>
     * Then the function will revert class static fields to what they are before
     * the serialization. After that, it will unserialize the variable given as
     * a parameter in {@link Framework::Serialize()}, and all the
     * {@link TObject} extended objects will got their
     * {@link TObject::WakeUp() WakeUp()} called, to do the custom code, like
     * for reverting the resource variables.<br>
     * When <var>$SerializedVar</var> are checked as corrupted, such as a wrong
     * class name, the class cannot be found, a class static field are not
     * existed, an {@link EBadSerializedData} exception will be thrown.
     * @param  string  $SerializedData The string of the serialization.
     * @return mixed                   The unserialized variable.
     */
    public static function Unserialize($SerializedVar) {
        $mMeta = unserialize($SerializedVar);
        if (((!is_array($mMeta[0][self::CClassInfo])) && (isset($mMeta[0][self::CClassInfo]))) || (!is_string($mMeta[0][self::CContent]))) {
            throw new EBadSerializedData();
        }
        
        self::$FClassInfo = $mMeta[0][self::CClassInfo];
        if (isset(self::$FClassInfo)) {
            foreach (self::$FClassInfo as $mIndex => $mVal) {
                require_once realpath($mVal);
                $mIndex::ClassWakeUp();
            }
        }
        
        $mMeta = unserialize($mMeta[0][self::CContent]);
        if ((!isset($mMeta[self::CVar])) || ((!is_array($mMeta[self::CStaticTable])) && (isset($mMeta[self::CStaticTable])))) {
            throw new EBadSerializedData();
        }
        
        self::$FStaticTable = $mMeta[self::CStaticTable];
        if (isset(self::$FStaticTable)) {
            foreach (self::$FStaticTable as $mIndex => &$mVal) {
                $mOffset = strpos($mIndex, self::CClassDelimiter);
                $mClass = substr($mIndex, 0, $mOffset);
                $mField = substr($mIndex, ++$mOffset);
                
                $mReflection = new ReflectionProperty($mClass, $mField);
                $mReflection->setAccessible(true);
                $mReflection->setValue(null, $mVal);
            }
            self::$FStaticTable = null;
        }
        
        return $mMeta[self::CVar];
    }

    /**
     * Act like FreeAndNil() in VCL.
     * @param	TObject	$Object
     */
    public static final function Free(&$Object) {
        unset($Object);
        gc_collect_cycles();
    }
}

//TODO: use SPL containers to store FClassInfo field.
//TODO: use get_included_files to collect included files.