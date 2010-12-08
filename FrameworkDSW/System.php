<?php
/**
 * System
 * @author  ExSystem
 * @version $Id$
 * @since   separate file since reversion 1
 */
require_once 'FrameworkDSW/Framework.php';
require_once 'FrameworkDSW/Utilities.php';

/**
 * The ultimate base class of all exception classes inside FrameworkDSW.
 * @author  许子健
 */
class EException extends Exception {

    /**
     * @return	string
     */
    final static function ClassType() {
        return get_called_class();
    }
}
/**
 * System exception.
 * @author  许子健
 */
class ESysException extends EException {}
/**
 * Runtime Exception.
 * Never catch ERuntimeException, since it will thrown for wrong code. Fix your
 * code instead.
 * @author  许子健
 */
class ERuntimeException extends ESysException {}
/**
 * EIsNotNullable
 * @author	许子健
 */
class EIsNotNullable extends ESysException {}

/**
 * ENotImplemented
 * @author 许子健
 */
class ENotImplemented extends ERuntimeException {}
/**
 * 
 * Enter description here ...
 * @author 许子健
 */
class EInvalidParameter extends ERuntimeException {}
/**
 * Field not existed exception.
 * It will be thrown for visiting object's field that is not existed.
 * @author  许子健
 */
class EFieldNotExisted extends ERuntimeException {
    /**
     * 
     * Enter description here ...
     * @var string
     */
    const CMsg=' is an invalid parameter.';
}
/**
 * Method not existed exception.
 * It will be thrown for calling object's method that is not existed.
 * @author  许子健
 */
class EMethodNotExisted extends ERuntimeException {}
/**
 *
 * @author	许子健
 */
class ENoSuchEnumElement extends ERuntimeException {}
/**
 *
 * @author	许子健
 */
class ENoSuchSetElement extends ERuntimeException {}
/**
 *
 * @author	许子健
 */
class EUnableToConnect extends ERuntimeException {}
/**
 *
 * @author	许子健
 */
class EUnableToDisconnect extends ERuntimeException {}
/**
 *
 * @author	许子健
 */
class EUnableToDispatch extends ERuntimeException {}

/**
 * 
 * @author	许子健
 */
class EGenericException extends ERuntimeException {}
/**
 * 
 * @author	许子健
 */
class ENoSuchGenericArg extends EGenericException {}
/**
 * 
 * @author	许子健
 */
class EBadGenericArgsStructure extends EGenericException {}

/**
 * TObject class.
 * This is the base class of all framework classes.
 * @author  许子健
 */
class TObject {
    /**
     *
     * @var	string
     */
    private static $FConnected = array ();
    /**
     * 
     * @var	array
     */
    private static $FNextGenericArgs = array ();
    /**
     * 
     * @var	array
     */
    private $FGenericArgs = array ();

    /**
     * Default constructor used in FrameworkDSW.
     *
     * You can implement your custom construction with your own needed
     * parameters by redefine it in your derived class. Never return a result in
     * this constructor, for it is ignored.
     */
    public function __construct() {
        if (count(self::$FNextGenericArgs) != 0) {
            $this->FGenericArgs = self::$FNextGenericArgs;
        }
        self::$FNextGenericArgs = array ();
    }

    /**
     * Default destructor used in FrameworkDSW.
     *
     * You can implement your custom destruction with your own needed parameters
     * by redefine it in your derived class. Never return a result in this
     * destructor, for it is ignored.
     */
    public function __destruct() {
    }

    /**
     * Compare with another object.
     *
     * @param  TObject     $Obj
     * @return boolean
     */
    public function Equals($Obj) {
        TType::Object($Obj);
        return $this === $Obj;
    }

    /**
     * Tell if the object supports the given interface.
     *
     * @param  string  $AInterface The interface name to be tested.
     * @return boolean             True for supported, false for unsupported.
     */
    public final function Supports($AInterface) {
        TType::Intf($AInterface);
        return $this instanceof $AInterface;
    }

    /**
     * Get the object type, with generic infomation.
     * @return	mixed
     */
    public final function ObjectType() {
        if (empty($this->FGenericArgs)) {
            return get_class($this);
        }
        return array (get_class($this) => $this->FGenericArgs);
    }

    /**
     * Get the class type.
     * @return string  The name of the class.
     */
    public static final function ClassType() {
        return get_called_class();
    }

    /**
     * @return	mixed
     */
    public final function ObjectParentType() {
        $mClass = get_parent_class($this);
        if ($mClass == false) {
            return null;
        }
        if (empty($this->FGenericArgs)) {
            return $mClass;
        }
        return array ($mClass => $this->FGenericArgs);
    }

    /**
     * Get the parent's class type.
     * @return string  The name of the parent class.
     * @see    TObject::InheritsFrom()
     */
    public final static function ClassParent() {
        $mResult = get_parent_class(self::ClassType());
        if ($mResult == false) {
            return null;
        }
        return $mResult;
    }

    /**
     * 
     * @param	mixed	$Type
     * @return	boolean
     */
    public final function IsInstanceOf($Type) {
        if (is_string($Type)) {
            return $this->InheritsFrom($Type);
        }
        
        $mClass = array_keys($Type);
        $mClass = (string) $mClass[0];
        TType::MetaClass($mClass);
        return is_subclass_of($this, $mClass) && $this->FGenericArgs == $Type[$mClass];
    }

    /**
     * Tell if this class inherits from the given class.
     * @param  string  $AClass The given class.
     * @return boolean         If the object is inherited from
     * <var>$AClass</var>.
     * @see    TObject::ClassParent()
     */
    public final static function InheritsFrom($AClass) {
        TType::MetaClass($AClass);
        
        return is_subclass_of(self::ClassType(), $AClass);
    }

    /**
     * @return	array
     */
    public final function GenericArgs() {
        if (count($this->FGenericArgs) == 0) {
            return null;
        }
        return $this->FGenericArgs;
    }

    /**
     * 
     * @param	string	$ArgName
     * @return	mixed
     */
    public final function GenericArg($ArgName) {
        if (!array_key_exists($ArgName, $this->FGenericArgs)) {
            throw new ENoSuchGenericArg();
        }
        return $this->FGenericArgs[$ArgName];
    }

    /**
     * @return	array
     */
    public final static function StaticGenericArgs() {
        if (count(self::$FNextGenericArgs) == 0) {
            return null;
        }
        return self::$FNextGenericArgs;
    }

    /**
     * 
     * @param	string	$ArgName
     * @return	mixed
     */
    public final static function StaticGenericArg($ArgName) {
        if (!array_key_exists($ArgName, self::$FNextGenericArgs)) {
            throw new ENoSuchGenericArg();
        }
        return self::$FNextGenericArgs[$ArgName];
    }

    /**
     * Returns the source file path which defined the class.
     * @return string  The path of this class.
     */
    public final static function DeclaredIn() {
        $mInfo = new ReflectionClass(self::ClassType());
        return $mInfo->getFileName();
    }

    /**
     * Wake up the object.
     * The method will be invoked when the Framework wants to wake up the
     * object. Write your own code inside this method for a customized waking up
     * in the derived class.
     * @see    TObject::Sleep()
     * @see    TObject::ClassSleep()
     * @see    Framework::Serialize()
     * @see    Framework::Unserialize()
     */
    public function WakeUp() {
    }

    /**
     * Make the object to sleep.
     * The method will be invoked when the Framework wants to make the object to
     * sleep. Write your own code inside this method for a customized sleeping
     * in the derived class.
     * @see    TObject::WakeUp()
     * @see    TObject::ClassSleep()
     * @see    Framework::Serialize()
     * @see    Framework::Unserialize()
     */
    public function Sleep() {
    }

    /**
     * Class wake up method.
     * Defines what to do after the class is waked up.
     * @see    TObject::ClassSleep()
     * @see    TObject::WakeUp()
     * @see    Framework::Serialize()
     * @see    Framework::Unserialize()
     */
    public static function ClassWakeUp() {
    }

    /**
     * Class sleep method.
     * Defines what to do before the class fall asleep.
     * @return array
     * @see    TObject::ClassWakeUp()
     * @see    TObject::Sleep()
     * @see    Framework::Serialize()
     * @see    Framework::Unserialize()
     */
    public static function ClassSleep() {
        return array ();
    }

    /**
     *
     * @param	array	$Signal
     * @param	array	$Slot
     */
    public final static function Link($Signal, $Slot) {
        TType::Arr($Signal);
        TType::Arr($Slot);
        
        if (!method_exists($Signal[0], 'signal' . $Signal[1]) || !method_exists($Slot[0], 'slot' . $Slot[1])) {
            throw new EUnableToConnect();
        }
        
        if (is_string($Signal[0])) {
            $mSignal = $Signal[0] . ':' . $Signal[1];
        }
        else {
            $mSignal = spl_object_hash($Signal[0]) . $Signal[1];
        }
        
        if (!isset(self::$FConnected[$mSignal]) || !in_array($Slot, self::$FConnected[$mSignal])) {
            self::$FConnected[$mSignal][] = $Slot;
        }
    }

    /**
     *
     * @param	array	$Signal
     * @param	array	$Slot
     */
    public final static function Unlink($Signal, $Slot) {
        TType::Arr($Signal);
        
        if (!method_exists($Signal[0], 'signal' . $Signal[1]) || !method_exists($Slot[0], 'slot' . $Slot[1])) {
            throw new EUnableToDisconnect();
        }
        
        if (is_string($Signal[0])) {
            $mSignal = $Signal[0] . ':' . $Signal[1];
        }
        else {
            $mSignal = spl_object_hash($Signal[0]) . $Signal[1];
        }
        
        if (isset(self::$FConnected[$mSignal])) {
            $mIndex = array_search($Slot, self::$FConnected[$mSignal]);
        }
        else {
            $mIndex = false;
        }
        
        if ($mIndex !== false) {
            unset(self::$FConnected[$mSignal][$mIndex]);
        }
    }

    /**
     *
     * @param	array	$Signal
     * @param	array	$Param
     */
    public final static function Dispatch($Signal, $Param) {
        TType::Arr($Signal);
        TType::Arr($Param);
        
        if (!method_exists($Signal[0], 'signal' . $Signal[1])) {
            throw new EUnableToDispatch();
        }
        
        if (is_string($Signal[0])) {
            $mSignal = $Signal[0] . ':' . $Signal[1];
        }
        else {
            $mSignal = spl_object_hash($Signal[0]) . $Signal[1];
        }
        
        foreach (self::$FConnected[$mSignal] as $mSlot) {
            $mSlot[1] = 'slot' . $mSlot[1];
            call_user_func_array($mSlot, $Param);
        }
    }

    /**
     * 
     * @param	array	$Args
     */
    public final static function PrepareGeneric($Args) {
        array_walk_recursive($Args, function (&$Value, $Key) {
            // if (is_array($Value) && !(class_exists($Key) || interface_exists($Key))) {
            //     throw new Exception('error');
            // }
            // else
            if (is_string($Value) && !($Value == 'boolean' || $Value == 'integer' || $Value == 'float' || $Value == 'string' || $Value == 'array' || class_exists($Value) || interface_exists($Value))) {
                throw new EInvalidTypeCasting();
            }
            elseif (!is_string($Value)) {
                throw new EBadGenericArgsStructure();
            }
        });
        self::$FNextGenericArgs = $Args;
    }

    /**
     * Banned to call.
     * A {@link EMethodNotExisted} exception will be always thrown.
     * @param  $name
     * @param  $arguments
     */
    public final function __call($name, $arguments) {
        throw new EMethodNotExisted();
    }

    /**
     * Banned to call.
     * A {@link EMethodNotExisted} exception will be always thrown.
     * @param	string	$name
     * @param	array	$arguments
     * @return	mixed
     */
    public static final function __callStatic($name, $arguments) {
        $mReflection = new ReflectionClass(get_called_class());
        $mIsEnumOrSet = true;
        if (!$mReflection->isSubclassOf(new ReflectionClass('TEnum'))) {
            $mIsEnumOrSet = false;
            if (!$mReflection->isSubclassOf(new ReflectionClass('TSet'))) {
                throw new EMethodNotExisted();
            }
        }
        if ($mIsEnumOrSet) {
            if (!$mReflection->hasConstant($name)) {
                throw new ENoSuchEnumElement();
            }
            return $mReflection->newInstance($mReflection->getConstant($name));
        }
        else {
            return $name;
        }
    }

    /**
     * Banned to call.
     * A {@link EMethodNotExisted} exception will be always thrown.
     * @param  $arr
     */
    public static final function __set_state($arr) {
        throw new EMethodNotExisted();
    }

    /**
     * Banned to call.
     * A {@link EFieldNotExisted} exception will be always thrown.
     * @param  $name
     */
    public function __get($name) {
        throw new EFieldNotExisted();
    }

    /**
     * Banned to call.
     * A {@link EFieldNotExisted} exception will be always thrown.
     * @param  $name
     * @param  $value
     */
    public function __set($name, $value) {
        throw new EFieldNotExisted();
    }

    /**
     * Banned to call.
     * A {@link EFieldNotExisted} exception will be always thrown.
     * @param  $name
     */
    public function __isset($name) {
        throw new EFieldNotExisted();
    }

    /**
     * Banned to call.
     * A {@link EFieldNotExisted} exception will be always thrown.
     * @param  $name
     */
    public function __unset($name) {
        throw new EFieldNotExisted();
    }

    /**
     * PHP magic method.
     */
    public function __toString() {
        throw new EInvalidStringCasting();
    }

    /**
     * PHP magic method.
     */
    public function __invoke() {
        throw new EMethodNotExisted();
    }

    /**
     * PHP magic method.
     */
    public function __clone() {
    }
}

/**
 * IInterface
 * The ultimate base interface for all interfaces inside FrameworkDSW.
 * @author  许子健
 */
interface IInterface {}

/**
 * TRecord
 * @author  许子健
 */
abstract class TRecord extends TObject {

    /**
     * Duplicate a record.
     * @return  TRecord
     */
    public final function Duplicate() {
        return clone $this;
    }

    /**
     * PHP magic method.
     */
    public final function __clone() {
    }

    /**
     * Empty constructor.
     */
    public final function __construct() {
        parent::__construct();
    }

    /**
     * Empty destructor.
     */
    public final function __destruct() {
    }
}

/**
 * TEnum
 * Use TEnum as an enum in the following way:
 * <code>
 * final class THappyColor extends TEnum {
 * const clRed = 0, clGreen = 1, clBlue = 2;
 * }
 * $mColor1 = THappyColor::clRed();
 * $mColor2 = THappyColor::clGreen();
 * if ($mColor1 instanceof THappyColor)  { echo 'I am a value in THappyColor.'; }
 * if ($mColor1 != $mColor2)             { echo 'It is not the same.';          }
 * if ($mColor1 == THappyColor::clRed()) { echo 'I am red.';                    }
 * echo "The code of red is {$mColor1->Value()}.";
 * </code>
 * never comapre TEnum by using '===' operator.
 * @author	许子健
 */
abstract class TEnum extends TObject {
    /**
     * @var	mixed
     */
    protected $FValue = null;

    /**
     *
     * @param	mixed	$Value
     */
    public final function __construct($Value) {
        $this->FValue = $Value;
    }

    /**
     *
     */
    public final function __destruct() {
    }

    /**
     *
     * @return	mixed
     */
    public final function Value() {
        return $this->FValue;
    }
}

/**
 * TSet
 * Use '==' to compare to set.
 * <code>
 * final class TMySet extends TSet {const eRed=0, eYellow=1, eGreen=2;}
 * $myset=new TMySet();
 * $myset->In(TMySet::eRed);
 * $myset->Out('eRed');
 * $myset->In(TMySet::eYellow);
 * $myset->In(TMySet::eGreen);
 * var_dump($myset->IsIn('eGreen')); //true
 * var_dump($myset->IsIn('eYellow')); //true
 * </code>
 * @author	许子健
 */
abstract class TSet extends TObject {
    /**
     *
     * @var	array
     */
    private $FSet = array ();

    /**
     *
     */
    public final function __construct() {
        $mReflection = new ReflectionObject($this);
        foreach ($mReflection->getConstants() as $mElement => $mDummy) {
            $this->FSet[$mElement] = false;
        }
    }

    /**
     *
     * @return	array
     */
    protected final function FetchContent() {
        return $this->FSet;
    }

    /**
     *
     * @param	string	$Element
     */
    public final function In($Element) {
        if (!array_key_exists($Element)) {
            throw new ENoSuchSetElement();
        }
        $this->FSet[$Element] = true;
    }

    /**
     *
     * @param	string	$Element
     */
    public final function Out($Element) {
        if (!array_key_exists($Element)) {
            throw new ENoSuchSetElement();
        }
        $this->FSet[$Element] = false;
    }

    /**
     *
     * @param	string	$Element
     * @return	boolean
     */
    public final function IsIn($Element) {
        if (!array_key_exists($Element)) {
            throw new ENoSuchSetElement();
        }
        return $this->FSet[$Element];
    }

    /**
     *
     * @param	TSet	$Set
     */
    public final function Union($Set) {
        TType::Object($Set, $this->ClassType());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            $this->FSet[$mName] = $mValue || $this->FSet[$mName];
        }
    }

    /**
     *
     * @param	TSet	$Set
     */
    public final function Subtract($Set) {
        TType::Object($Set, $this->ClassType());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            $this->FSet[$mName] = !($mValue && $this->FSet[$mName]);
        }
    }

    /**
     *
     * @param	TSet	$Set
     */
    public final function Intersect($Set) {
        TType::Object($Set, $this->ClassType());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            $this->FSet[$mName] = $mValue && $this->FSet[$mName];
        }
    }

    /**
     *
     * @param	TSet	$Set
     * @return	boolean
     */
    public final function IsSubsetOf($Set) {
        TType::Object($Set, $this->ClassType());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            if (!$mValue && $this->FSet[$mName]) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @param	TSet	$Set
     * @return	boolean
     */
    public final function IsSupersetOf($Set) {
        TType::Object($Set, $this->ClassType());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            if ($mName && !$this->FSet[$mName]) {
                return false;
            }
        }
        return true;
    }
    
//TODO: to store fixed length hash codes of each elements instead of an array.
//TODO: store the set by using bit-mask for efficiency.
}

/**
 * IDelegate
 * @author	许子健
 */
interface IDelegate extends IInterface {/* public funciton Invoke(...); */}

/**
 * TDelegate
 * <code>
 * $mDelegate = new TDelegate(array($mSomeObj, 'DoSomething'), 'TSomeDelegateType');
 * $mDelegate->setDelegate('SomeFunction'); //'SomeFunction' is a defined function name.
 * $mDelegate->setDelegate(function($Param1, $Param2, ...) {...});
 * $mResult = $mDelegate($Param1, $Param2, ...);
 * </code>
 * @author	许子健
 */
final class TDelegate {
    /**
     * @var mixed an array or string
     */
    private $FDelegate = null;
    /**
     *
     * @var	integer
     */
    private $FAtLeast = -1;
    /**
     *
     * @var	integer
     */
    private $FNoMoreThan = -1;

    /**
     *
     * @param	mixed	$Callback	an array, string or closure
     * @param	string	$Type		Delegate type
     */
    public final function __construct($Callback, $Type) {
        try {
            $mPorotype = new ReflectionMethod($Type, 'Invoke');
            $this->FAtLeast = $mPorotype->getNumberOfRequiredParameters();
            $this->FNoMoreThan = $mPorotype->getNumberOfParameters();
            $this->setDelegate($Callback);
        }
        catch (ReflectionException $e) {
            throw new EInvalidDelegateCasting();
        }
    }

    /**
     *
     * @return	mixed
     */
    public final function __invoke() {
        return call_user_func_array($this->FDelegate, func_get_args());
    }

    /**
     *
     * @return	mixed
     */
    public final function getDelegate() {
        return $this->FDelegate;
    }

    /**
     *
     * @param	mixed	$Callback
     */
    public final function setDelegate($Callback) {
        if (is_callable($Callback)) {
            if (is_string($Callback) || $Callback instanceof Closure) {
                $mCallback = new ReflectionFunction($Callback);
            }
            else { //then it must be an array if callable
                $mCallback = new ReflectionMethod($Callback[0], $Callback[1]);
            }
            
            $mNumber = $mCallback->getNumberOfParameters();
            if ($mNumber >= $this->FAtLeast && $mNumber <= $this->FNoMoreThan) {
                $this->FDelegate = $Callback;
            }
            else {
                throw new EInvalidDelegateCasting();
            }
        }
        else {
            throw new EInvalidDelegateCasting();
        }
    }
}

/**
 * IPrimitive
 * param	T
 * @author	许子健
 */
interface IPrimitive extends IInterface {

    /**
     * descHere
     * @param	T	$Value
     */
    public function __construct($Value);

    /**
     * descHere
     * @param	T	$Value
     */
    public function Box($Value);

    /**
     * descHere
     * @param	mixed	$Value
     * @return	T
     */
    public static function ConvertFrom($Value);

    /**
     * descHere
     * @return	T
     */
    public function Unbox();

    /**
     * descHere
     * @return	boolean
     */
    public function UnboxToBoolean();

    /**
     * descHere
     * @return	float
     */
    public function UnboxToFloat();

    /**
     * descHere
     * @return	integer
     */
    public function UnboxToInteger();

    /**
     * descHere
     * @return	string
     */
    public function UnboxToString();
}

/**
 * IComparable
 * param	T
 * @author	许子健
 */
interface IComparable extends IInterface {

    /**
     * descHere
     * @param	T		$Value
     * @return	integer
     */
    public function CompareTo($Value);
}

/**
 * TBoolean
 * extends IPrimitive<T: boolean>, IComparable<T: TBoolean>
 * @author	许子健
 */
final class TBoolean extends TObject implements IPrimitive, IComparable {
    /**
     * @var	boolean
     */
    private $FValue = false;

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function __construct($Value = false) {
        parent::__construct();
        $this->Box($Value);
    }

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function Box($Value) {
        TType::Bool($Value);
        $this->FValue = $Value;
    }

    /**
     * descHere
     * @param	TBoolean	$Value
     * @return	integer
     */
    public function CompareTo($Value) {
        TType::Object($Value, 'TBoolean');
        if ($this->FValue == $Value->Unbox()) {
            return 0;
        }
        if ($this->FValue) {
            return 1;
        }
        else {
            return -1;
        }
    }

    /**
     * descHere
     * @param	mixed	$Value
     * @return	mixed
     */
    public static function ConvertFrom($Value) {
        return (boolean) $Value;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function Unbox() {
        return $this->FValue;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function UnboxToBoolean() {
        return $this->FValue;
    }

    /**
     * descHere
     * @return	float
     */
    public function UnboxToFloat() {
        if ($this->FValue) {
            return 1.0;
        }
        else {
            return 0.0;
        }
    }

    /**
     * descHere
     * @return	integer
     */
    public function UnboxToInteger() {
        if ($this->FValue) {
            return 1;
        }
        else {
            return 0;
        }
    }

    /**
     * descHere
     * @return	string
     */
    public function UnboxToString() {
        if ($this->FValue) {
            return 'true';
        }
        else {
            return 'false';
        }
    }
}

/**
 * TInteger
 * extends IComparable<T: TInteger>, IPrimitive<T: integer>
 * @author	许子健
 */
final class TInteger extends TObject implements IComparable, IPrimitive {
    /**
     * @var	integer
     */
    private $FValue = 0;

    /**
     * descHere
     * @param	integer	$Value
     */
    public function __construct($Value = 0) {
        parent::__construct();
        $this->Box($Value);
    }

    /**
     * descHere
     * @param	integer	$Value
     */
    public function Box($Value) {
        TType::Int($Value);
        $this->FValue = $Value;
    }

    /**
     * descHere
     * @param	TInteger	$Value
     * @return	integer
     */
    public function CompareTo($Value) {
        TType::Object($Value, 'TInteger');
        return $this->FValue - $Value->Unbox();
    }

    /**
     * descHere
     * @param	mixed	$Value
     * @return	integer
     */
    public static function ConvertFrom($Value) {
        return (integer) $Value;
    }

    /**
     * descHere
     * @return	integer
     */
    public function Unbox() {
        return $this->FValue;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function UnboxToBoolean() {
        return $this->FValue != 0;
    }

    /**
     * descHere
     * @return	float
     */
    public function UnboxToFloat() {
        return (float) $this->FValue;
    }

    /**
     * descHere
     * @return	integer
     */
    public function UnboxToInteger() {
        return $this->FValue;
    }

    /**
     * descHere
     * @return	string
     */
    public function UnboxToString() {
        return (string) $this->FValue;
    }
}

/**
 * TFloat
 * extends	IPrimitive<T: float>, IComparable<T: TFloat>
 * @author	许子健
 */
final class TFloat extends TObject implements IPrimitive, IComparable {
    /**
     * @var	float
     */
    private $FValue = 0.0;

    /**
     * descHere
     * @param	float	$Value
     */
    public function __construct($Value = 0.0) {
        parent::__construct();
        $this->Box($Value);
    }

    /**
     * descHere
     * @param	float	$Value
     */
    public function Box($Value) {
        TType::Float($Value);
        $this->FValue = $Value;
    }

    /**
     * descHere
     * @param	TFloat	$Value
     * @return	integer
     */
    public function CompareTo($Value) {
        TType::Object($Value, 'TFloat');
        return (float) ($this->FValue - $Value->Unbox());
    }

    /**
     * descHere
     * @param	mixed	$Value
     * @return	float
     */
    public static function ConvertFrom($Value) {
        return (float) $Value;
    }

    /**
     * descHere
     * @return	float
     */
    public function Unbox() {
        return $this->FValue;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function UnboxToBoolean() {
        return (boolean) $this->FValue;
    }

    /**
     * descHere
     * @return	float
     */
    public function UnboxToFloat() {
        return $this->FValue;
    }

    /**
     * descHere
     * @return	integer
     */
    public function UnboxToInteger() {
        return (integer) $this->FValue;
    }

    /**
     * descHere
     * @return	string
     */
    public function UnboxToString() {
        return (string) $this->FValue;
    }
}

/**
 * TString
 * extends	IComparable<T: TString>, IPrimitive<T: string>
 * @author	许子健
 */
final class TString extends TObject implements IComparable, IPrimitive {
    /**
     * @var	string
     */
    private $FValue = '';

    /**
     * descHere
     * @param	string	$Value
     */
    public function __construct($Value = '') {
        parent::__construct();
        $this->Box($Value);
    }

    /**
     * descHere
     * @param	string	$Value
     */
    public function Box($Value) {
        TType::String($Value);
        $this->FValue = $Value;
    }

    /**
     * descHere
     * @param	TString	$Value
     * @return	integer
     */
    public function CompareTo($Value) {
        return strcmp($this->FValue, $Value->Unbox());
    }

    /**
     * descHere
     * @param	mixed	$Value
     * @return	string
     */
    public static function ConvertFrom($Value) {
        return (string) $Value;
    }

    /**
     * descHere
     * @return	string
     */
    public function Unbox() {
        return $this->FValue;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function UnboxToBoolean() {
        return (boolean) $this->FValue;
    }

    /**
     * descHere
     * @return	float
     */
    public function UnboxToFloat() {
        return (float) $this->FValue;
    }

    /**
     * descHere
     * @return	integer
     */
    public function UnboxToInteger() {
        return (integer) $this->FValue;
    }

    /**
     * descHere
     * @return	string
     */
    public function UnboxToString() {
        return $this->FValue;
    }
}