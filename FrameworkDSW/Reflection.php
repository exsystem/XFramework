<?php
/**
 * \FrameworkDSW\Reflection
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 62
 */
namespace FrameworkDSW\Reflection;

use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EError;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\IDelegate;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TBoolean;
use FrameworkDSW\System\TEnum;
use FrameworkDSW\System\TFloat;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TRecord;
use FrameworkDSW\System\TSet;
use FrameworkDSW\System\TString;
use FrameworkDSW\Utilities\EInvalidObjectCasting;
use FrameworkDSW\Utilities\TType;

/**
 * Class EReflectionException
 * @package FrameworkDSW\Reflection
 */
class EReflectionException extends EException {
}

/**
 * Class EIllegalAccess
 * @package FrameworkDSW\Reflection
 */
class EIllegalAccess extends EReflectionException {
}

/**
 * Class ENoSuchNamespace
 * @package FrameworkDSW\Reflection
 */
class ENoSuchNamespace extends EReflectionException {

}

/**
 * Class ENoSuchTypeDefinition
 * @package FrameworkDSW\Reflection
 */
class ENoSuchTypeDefinition extends EReflectionException {
}

/**
 *
 * @author 许子健
 */
class ENoSuchFieldMember extends EReflectionException {
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
 * Class ENoSuchMethodMember
 * @package FrameworkDSW\Reflection
 */
class ENoSuchMethodMember extends EReflectionException {
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
 * Class ENoSuchPropertyMember
 * @package FrameworkDSW\Reflection
 */
class ENoSuchPropertyMember extends EReflectionException {
    /**
     * @var \FrameworkDSW\Reflection\TClass<T: ?>
     */
    private $FClass = null;
    /**
     * @var string
     */
    private $FPropertyName = '';
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    private $FObject = null;

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\System\IInterface $Object
     * @param string $PropertyName
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     */
    public function __construct($Message, $Previous, $Object, $PropertyName, $Class = null) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($Object, IInterface::class);
        TType::String($PropertyName);
        TType::Object($Class, [TClass::class => ['T' => null]]);

        $this->FObject       = $Object;
        $this->FPropertyName = $PropertyName;
        $this->FClass        = $Class;
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
    public function getPropertyName() {
        return $this->FPropertyName;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getObject() {
        return $this->FObject;
    }
}

/**
 * Class ENoSuchSignalMember
 * @package FrameworkDSW\Reflection
 */
class ENoSuchSignalMember extends EReflectionException {
    /**
     * @var \FrameworkDSW\Reflection\TClass<T: ?>
     */
    private $FClass = null;
    /**
     * @var string
     */
    private $FSignalName = '';
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    private $FObject = null;

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
     * @return string
     */
    public function getSignalName() {
        return $this->FSignalName;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getObject() {
        return $this->FObject;
    }
}

/**
 * Class ENoSuchSlotMember
 * @package FrameworkDSW\Reflection
 */
class ENoSuchSlotMember extends EReflectionException {
    /**
     * @var \FrameworkDSW\Reflection\TClass<T: ?>
     */
    private $FClass = null;
    /**
     * @var string
     */
    private $FSlotName = '';
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    private $FObject = null;

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\System\IInterface $Object
     * @param string $SlotName
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     */
    public function __construct($Message, $Previous, $Object, $SlotName, $Class = null) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Object($Object, IInterface::class);
        TType::String($SlotName);
        TType::Object($Class, [TClass::class => ['T' => null]]);

        $this->FObject   = $Object;
        $this->FSlotName = $SlotName;
        $this->FClass    = $Class;
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
    public function getSlotName() {
        return $this->FSlotName;
    }

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function getObject() {
        return $this->FObject;
    }
}

/**
 * Class EInstantiation
 * @package FrameworkDSW\Reflection
 */
class EInstantiation extends EReflectionException {
}

class EInvocationTarget extends EReflectionException {
}

/**
 * Class EBadTypeDefinition
 * @package FrameworkDSW\Reflection
 */
class EBadTypeDefinition extends EError {
    /**
     * @var string
     */
    private $FTypeDefinition = '';

    /**
     * @return string
     */
    public function getDoc() {
        return $this->FTypeDefinition;
    }

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param string $TypeDefinition
     */
    public function __construct($Message = '', $Previous = null, $TypeDefinition = '') {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::String($TypeDefinition);

        $this->FTypeDefinition = $TypeDefinition;
    }
}

/**
 * \FrameworkDSW\Reflection\TModifiers
 * @author 许子健
 */
class TModifiers extends TSet {

    /**
     * @var integer
     */
    const eAbstract = 3;
    /**
     * @var integer
     */
    const eConst = 7;
    /**
     * @var integer
     */
    const eFinal = 4;
    /**
     * @var integer
     */
    const eInterface = 6;
    /**
     * @var integer
     */
    const ePrivate = 0;
    /**
     * @var integer
     */
    const eProtected = 1;
    /**
     * @var integer
     */
    const ePublic = 2;
    /**
     * @var integer
     */
    const eStatic = 5;
}

/**
 * \FrameworkDSW\Reflection\IType
 * @author 许子健
 */
interface IType extends IInterface {

}

/**
 * \FrameworkDSW\Reflection\IMember
 * @author 许子健
 */
interface IMember extends IInterface {

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getDeclaringClass();

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TModifiers
     */
    public function getModifiers();

    /**
     * descHere
     * @return string
     */
    public function getName();
}

/**
 * \FrameworkDSW\Reflection\TNamespace\TArray
 * @author 许子健
 */
class TArray extends TRecord {
    /**
     *
     * @var array
     */
    public $Array = [];
}

/**
 * \FrameworkDSW\Reflection\TNamespace
 * @author 许子健
 */
class TNamespace extends TObject {
    /**
     *
     * @var string
     */
    private $FNamespace = '';

    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\Reflection\TNamespace>
     */
    private static $FNamespaces = null;

    /**
     * descHere
     * @param string $Namespace
     */
    public function __construct($Namespace) {
        parent::__construct();
        TType::String($Namespace);
        parent::EnsureSingleton();

        $this->FNamespace = $Namespace;
    }

    /**
     * descHere
     * @return string
     */
    public function getName() {
        return $this->FNamespace;
    }

    /**
     * descHere
     * @param string $Namespace
     * @throws ENoSuchNamespace
     * @return \FrameworkDSW\Reflection\TNamespace
     */
    public static function getNamespace($Namespace) {
        TType::String($Namespace);
        if (self::$FNamespaces === null) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => TNamespace::class]);
            self::$FNamespaces = new TMap(true);
        }

        if (self::$FNamespaces->ContainsKey($Namespace)) {
            return self::$FNamespaces[$Namespace];
        }

        $mTemp = "{$Namespace}\\";
        foreach (get_declared_classes() as $mClassName) {
            if (strpos($mClassName, $mTemp) === 0) {
                $mNamespace                    = new TNamespace($Namespace);
                self::$FNamespaces[$Namespace] = $mNamespace;

                return $mNamespace;
            }
        }
        throw new ENoSuchNamespace(sprintf('No such namespace: "%s".', $Namespace));
    }
}

/**
 * \FrameworkDSW\Reflection\TClass
 * params <T: ?>
 * @author 许子健
 */
class TClass extends TObject implements IType {
    /**
     *
     * @var \ReflectionClass
     */
    private $FMetaInfo = null;
    /**
     *
     * @var string
     */
    private $FClassName = '';
    /**
     *
     * @var boolean
     */
    private $FIsPrimitive = false;
    /**
     *
     * @var boolean
     */
    private $FIsArray = false;
    /**
     *
     * @var boolean
     */
    private $FIsRecord = false;
    /**
     *
     * @var boolean
     */
    private $FIsEnum = false;
    /**
     *
     * @var boolean
     */
    private $FIsSet = false;
    /**
     *
     * @var boolean
     */
    private $FIsDelegate = false;
    /**
     * @var boolean
     */
    private $FIsType = false;
    /**
     * @var array
     */
    private $FExtendsInfo = [];

    /**
     *
     *
     */
    private function EnsureType() {
        if (!$this->FIsType) {
            throw new EIllegalAccess(sprintf('Illegal access: "%s" is neither a class or a record type.', $this->FClassName));
        }
    }

    /**
     * @return array
     */
    private function getExtendsInfo() {
        if ($this->FExtendsInfo === []) {
            $mDocComment = $this->getMetaInfo()->getDocComment();
            $mExtends    = [];
            if ($mDocComment !== false) {
                $mDocComment = explode("\n", $mDocComment);
                if (isset($mDocComment[3]) && substr($mDocComment[3] = trim($mDocComment[3]), 0, 10) === '* extends ') {
                    $mDocComment = substr($mDocComment[3], 10);
                    $mTokens     = preg_split('/([a-zA-Z_\x7f-\xff\x5c][a-zA-Z0-9_\x7f-\xff\x5c\[\]]*)|(<)|(: )|(>)|(\?)|(\, )/', $mDocComment, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                    $mPairCount  = 0;
                    foreach ($mTokens as $mIndex => &$mToken) {
                        switch ($mToken) {
                            case '<':
                                $mToken = '=>[';
                                break;
                            case '>':
                                if ($mPairCount > 0) {
                                    --$mPairCount;
                                    $mToken = ']]';
                                }
                                else {
                                    $mToken = ']';
                                }
                                break;
                            case ': ':
                                if (isset($mTokens[$mIndex + 2]) && ($mTokens[$mIndex + 2] === '<')) {
                                    $mToken = '=>[';
                                    ++$mPairCount;
                                }
                                else {
                                    $mToken = '=>';
                                }
                                break;
                            case '?':
                                $mToken = '\'mixed\'';
                                break;
                            case ', ':
                                break;
                            default:
                                if ($mToken[0] == '\\') {
                                    $mToken = substr($mToken, 1);
                                }
                                $mToken = "'{$mToken}'";
                                break;
                        }
                    }
                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $mGenericsExpression = implode('', $mTokens);
                    $mExtends            = eval("return [{$mGenericsExpression}];");
                    if (isset($this->GenericArg('T')[$this->FClassName])) {
                        $mGenericsArgs = $this->GenericArg('T')[$this->FClassName];
                        array_walk_recursive($mExtends, function (&$Type) use ($mGenericsArgs) {
                            if (isset($mGenericsArgs[$Type])) {
                                $Type = $mGenericsArgs[$Type];
                            }
                        });
                    }
                }
            }
            $this->FExtendsInfo = $mExtends;
        }

        return $this->FExtendsInfo;
    }

    /**
     *
     * @param string $Name
     * @return boolean
     */
    private function CheckMethodName($Name) {
        $mInitialLetterAscii = ord($Name[0]);

        return (($mInitialLetterAscii >= ord('A')) && ($mInitialLetterAscii <= ord('Z')));
    }

    /**
     *
     * @return \ReflectionClass
     */
    private function getMetaInfo() {
        if ($this->FMetaInfo === null) {
            $mRaw = $this->GenericArg('T');
            if (!is_string($mRaw)) {
                $mRaw = array_keys($mRaw)[0];
            }
            $this->FMetaInfo = new \ReflectionClass($mRaw);
        }

        return $this->FMetaInfo;
    }

    /**
     * descHere
     */
    public function __construct() {
        parent::__construct();

        $mTemp = $this->GenericArg('T');
        if (is_array($mTemp)) {
            $mTemp = array_keys($mTemp)[0];
        }

        if (strrpos($mTemp, ']', -1) !== false) {
            $this->FIsArray   = true;
            $this->FClassName = $mTemp;
        }
        elseif (($mTemp == Framework::Boolean) || ($mTemp == Framework::Integer) || ($mTemp == Framework::Float) || ($mTemp == Framework::String)) {
            $this->FIsPrimitive = true;
            $this->FClassName   = $mTemp;
        }
        elseif (isset(class_implements($mTemp)[IDelegate::class])) {
            $this->FIsDelegate = true;
            $this->FClassName  = $mTemp;
        }
        else {
            $mRawParentClass = (string)get_parent_class($mTemp);
            switch ($mRawParentClass) {
                case TRecord::class:
                    $this->FIsRecord = true;
                    $this->FIsType   = true;
                    break;
                case TEnum::class:
                    $this->FIsEnum = true;
                    break;
                case TSet::class:
                    $this->FIsSet = true;
                    break;
                default:
                    if ((class_exists($mTemp, true) === false) && (interface_exists($mTemp, true) === false)) {
                        throw new ENoSuchTypeDefinition(sprintf('No such type: "%s".', $mTemp));
                    }
                    $this->FIsType = true;
                    break;
            }
            $this->FClassName = $mTemp;
        }
        if ($this->FClassName[0] == '\\') {
            $this->FClassName = substr($this->FClassName, 1);
        }
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface $Object
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @return T
     */
    public function Cast($Object) {
        TType::Object($Object, IInterface::class);
        $this->EnsureType();
        if ($Object === null || $Object->IsInstanceOf($this->GenericArg('T'))) {
            return $Object;
        }
        else {
            throw new EInvalidObjectCasting();
        }
    }

    /**
     * descHere
     * @throws EIllegalAccess
     * @return \FrameworkDSW\Reflection\TConstructor <T: T>
     */
    public function getConstructor() {
        if ($this->FIsRecord) {
            throw new EIllegalAccess(sprintf('Illegal access: constructor is only for class types, not for record "%s".', $this->FClassName));
        }

        TConstructor::PrepareGeneric($this->GenericArgs());
        return new TConstructor();
    }

    /**
     * descHere
     * @param string $Name
     * @throws ENoSuchFieldMember
     * @return \FrameworkDSW\Reflection\TField
     */
    public function GetDeclaredField($Name) {
        TType::String($Name);
        $this->EnsureType();

        try {
            $mRaw = $this->getMetaInfo()->getProperty($Name);
            if ($mRaw->getDeclaringClass()->getName() !== $this->getName()) {
                throw new ENoSuchFieldMember(sprintf('No such field member: "%s".', $Name), null, null, $Name, $this);
            }

            return new TField($this, $Name);
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchFieldMember(sprintf('No such field member: "%s".', $Name), null, null, $Name, $this);
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TField[]
     */
    public function getDeclaredFields() {
        $this->EnsureType();

        $mFields = $this->getMetaInfo()->getProperties();
        $mResult = [];
        foreach ($mFields as $mField) {
            if ($mField->getDeclaringClass()->getName() === $this->getMetaInfo()->getName()) {
                $mResult[] = new TField($this, $mField->getName());
            }
        }

        return $mResult;
    }

    /**
     * descHere
     * @param string $Name
     * @throws ENoSuchMethodMember
     * @return \FrameworkDSW\Reflection\TMethod
     */
    public function GetDeclaredMethod($Name) {
        TType::String($Name);
        $this->EnsureType();
        if ((!$this->CheckMethodName($Name)) || ($this->getMetaInfo()->getMethod($Name)->getDeclaringClass()->getName() !== $this->FClassName)) {
            throw new ENoSuchMethodMember(sprintf('No such method member: "%s".', $Name), null, null, $Name, $this);
        }

        try {
            return new TMethod($this, $Name);
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchMethodMember(sprintf('No such method member: "%s".', $Name), null, null, $Name, $this);
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TMethod[]
     */
    public function getDeclaredMethods() {
        $this->EnsureType();
        $mMethods = $this->getMetaInfo()->getMethods();
        $mResult  = [];
        foreach ($mMethods as $mMethod) {
            $mName = $mMethod->getName();
            if ($this->CheckMethodName($mName) && ($mMethod->getDeclaringClass()->getName() === $this->FClassName)) {
                $mResult[] = new TMethod($this, $mName);
            }
        }

        return $mResult;
    }

    /**
     * descHere
     * @throws EIllegalAccess
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getElementType() {
        if (!$this->FIsArray) {
            throw new EIllegalAccess(sprintf('Illegal access: not an array.'));
        }
        $mRawElementType      = substr($this->FClassName, 0, -2);
        $mOriginalGenericArgT = $this->GenericArg('T');
        if (is_array($mOriginalGenericArgT)) {
            $mGenericArgs = ['T' => [$mRawElementType => $mOriginalGenericArgT[$this->FClassName]]];
        }
        else {
            $mGenericArgs = ['T' => $mRawElementType];
        }
        TClass::PrepareGeneric($mGenericArgs);

        return new TClass();
    }

    /**
     * descHere
     * @throws EIllegalAccess
     * @return \FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\IType>
     */
    public function getGenericsValues() {
        if (!($this->FIsType || $this->FIsArray)) {
            throw new EIllegalAccess(sprintf('Illegal access: not a class, interface or record type'));
        }

        $mGenericArgs = $this->GenericArg('T');
        if (is_array($mGenericArgs)) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => IType::class]);
            $mResult      = new TMap(true);
            $mGenericArgs = $mGenericArgs[$this->FClassName];
            foreach ($mGenericArgs as $mName => $mType) {
                TClass::PrepareGeneric(['T' => $mType]);
                $mResult[$mName] = new TClass();
            }

            return $mResult;
        }
        else {
            return null;
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TClass[] <T: ?>
     */
    public function getInterfaces() {
        $this->EnsureType();
        $mResult            = [];
        $mRawInterfaceNames = $this->getMetaInfo()->getInterfaceNames();
        $mImplements        = $this->getExtendsInfo();
        foreach ($mRawInterfaceNames as $mRawInterfaceName) {
            if (isset($mImplements[$mRawInterfaceName])) {
                $mInterface = [$mRawInterfaceName => $mImplements[$mRawInterfaceName]];
            }
            else {
                $mInterface = $mRawInterfaceName;
            }
            TClass::PrepareGeneric(['T' => $mInterface]);
            $mResult[] = new TClass();
        }

        return $mResult;
    }

    /**
     * @param string $Name
     * @return \FrameworkDSW\Reflection\TField
     */
    public function GetField($Name) {
        TType::String($Name);
        $this->EnsureType();

        return new TField($this, $Name);
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TField[]
     */
    public function getFields() {
        $this->EnsureType();
        $mFields = $this->getMetaInfo()->getProperties();
        $mResult = [];
        foreach ($mFields as $mField) {
            $mName     = $mField->getName();
            $mResult[] = new TField($this, $mName);
        }

        return $mResult;
    }

    /**
     * descHere
     * @param string $Name
     * @throws ENoSuchMethodMember
     * @return \FrameworkDSW\Reflection\TMethod
     */
    public function GetMethod($Name) {
        TType::String($Name);
        $this->EnsureType();
        if ($this->CheckMethodName($Name)) {
            return new TMethod($this, $Name);
        }
        else {
            throw new ENoSuchMethodMember(sprintf('No such method member: "%s".', $Name), null, null, $Name, $this);
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TMethod[]
     */
    public function getMethods() {
        $this->EnsureType();
        $mMethods = $this->getMetaInfo()->getMethods();
        $mResult  = [];
        foreach ($mMethods as $mMethod) {
            $mName = $mMethod->getName();
            if ($this->CheckMethodName($mName)) {
                $mResult[] = new TMethod($this, $mName);
            }
        }

        return $mResult;
    }

    /**
     * descHere
     * @return string
     */
    public function getName() {
        return $this->FClassName;
    }

    /**
     * descHere
     * @throws EIllegalAccess
     * @return \FrameworkDSW\Reflection\TNamespace
     */
    public function getNamespace() {
        if ($this->FIsArray || $this->FIsPrimitive) {
            throw new EIllegalAccess(sprintf('Illegal access: namespace inapplicable.'));
        }
        else {
            return TNamespace::getNamespace($this->getMetaInfo()->getNamespaceName());
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getParentClass() {
        $this->EnsureType();
        $mParentClassMetaInfo = $this->getMetaInfo()->getParentClass();
        if ($mParentClassMetaInfo === false) {
            return null;
        }
        else {
            TClass::PrepareGeneric(['T' => [$mParentClassMetaInfo->getName() => $this->getExtendsInfo()[$mParentClassMetaInfo->getName()]]]);

            return new TClass();
        }
    }

    /**
     * descHere
     * @return string
     */
    public function getSimpleName() {
        $this->EnsureType();

        return $this->getMetaInfo()->getShortName();
    }

    /**
     * descHere
     * @return boolean
     */
    public function IsArray() {
        return $this->FIsArray;
    }

    /**
     * descHere
     * @return boolean
     */
    public function IsClass() {
        return $this->FIsType && (!$this->getMetaInfo()->isInterface());
    }

    /**
     * descHere
     * @return boolean
     */
    public function IsEnum() {
        return $this->FIsEnum;
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface $Object
     * @return boolean
     */
    public function IsInstance($Object) {
        try {
            TType::Object($Object, $this->GenericArg(['T']));
        }
        catch (EInvalidObjectCasting $Ex) {
            return false;
        }

        return true;
    }

    /**
     * descHere
     * @return boolean
     */
    public function IsInterface() {
        return $this->FIsType && $this->getMetaInfo()->isInterface();
    }

    /**
     * descHere
     * @return boolean
     */
    public function IsPrimitive() {
        return $this->FIsPrimitive;
    }

    /**
     * descHere
     * @return boolean
     */
    public function IsSetStruct() {
        return $this->FIsSet;
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface[] $Parameters
     * @throws EIllegalAccess
     * @return T
     */
    public function NewInstance($Parameters) {
        TType::Arr($Parameters);
        $this->EnsureType();
        if ($this->getMetaInfo()->isInterface()) {
            throw new EIllegalAccess(sprintf('Illegal access: instantiation failed by attempting using interface.'));
        }
        $mConstructor = $this->getConstructor();
        $mResult      = $mConstructor->NewInstance($Parameters);
        Framework::Free($mConstructor);

        return $mResult;
    }
}

/**
 * \FrameworkDSW\Reflection\TAbstractMember
 * @author 许子健
 */
abstract class TAbstractMember extends TObject {
    /**
     * @var string
     */
    protected $FName = '';
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    protected $FClass = null;
    /**
     * @var boolean
     */
    protected $FAccessible = false;
    /**
     * @var array
     */
    protected $FParameterTypes = [];
    /**
     * @var array
     */
    protected $FParameterValues = [];

    /**
     * @param string $DocComment
     * @throws EBadTypeDefinition
     */
    protected function ParseParameterTypes($DocComment) {
        $mLines           = [];
        $mDocCommentLines = explode("\n", $DocComment);
        foreach ($mDocCommentLines as $mLine) {
            $mLine = trim($mLine);
            if (substr($mLine, 0, 9) === '* @param ') {
                $mLine = substr($mLine, 9);
                if ($mLine === false || $mLine === '') {
                    throw new EBadTypeDefinition(sprintf('Bad type definition: parameter type expected, but end-of-line found.'), null, $DocComment);
                }
                $mPos = strpos($mLine, '$');
                if ($mPos === false) {
                    throw new EBadTypeDefinition(sprintf('Bad type definition: "$" expected for parameter name.'), null, $DocComment);
                }
                $mClassPartString = substr($mLine, 0, $mPos);
                if ($mClassPartString === false || $mClassPartString === '') {
                    throw new EBadTypeDefinition(sprintf('Bad type definition: parameter type of class part expected, but end-of-line found.'), null, $DocComment);
                }
                $mClassPartString = trim($mClassPartString);
                $mLine            = substr($mLine, $mPos + 1);
                if ($mLine === false || $mLine === '') {
                    throw new EBadTypeDefinition(sprintf('Bad type definition: parameter type of class part expected, but end-of-line found.'), null, $DocComment);
                }
                $mPos = strpos($mLine, ' ');
                if ($mPos === false || $mPos === strlen($mLine)) {
                    $mLines[] = [$mClassPartString, null];
                    continue;
                }
                $mLine = substr($mLine, $mPos + 1); //GENERICS ＆ COMMENTS
                if ($mLine === false || $mLine === '' || $mLine[0] !== '<') {
                    $mLines[] = [$mClassPartString, null];
                    continue;
                }
                $mGenericsPartString = strstr($mLine, '> ', true);
                if ($mGenericsPartString === false) {
                    if ($mLine[strlen($mLine)] === '>') {
                        $mGenericsPartString = $mLine;
                        $mLines[]            = [$mClassPartString, $mGenericsPartString];
                        continue;
                    }
                    $mLines[] = [$mClassPartString, $mGenericsPartString];
                    continue;
                }
                $mLines[] = [$mClassPartString, $mGenericsPartString];
            }
            elseif (substr($mLine, 0, 10) === '* @return ') {
                $mLine = substr($mLine, 10);
                if ($mLine === false || $mLine === '') {
                    throw new EBadTypeDefinition(sprintf('Bad type definition: return type expected, but end-of-line found.'), null, $DocComment);
                }
                $mPos = strpos($mLine, ' ');
                if ($mPos === false) {
                    $mPos = strlen($mLine);
                }
                $mClassPartString = substr($mLine, 0, $mPos);
                $mLine            = trim(substr($mLine, $mPos));
                if ($mLine === false || $mLine === '' || $mLine[0] !== '<') {
                    array_unshift($mLines, [$mClassPartString, null]);
                    continue;
                }
                $mGenericsPartString = strstr($mLine, '> ', true);
                if ($mGenericsPartString === false) {
                    if ($mLine[strlen($mLine)] === '>') {
                        $mGenericsPartString = $mLine;
                        array_push($mLines, [$mClassPartString, $mGenericsPartString]);
                        continue;
                    }
                    array_unshift($mLines, [$mClassPartString, null]);
                    continue;
                }
                array_unshift($mLines, [$mClassPartString, $mGenericsPartString]);
            }
            elseif (substr($mLine, 0, 7) === '* @var ') {
                $mLine = substr($mLine, 7);
                if ($mLine === false || $mLine === '') {
                    throw new EBadTypeDefinition(sprintf('Bad type definition: field type expected, but end-of-line found.'), null, $DocComment);
                }
                $mPos = strpos($mLine, ' ');
                if ($mPos === false) {
                    $mPos = strlen($mLine);
                }
                $mClassPartString = substr($mLine, 0, $mPos);
                $mLine            = trim(substr($mLine, $mPos));
                if ($mLine === false || $mLine === '' || $mLine[0] !== '<') {
                    $mLines[0] = [$mClassPartString, null];
                    continue;
                }
                $mGenericsPartString = strstr($mLine, '> ', true);
                if ($mGenericsPartString === false) {
                    if ($mLine[strlen($mLine)] === '>') {
                        $mGenericsPartString = $mLine;
                        $mLines[0]           = [$mClassPartString, $mGenericsPartString];
                        continue;
                    }
                    $mLines[0] = [$mClassPartString, null];
                    continue;
                }
                $mLines[0] = [$mClassPartString, "{$mGenericsPartString}>"];
            }
        }

        $this->FParameterTypes = [];
        foreach ($mLines as $mLine) {
            if ($mLine[0][0] == '\\') {
                $mLine[0] = substr($mLine[0], 1);
            }
            if ($mLine[1] !== null) {
                $mTokens    = preg_split('/([a-zA-Z_\x7f-\xff\x5c][a-zA-Z0-9_\x7f-\xff\x5c\[\]]*)|(<)|(: )|(>)|(\?)|(\, )/', $mLine[1], 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                $mPairCount = 0;
                foreach ($mTokens as $mIndex => &$mToken) {
                    switch ($mToken) {
                        case '<':
                            $mToken = '=>[';
                            break;
                        case '>':
                            if ($mPairCount > 0) {
                                --$mPairCount;
                                $mToken = ']]';
                            }
                            else {
                                $mToken = ']';
                            }
                            break;
                        case ': ':
                            if (isset($mTokens[$mIndex + 2]) && ($mTokens[$mIndex + 2] === '<')) {
                                $mToken = '=>[';
                                ++$mPairCount;
                            }
                            else {
                                $mToken = '=>';
                            }
                            break;
                        case '?':
                            $mToken = '\'mixed\'';
                            break;
                        case ', ':
                            break;
                        default:
                            if ($mToken[0] == '\\') {
                                $mToken = substr($mToken, 1);
                            }
                            $mToken = "'{$mToken}'";
                            break;
                    }
                }
                /** @noinspection PhpUnusedLocalVariableInspection */
                $mGenericsExpression = implode('', $mTokens);
                $mParameter          = eval("return ['{$mLine[0]}' {$mGenericsExpression}];");
            }
            else {
                $mParameter = $mLine[0];
            }
            $this->FParameterTypes[] = $mParameter;
        }
    }

    /**
     * @param string $DocComment
     */
    protected function ParseParameterValues($DocComment) {
        $this->ParseParameterTypes($DocComment);
        $this->FParameterValues = $this->FParameterTypes;
        if (isset($this->FClass->GenericArg('T')[$this->FClass->getName()])) {
            $mGenericsArgs = $this->FClass->GenericArg('T')[$this->FClass->getName()];
            //TODO additional generics args from methods/ctors. -- need declare something implements IType describing GENERICS.
            foreach ($this->FParameterValues as &$mParameter) {
                if (is_string($mParameter)) {
                    if (isset($mGenericsArgs[$mParameter])) {
                        $mParameter = $mGenericsArgs[$mParameter];
                        continue;
                    }
                }
                else {
                    array_walk_recursive($mParameter, function (&$Type) use ($mGenericsArgs) {
                        if (isset($mGenericsArgs[$Type])) {
                            $Type = $mGenericsArgs[$Type];
                        }
                    });
                }
            }
        }
    }

    /**
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @throws EIllegalAccess
     * @throws ENoSuchTypeDefinition
     * @return string
     */
    protected function SetClass($Class) {
        $mTemp = $Class->GenericArg('T');
        if (is_array($mTemp)) {
            $mTemp = array_keys($mTemp)[0];
        }

        if (strrpos($mTemp, ']', -1) !== false) {
            throw new EIllegalAccess(sprintf('Illegal access: class or record type expected, but array typed as "%s" found.'), $mTemp);
        }

        if (($mTemp == Framework::Boolean) || ($mTemp == Framework::Integer) || ($mTemp == Framework::Float) || ($mTemp == Framework::String)) {
            throw new EIllegalAccess(sprintf('Illegal access: class or record type expected, but %s type found.', $mTemp));
        }

        if (isset(class_implements($mTemp)[IDelegate::class])) {
            throw new EIllegalAccess(sprintf('Illegal access: class or record type expected, but delegate type "%s" found.', $mTemp));
        }

        $mRawParentClass = (string)get_parent_class($mTemp);
        switch ($mRawParentClass) {
            case TEnum::class:
                throw new EIllegalAccess(sprintf('Illegal access: class or record type expected, but enumeration type "%s" found.', $mTemp));
            case TSet::class:
                throw new EIllegalAccess(sprintf('Illegal access: class or record type expected, but set type "%s" found.', $mTemp));
            default:
                if ((class_exists($mTemp, true) === false) && (interface_exists($mTemp, true) === false)) {
                    throw new ENoSuchTypeDefinition(sprintf('No such class type: %s'), $mTemp);
                }
                break;
        }

        $this->FClass = $Class;

        return $mTemp;
    }

    /**
     * descHere
     * @return boolean
     */
    public function getAccessible() {
        return $this->FAccessible;
    }

    /**
     * descHere
     * @param boolean $Value
     */
    public function setAccessible($Value) {
        TType::Bool($Value);
        $this->FAccessible = true;
    }

    /**
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getDeclaringClass() {
        return $this->FClass;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->FName;
    }

}

/**
 * \FrameworkDSW\Reflection\TField
 * @author 许子健
 */
final class TField extends TAbstractMember implements IMember {
    /**
     * @var \ReflectionProperty
     */
    private $FMetaInfo = null;

    /**
     * descHere
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @param string $Name
     * @throws ENoSuchFieldMember
     */
    public function __construct($Class, $Name) {
        TType::Object($Class, [TClass::class => ['T' => null]]);
        TType::String($Name);

        try {
            $mClassMetaInfo  = new \ReflectionClass($Class->getName());
            $this->FMetaInfo = $mClassMetaInfo->getProperty($Name);
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchFieldMember(sprintf('No such field member: %s.'), null, null, $Name, $Class);
        }
        $this->SetClass($Class);
        $this->FName = $Name;
    }

    //     /**
    //      * descHere
    //      * @return	\FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\IType>
    //      */
    //     public function getGenericsTypes() {
    //     }

    /**
     * descHere
     * @throws EBadTypeDefinition
     * @return \FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\TClass<T: ?>>
     */
    public function getGenericsValues() {
        if ($this->FParameterValues == []) {
            $DocComment = $this->FMetaInfo->getDocComment();
            if ($DocComment === false) {
                throw new EBadTypeDefinition(sprintf('Bad type definition: undefined type of field "%s.'), $this->FName);
            }
            $this->ParseParameterValues($DocComment);
        }
        $mType = $this->FParameterValues[0];
        if (is_string($mType)) {
            return null;
        }
        else {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => IType::class]);
            $mResult = new TMap(true);
            $mRaw    = $mType[array_keys($mType)[0]];
            foreach ($mRaw as $mName => $mValue) {
                TClass::PrepareGeneric(['T' => $mValue]);
                $mResult[$mName] = new TClass();
            }

            return $mResult;
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TModifiers
     */
    public function getModifiers() {
        $mResult = new TModifiers();
        if ($this->FMetaInfo->isPrivate()) {
            $mResult->In(TModifiers::ePrivate);
        }
        elseif ($this->FMetaInfo->isProtected()) {
            $mResult->In(TModifiers::eProtected);
        }
        elseif ($this->FMetaInfo->isPublic()) {
            $mResult->In(TModifiers::ePublic);
        }
        if ($this->FMetaInfo->isStatic()) {
            $mResult->In(TModifiers::eStatic);
        }

        return $mResult;
    }

    /**
     * descHere
     * @throws EBadTypeDefinition
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getType() {
        if ($this->FParameterValues == []) {
            $mDocComment = $this->FMetaInfo->getDocComment();
            if ($mDocComment === false) {
                throw new EBadTypeDefinition(sprintf('Bad type definition: undefined type of field %s.'), $this->FName);
            }
            $this->ParseParameterValues($mDocComment);
        }
        $mType = $this->FParameterValues[0];
        TClass::PrepareGeneric(['T' => $mType]);

        return new TClass();
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface $Object
     * @throws EIllegalAccess
     * @return \FrameworkDSW\System\IInterface
     */
    public function GetValue($Object) {
        TType::Object($Object, IInterface::class);
        try {
            $mRaw  = $this->FMetaInfo->getValue($Object);
            $mType = $this->getType()->getName();
            switch ($mType) {
                case 'boolean':
                    return new TBoolean($mRaw);
                    break;
                case 'integer':
                    return new TInteger($mRaw);
                    break;
                case 'float':
                    return new TFloat($mRaw);
                    break;
                case 'string':
                    return new TString($mRaw);
                    break;
                default:
                    if ($this->getType()->IsArray()) {
                        $mResult        = new TArray();
                        $mResult->Array = $mRaw;

                        return $mResult;
                    }

                    return $mRaw;
                    break;
            }
        }
        catch (\ReflectionException $Ex) {
            throw new EIllegalAccess(sprintf('Illegal access: member "%s" is not readable.', $this->FName));
        }
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface $Object
     * @param \FrameworkDSW\System\IInterface $Value
     * @throws EIllegalAccess
     */
    public function SetValue($Object, $Value) {
        TType::Object($Object, IInterface::class);
        TType::Object($Value, IInterface::class);
        try {
            $mType = $this->getType();
            switch ($mType->getName()) {
                case 'boolean':
                    /**@var $Value \FrameworkDSW\System\TBoolean */
                    TType::Object($Value, TBoolean::class);
                    $this->FMetaInfo->setValue($Object, $Value->Unbox());
                    break;
                case 'integer':
                    /**@var $Value \FrameworkDSW\System\TInteger */
                    TType::Object($Value, TInteger::class);
                    $this->FMetaInfo->setValue($Object, $Value->Unbox());
                    break;
                case 'float':
                    /**@var $Value \FrameworkDSW\System\TFloat */
                    TType::Object($Value, TFloat::class);
                    $this->FMetaInfo->setValue($Object, $Value->Unbox());
                    break;
                case 'string':
                    /**@var $Value \FrameworkDSW\System\TString */
                    TType::Object($Value, TString::class);
                    $this->FMetaInfo->setValue($Object, $Value->Unbox());
                    break;
                default:
                    if ($this->getType()->IsArray()) {
                        /**@var $Value \FrameworkDSW\Reflection\TArray */
                        TType::Object($Value, TArray::class);
                        $this->FMetaInfo->setValue($Object, $Value->Array);
                    }
                    else {
                        TType::Object($Value, $mType->GenericArg('T'));
                        $this->FMetaInfo->setValue($Object, $Value);
                    }
                    break;
            }
        }
        catch (\ReflectionException $Ex) {
            throw new EIllegalAccess(sprintf('Illegal access: member "%s" is not writable.', $this->FName));
        }
    }

}

/**
 * \FrameworkDSW\Reflection\TProperty
 * @author 许子健
 */
final class TProperty extends TAbstractMember implements IMember {
    /**
     * @var \ReflectionMethod
     */
    private $FGetterMetaInfo = null;
    /**
     * @var \ReflectionMethod
     */
    private $FSetterMetaInfo = null;

    /**
     * descHere
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @param string $Name
     * @throws ENoSuchPropertyMember
     */
    public function __construct($Class, $Name) {
        TType::Object($Class, [TClass::class => ['T' => null]]);
        TType::String($Name);

        try {
            $mClassMetaInfo = new \ReflectionClass($Class->getName());
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchPropertyMember(sprintf('No such property member: no such class: %s.', $Class->getName()), null, null, $Name, $Class);
        }
        try {
            $this->FGetterMetaInfo = $mClassMetaInfo->getMethod("get{$Name}");
        }
        catch (\ReflectionException $Ex) {

        }
        try {
            $this->FSetterMetaInfo = $mClassMetaInfo->getMethod("set{$Name}");
        }
        catch (\ReflectionException $Ex) {

        }
        if ($this->FGetterMetaInfo === null && $this->FSetterMetaInfo === null) {
            throw new ENoSuchPropertyMember(sprintf('No such property member: $%s->%s.', $Class->getName(), $Name), null, null, $Name, $Class);
        }
        $this->SetClass($Class);
        $this->FName = $Name;
    }

    //     /**
    //      * descHere
    //      * @return	\FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\IType>
    //      */
    //     public function getGenericsTypes() {
    //     }

    /**
     * descHere
     * @throws EBadTypeDefinition
     * @return \FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\TClass<T: ?>>
     */
    public function getGenericsValues() {
        if ($this->FParameterValues == []) {
            if ($this->FGetterMetaInfo === null) {
                $DocComment = $this->FSetterMetaInfo->getDocComment();
            }
            else {
                $DocComment = $this->FGetterMetaInfo->getDocComment();
            }
            if ($DocComment === false) {
                throw new EBadTypeDefinition(sprintf('Bad type definition: undefined type of property %s.', $this->FName));
            }
            $this->ParseParameterValues($DocComment);
        }
        $mType = $this->FParameterValues[0];
        if (is_string($mType)) {
            return null;
        }
        else {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => IType::class]);
            $mResult = new TMap(true);
            $mRaw    = $mType[array_keys($mType)[0]];
            foreach ($mRaw as $mName => $mValue) {
                TClass::PrepareGeneric(['T' => $mValue]);
                $mResult[$mName] = new TClass();
            }

            return $mResult;
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TModifiers
     */
    public function getModifiers() {
        $mResult = new TModifiers();
        if ($this->FGetterMetaInfo->isPrivate()) {
            $mResult->In(TModifiers::ePrivate);
        }
        elseif ($this->FGetterMetaInfo->isProtected()) {
            $mResult->In(TModifiers::eProtected);
        }
        elseif ($this->FGetterMetaInfo->isPublic()) {
            $mResult->In(TModifiers::ePublic);
        }
        if ($this->FGetterMetaInfo->isStatic()) {
            $mResult->In(TModifiers::eStatic);
        }

        return $mResult;
    }

    /**
     * descHere
     * @throws EBadTypeDefinition
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getType() {
        if ($this->FParameterValues == []) {
            if ($this->FGetterMetaInfo === null) {
                $mDocComment = $this->FSetterMetaInfo->getDocComment();
            }
            else {
                $mDocComment = $this->FGetterMetaInfo->getDocComment();
            }
            if ($mDocComment === false) {
                throw new EBadTypeDefinition(sprintf('Bad type definition: undefined type of property %s.', $this->FName));
            }
            $this->ParseParameterValues($mDocComment);
        }
        $mType = $this->FParameterValues[0];
        TClass::PrepareGeneric(['T' => $mType]);

        return new TClass();
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface $Object
     * @throws EIllegalAccess
     * @return \FrameworkDSW\System\IInterface
     */
    public function GetValue($Object) {
        TType::Object($Object, IInterface::class);
        try {
            $mRaw  = $this->FGetterMetaInfo->invoke($Object);
            $mType = $this->getType()->getName();
            switch ($mType) {
                case 'boolean':
                    return new TBoolean($mRaw);
                    break;
                case 'integer':
                    return new TInteger($mRaw);
                    break;
                case 'float':
                    return new TFloat($mRaw);
                    break;
                case 'string':
                    return new TString($mRaw);
                    break;
                default:
                    if ($this->getType()->IsArray()) {
                        $mResult        = new TArray();
                        $mResult->Array = $mRaw;

                        return $mResult;
                    }

                    return $mRaw;
                    break;
            }
        }
        catch (\ReflectionException $Ex) {
            throw new EIllegalAccess(sprintf('Illegal access: property "%s" is not readable or wrong object provided.'), $this->FName);
        }
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface $Object
     * @param \FrameworkDSW\System\IInterface $Value
     * @throws EIllegalAccess
     */
    public function SetValue($Object, $Value) {
        TType::Object($Object, IInterface::class);
        TType::Object($Value, IInterface::class);
        try {
            $mType = $this->getType();
            switch ($mType->getName()) {
                case 'boolean':
                    /**@var $Value \FrameworkDSW\System\TBoolean */
                    TType::Object($Value, TBoolean::class);
                    $this->FSetterMetaInfo->invoke($Object, $Value->Unbox());
                    break;
                case 'integer':
                    /**@var $Value \FrameworkDSW\System\TInteger */
                    TType::Object($Value, TInteger::class);
                    $this->FSetterMetaInfo->invoke($Object, $Value->Unbox());
                    break;
                case 'float':
                    /**@var $Value \FrameworkDSW\System\TFloat */
                    TType::Object($Value, TFloat::class);
                    $this->FGetterMetaInfo->invoke($Object, $Value->Unbox());
                    break;
                case 'string':
                    /**@var $Value \FrameworkDSW\System\TString */
                    TType::Object($Value, TString::class);
                    $this->FSetterMetaInfo->invoke($Object, $Value->Unbox());
                    break;
                default:
                    if ($this->getType()->IsArray()) {
                        /**@var $Value \FrameworkDSW\Reflection\TArray */
                        TType::Object($Value, TArray::class);
                        $this->FSetterMetaInfo->invoke($Object, $Value->Array);
                    }
                    else {
                        TType::Object($Value, $mType->GenericArg('T'));
                        $this->FSetterMetaInfo->invoke($Object, $Value);
                    }
                    break;
            }
        }
        catch (\ReflectionException $Ex) {
            throw new EIllegalAccess(sprintf('Illegal access: property "%s" is not writable or wrong object provided.'), $this->FName);
        }
    }

}

/**
 * \FrameworkDSW\Reflection\TConstructor
 * params <T: ?>
 * @author 许子健
 */
class TConstructor extends TAbstractMember implements IMember {
    /**
     * descHere
     * @var \ReflectionMethod
     */
    private $FMetaInfo = null;
    /**
     * @var string
     */
    private $FClassName = '';

    /**
     * descHere
     */
    public function __construct() {
        parent::__construct();

        TClass::PrepareGeneric($this->GenericArgs());
        $mClass = new TClass();

        $this->FClassName = $this->SetClass($mClass);
        $this->FName      = '__construct';
        $this->FMetaInfo  = new \ReflectionMethod($this->FClassName, '__construct');
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TClass <T: T>
     */
    public function getDeclaringClass() {
        TClass::PrepareGeneric($this->GenericArgs());

        return new TClass();
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TModifiers
     */
    public function getModifiers() {
        $mResult = new TModifiers();
        if ($this->FMetaInfo->isAbstract()) {
            $mResult->In(TModifiers::eAbstract);
        }
        if ($this->FMetaInfo->isFinal()) {
            $mResult->In(TModifiers::eFinal);
        }
        if ($this->FMetaInfo->isPrivate()) {
            $mResult->In(TModifiers::ePrivate);
        }
        elseif ($this->FMetaInfo->isProtected()) {
            $mResult->In(TModifiers::eProtected);
        }
        elseif ($this->FMetaInfo->isPublic()) {
            $mResult->In(TModifiers::ePublic);
        }

        return $mResult;
    }

    /**
     * descHere
     * @throws EBadTypeDefinition
     * @return \FrameworkDSW\Reflection\TClass[] <T: ?>
     */
    public function getParameterTypes() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EBadTypeDefinition(sprintf('Bad type definition: undefined type information of constructor for %s.', $this->FClassName));
        }
        $this->ParseParameterTypes($mDocComment);
        $mResult = [];
        foreach ($this->FParameterValues as $mValue) {
            TClass::PrepareGeneric(['T' => $mValue]);
            $mResult[] = new TClass();
        }

        return $mResult;
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface[] $Parameters
     * @throws EInstantiation
     * @return T
     */
    public function NewInstance($Parameters) {
        $mMetaClass = new \ReflectionClass($this->FClassName);
        if (isset($this->GenericArg('T')[$this->FClassName])) {
            $mGenericArgs = $this->GenericArg('T')[$this->FClassName];
        }
        else {
            $mGenericArgs = null;
        }
        $mRawParameters = [];
        $mDocComment    = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInstantiation(sprintf('Instantiation failed: no constructor type information found for class %s.', $this->FClassName));
        }
        $this->ParseParameterValues($mDocComment);
        foreach ($this->FParameterValues as $mIndex => $mType) {
            switch ($mType) {
                case 'boolean':
                    /** @var $Parameters \FrameworkDSW\System\TBoolean[] */
                    TType::Object($Parameters[$mIndex], TBoolean::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'integer':
                    /** @var $Parameters \FrameworkDSW\System\TInteger[] */
                    TType::Object($Parameters[$mIndex], TInteger::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'float':
                    /** @var $Parameters \FrameworkDSW\System\TFloat[] */
                    TType::Object($Parameters[$mIndex], TFloat::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'string':
                    /** @var $Parameters \FrameworkDSW\System\TString[] */
                    TType::Object($Parameters[$mIndex], TString::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'array':
                    /** @var $Parameters \FrameworkDSW\Reflection\TArray[] */
                    TType::Object($Parameters[$mIndex], TArray::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Array;
                    break;
                default:
                    TType::Object($Parameters[$mIndex], $mType);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex];
                    break;
            }
        }

        if (is_array($mGenericArgs)) {
            $mMetaClass->getMethod('PrepareGeneric')->invoke(null, $mGenericArgs);
        }

        return $mMetaClass->newInstanceArgs($mRawParameters);
    }
}

/**
 * \FrameworkDSW\Reflection\TMethod
 * @author 许子健
 */
final class TMethod extends TAbstractMember implements IMember {
    /**
     * @var \ReflectionMethod
     */
    private $FMetaInfo = null;

    /**
     * descHere
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @param string $Name
     * @throws ENoSuchMethodMember
     */
    public function __construct($Class, $Name) {
        parent::__construct();
        TType::Object($Class, [TClass::class => ['T' => null]]);
        TType::String($Name);

        $mTemp       = $this->SetClass($Class);
        $this->FName = $Name;
        try {
            $this->FMetaInfo = new \ReflectionMethod($mTemp, $Name);
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchMethodMember(sprintf('No such method member: %s.', $Name), null, null, $Class);
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TModifiers
     */
    public function getModifiers() {
        $mResult = new TModifiers();
        if ($this->FMetaInfo->isAbstract()) {
            $mResult->In(TModifiers::eAbstract);
        }
        if ($this->FMetaInfo->isFinal()) {
            $mResult->In(TModifiers::eFinal);
        }
        if ($this->FMetaInfo->isPrivate()) {
            $mResult->In(TModifiers::ePrivate);
        }
        elseif ($this->FMetaInfo->isProtected()) {
            $mResult->In(TModifiers::eProtected);
        }
        elseif ($this->FMetaInfo->isPublic()) {
            $mResult->In(TModifiers::ePublic);
        }

        return $mResult;
    }

    /**
     * descHere
     * @throws EBadTypeDefinition
     * @return \FrameworkDSW\Reflection\TClass[] <T: ?>
     */
    public function getParameterTypes() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EBadTypeDefinition(sprintf('Bad type definition: undefined type information for method %s.', $this->FName));
        }
        $this->ParseParameterTypes($mDocComment);
        $mResult = [];
        array_shift($this->FParameterValues);
        foreach ($this->FParameterValues as $mValue) {
            TClass::PrepareGeneric(['T' => $mValue]);
            $mResult[] = new TClass();
        }

        return $mResult;
    }

    /**
     * descHere
     * @throws EBadTypeDefinition
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getReturnType() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EBadTypeDefinition(sprintf('Bad type definition: undefined type information for method %s.', $this->FName));
        }
        $this->ParseParameterValues($mDocComment);
        $mValue = array_shift($this->FParameterValues);
        if ($mValue === null) {
            return null;
        }
        else {
            TClass::PrepareGeneric(['T' => $mValue]);

            return new TClass();
        }
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface $Object
     * @param \FrameworkDSW\System\IInterface[] $Parameters
     * @throws EInvocationTarget
     * @return \FrameworkDSW\System\IInterface
     */
    public function Invoke($Object, $Parameters) {
        TType::Object($Object, IInterface::class);
        TType::Arr($Parameters);

        $mRawParameters = [];
        $mDocComment    = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvocationTarget(sprintf('Invocation failed: undefined type information for method %s.', $this->FName));
        }
        $this->ParseParameterValues($mDocComment);
        array_shift($this->FParameterValues);
        foreach ($this->FParameterValues as $mIndex => $mType) {
            switch ($mType) {
                case 'boolean':
                    /**@var $Parameters \FrameworkDSW\System\TBoolean[] */
                    TType::Object($Parameters[$mIndex], TBoolean::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'integer':
                    /**@var $Parameters \FrameworkDSW\System\TInteger[] */
                    TType::Object($Parameters[$mIndex], TInteger::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'float':
                    /**@var $Parameters \FrameworkDSW\System\TFloat[] */
                    TType::Object($Parameters[$mIndex], TFloat::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'string':
                    /**@var $Parameters \FrameworkDSW\System\TString[] */
                    TType::Object($Parameters[$mIndex], TString::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'array':
                    /**@var $Parameters \FrameworkDSW\Reflection\TArray[] */
                    TType::Object($Parameters[$mIndex], TArray::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Array;
                    break;
                default:
                    TType::Object($Parameters[$mIndex], $mType);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex];
                    break;
            }
        }
        //TODO methods call with method-generics -- need declare something implements IType describing GENERICS.

        try {
            $mRawResult = $this->FMetaInfo->invokeArgs($Object, $mRawParameters);
            $mType      = $this->getReturnType()->getName();
            switch ($mType) {
                case 'boolean':
                    return new TBoolean($mRawResult);
                    break;
                case 'integer':
                    return new TInteger($mRawResult);
                    break;
                case 'float':
                    return new TFloat($mRawResult);
                    break;
                case 'string':
                    return new TString($mRawResult);
                    break;
                default:
                    if ($this->getReturnType()->IsArray()) {
                        $mResult        = new TArray();
                        $mResult->Array = $mRawResult;

                        return $mResult;
                    }

                    return $mRawResult;
                    break;
            }
        }
        catch (\ReflectionException $Ex) {
            throw new EInvocationTarget(sprintf('Wrong invocation target: wrong target for method %s.', $this->FName));
        }
    }

}

/**
 * \FrameworkDSW\Reflection\TSignal
 * @author 许子健
 */
final class TSignal extends TAbstractMember implements IMember {
    /**
     * @var \ReflectionMethod
     */
    private $FMetaInfo = null;

    /**
     * descHere
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @param string $Name
     * @throws ENoSuchSignalMember
     */
    public function __construct($Class, $Name) {
        parent::__construct();
        TType::Object($Class, [TClass::class => ['T' => null]]);
        TType::String($Name);

        $mClassName  = $this->SetClass($Class);
        $this->FName = $Name;
        try {
            $this->FMetaInfo = new \ReflectionMethod($mClassName, "signal{$Name}");
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchSignalMember(sprintf('No such signal member: %s.', $Name), null, null, $Class);
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TModifiers
     */
    public function getModifiers() {
        $mResult = new TModifiers();
        if ($this->FMetaInfo->isPrivate()) {
            $mResult->In(TModifiers::ePrivate);
        }
        elseif ($this->FMetaInfo->isProtected()) {
            $mResult->In(TModifiers::eProtected);
        }
        elseif ($this->FMetaInfo->isPublic()) {
            $mResult->In(TModifiers::ePublic);
        }

        return $mResult;
    }

    /**
     * descHere
     * @throws EBadTypeDefinition
     * @return \FrameworkDSW\Reflection\TClass[] <T: ?>
     */
    public function getParametersTypes() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EBadTypeDefinition(sprintf('Bad type definition: undefined type information for signal %s.', $this->FName));
        }
        $this->ParseParameterTypes($mDocComment);
        $mResult = [];
        array_shift($this->FParameterValues);
        foreach ($this->FParameterValues as $mValue) {
            TClass::PrepareGeneric(['T' => $mValue]);
            $mResult[] = new TClass();
        }

        return $mResult;
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\IInterface $Object
     * @param \FrameworkDSW\System\IInterface[] $Parameters
     * @throws EInvocationTarget
     */
    public function DispatchSignal($Object, $Parameters) {
        TType::PrepareGeneric($Object, IInterface::class);
        TType::Arr($Parameters);

        $mRawParameters = [];
        $mDocComment    = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvocationTarget(sprintf('Signal dispatched failed: undefined type information of signal %s.', $this->FName));
        }
        $this->ParseParameterValues($mDocComment);
        array_shift($this->FParameterValues);
        foreach ($this->FParameterValues as $mIndex => $mType) {
            switch ($mType) {
                case 'boolean':
                    /**@var $Parameters \FrameworkDSW\System\TBoolean[] */
                    TType::Object($Parameters[$mIndex], TBoolean::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'integer':
                    /**@var $Parameters \FrameworkDSW\System\TInteger[] */
                    TType::Object($Parameters[$mIndex], TInteger::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'float':
                    /**@var $Parameters \FrameworkDSW\System\TFloat[] */
                    TType::Object($Parameters[$mIndex], TFloat::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'string':
                    /**@var $Parameters \FrameworkDSW\System\TString[] */
                    TType::Object($Parameters[$mIndex], TString::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                    break;
                case 'array':
                    /**@var $Parameters \FrameworkDSW\Reflection\TArray[] */
                    TType::Object($Parameters[$mIndex], TArray::class);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex]->Array;
                    break;
                default:
                    TType::Object($Parameters[$mIndex], $mType);
                    $mRawParameters[$mIndex] = $Parameters[$mIndex];
                    break;
            }
        }
        //TODO methods call with method-generics -- need declare something implements IType describing GENERICS.
        TObject::Dispatch(array($Object, $this->FName), $mRawParameters);
    }

}

/**
 * \FrameworkDSW\Reflection\TSlot
 * @author 许子健
 */
final class TSlot extends TAbstractMember implements IMember {
    /**
     * @var \ReflectionMethod
     */
    private $FMetaInfo = null;

    /**
     * descHere
     * @param \FrameworkDSW\Reflection\TClass $Class <T>
     * @param string $Name
     * @throws ENoSuchSlotMember
     */
    public function __construct($Class, $Name) {
        parent::__construct();
        TType::Object($Class, [TClass::class => ['T' => null]]);
        TType::String($Name);

        $mClassName  = $this->SetClass($Class);
        $this->FName = $Name;
        try {
            $this->FMetaInfo = new \ReflectionMethod($mClassName, "slot{$Name}");
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchSlotMember(sprintf('No such slot member: %s.', $Name), null, null, $Class);
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Reflection\TModifiers
     */
    public function getModifiers() {
        $mResult = new TModifiers();
        if ($this->FMetaInfo->isPrivate()) {
            $mResult->In(TModifiers::ePrivate);
        }
        elseif ($this->FMetaInfo->isProtected()) {
            $mResult->In(TModifiers::eProtected);
        }
        elseif ($this->FMetaInfo->isPublic()) {
            $mResult->In(TModifiers::ePublic);
        }

        return $mResult;
    }

    /**
     * descHere
     * @throws EBadTypeDefinition
     * @return \FrameworkDSW\Reflection\TClass[] <T: ?>
     */
    public function getParameterTypes() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EBadTypeDefinition(sprintf('Bad type definition: undefined type information of slot %s.', $this->FName));
        }
        $this->ParseParameterTypes($mDocComment);
        $mResult = [];
        array_shift($this->FParameterValues);
        foreach ($this->FParameterValues as $mValue) {
            TClass::PrepareGeneric(['T' => $mValue]);
            $mResult[] = new TClass();
        }

        return $mResult;
    }
}
