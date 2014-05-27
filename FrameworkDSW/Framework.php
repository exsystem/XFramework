<?php
/**
 * \FrameworkDSW\Framework
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 1
 */
namespace FrameworkDSW\Framework;

require_once 'FrameworkDSW/System.php';
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\System\EError;
use FrameworkDSW\System\ENoSuchType;
use FrameworkDSW\System\ERuntimeException;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TDelegate;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TType;

/**
 * Serialization exception.
 * @author  许子健
 */
class ESerializationException extends EError {
}

/**
 *
 * @author  许子健
 */
class ESerializeResource extends ESerializationException {
}

/**
 *
 * @author  许子健
 */
class EBadSerializedData extends ESerializationException {
}

/**
 *
 * @author  许子健
 */
class EIllegalClass extends ESerializationException {
}

/**
 * \FrameworkDSW\Framework\Framework
 *
 * The Framework implements some static methods for framework objects to use.
 * @author  许子健
 */
class Framework extends TObject {
    /**
     * @var string
     */
    const Boolean = 'boolean';
    /**
     * @var string
     */
    const Integer = 'integer';
    /**
     * @var string
     */
    const Float = 'float';
    /**
     * @var string
     */
    const String = 'string';
    /**
     * @var string
     */
    const Variant = 'mixed';

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
    private static $FStaticTable = [];
    /**
     * @var    array
     */
    private static $FClassInfo = [];

    /**
     * Data Structure:
     * {<TypeNameWithNamespace, UnitPath>} + <*, {UnitPath}>
     * @var array
     */
    private static $FExternalUnits = [];
    /**
     *
     * @var integer
     */
    private static $FStartAt = null;
    /**
     *
     * @var array
     */
    private static $FDeclaredClasses = null;
    /**
     * @var array
     */
    private static $FDelegates = [];
    /**
     * @var boolean
     */
    private static $FDebug = false;
    /**
     * @var array
     */
    private static $FTypeInfo = [];
    /**
     * @var array
     */
    private static $FTypeObjs = [];

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
     * @param  mixed $Var The variable or the name of the class to be
     * serialized.
     * @throws ESerializeResource
     * @return string              The result of the serialization.
     */
    public static function Serialize($Var) {
        /** @noinspection PhpUnusedParameterInspection */
        $mCallback = function (&$mDummy, &$mElement) {
            if ($mElement instanceof TObject) {
                $mElement->Sleep();
            }
        };

        self::$FStaticTable = [];
        self::$FClassInfo   = [];

        if (is_resource($Var)) {
            throw new ESerializeResource('Resource can not be serialized.');
        }

        if ($Var instanceof TObject) {
            $Var->Sleep();
        }
        elseif (is_array($Var)) {
            array_walk_recursive($Var, $mCallback);
        }

        self::WriteStaticTable(self::$FStaticTable, self::$FClassInfo,
            self::CClassDelimiter);

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
        $mResult = serialize(
            [self::CVar         => $Var,
             self::CStaticTable => self::$FStaticTable]);
        $mResult = serialize(
            [
                [self::CClassInfo => self::$FClassInfo,
                 self::CContent   => $mResult]]);

        self::$FStaticTable = [];
        self::$FClassInfo   = [];

        return $mResult;
    }

    /**
     *
     * @param array $StaticTable
     * @param array $ClassInfo
     * @param string $ClassDelimiter
     * @throws \Exception
     */
    static public function WriteStaticTable(&$StaticTable, &$ClassInfo, $ClassDelimiter) {
        if (!isset(self::$FDeclaredClasses)) {
            throw new \Exception('FATAL ERROR!');
        }

        self::$FDeclaredClasses = array_slice(get_declared_classes(), self::$FStartAt);
        foreach (self::$FDeclaredClasses as $mClass) {
            if ( /*$mClass[0] == 'T' &&*/
            is_subclass_of($mClass, TObject::class)
            ) {
                $mReflection        = new \ReflectionClass($mClass);
                $ClassInfo[$mClass] = $mReflection->getFileName();

                foreach ($mClass::ClassSleep() as $mFieldName) {
                    $mProperty = $mReflection->getProperty($mFieldName);
                    $mProperty->setAccessible(true);
                    $StaticTable[$mClass . $ClassDelimiter . $mFieldName] = $mProperty->getValue();
                }

                //see the PHP BUG #49074 (http://bugs.php.net/bug.php?id=49074).
                //pay attention at the comment of [1 Aug 9:10pm UTC] felipe@php.net.
            }
        }
    }

    /**
     * Unserialize a string into a variable or a class.
     *
     * This function will unserialize a variable or a class with its
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
     * @param string $SerializedVar The string of the serialization.
     * @throws EBadSerializedData
     * @return mixed                   The unserialized variable.
     */
    public static function Unserialize($SerializedVar) {
        $mMeta = unserialize($SerializedVar);
        if (((!is_array($mMeta[0][self::CClassInfo]))
                && (isset($mMeta[0][self::CClassInfo])))
            || (!is_string($mMeta[0][self::CContent]))
        ) {
            throw new EBadSerializedData('Bad serialized data.');
        }

        self::$FClassInfo = $mMeta[0][self::CClassInfo];
        if (isset(self::$FClassInfo)) {
            foreach (self::$FClassInfo as $mIndex => $mVal) {
                /** @noinspection PhpIncludeInspection */
                require_once realpath($mVal);
                $mIndex::ClassWakeUp();
            }
        }

        $mMeta = unserialize($mMeta[0][self::CContent]);
        if ((!isset($mMeta[self::CVar]))
            || ((!is_array($mMeta[self::CStaticTable]))
                && (isset($mMeta[self::CStaticTable])))
        ) {
            throw new EBadSerializedData('Bad serialized data.');
        }

        self::$FStaticTable = $mMeta[self::CStaticTable];
        if (isset(self::$FStaticTable)) {
            foreach (self::$FStaticTable as $mIndex => &$mVal) {
                $mOffset = strpos($mIndex, self::CClassDelimiter);
                $mClass  = substr($mIndex, 0, $mOffset);
                $mField  = substr($mIndex, ++$mOffset);

                $mReflection = new \ReflectionProperty($mClass, $mField);
                $mReflection->setAccessible(true);
                $mReflection->setValue(null, $mVal);
            }
            self::$FStaticTable = null;
        }

        return $mMeta[self::CVar];
    }

    /**
     * Act like FreeAndNil() in VCL.
     * @param \FrameworkDSW\System\IInterface $Object
     */
    public static final function Free(&$Object) {
        if ($Object !== null) {
            $Object->Destroy();
            $Object = null;
            gc_collect_cycles();
        }
    }

    /**
     *
     * @param string $TypeName
     * @param mixed $UnitPath
     */
    public static final function RegisterExternalUnit($TypeName, $UnitPath) {
        self::$FExternalUnits[(string)$TypeName] = $UnitPath;
    }

    /**
     *
     * @param string $Name
     * @throws \FrameworkDSW\System\ENoSuchType
     */
    public static final function AutoLoader($Name) {
        if (substr($Name, 0, 13) === 'FrameworkDSW\\') {
            $Name = substr($Name, 13);
            if ($Name === false) {
                throw new ENoSuchType(sprintf('Illegal type name for autoloading: %s.', $Name), null, $Name);
            }
            $mPos = strrpos($Name, '\\');
            if ($mPos === false) {
                throw new ENoSuchType(sprintf('Illegal type name for autoloading: %s.', $Name), null, $Name);
            }
            $Name = substr($Name, 0, $mPos);
            if ($Name === false) {
                throw new ENoSuchType(sprintf('Illegal type name for autoloading: %s.', $Name), null, $Name);
            }
            $Name = str_replace('\\', '_', $Name);
            if ($Name === false) {
                throw new ENoSuchType(sprintf('Illegal type name for autoloading: %s.', $Name), null, $Name);
            }
            /** @var string $Name */
            /** @noinspection PhpIncludeInspection */
            require_once "FrameworkDSW/{$Name}.php";

            return;
        }

        if (isset(self::$FExternalUnits[$Name])) {
            /** @noinspection PhpIncludeInspection */
            require_once (string)self::$FExternalUnits[$Name];
        }
        elseif (isset(self::$FExternalUnits['*'])) {
            foreach (self::$FExternalUnits['*'] as $mUnit) {
                /** @noinspection PhpIncludeInspection */
                require_once (string)$mUnit;
            }
        }
        else {
            $mFilename = str_replace('\\', DIRECTORY_SEPARATOR, $Name) . ".php";
            if (file_exists($mFilename)) {
                /** @noinspection PhpIncludeInspection */
                require_once $mFilename;
            }
            else {
                foreach (Framework::$FExternalUnits as $mNamespacePrefix => $mPathPrefix) {
                    $mNamespacePrefixLength = strlen($mNamespacePrefix);
                    if ($mNamespacePrefix[$mNamespacePrefixLength - 1] == '\\') {
                        $mPos = strpos($Name, $mNamespacePrefix);
                        if ($mPos === 0) {
                            $mFilename = str_replace('\\', DIRECTORY_SEPARATOR, substr($Name, $mNamespacePrefixLength));
                            $mFilename = "{$mPathPrefix}{$mFilename}.php";
                            if (file_exists($mFilename)) {
                                /** @noinspection PhpIncludeInspection */
                                require_once $mFilename;
                                return;
                            }
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     *
     */
    static public function PreBoot() {
        if (isset(Framework::$FDeclaredClasses)) {
            return;
        }

        self::$FDeclaredClasses = get_declared_classes();
        self::$FStartAt         = count(self::$FDeclaredClasses);

        ini_set('display_errors', 0);
        set_error_handler([Framework::class, 'HandleError']);
        if (Framework::$FReservedMemorySize > 0) {
            Framework::$FReservedMemory = str_repeat('x', Framework::$FReservedMemorySize);
        }
        register_shutdown_function([Framework::class, 'HandleFatalError']);
        spl_autoload_register('FrameworkDSW\Framework\Framework::AutoLoader', true);
    }

    /**
     *
     * @param mixed $Callback string or closure
     * @param string $Type
     * @throws ENoSuchType
     * @return \FrameworkDSW\System\TDelegate
     */
    public static function Delegate($Callback, $Type) {
        foreach (Framework::$FDelegates as $mDelegate) {
            if ($mDelegate->getDelegate() == $Callback) {
                return $mDelegate;
            }
        }
        $mDelegate               = new TDelegate($Callback, $Type);
        Framework::$FDelegates[] = $mDelegate;
        return $mDelegate;
    }

    /**
     * @var \FrameworkDSW\CoreClasses\IApplication
     */
    private static $FApplication = null;

    /**
     * @param \FrameworkDSW\Reflection\TClass $ApplicationClass <T: ?> T: extends \FrameworkDSW\CoreClasses\IApplication
     * @param \FrameworkDSW\System\IInterface[] $Parameters
     */
    public static function CreateApplication($ApplicationClass, $Parameters) {
        TType::Object($ApplicationClass, [TClass::class => ['T' => null]]);
        TType::Type($Parameters, IInterface::class . '[]');
        Framework::$FApplication = $ApplicationClass->NewInstance($Parameters);
    }

    /**
     * @return \FrameworkDSW\CoreClasses\IApplication
     */
    public static function Application() {
        return Framework::$FApplication;
    }

    /**
     *
     */
    public static function Debug() {
        Framework::$FDebug = true;
    }

    /**
     *
     */
    public static function Release() {
        Framework::$FDebug = false;
    }

    /**
     * @return boolean
     */
    public static function IsDebug() {
        return Framework::$FDebug;
    }

    /**
     * @return boolean
     */
    public static function IsRelease() {
        return !Framework::$FDebug;
    }

    /**
     * @param mixed $Type
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public static function Type($Type) {
        if (is_string($Type)) {
            $mKey   = $Type;
            $mValue = null;
        }
        else {
            $mKey   = array_keys($Type)[0];
            $mValue = $Type[$mKey];
        }
        if (isset(Framework::$FTypeInfo[$mKey])) {
            $mIndex = array_search($mValue, Framework::$FTypeInfo[$mKey], true);
            if ($mIndex === false) {
                TClass::PrepareGeneric(['T' => $Type]);
                $mClass                        = new TClass();
                Framework::$FTypeInfo[$mKey][] = $mValue;
                Framework::$FTypeObjs[$mKey][] = $mClass;
                return $mClass;
            }
            else {
                return Framework::$FTypeObjs[$mKey][$mIndex];
            }
        }
        else {
            TClass::PrepareGeneric(['T' => $Type]);
            $mClass                      = new TClass();
            Framework::$FTypeInfo[$mKey] = [$mValue];
            Framework::$FTypeObjs[$mKey] = [$mClass];
            return $mClass;
        }
    }

    /**
     * @var integer
     */
    private static $FReservedMemorySize = 262144;
    /**
     * @var string
     */
    private static $FReservedMemory = '';

    /**
     * @param integer $Size
     */
    public static function SetReservedMemorySize($Size) {
        TType::Int($Size);
        if ($Size < 0) {
            $Size = 0;
        }
        Framework::$FReservedMemorySize = $Size;
    }

    /**
     * @return integer
     */
    public static function GetReservedMemorySize() {
        return Framework::$FReservedMemorySize;
    }

    /**
     * @param integer $Code
     * @param string $Message
     * @param string $File
     * @param integer $Line
     * @param mixed $Context
     * @throws \FrameworkDSW\System\ERuntimeException
     */
    public static function HandleError($Code, $Message, $File, $Line, $Context) {
        if (error_reporting() & $Code) {
            if (!class_exists('FrameworkDSW\\System\\ERuntimeException', false)) {
                require_once __DIR__ . '/System.php';
            }
            throw new ERuntimeException($Message, null); //TODO classify errors into different types of runtime exceptions.
        }
    }

    /**
     *
     */
    public static function HandleFatalError() {
        Framework::$FReservedMemory = '';
        if (!class_exists('FrameworkDSW\\System\\ERuntimeException', false)) {
            require_once __DIR__ . '/System.php';
        }
        $mError = error_get_last();
        if (isset($mError['type']) && in_array($mError['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING])) {
            $mException = new ERuntimeException($mError['message']);
            PHP_SAPI === 'cli' or error_log($mException);
            if (Framework::Application() === null) {
                echo $mError['message'];
            }
            else {
                Framework::Application()->getExceptionHandler()->HandleException($mException);
            }
            exit(1);
        }

    }
}

Framework::PreBoot();
//TODO: use SPL containers to store FClassInfo field.
//TODO: use get_included_files to collect included files.