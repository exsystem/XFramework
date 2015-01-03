<?php
/**
 * \FrameworkDSW\System
 * @author  ExSystem
 * @version $Id$
 * @since   separate file since reversion 1
 */
namespace FrameworkDSW\System;

use FrameworkDSW\Containers\IIteratorAggregate;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\Utilities\EInvalidStringCasting;
use FrameworkDSW\Utilities\TType;

/**
 * The ultimate base class of all exception classes inside FrameworkDSW.
 *
 * @author 许子健
 */
class EException extends \Exception implements IInterface {
    /**
     * Get the object type, with generic information.
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public final function ObjectType() {
        $mType = get_class($this);
        return Framework::Type($mType);
    }

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     */
    public function __construct($Message = '', $Previous = null) {
        TType::String($Message);
        TType::Object($Previous, EException::class);

        parent::__construct($Message, -1, $Previous);
    }

    /**
     * Enter description here .
     *
     * ..
     */
    public function Destroy() {
    }

    /**
     * Compare with another object.
     *
     * @param \FrameworkDSW\System\IInterface $Obj
     * @return boolean
     */
    public function Equals($Obj) {
        TType::Object($Obj);

        return $this === $Obj;
    }

    /**
     * Tell if the object supports the given interface.
     *
     * @param \FrameworkDSW\Reflection\TClass $AInterface <T: ?> The interface name to be tested.
     * @return boolean True for supported, false for unsupported.
     */
    public function Supports($AInterface) {
        TType::Object($AInterface, [TClass::class => ['T' => null]]);

        return $AInterface->IsInterface() && $this->IsInstanceOf($AInterface);
    }

    /**
     * Get the class type.
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?> The name of the class.
     */
    public static function ClassType() {
        return Framework::Type(get_called_class());
    }

    /**
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function ObjectParentType() {
        $mClass = get_parent_class($this);
        if ($mClass == false) {
            return null;
        }

        return Framework::Type($mClass);
    }

    /**
     * Get the parent's class type.
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?> The name of the parent class.
     * @see TObject::InheritsFrom()
     */
    public static function ClassParent() {
        $mResult = get_parent_class(get_called_class());
        if ($mResult == false) {
            return null;
        }

        return Framework::Type($mResult);
    }

    /**
     *
     * @param \FrameworkDSW\Reflection\TClass $Type <T: ?>
     * @return boolean
     */
    public function IsInstanceOf($Type) {
        return $Type->IsInstance($this);
    }

    /**
     * Tell if this class inherits from the given class.
     *
     * @param \FrameworkDSW\Reflection\TClass $AClass <T: ?> The given class.
     * @return boolean If the object is inherited from
     *         <var>$AClass</var>.
     * @see TObject::ClassParent()
     */
    public static function InheritsFrom($AClass) {
        TType::Object($AClass, [TClass::class => ['T' => null]]);

        return is_subclass_of(get_called_class(), $AClass->getName());
    }

    /**
     *
     * @return array
     */
    public function GenericArgs() {
        return null;
    }

    /**
     *
     * @param string $ArgName
     * @return mixed
     * @throws ENoSuchGenericArg
     */
    public function GenericArg($ArgName) {
        throw new ENoSuchGenericArg(sprintf('No such generic arg: %s.', $ArgName), null, $ArgName);
    }

    /**
     *
     * @return array
     */
    public static function StaticGenericArgs() {
        return null;
    }

    /**
     *
     * @param string $ArgName
     * @return mixed
     * @throws ENoSuchGenericArg
     */
    public static function StaticGenericArg($ArgName) {
        throw new ENoSuchGenericArg(sprintf('No such generic arg: %s.', $ArgName), null, $ArgName);
    }

    /**
     * Returns the source file path which defined the class.
     *
     * @return string The path of this class.
     */
    public static function DeclaredIn() {
        $mInfo = new \ReflectionClass(get_called_class());

        return $mInfo->getFileName();
    }

    /**
     * Wake up the object.
     * The method will be invoked when the Framework wants to wake up the
     * object. Write your own code inside this method for a customized waking up
     * in the derived class.
     *
     * @see TObject::Sleep()
     * @see TObject::ClassSleep()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public function WakeUp() {
    }

    /**
     * Make the object to sleep.
     * The method will be invoked when the Framework wants to make the object to
     * sleep. Write your own code inside this method for a customized sleeping
     * in the derived class.
     *
     * @see TObject::WakeUp()
     * @see TObject::ClassSleep()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public function Sleep() {
    }

    /**
     * Class wake up method.
     * Defines what to do after the class is waked up.
     *
     * @see TObject::ClassSleep()
     * @see TObject::WakeUp()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public static function ClassWakeUp() {
    }

    /**
     * Class sleep method.
     * Defines what to do before the class fall asleep.
     *
     * @return array
     * @see TObject::ClassWakeUp()
     * @see TObject::Sleep()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public static function ClassSleep() {
        return [];
    }

    /**
     *
     * @param array $Signal
     * @param array $Slot
     * @throws ERuntimeException
     */
    public static function Link($Signal, $Slot) {
        throw new ERuntimeException(sprintf('Signal and slot are not supported on exceptions.'));
    }

    /**
     *
     * @param array $Signal
     * @param array $Slot
     * @throws ERuntimeException
     */
    public static function Unlink($Signal, $Slot) {
        throw new ERuntimeException(sprintf('Signal and slot are not supported on exceptions.'));
    }

    /**
     *
     * @param array $Signal
     * @param array $Param
     * @throws ERuntimeException
     */
    public static function Dispatch($Signal, $Param) {
        throw new ERuntimeException(sprintf('Signal and slot are not supported on exceptions.'));
    }

    /**
     *
     * @param array $Args
     * @throws ERuntimeException
     */
    public static function PrepareGeneric($Args) {
        throw new ERuntimeException(sprintf('Generics is not supported on exceptions.'));
    }

    /**
     *
     * @param array $Args
     * @throws ERuntimeException
     */
    public function PrepareMethodGeneric($Args) {
        throw new ERuntimeException(sprintf('Generics is not supported on exceptions.'));
    }
}

/**
 * System exception.
 *
 * @author 许子健
 */
class EError extends EException {
}

/**
 * Runtime Exception.
 * Never catch ERuntimeException, since it will thrown for wrong code. Fix your
 * code instead.
 *
 * @author 许子健
 */
class ERuntimeException extends EError {
}

/**
 * EAccessViolation
 *
 * @author 许子健
 */
class EAccessViolation extends EError {
}

/**
 * ENotImplemented
 *
 * @author 许子健
 */
class ENotImplemented extends ERuntimeException {
}

/**
 *
 * Enter description here ...
 *
 * @author 许子健
 */
class EInvalidParameter extends ERuntimeException {
}

/**
 * Field not existed exception.
 * It will be thrown for visiting object's field that is not existed.
 *
 * @author 许子健
 */
class ENoSuchField extends EError {
    /**
     * @var \FrameworkDSW\Reflection\TClass<T: ?>
     */
    private $FClass = null;
    /**
     * @var string
     */
    private $FFieldName = '';
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    private $FObject = null;

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\System\IInterface $Object
     * @param string $FieldName
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     */
    public function __construct($Message, $Previous, $Object, $FieldName, $Class = null) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($Object, IInterface::class);
        TType::String($FieldName);
        TType::Object($Class, [TClass::class => ['T' => null]]);

        $this->FObject    = $Object;
        $this->FFieldName = $FieldName;
        $this->FClass     = $Class;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getClass() {
        return $this->FClass;
    }

    /**
     * @return string
     */
    public function getFieldName() {
        return $this->FFieldName;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getObject() {
        return $this->FObject;
    }
}

/**
 * ENoSuchType
 * @author 许子健
 */
class ENoSuchType extends EError {
    /**
     * @var mixed
     */
    private $FType = '';

    /**
     * descHere
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param mixed $Type
     */
    public function __construct($Message, $Previous = null, $Type) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);

        $this->FType = $Type;
    }

    /**
     * descHere
     * @return mixed
     */
    public function getType() {
        return $this->FType;
    }
}

/**
 * Method not existed exception.
 * It will be thrown for calling object's method that is not existed.
 *
 * @author 许子健
 */
class ENoSuchMethod extends ERuntimeException {
    /**
     * @var \FrameworkDSW\Reflection\TClass<T: ?>
     */
    private $FClass = null;
    /**
     * @var string
     */
    private $FMethodName = '';
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    private $FObject = null;

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\System\IInterface $Object
     * @param string $MethodName
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     */
    public function __construct($Message, $Previous, $Object, $MethodName, $Class = null) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($Object, IInterface::class);
        TType::String($MethodName);
        TType::Object($Class, [TClass::class => ['T' => null]]);

        $this->FObject     = $Object;
        $this->FMethodName = $MethodName;
        $this->FClass      = $Class;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getClass() {
        return $this->FClass;
    }

    /**
     * @return string
     */
    public function getMethodName() {
        return $this->FMethodName;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getObject() {
        return $this->FObject;
    }
}

/**
 *
 * @author 许子健
 */
class ENoSuchEnumElement extends EError {
    /**
     * @var string
     */
    private $FElementName = '';
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: \FrameworkDSW\System\TEnum>
     */
    private $FEnum = null;

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\Reflection\TClass $Enum <T: \FrameworkDSW\System\TEnum>
     * @param string $ElementName
     */
    public function __construct($Message, $Previous, $Enum, $ElementName) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($Enum, [TClass::class => ['T' => TEnum::class]]);
        TType::String($ElementName);

        $this->FEnum        = $Enum;
        $this->FElementName = $ElementName;
    }

    /**
     * @return string
     */
    public function getElementName() {
        return $this->FElementName;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: \FrameworkDSW\System\TEnum>
     */
    public function getEnum() {
        return $this->FEnum;
    }
}

/**
 *
 * @author 许子健
 */
class ENoSuchSetElement extends EError {
    /**
     * @var string
     */
    private $FElementName = '';
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: \FrameworkDSW\System\TSet>
     */
    private $FSet = null;

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\Reflection\TClass $Set <T: \FrameworkDSW\System\TSet>
     * @param string $ElementName
     */
    public function __construct($Message, $Previous, $Set, $ElementName) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($Set, [TClass::class => ['T' => TSet::class]]);
        TType::String($ElementName);

        $this->FSet         = $Set;
        $this->FElementName = $ElementName;
    }

    /**
     * @return string
     */
    public function getElementName() {
        return $this->FElementName;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: \FrameworkDSW\System\TSet>
     */
    public function getSet() {
        return $this->FSet;
    }
}

/**
 *
 * @author 许子健
 */
class ELinkFailed extends EError {
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public $FSignalClass = null;
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    public $FSignalObject = null;
    /**
     * @var string
     */
    public $FSignalName = '';
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public $FSlotClass = null;
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    public $FSlotObject = null;
    /**
     * @var string
     */
    public $FSlotName = '';

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\System\IInterface $SignalObject
     * @param string $SignalName
     * @param \FrameworkDSW\Reflection\TClass $SignalClass <T: ?>
     * @param \FrameworkDSW\System\IInterface $SlotObject
     * @param string $SlotName
     * @param \FrameworkDSW\Reflection\TClass $SlotClass <T: ?>
     */
    public function __construct($Message, $Previous, $SignalObject, $SignalName, $SignalClass, $SlotObject, $SlotName, $SlotClass = null) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($SignalObject, IInterface::class);
        TType::String($SignalName);
        TType::Object($SignalClass, [TClass::class => ['T' => null]]);
        TType::Object($SlotObject, IInterface::class);
        TType::String($SlotName);
        TType::Object($SlotClass, [TClass::class => ['T' => null]]);

        $this->FSignalObject = $SignalObject;
        $this->FSignalName   = $SignalName;
        $this->FSignalClass  = $SignalClass;
        $this->FSlotObject   = $SlotObject;
        $this->FSlotName     = $SlotName;
        $this->FSlotClass    = $SlotClass;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getSignalClass() {
        return $this->FSignalClass;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getSignalObject() {
        return $this->FSignalObject;
    }

    /**
     * @return string
     */
    public function getSignalName() {
        return $this->FSignalName;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getSlotClass() {
        return $this->FSlotClass;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getSlotObject() {
        return $this->FSlotObject;
    }

    /**
     * @return string
     */
    public function getSlotName() {
        return $this->FSlotName;
    }
}

/**
 *
 * @author 许子健
 */
class EUnlinkFailed extends EError {
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public $FSignalClass = null;
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    public $FSignalObject = null;
    /**
     * @var string
     */
    public $FSignalName = '';
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public $FSlotClass = null;
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    public $FSlotObject = null;
    /**
     * @var string
     */
    public $FSlotName = '';

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\System\IInterface $SignalObject
     * @param string $SignalName
     * @param \FrameworkDSW\Reflection\TClass $SignalClass <T: ?>
     * @param \FrameworkDSW\System\IInterface $SlotObject
     * @param string $SlotName
     * @param \FrameworkDSW\Reflection\TClass $SlotClass <T: ?>
     */
    public function __construct($Message, $Previous, $SignalObject, $SignalName, $SignalClass, $SlotObject, $SlotName, $SlotClass = null) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($SignalObject, IInterface::class);
        TType::String($SignalName);
        TType::Object($SignalClass, [TClass::class => ['T' => null]]);
        TType::Object($SlotObject, IInterface::class);
        TType::String($SlotName);
        TType::Object($SlotClass, [TClass::class => ['T' => null]]);

        $this->FSignalObject = $SignalObject;
        $this->FSignalName   = $SignalName;
        $this->FSignalClass  = $SignalClass;
        $this->FSlotObject   = $SlotObject;
        $this->FSlotName     = $SlotName;
        $this->FSlotClass    = $SlotClass;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getSignalClass() {
        return $this->FSignalClass;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getSignalObject() {
        return $this->FSignalObject;
    }

    /**
     * @return string
     */
    public function getSignalName() {
        return $this->FSignalName;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getSlotClass() {
        return $this->FSlotClass;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getSlotObject() {
        return $this->FSlotObject;
    }

    /**
     * @return string
     */
    public function getSlotName() {
        return $this->FSlotName;
    }
}

/**
 *
 * @author 许子健
 */
class EDispatchFailed extends EError {
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public $FClass = null;
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    public $FObject = null;
    /**
     * @var string
     */
    public $FSignalName = '';

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\System\IInterface $Object
     * @param string $SignalName
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     */
    public function __construct($Message, $Previous, $Object, $SignalName, $Class = null) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($Object, IInterface::class);
        TType::String($SignalName);
        TType::Object($Class, [TClass::class => ['T' => null]]);

        $this->FObject     = $Object;
        $this->FSignalName = $SignalName;
        $this->FClass      = $Class;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getClass() {
        return $this->FClass;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getObject() {
        return $this->FObject;
    }

    /**
     * @return string
     */
    public function getSignalName() {
        return $this->FSignalName;
    }
}

/**
 * EIncompatibleTypeAssignment
 * @author 许子健
 */
class EIncompatibleTypeAssignment extends EError {
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    private $FExpected = null;
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    private $FFound = null;

    /**
     * descHere
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\Reflection\TClass $Expected <T: ?>
     * @param \FrameworkDSW\Reflection\TClass $Found <T: ?>
     */
    public function __construct($Message, $Previous = null, $Expected, $Found) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($Expected, [TClass::class => ['T' => null]]);
        TType::Object($Found, [TClass::class => ['T' => null]]);

        $this->FExpected = $Expected;
        $this->FFound    = $Found;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getExpected() {
        return $this->FExpected;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getFound() {
        return $this->FFound;
    }
}

/**
 *
 * @author 许子健
 */
class EGenericException extends ERuntimeException {
}

/**
 *
 * @author 许子健
 */
class ENoSuchGenericArg extends EGenericException {
    /**
     * @var string
     */
    private $FArgName = '';
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    private $FClass = null;

    /**
     * descHere
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param string $ArgName
     */
    public function __construct($Message, $Previous, $ArgName) {
        parent::__construct($Message, $Previous);
        TType::String($ArgName);

        $this->FArgName = $ArgName;
    }

    /**
     * descHere
     * @return string
     */
    public function getArgName() {
        return $this->FArgName;
    }
}

/**
 *
 * @author 许子健
 */
class EBadGenericArgsStructure extends EGenericException {
}

/**
 * Class EConstructorInvocationNotAllowed
 * @package FrameworkDSW\System
 */
class EConstructorInvocationNotAllowed extends EError {
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    private $FClass = null;

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     */
    public function __construct($Message, $Previous, $Class) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($Class, [TClass::class => ['T' => null]]);

        $this->FClass = $Class;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getClass() {
        return $this->FClass;
    }
}

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
 * IInterface
 * The ultimate base interface for all interfaces inside FrameworkDSW.
 *
 * @author 许子健
 */
interface IInterface {
    /**
     *
     */
    public function Destroy();

    /**
     * Compare with another object.
     *
     * @param \FrameworkDSW\System\IInterface $Obj
     * @return boolean
     */
    public function Equals($Obj);

    /**
     * Tell if the object supports the given interface.
     *
     * @param \FrameworkDSW\Reflection\TClass $AInterface <T: ?> The interface name to be tested.
     * @return boolean True for supported, false for unsupported.
     */
    public function Supports($AInterface);

    /**
     * Get the object type, with generic information.
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function ObjectType();

    /**
     * Get the class type.
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?> The name of the class.
     */
    public static function ClassType();

    /**
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function ObjectParentType();

    /**
     * Get the parent's class type.
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?> The name of the parent class.
     * @see TObject::InheritsFrom()
     */
    public static function ClassParent();

    /**
     *
     * @param \FrameworkDSW\Reflection\TClass $Type <T: ?>
     * @return boolean
     */
    public function IsInstanceOf($Type);

    /**
     * Tell if this class inherits from the given class.
     *
     * @param \FrameworkDSW\Reflection\TClass $AClass <T: ?> The given class.
     * @return boolean If the object is inherited from
     *         <var>$AClass</var>.
     * @see TObject::ClassParent()
     */
    public static function InheritsFrom($AClass);

    /**
     *
     * @return array
     */
    public function GenericArgs();

    /**
     *
     * @param string $ArgName
     * @return mixed
     */
    public function GenericArg($ArgName);

    /**
     *
     * @return array
     */
    public static function StaticGenericArgs();

    /**
     *
     * @param string $ArgName
     * @return mixed
     */
    public static function StaticGenericArg($ArgName);

    /**
     * Returns the source file path which defined the class.
     *
     * @return string The path of this class.
     */
    public static function DeclaredIn();

    /**
     * Wake up the object.
     * The method will be invoked when the Framework wants to wake up the
     * object. Write your own code inside this method for a customized waking up
     * in the derived class.
     *
     * @see TObject::Sleep()
     * @see TObject::ClassSleep()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public function WakeUp();

    /**
     * Make the object to sleep.
     * The method will be invoked when the Framework wants to make the object to
     * sleep. Write your own code inside this method for a customized sleeping
     * in the derived class.
     *
     * @see TObject::WakeUp()
     * @see TObject::ClassSleep()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public function Sleep();

    /**
     * Class wake up method.
     * Defines what to do after the class is waked up.
     *
     * @see TObject::ClassSleep()
     * @see TObject::WakeUp()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public static function ClassWakeUp();

    /**
     * Class sleep method.
     * Defines what to do before the class fall asleep.
     *
     * @return array
     * @see TObject::ClassWakeUp()
     * @see TObject::Sleep()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public static function ClassSleep();

    /**
     *
     * @param $Signal array
     * @param $Slot array
     */
    public static function Link($Signal, $Slot);

    /**
     *
     * @param $Signal array
     * @param $Slot array
     */
    public static function Unlink($Signal, $Slot);

    /**
     *
     * @param $Signal array
     * @param $Param array
     */
    public static function Dispatch($Signal, $Param);

    /**
     *
     * @param $Args array
     */
    public static function PrepareGeneric($Args);

    /**
     *
     * @param $Args array
     */
    public function PrepareMethodGeneric($Args);
}

/**
 * TObject class.
 * This is the base class of all framework classes.
 *
 * @author 许子健
 */
class TObject implements IInterface {
    /**
     *
     * @var array
     */
    private static $FConnected = [];
    /**
     *
     * @var array
     */
    private static $FNextGenericArgs = [];
    /**
     *
     * @var array
     */
    private $FGenericArgs = [];

    /**
     * Default constructor used in FrameworkDSW.
     *
     * You can implement your custom construction with your own needed
     * parameters by redefine it in your derived class. Never return a result in
     * this constructor, for it is ignored.
     */
    public function __construct() {
        if (count(self::$FNextGenericArgs) != 0) {
            $this->FGenericArgs = array_replace($this->FGenericArgs, self::$FNextGenericArgs);
        }
        self::$FNextGenericArgs = [];
    }

    /**
     * Default destructor used in FrameworkDSW.
     *
     * You can implement your custom destruction with your own needed parameters
     * by redefine it in your derived class. Never return a result in this
     * destructor, for it is ignored.
     */
    public function Destroy() {
    }

    /**
     * Compare with another object.
     *
     * @param \FrameworkDSW\System\IInterface $Obj
     * @return boolean
     */
    public function Equals($Obj) {
        TType::Object($Obj);

        return $this === $Obj;
    }

    /**
     * Tell if the object supports the given interface.
     *
     * @param \FrameworkDSW\Reflection\TClass $AInterface <T: ?> The interface o be tested.
     * @return boolean True for supported, false for unsupported.
     */
    public final function Supports($AInterface) {
        TType::Object($AInterface, [TClass::class => ['T' => null]]);

        return $AInterface->IsInterface() && $this->IsInstanceOf($AInterface);
    }

    /**
     * Get the object type, with generic information.
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public final function ObjectType() {
        if (empty($this->FGenericArgs)) {
            $mType = get_class($this);
        }
        else {
            $mType = [get_class($this) => $this->FGenericArgs];
        }
        return Framework::Type($mType);
    }

    /**
     * Get the class type.
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?> The name of the class.
     */
    public static final function ClassType() {
        return Framework::Type(get_called_class());
    }

    /**
     *
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public final function ObjectParentType() {
        $mClass = get_parent_class($this);
        if ($mClass == false) {
            return null;
        }
        if (empty($this->FGenericArgs)) {
            return Framework::Type($mClass);
        }

        return Framework::Type([$mClass => $this->FGenericArgs]);
    }

    /**
     * Get the parent's class type.
     *
     * @return string The name of the parent class.
     * @see TObject::InheritsFrom()
     */
    public final static function ClassParent() {
        $mResult = get_parent_class(get_called_class());
        if ($mResult == false) {
            return null;
        }

        return Framework::Type($mResult);
    }

    /**
     *
     * @param \FrameworkDSW\Reflection\TClass $Type <T: ?>
     * @return boolean
     */
    public final function IsInstanceOf($Type) {
        return $Type->IsInstance($this);
    }

    /**
     * Tell if this class inherits from the given class.
     *
     * @param \FrameworkDSW\Reflection\TClass $AClass <T: ?> The given class.
     * @return boolean If the object is inherited from
     *         <var>$AClass</var>.
     * @see TObject::ClassParent()
     */
    public final static function InheritsFrom($AClass) {
        TType::Object($AClass, [TClass::class => ['T' => null]]);

        return is_subclass_of(get_called_class(), $AClass->getName());
    }

    /**
     *
     * @return array
     */
    public final function GenericArgs() {
        if (count($this->FGenericArgs) == 0) {
            return null;
        }

        return $this->FGenericArgs;
    }

    /**
     *
     * @param string $ArgName
     * @throws ENoSuchGenericArg
     * @return mixed
     */
    public final function GenericArg($ArgName) {
        if (!array_key_exists($ArgName, $this->FGenericArgs)) {
            throw new ENoSuchGenericArg(sprintf('No such generic arg: %s.', $ArgName), null, $ArgName);
        }

        return $this->FGenericArgs[$ArgName];
    }

    /**
     *
     * @return array
     */
    public final static function StaticGenericArgs() {
        if (count(self::$FNextGenericArgs) == 0) {
            return null;
        }

        return self::$FNextGenericArgs;
    }

    /**
     *
     * @param string $ArgName
     * @throws ENoSuchGenericArg
     * @return mixed
     */
    public final static function StaticGenericArg($ArgName) {
        if (!array_key_exists($ArgName, self::$FNextGenericArgs)) {
            throw new ENoSuchGenericArg(sprintf('No such generic arg: %s.', $ArgName), null, $ArgName);
        }

        return self::$FNextGenericArgs[$ArgName];
    }

    /**
     * Returns the source file path which defined the class.
     *
     * @return string The path of this class.
     */
    public final static function DeclaredIn() {
        $mInfo = new \ReflectionClass(get_called_class());

        return $mInfo->getFileName();
    }

    /**
     * Wake up the object.
     * The method will be invoked when the Framework wants to wake up the
     * object. Write your own code inside this method for a customized waking up
     * in the derived class.
     *
     * @see TObject::Sleep()
     * @see TObject::ClassSleep()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public function WakeUp() {
    }

    /**
     * Make the object to sleep.
     * The method will be invoked when the Framework wants to make the object to
     * sleep. Write your own code inside this method for a customized sleeping
     * in the derived class.
     *
     * @see TObject::WakeUp()
     * @see TObject::ClassSleep()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public function Sleep() {
    }

    /**
     * Class wake up method.
     * Defines what to do after the class is waked up.
     *
     * @see TObject::ClassSleep()
     * @see TObject::WakeUp()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public static function ClassWakeUp() {
    }

    /**
     * Class sleep method.
     * Defines what to do before the class fall asleep.
     *
     * @return array
     * @see TObject::ClassWakeUp()
     * @see TObject::Sleep()
     * @see Framework::Serialize()
     * @see Framework::Unserialize()
     */
    public static function ClassSleep() {
        return [];
    }

    /**
     *
     * @param array $Signal
     * @param array $Slot
     * @throws ELinkFailed
     */
    public final static function Link($Signal, $Slot) {
        TType::Arr($Signal);
        TType::Arr($Slot);

        if (!method_exists($Signal[0], 'signal' . $Signal[1]) || !method_exists($Slot[0], 'slot' . $Slot[1])) {
            /**@var IInterface $mSignalObject * */
            $mSignalObject = null;
            $mSignalClass  = null;
            /**@var IInterface $mSlotObject * */
            $mSlotObject = null;
            $mSlotClass  = null;
            if (is_string($Signal[0])) {
                $mSignalMessage = sprintf('%s::%s', Framework::Type($Signal[0])->getName(), $Signal[1]);
            }
            else {
                $mSignalObject  = $Signal[0];
                $mSignalMessage = sprintf('%s->%s', $mSignalObject->ObjectType()->getName(), $Signal[1]);
            }
            if (is_string($Slot[0])) {
                $mSlotMessage = sprintf('%s::%s', Framework::Type($Slot[0])->getName(), $Slot[1]);
            }
            else {
                $mSlotObject  = $Slot[0];
                $mSlotMessage = sprintf('%s::%s', $mSlotObject->ObjectType()->getName(), $Slot[1]);
            }
            throw new ELinkFailed("Link failed: signal {$mSignalMessage} with slot {$mSlotMessage}.", null, $mSignalObject, $Signal[1], $mSignalClass, $mSlotObject, $Slot[1], $mSlotClass);
        }

        if (is_string($Signal[0])) {
            $mSignal = $Signal[0] . ':' . $Signal[1];
        }
        else {
            $mSignal = spl_object_hash($Signal[0]) . $Signal[1];
        }

        if (!isset(self::$FConnected[$mSignal])
            || !in_array($Slot, self::$FConnected[$mSignal])
        ) {
            self::$FConnected[$mSignal][] = $Slot;
        }
    }

    /**
     *
     * @param array $Signal
     * @param array $Slot
     * @throws EUnlinkFailed
     */
    public final static function Unlink($Signal, $Slot) {
        TType::Arr($Signal);

        if (!method_exists($Signal[0], 'signal' . $Signal[1]) || !method_exists($Slot[0], 'slot' . $Slot[1])) {
            /**@var IInterface $mSignalObject * */
            $mSignalObject = null;
            $mSignalClass  = null;
            /**@var IInterface $mSlotObject * */
            $mSlotObject = null;
            $mSlotClass  = null;
            if (is_string($Signal[0])) {
                $mSignalMessage = sprintf('%s::%s', Framework::Type($Signal[0])->getName(), $Signal[1]);
            }
            else {
                $mSignalObject  = $Signal[0];
                $mSignalMessage = sprintf('%s->%s', $mSignalObject->ObjectType()->getName(), $Signal[1]);
            }
            if (is_string($Slot[0])) {
                TClass::PrepareGeneric(['T' => $Slot[0]]);
                $mSlotClass   = new TClass();
                $mSlotMessage = sprintf('%s::%s', $mSlotClass->getName(), $Slot[1]);
            }
            else {
                $mSlotObject  = $Slot[0];
                $mSlotMessage = sprintf('%s::%s', $mSlotObject->ObjectType()->getName(), $Slot[1]);
            }
            throw new EUnlinkFailed("Unlink failed: signal {$mSignalMessage} with slot {$mSlotMessage}.", null, $mSignalObject, $Signal[1], $mSignalClass, $mSlotObject, $Slot[1], $mSlotClass);
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
     * @param array $Signal
     * @param array $Param
     * @throws EDispatchFailed
     */
    public final static function Dispatch($Signal, $Param) {
        TType::Arr($Signal);
        TType::Arr($Param);

        if (!method_exists($Signal[0], 'signal' . $Signal[1])) {
            /**@var IInterface $mSignalObject * */
            $mSignalObject = null;
            $mSignalClass  = null;
            if (is_string($Signal[0])) {
                $mSignalMessage = sprintf('%s::%s', Framework::Type($Signal[0])->getName(), $Signal[1]);
            }
            else {
                $mSignalObject  = $Signal[0];
                $mSignalMessage = sprintf('%s->%s', $mSignalObject->ObjectType()->getName(), $Signal[1]);
            }
            throw new EDispatchFailed("Dispatch failed: {$mSignalMessage}.", null, $mSignalObject, $Signal[1], $mSignalClass);
        }

        if (is_string($Signal[0])) {
            $mSignal = $Signal[0] . ':' . $Signal[1];
        }
        else {
            $mSignal = spl_object_hash($Signal[0]) . $Signal[1];
        }

        if (array_key_exists($mSignal, self::$FConnected) === false) {
            return;
        }

        foreach (self::$FConnected[$mSignal] as $mSlot) {
            $mSlot[1] = 'slot' . $mSlot[1];
            call_user_func_array($mSlot, $Param);
        }
    }

    /**
     *
     * @param array $Args
     */
    public final static function PrepareGeneric($Args) {
        /** @noinspection PhpUnusedParameterInspection */
        if ($Args === null) {
            return;
        }

        array_walk_recursive($Args,
            function (&$Value, $Key) {
                // if (is_array($Value) && !(class_exists($Key) ||
                // interface_exists($Key))) {
                // throw new Exception('error');
                // }
                // else
                if (is_string($Value)
                    && !($Value == Framework::Boolean || $Value == Framework::Integer
                        || $Value == Framework::Float || $Value == Framework::String
                        || strrpos($Value, ']', -1) !== false
                        || class_exists($Value)
                        || interface_exists($Value)
                        || $Value == Framework::Variant)
                ) {
                    throw new ENoSuchType(sprintf('No such type: %s.', $Value), null, $Value);
                }
                elseif (!is_string($Value) && $Value !== null) {
                    throw new EBadGenericArgsStructure('Bad generic args structure.');
                }
            });
        self::$FNextGenericArgs = $Args;
    }

    /**
     *
     * @param array $Args
     * @throws EBadGenericArgsStructure
     */
    public final function PrepareMethodGeneric($Args) {
        /** @noinspection PhpUnusedParameterInspection */
        if ($Args === null) {
            return;
        }

        array_walk_recursive($Args,
            function (&$Value, $Key) {
                // if (is_array($Value) && !(class_exists($Key) ||
                // interface_exists($Key))) {
                // throw new Exception('error');
                // }
                // else
                if (is_string($Value)
                    && !($Value == Framework::Boolean || $Value == Framework::Integer
                        || $Value == Framework::Float || $Value == Framework::String
                        || strrpos($Value, ']', -1) !== false
                        || class_exists($Value)
                        || interface_exists($Value)
                        || $Value == Framework::Variant)
                ) {
                    throw new ENoSuchType(sprintf('No such type: %s.', $Value), null, $Value);
                }
                elseif (!is_string($Value) && $Value !== null) {
                    throw new EBadGenericArgsStructure('Bad generic args structure.');
                }
            });
        $this->FGenericArgs = array_replace($Args, $this->FGenericArgs);
        if ($this->FGenericArgs === null) {
            throw new EBadGenericArgsStructure('Bad generic args structure.');
        }
    }

    /**
     * Banned to call.
     * A {@link ENoSuchMethod} exception will be always thrown.
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws ENoSuchMethod
     */
    public final function __call($name, $arguments) {
        if ($this->$name instanceof TDelegate) {
            $mDelegate = $this->$name;
            return call_user_func_array($mDelegate->getDelegate(), $arguments);
        }
        throw new ENoSuchMethod(sprintf('No such method: %s->%s.', get_class(), $name), null, $this, $name);
    }

    /**
     * Banned to call.
     * A {@link ENoSuchMethod} exception will be always thrown.
     *
     * @param $name string
     * @param $arguments array
     * @throws ENoSuchMethod
     * @throws ENoSuchEnumElement
     * @throws ENoSuchSetElement
     * @return mixed
     */
    public static final function __callStatic($name, $arguments) {
        $mReflection  = new \ReflectionClass(get_called_class());
        $mIsEnumOrSet = true;
        $mIsEnum      = true;
        if (!$mReflection->isSubclassOf(new \ReflectionClass(TEnum::class))) {
            $mIsEnum      = false;
            $mIsEnumOrSet = false;
            if (!$mReflection->isSubclassOf(new \ReflectionClass(TSet::class))) {
                try {
                    $mReflection->getProperty($name);
                    if (static::$$name instanceof TDelegate) {
                        $mDelegate = static::$$name;
                        return call_user_func_array($mDelegate->getDelegate(), $arguments);
                    }
                }
                catch (\ReflectionException $Ex) {
                }
                throw new ENoSuchMethod(sprintf('No such method: %s::%s().', get_called_class(), $name), null, null, $name, self::ClassType());
            }
        }
        if ($mIsEnumOrSet) {
            if (!$mReflection->hasConstant($name)) {
                if ($mIsEnum) {
                    throw new ENoSuchEnumElement(sprintf('No such enum element: %s::%s().', $mReflection->getName(), $name), null, self::ClassType(), $name);
                }
                else {
                    throw new ENoSuchSetElement(sprintf('No such set element: %s::%s().', $mReflection->getName(), $name), null, self::ClassType(), $name);
                }
            }

            return $mReflection->newInstance($mReflection->getConstant($name));
        }
        else {
            return $name;
        }

    }

    /**
     * Banned to call.
     * A {@link ENoSuchMethod} exception will be always thrown.
     *
     * @param array $arr
     * @throws EAccessViolation
     */
    public static final function __set_state($arr) {
        throw new EAccessViolation('Access violation.');
    }

    /**
     * Banned to call.
     * A {@link ENoSuchField} exception will be always thrown.
     *
     * @param string $name
     * @throws ENoSuchField
     */
    public function __get($name) {
        throw new ENoSuchField(sprintf('No such field: %s->%s.', get_class($this), $name), null, $this, $name);
    }

    /**
     * Banned to call.
     * A {@link ENoSuchField} exception will be always thrown.
     *
     * @param string $name
     * @param mixed $value
     * @throws ENoSuchField
     */
    public function __set($name, $value) {
        throw new ENoSuchField(sprintf('No such field: %s->%s.', get_class($this), $name), null, $this, $name);
    }

    /**
     * Banned to call.
     * A {@link ENoSuchField} exception will be always thrown.
     *
     * @param string $name
     * @throws ENoSuchField
     */
    public function __isset($name) {
        throw new ENoSuchField(sprintf('No such field: %s->%s.', get_class($this), $name), null, $this, $name);
    }

    /**
     * Banned to call.
     * A {@link ENoSuchField} exception will be always thrown.
     *
     * @param string $name
     * @throws ENoSuchField
     */
    public function __unset($name) {
        throw new ENoSuchField(sprintf('No such field: %s->%s.', get_class($this), $name), null, $this, $name);
    }

    /**
     * PHP magic method.
     */
    /** @noinspection PhpToStringReturnInspection */
    public function __toString() {
        throw new EInvalidStringCasting();
    }

    /**
     * PHP magic method.
     */
    public function __invoke() {
        throw new ENoSuchMethod(sprintf('Object not invokable as of type: %s.', get_class($this)), null, $this, '__invoke');
    }

    /**
     * never call this method by yourself.
     *
     * @return \FrameworkDSW\Containers\IIterator <T: T>
     * @throws \FrameworkDSW\System\EAccessViolation
     */
    public function getIterator() {
        if ($this->Supports(Framework::Type(IIteratorAggregate::class))) {
            return $this->Iterator();
        }
        else {
            throw new EAccessViolation(sprintf('Class must supports FrameworkDSW\System\IIteratorAggregate: %s.', get_class($this)));
        }
    }

    /**
     * @throws ERuntimeException
     */
    protected static function EnsureSingleton() {
        $mDummy = new \Exception();
        $mTrace = $mDummy->getTrace();

        if (!isset($mTrace[2]) || $mTrace[2]['type'] === '->' || $mTrace[2]['class'] !== get_called_class()) {
            TClass::PrepareGeneric(['T' => get_called_class()]);
            $mClass = new TClass();
            throw new EConstructorInvocationNotAllowed(sprintf('Constructor invocation denied: %s.', get_called_class()), null, $mClass);
        }
    }
}

/**
 * \FrameworkDSW\System\TRecord
 *
 * @author 许子健
 */
abstract class TRecord extends TObject {

    /**
     * Duplicate a record.
     *
     * @return TRecord
     */
    public final function Duplicate() {
        return clone $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @param \FrameworkDSW\System\IInterface $Obj
     * @return boolean
     * @see TObject::Equals()
     */
    public final function Equals($Obj) {
        TType::Object($Obj, $this->ObjectType()->GenericArg('T'));
        foreach ($Obj as $mField => &$mValue) {
            if ($this->$mField !== $mValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * PHP magic method.
     */
    public final function __clone() {
        // prevent modifying by children classed.
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
 * if ($mColor1 instanceof THappyColor) { echo 'I am a value in THappyColor.'; }
 * if ($mColor1 != $mColor2) { echo 'It is not the same.'; }
 * if ($mColor1 == THappyColor::clRed()) { echo 'I am red.'; }
 * echo "The code of red is {$mColor1->Value()}.";
 * </code>
 * never compare TEnum by using '===' operator.
 *
 * @author 许子健
 */
abstract class TEnum extends TObject {
    /**
     *
     * @var mixed
     */
    protected $FValue = null;

    /**
     *
     * @param mixed $Value
     */
    public final function __construct($Value) {
        $this->FValue = $Value;
    }

    /**
     * desc...
     */
    public final function __destruct() {
        //nothing.
    }

    /**
     *
     * @return mixed
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
 *
 * @author 许子健
 */
abstract class TSet extends TObject {
    /**
     *
     * @var array
     */
    private $FSet = [];

    /**
     * constructor.
     */
    public final function __construct() {
        $mReflection = new \ReflectionObject($this);
        foreach ($mReflection->getConstants() as $mElement) {
            $this->FSet[$mElement] = false;
        }
    }

    /**
     *
     * @return array
     */
    protected final function FetchContent() {
        return $this->FSet;
    }

    /**
     *
     * @param string $Element
     * @throws ENoSuchSetElement
     */
    public final function In($Element) {
        if (!array_key_exists($Element, $this->FSet)) {
            TClass::PrepareGeneric(['T' => get_class($this)]);
            $mSet = new TClass();
            throw new ENoSuchSetElement(sprintf('No such set element: %s::%s.', get_class($this), $Element), null, $mSet, $Element);
        }
        $this->FSet[$Element] = true;
    }

    /**
     *
     * @param string $Element
     * @throws ENoSuchSetElement
     */
    public final function Out($Element) {
        if (!array_key_exists($Element, $this->FSet)) {
            TClass::PrepareGeneric(['T' => get_class($this)]);
            $mSet = new TClass();
            throw new ENoSuchSetElement(sprintf('No such set element: %s::%s.', get_class($this), $Element), null, $mSet, $Element);
        }
        $this->FSet[$Element] = false;
    }

    /**
     *
     * @param string $Element
     * @throws ENoSuchSetElement
     * @return boolean
     */
    public final function IsIn($Element) {
        if (!array_key_exists($Element, $this->FSet)) {
            TClass::PrepareGeneric(['T' => get_class($this)]);
            $mSet = new TClass();
            throw new ENoSuchSetElement(sprintf('No such set element: %s::%s.', get_class($this), $Element), null, $mSet, $Element);
        }

        return $this->FSet[$Element];
    }

    /**
     *
     * @param \FrameworkDSW\System\TSet $Set
     */
    public final function Union($Set) {
        TType::Object($Set, get_called_class());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            $this->FSet[$mName] = $mValue || $this->FSet[$mName];
        }
    }

    /**
     *
     * @param \FrameworkDSW\System\TSet $Set
     */
    public final function Subtract($Set) {
        TType::Object($Set, get_called_class());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            $this->FSet[$mName] = !($mValue && $this->FSet[$mName]);
        }
    }

    /**
     *
     * @param \FrameworkDSW\System\TSet $Set
     */
    public final function Intersect($Set) {
        TType::Object($Set, get_called_class());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            $this->FSet[$mName] = $mValue && $this->FSet[$mName];
        }
    }

    /**
     *
     * @param \FrameworkDSW\System\TSet $Set
     * @return boolean
     */
    public final function IsSubsetOf($Set) {
        TType::Object($Set, get_called_class());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            if (!$mValue && $this->FSet[$mName]) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param \FrameworkDSW\System\TSet $Set
     * @return boolean
     */
    public final function IsSuperSetOf($Set) {
        TType::Object($Set, get_called_class());
        foreach ($Set->FetchContent() as $mName => $mValue) {
            if ($mName && !$this->FSet[$mName]) {
                return false;
            }
        }

        return true;
    }

    // TODO: to store fixed length hash codes of each elements instead of an array.
    // TODO: store the set by using bit-mask for efficiency.
}

/**
 * IDelegate
 *
 * @author 许子健
 */
interface IDelegate extends IInterface { /* public function Invoke(...); */
}

/**
 * TDelegate
 * <code>
 * $mDelegate = new TDelegate(array($mSomeObj, 'DoSomething'),
 * 'TSomeDelegateType');
 * $mDelegate->setDelegate('SomeFunction'); //'SomeFunction' is a defined
 * function name.
 * $mDelegate->setDelegate(function($Param1, $Param2, ...) {...});
 * $mResult = $mDelegate($Param1, $Param2, ...);
 * </code>
 *
 * @author 许子健
 */
final class TDelegate {
    /**
     *
     * @var mixed an array or string
     */
    private $FDelegate = null;
    /**
     *
     * @var integer
     */
    private $FAtLeast = -1;
    /**
     *
     * @var integer
     */
    private $FNoMoreThan = -1;

    /**
     *
     * @param mixed $Callback string or closure
     * @param string $Type
     * @throws ENoSuchType
     */
    public final function __construct($Callback, $Type) {
        try {
            $mPrototype        = new \ReflectionMethod($Type, 'Invoke');
            $this->FAtLeast    = $mPrototype->getNumberOfRequiredParameters();
            $this->FNoMoreThan = $mPrototype->getNumberOfParameters();
            $this->setDelegate($Callback);
        }
        catch (\ReflectionException $e) {
            throw new ENoSuchType(sprintf('No such type: %s.', $Type), null, $Type);
        }
    }

    /**
     *
     * @return mixed
     */
    public final function __invoke() {
        return call_user_func_array($this->FDelegate, func_get_args());
    }

    /**
     *
     * @return mixed
     */
    public final function getDelegate() {
        return $this->FDelegate;
    }

    /**
     *
     * @param mixed $Callback
     * @throws EIncompatibleTypeAssignment
     */
    public final function setDelegate($Callback) {
        if (is_callable($Callback)) {
            if (is_string($Callback) || $Callback instanceof \Closure) {
                $mCallback = new \ReflectionFunction($Callback);
            }
            else { // then it must be an array if callable
                $mCallback = new \ReflectionMethod($Callback[0], $Callback[1]);
            }

            $mNumber = $mCallback->getNumberOfParameters();
            if ($mNumber >= $this->FAtLeast && $mNumber <= $this->FNoMoreThan) {
                $this->FDelegate = $Callback;
            }
            else {
                throw new EIncompatibleTypeAssignment('Incompatible type assignment.', null, null, null);
            }
        }
        else {
            throw new EIncompatibleTypeAssignment('Incompatible type assignment.', null, null, null);
        }
    }
}

/**
 * IPrimitive
 * param    T
 *
 * @author 许子健
 */
interface IPrimitive extends IInterface {

    /**
     * descHere
     *
     * @param T $Value
     */
    public function __construct($Value);

    /**
     * descHere
     *
     * @param T $Value
     */
    public function Box($Value);

    /**
     * descHere
     *
     * @param mixed $Value
     * @return T
     */
    public static function ConvertFrom($Value);

    /**
     * descHere
     *
     * @return T
     */
    public function Unbox();

    /**
     * descHere
     *
     * @return boolean
     */
    public function UnboxToBoolean();

    /**
     * descHere
     *
     * @return float
     */
    public function UnboxToFloat();

    /**
     * descHere
     *
     * @return integer
     */
    public function UnboxToInteger();

    /**
     * descHere
     *
     * @return string
     */
    public function UnboxToString();
}

/**
 * IComparable
 * param    T
 *
 * @author 许子健
 */
interface IComparable extends IInterface {

    /**
     * descHere
     *
     * @param T $Value
     * @return integer
     */
    public function CompareTo($Value);
}

/**
 * TBoolean
 * extends IPrimitive<T: boolean>, IComparable<T: TBoolean>
 *
 * @author 许子健
 */
final class TBoolean extends TObject implements IPrimitive, IComparable {
    /**
     *
     * @var boolean
     */
    private $FValue = false;

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function __construct($Value = false) {
        parent::__construct();
        $this->Box($Value);
    }

    /**
     * descHere
     *
     * @param boolean $Value
     */
    public function Box($Value) {
        TType::Bool($Value);
        $this->FValue = $Value;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\System\TBoolean $Value
     * @return integer
     */
    public function CompareTo($Value) {
        TType::Object($Value, TBoolean::class);
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
     *
     * @param mixed $Value
     * @return mixed
     */
    public static function ConvertFrom($Value) {
        return (boolean)$Value;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function Unbox() {
        return $this->FValue;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function UnboxToBoolean() {
        return $this->FValue;
    }

    /**
     * descHere
     *
     * @return float
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
     *
     * @return integer
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
     *
     * @return string
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
 * extends \FrameworkDSW\System\IComparable<T: \FrameworkDSW\System\TInteger>, \FrameworkDSW\System\IPrimitive<T: integer>
 *
 * @author 许子健
 */
final class TInteger extends TObject implements IComparable, IPrimitive {
    /**
     *
     * @var integer
     */
    private $FValue = 0;

    /**
     * descHere
     *
     * @param integer $Value
     */
    public function __construct($Value = 0) {
        parent::__construct();
        $this->Box($Value);
    }

    /**
     * descHere
     *
     * @param integer $Value
     */
    public function Box($Value) {
        TType::Int($Value);
        $this->FValue = $Value;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\System\TInteger $Value
     * @return integer
     */
    public function CompareTo($Value) {
        TType::Object($Value, TInteger::class);

        return $this->FValue - $Value->Unbox();
    }

    /**
     * descHere
     *
     * @param mixed $Value
     * @return integer
     */
    public static function ConvertFrom($Value) {
        return (integer)$Value;
    }

    /**
     * descHere
     *
     * @return integer
     */
    public function Unbox() {
        return $this->FValue;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function UnboxToBoolean() {
        return $this->FValue != 0;
    }

    /**
     * descHere
     *
     * @return float
     */
    public function UnboxToFloat() {
        return (float)$this->FValue;
    }

    /**
     * descHere
     *
     * @return integer
     */
    public function UnboxToInteger() {
        return $this->FValue;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function UnboxToString() {
        return (string)$this->FValue;
    }
}

/**
 * TFloat
 * extends    \FrameworkDSW\System\IPrimitive<T: float>, \FrameworkDSW\System\IComparable<T: \FrameworkDSW\System\TFloat>
 *
 * @author 许子健
 */
final class TFloat extends TObject implements IPrimitive, IComparable {
    /**
     *
     * @var float
     */
    private $FValue = 0.0;

    /**
     * descHere
     *
     * @param float $Value
     */
    public function __construct($Value = 0.0) {
        parent::__construct();
        $this->Box($Value);
    }

    /**
     * descHere
     *
     * @param float $Value
     */
    public function Box($Value) {
        TType::Float($Value);
        $this->FValue = $Value;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\System\TFloat $Value
     * @return integer
     */
    public function CompareTo($Value) {
        TType::Object($Value, TFloat::class);

        return (float)($this->FValue - $Value->Unbox());
    }

    /**
     * descHere
     *
     * @param mixed $Value
     * @return float
     */
    public static function ConvertFrom($Value) {
        return (float)$Value;
    }

    /**
     * descHere
     *
     * @return float
     */
    public function Unbox() {
        return $this->FValue;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function UnboxToBoolean() {
        return (boolean)$this->FValue;
    }

    /**
     * descHere
     *
     * @return float
     */
    public function UnboxToFloat() {
        return $this->FValue;
    }

    /**
     * descHere
     *
     * @return integer
     */
    public function UnboxToInteger() {
        return (integer)$this->FValue;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function UnboxToString() {
        return (string)$this->FValue;
    }
}

/**
 * \FrameworkDSW\System\TString
 * extends    \FrameworkDSW\System\IComparable<T: \FrameworkDSW\System\TString>, \FrameworkDSW\System\IPrimitive<T: string>
 *
 * @author 许子健
 */
final class TString extends TObject implements IComparable, IPrimitive {
    /**
     *
     * @var string
     */
    private $FValue = '';

    /**
     * descHere
     *
     * @param string $Value
     */
    public function __construct($Value = '') {
        parent::__construct();
        $this->Box($Value);
    }

    /**
     * descHere
     *
     * @param string $Value
     */
    public function Box($Value) {
        TType::String($Value);
        $this->FValue = $Value;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\System\TString $Value
     * @return integer
     */
    public function CompareTo($Value) {
        return strcmp($this->FValue, $Value->Unbox());
    }

    /**
     * descHere
     *
     * @param mixed $Value
     * @return string
     */
    public static function ConvertFrom($Value) {
        return (string)$Value;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function Unbox() {
        return $this->FValue;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function UnboxToBoolean() {
        return (boolean)$this->FValue;
    }

    /**
     * descHere
     *
     * @return float
     */
    public function UnboxToFloat() {
        return (float)$this->FValue;
    }

    /**
     * descHere
     *
     * @return integer
     */
    public function UnboxToInteger() {
        return (integer)$this->FValue;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function UnboxToString() {
        return $this->FValue;
    }
}

/**
 * Class TExceptionHandler
 * thanks to: Yii 2.0: yii\base\ErrorHandler
 * @package FrameworkDSW\System
 */
abstract class TExceptionHandler extends TObject {
    /**
     * @var EException
     */
    private $FException = null;

    /**
     *
     */
    public function Register() {
        set_exception_handler([$this, 'HandleException']);
    }

    /**
     * @param \FrameworkDSW\System\EException $Exception
     */
    public function HandleException($Exception) {
        $this->FException = $Exception;

        restore_error_handler();
        restore_exception_handler();
        try {
            $this->RenderException($Exception);
            if (Framework::IsRelease()) {
                exit(1);
            }
        }
        catch (EException $Ex) {
            $mMessage = "{$Ex}\nPrevious exception:\n{$Exception}";
            if (Framework::IsDebug()) {
                if (PHP_SAPI === 'cli') {
                    echo "{$mMessage}\n";
                }
                else {
                    echo '<pre>' . htmlspecialchars($mMessage, ENT_QUOTES) . '</pre>';
                }
            }
            $mMessage .= "\n\$_SERVER = " . var_export($_SERVER, true);
            error_log($mMessage);
            exit(1);
        }

        $this->FException = null;
    }

    /**
     * @param \FrameworkDSW\System\EException $Exception
     */
    abstract protected function RenderException($Exception);
}