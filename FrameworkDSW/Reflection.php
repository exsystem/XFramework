<?php
/**
 * Reflection
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 62
 */
namespace FrameworkDSW\Reflection;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\TRecord;
use FrameworkDSW\System\TString;
use FrameworkDSW\System\TFloat;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TBoolean;
use FrameworkDSW\Database\TPrimitiveParam;
use FrameworkDSW\System\EBadGenericArgsStructure;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\System\TSet;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\Utilities\EInvalidObjectCasting;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TType;

/**
 * \FrameworkDSW\Reflection\TModifiers
 * @author	许子健
 */
class TModifiers extends TSet {

    /**
     * @var	integer
     */
    const eAbstract = 3;
    /**
     * @var	integer
     */
    const eConst = 7;
    /**
     * @var	integer
     */
    const eFinal = 4;
    /**
     * @var	integer
     */
    const eInterface = 6;
    /**
     * @var	integer
     */
    const ePrivate = 0;
    /**
     * @var	integer
     */
    const eProtected = 1;
    /**
     * @var	integer
     */
    const ePublic = 2;
    /**
     * @var	integer
     */
    const eStatic = 5;
}

/**
 * \FrameworkDSW\Reflection\IType
 * @author	许子健
 */
interface IType extends IInterface {

}

/**
 * \FrameworkDSW\Reflection\IMember
 * @author	许子健
 */
interface IMember extends IInterface {

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getDeclaringClass();

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TModifiers
     */
    public function getModifiers();

    /**
     * descHere
     * @return	string
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
 * @author	许子健
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
     * @param	string	$Namespace
     */
    private function __construct($Namespace) {
        parent::__construct();
        TType::String($Namespace);
        $this->FNamespace = $Namespace;
    }

    /**
     * descHere
     * @return	string
     */
    public function getName() {
        return $this->FNamespace;
    }

    /**
     * descHere
     * @param	string	$Namespace
     * @return	\FrameworkDSW\Reflection\TNamespace
     */
    public static static function getNamespace($Namespace) {
        TType::String($Namespace);
        if (self::$FNamespaces === null) {
            TType::PrepareGeneric(['K' => 'string', 'V' => 'FrameworkDSW\Reflection\TNamespace']);
            self::$FNamespaces = new TMap(true);
        }
        if (self::$FNamespaces->ContainsKey($Namespace)) {
            return self::$FNamespaces[$Namespace];
        }

        $mTemp = "{$Namespace}\\";
        foreach (get_declared_classes() as $mClassName) {
            if (strpos($mClassName, $mTemp) === 0) {
                $mNamespace = new TNamespace($Namespace);
                self::$FNamespaces[$Namespace] = $mNamespace;
                return $mNamespace;
            }
        }
        throw new EInvalidParameter();
    }
}

/**
 * \FrameworkDSW\Reflection\TClass
 * params <T: ?>
 * @author	许子健
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
            throw new EInvalidObjectCasting(); //TODO
        }
    }

    /**
     * @return array
     */
    private function getExtendsInfo() {
        if ($this->FExtendsInfo === []) {
            $mDocComment = $this->getMetaInfo()->getDocComment();
            $mExtends = [];
            if ($mDocComment !== false) {
                $mDocComment = explode("\n", $mDocComment);
                if (isset($mDocComment[3]) && substr($mDocComment, 0, 10) === '* extends ') {
                    $mDocComment = substr($mDocComment[3], 10);
                    $mTokens = preg_split('([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)|<|: |>|?', $mDocComment);
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
                            $mToken = "'{$mToken}'";
                            break;
                        }
                    }
                    $mGernericsExpression = implode('', $mTokens);
                    $mExtends = eval("return [{$mGernericsExpression}];");
                    $mGernericsArgs = $this->GenericArg('T')[$this->FClassName];
                    array_walk_recursive($mExtends, function (&$Type) use ($mGernericsArgs) {
                        if (isset($mGernericsArgs[$Type])) {
                            $Type = $mGernericsArgs[$Type];
                        }
                    });
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
            $this->FIsArray = true;
            $this->FClassName = $mTemp;
            return;
        }

        if (($mTemp == 'boolean') || ($mTemp == 'integer') || ($mTemp == 'float') || ($mTemp == 'string')) {
            $this->FIsPrimitive = true;
            $this->FClassName = $mTemp;
            return;
        }

        if (isset(class_implements($mTemp)['FrameworkDSW\System\IDelegate'])) {
            $this->FIsDelegate = true;
            $this->FClassName = $mTemp;
            return;
        }

        $mRawParentClass = (string) get_parent_class($mTemp);
        switch ($mRawParentClass) {
        case 'FrameworkDSW\System\TRecord':
            $this->FIsRecord = true;
            break;
        case 'FrameworkDSW\TEnum':
            $this->FIsEnum = true;
            break;
        case 'FrameworkDSW\TSet':
            $this->FIsSet = true;
            break;
        default:
            if ((class_exists($mTemp, true) === false) || (interface_exists($mTemp, true) === false)) {
                throw new EBadGenericArgsStructure();
                //TODO throw class not exists.
            }
            $this->FIsType = true;
            break;
        }
        $this->FClassName = $mTemp;
    }

    /**
     * descHere
     * @param	\FrameworkDSW\System\IInterface	$Object
     * @return	T
     */
    public function Cast($Object) {
        TType::Object($Object, 'FrameworkDSW\System\IInterface');
        $this->EnsureType();
        if ($Object === null || $Object->IsInstanceOf($this->GenericArgs())) {
            return $Object;
        }
        else {
            throw new EInvalidObjectCasting();
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TConstructor <T: T>
     */
    public function getConstructor() {
        //$this->EnsureType(); //LAZY CHECKING.
        TConstructor::PrepareGeneric($this->GenericArgs());
        return new TConstructor();
    }

    /**
     * descHere
     * @param	string	$Name
     * @return	\FrameworkDSW\Reflection\TField
     */
    public function GetDeclaredField($Name) {
        TType::String($Name);
        $this->EnsureType();

        try {
            $mRaw = $this->getMetaInfo()->getProperty($Name);
            if ($mRaw->getDeclaringClass() !== $this->getName()) {
                throw new EInvalidParameter(); //TODO
            }
            return new TField($this, $Name);
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter();
            //TODO throw
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TField[]
     */
    public function getDeclaredFields() {
        $this->EnsureType();

        $mFields = $this->getMetaInfo()->getProperties();
        $mResult = [];
        foreach ($mFields as $mField) {
            if ($mField->getDeclaredClass() === $this->getMetaInfo()->getProperty($mField->getName())) {
                $mResult[] = new TField($this, $mField->getName());
            }
        }
        return $mResult;
    }

    /**
     * descHere
     * @param	string	$Name
     * @return	\FrameworkDSW\Reflection\TMethod
     */
    public function GetDeclaredMethod($Name) {
        TType::String($Name);
        $this->EnsureType();
        if (!$this->CheckMethodName($Name)) {
            throw new EInvalidParameter();
        }

        try {
            $this->getMetaInfo()->getMethod($Name);
            return new TMethod($this, $Name);
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter();
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TMethod[]
     */
    public function getDeclaredMethods() {
        $this->EnsureType();
        $mMethods = $this->getMetaInfo()->getMethods();
        $mResult = [];
        $mName = '';
        foreach ($mMethods as $mMethod) {
            $mName = $mMethod->getName();
            if ($this->CheckMethodName($mName) && ($mMethod->getDeclaredClass() === $this->getMetaInfo()->getMethod($mName))) {
                $mResult[] = new TMethod($this, $mName);
            }
        }
        return $mResult;
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getElementType() {
        if (!$this->FIsArray) {
            throw new EInvalidObjectCasting();//TODO
        }
        $mRawElementType = substr($this->FClassName, 0, -2);
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
     * @return	\FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\IType>
     */
    public function getGenericsValues() {
        TMap::PrepareGeneric(['K' => 'string', 'V' => '\FrameworkDSW\Reflection\IType']);
        if (!($this->FIsType || $this->FIsArray)) {
            throw new EInvalidObjectCasting();//TODO
        }

        $mGenericArgs = $this->GenericArg('T');
        if (is_array($mGenericArgs)) {
            $mResult = new TMap(true);
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
     * @return	\FrameworkDSW\Reflection\TClass[] <T>
     */
    public function getInterfaces() {
        $this->EnsureType();
        $mResult = [];
        $mRawInterfaceNames = $this->getMetaInfo()->getInterfaceNames();
        $mImplements = $this->getExtendsInfo();
        foreach ($mRawInterfaceNames as $mRawInterfaceName) {
            if (isset($mImplements[$mRawInterfaceName])) {
                $mInterface = $mImplements[$mRawInterfaceName];
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
     * @return	\FrameworkDSW\Reflection\TField[]
     */
    public function getFields() {
        $this->EnsureType();
        $mFields = $this->getMetaInfo()->getProperties();
        $mName = '';
        $mResult = [];
        foreach ($mFields as $mField) {
            $mName = $mField->getName();
            $mResult[] = new TField($this, $mName);
        }
        return $mResult;
    }

    /**
     * descHere
     * @param	string	$Name
     * @return	\FrameworkDSW\Reflection\TMethod
     */
    public function GetMethod($Name) {
        TType::String($Name);
        $this->EnsureType();
        if ($this->CheckMethodName($Name)) {
            return new TMethod($this, $Name);
        }
        else {
            throw new EInvalidParameter();
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TMethod[]
     */
    public function getMethods() {
        $this->EnsureType();
        $mMethods = $this->getMetaInfo()->getMethods();
        $mName = '';
        $mResult = [];
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
     * @return	string
     */
    public function getName() {
        return $this->FClassName;
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TNamespace
     */
    public function getNamespace() {
        return new TNamespace($this->FMetaInfo->getNamespaceName());
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getParentClass() {
        $this->EnsureType();
        $mParentClassMetaInfo = $this->FMetaInfo->getParentClass();
        if ($mParentClassMetaInfo === false) {
            return null;
        }
        else {
            TClass::PrepareGeneric(['T' => $this->getExtendsInfo()[$mParentClassMetaInfo->getName()]]);
            return new TClass();
        }
    }

    /**
     * descHere
     * @return	string
     */
    public function getSimpleName() {
        $this->EnsureType();
        return $this->FMetaInfo->getShortName();
    }

    /**
     * descHere
     * @return	boolean
     */
    public function IsArray() {
        return $this->FIsArray;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function IsClass() {
        return $this->FIsType && (!$this->getMetaInfo()->isInterface());
    }

    /**
     * descHere
     * @return	boolean
     */
    public function IsEnum() {
        return $this->FIsEnum;
    }

    /**
     * descHere
     * @param	\FrameworkDSW\System\IInterface	$Object
     * @return	boolean
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
     * @return	boolean
     */
    public function IsInterface() {
        return $this->FIsType && $this->getMetaInfo()->isInterface();
    }

    /**
     * descHere
     * @return	boolean
     */
    public function IsPrimitive() {
        return $this->FIsPrimitive;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function IsSetStruct() {
        return $this->FIsSet;
    }

    /**
     * descHere
     * @param	\FrameworkDSW\System\IInterface[]	$Parameters
     * @return	T
     */
    public function NewInstance($Parameters) {
        TType::Arr($Parameters);
        $this->EnsureType();
        if ($this->getMetaInfo()->isInterface()) {
            throw new EInvalidObjectCasting(); //TODO
        }
        $mConstructor = $this->getConstructor();
        $mResult = $mConstructor->NewInstance($Parameters);
        Framework::Free($mConstructor);
        return $mResult;
    }
}

/**
 * \FrameworkDSW\Reflection\TAbstractMember
 * @author	许子健
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
     * @var	boolean
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
     */
    protected function ParseParameterTypes($DocComment) {
        //TODO Support for [] - arrays

        $mLines = [];
        $mPos = false;
        $mClassPartString = '';
        $mGenericsPartString = '';
        foreach ($DocComment as $mLine) {
            if (substr($mLine, 0, 9) === '* @param ') {
                $mLine = substr($mLine, 9);
                if ($mLine === false || $mLine === '') {
                    throw new EInvalidParameter();//TODO exception type properly, should be.
                }
                $mPos = strpos($mLine, '$');
                if ($mPos === false) {
                    throw new EInvalidParameter();//TODO exception type properly, should be.
                }
                $mClassPartString = substr($mLine, 0, $mPos);
                if ($mClassPartString === false || $mClassPartString === '') {
                    throw new EInvalidParameter();//TODO exception type properly, should be.
                }
                $mClassPartString = trim($mClassPartString);
                $mLine = substr($mLine, $mPos + 1);
                if ($mLine === false || $mLine === '') {
                    throw new EInvalidParameter();//TODO exception type properly, should be.
                }
                $mPos = strpos($mLine, ' ');
                if ($mPos === false || $mPos === strlen($mLine)) {
                    $mLines[] = [$mClassPartString, null];
                    continue;
                }
                $mLine = substr($mLine, $mPos + 1);//GENERICS ＆ COMMENTS
                if ($mLine === false || $mLine === '' || $mLine[0] !== '<') {
                    $mLines[] = [$mClassPartString, null];
                    continue;
                }
                $mGenericsPartString = strstr($mLine, '> ', true);
                if ($mGenericsPartString === false) {
                    if ($mLine[strlen($mLine)] === '>') {
                        $mGenericsPartString = $mLine;
                        $mLines[] = [$mClassPartString, $mGenericsPartString];
                        continue;
                    }
                    $mLines[] = [$mClassPartString, $mGenericsPartString];
                    continue;
                }
                $mLines[] = [$mClassPartString, $mGenericsPartString];
            }
            elseif (substr($DocComment, 0, 10) === '* @return ') {
                $mLine = substr($mLine, 10);
                if ($mLine === false || $mLine === '') {
                    throw new EInvalidParameter();//TODO exception type properly, should be.
                }
                $mPos = strpos($mLine, ' ');
                $mClassPartString = substr($mLine, 0, $mPos);
                $mLine = trim($mLine);
                if ($mLine === false || $mLine === '' || $mLine[0] !== '<') {
                    array_push($mLines, [$mClassPartString, null]);
                    continue;
                }
                $mGenericsPartString = strstr($mLine, '> ', true);
                if ($mGenericsPartString === false) {
                    if ($mLine[strlen($mLine)] === '>') {
                        $mGenericsPartString = $mLine;
                        array_push($mLines, [$mClassPartString, $mGenericsPartString]);
                        continue;
                    }
                    array_push($mLines, [$mClassPartString, null]);
                    continue;
                }
                array_push($mLines, [$mClassPartString, $mGenericsPartString]);
            }
            elseif (substr($DocComment, 0, 7) === '* @var ') {
                $mLine = substr($mLine, 7);
                if ($mLine === false || $mLine === '') {
                    throw new EInvalidParameter();//TODO exception type properly, should be.
                }
                $mPos = strpos($mLine, ' ');
                $mClassPartString = substr($mLine, 0, $mPos);
                $mLine = trim($mLine);
                if ($mLine === false || $mLine === '' || $mLine[0] !== '<') {
                    $mLines[0] = [$mClassPartString, null];
                    continue;
                }
                $mGenericsPartString = strstr($mLine, '> ', true);
                if ($mGenericsPartString === false) {
                    if ($mLine[strlen($mLine)] === '>') {
                        $mGenericsPartString = $mLine;
                        $mLines[0] = [$mClassPartString, $mGenericsPartString];
                        continue;
                    }
                    $mLines[0] = [$mClassPartString, null];
                    continue;
                }
                $mLines[0] = [$mClassPartString, $mGenericsPartString];
            }
        }

        $this->FParameterTypes = [];
        foreach ($mLines as $mLine) {
            $mParameter = [];
            if ($mLine[1] !== null) {
                $mTokens = preg_split('([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)|<|: |>|?', $mLine[1]);
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
                        $mToken = "'{$mToken}'";
                        break;
                    }
                }
                $mGernericsExpression = implode('', $mTokens);
                $mParameter = eval("return [{$mGernericsExpression}];");
                $mParameter = [$mLine[0] => $mParameter];
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
        $mGernericsArgs = $this->FClass->GenericArg('T')[$this->FClass->getName()];
        //TODO additional generics args from methods/ctors.
        foreach ($this->FParameterValues as &$mParameter) {
            if (is_string($mParameter) && isset($mGernericsArgs[$mParameter])) {
                $mParameter = $mGernericsArgs[$mParameter];
                continue;
            }
            array_walk_recursive($mParameter, function (&$Type) use ($mGernericsArgs) {
                if (isset($mGernericsArgs[$Type])) {
                    $Type = $mGernericsArgs[$Type];
                }
            });
        }
    }

    /**
     * @param	\FrameworkDSW\Reflection\TClass	$Class <T: ?>
     * @return string
     */
    protected function SetClass($Class) {
        $mTemp = $Class->GenericArg('T');
        if (is_array($mTemp)) {
            $mTemp = array_keys($mTemp)[0];
        }

        if (strrpos($mTemp, ']', -1) !== false) {
            throw new EBadGenericArgsStructure(); //TODO non-class.
        }

        if (($mTemp == 'boolean') || ($mTemp == 'integer') || ($mTemp == 'float') || ($mTemp == 'string')) {
            throw new EBadGenericArgsStructure(); //TODO non-class.
        }

        if (isset(class_implements($mTemp)['FrameworkDSW\System\IDelegate'])) {
            throw new EBadGenericArgsStructure(); //TODO non-class.
        }

        $mRawParentClass = (string) get_parent_class($mTemp);
        switch ($mRawParentClass) {
        case 'FrameworkDSW\System\TRecord':
            throw new EBadGenericArgsStructure(); //TODO non-class.
        case 'FrameworkDSW\TEnum':
            throw new EBadGenericArgsStructure(); //TODO non-class.
        case 'FrameworkDSW\TSet':
            throw new EBadGenericArgsStructure(); //TODO non-class.
        default:
            if ((class_exists($mTemp, true) === false) || (interface_exists($mTemp, true) === false)) {
                throw new EBadGenericArgsStructure();
                //TODO throw class not exists.
            }
            break;
        }

        $this->FClass = $Class;
        return $mTemp;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getAccessible() {
        return $this->FAccessible;
    }

    /**
     * descHere
     * @param	boolean	$Value
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
 * @author	许子健
 */
final class TField extends TAbstractMember implements IMember {
    /**
     * @var \ReflectionProperty
     */
    private $FMetaInfo = null;

    /**
     * descHere
     * @param	\FrameworkDSW\Reflection\TClass	$Class <T: ?>
     * @param	string	$Name
     */
    public function __construct($Class, $Name) {
        TType::Object($Class, ['\FrameworkDSW\Reflection\TClass' => ['T' => 'mixed']]);
        TType::String($Name);

        try {
            $mClassMetaInfo = new \ReflectionClass($Class->getName());
            $this->FMetaInfo = $mClassMetaInfo->getProperty($Name);
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter(); //TODO
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
     * @return	\FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\TClass<T: ?>>
     */
    public function getGenericsValues() {
        if ($this->FParameterValues == []) {
            $DocComment = $this->FMetaInfo->getDocComment();
            if ($DocComment === false) {
                throw new EInvalidParameter();//TODO ex.
            }
            $this->ParseParameterValues($DocComment);
        }
        $mType = $this->FParametersInfo[0];
        if (is_string($mType)) {
            return null;
        }
        else {
            TMap::PrepareGeneric(['K' => 'string', 'V' => '\FrameworkDSW\Reflection\IType']);
            $mResult = new TMap(true);
            $mRaw = $mType[array_keys($mType)[0]];
            foreach ($mRaw as $mName => $mValue) {
                TClass::PrepareGeneric(['T' => $mValue]);
                $mResult[$mName] = new TClass();
            }
            return $mResult;
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TModifiers
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
     * @return	\FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getType() {
        if ($this->FParameterValues == []) {
            $mDocComment = $this->FMetaInfo->getDocComment();
            if ($mDocComment === false) {
                throw new EInvalidParameter();//TODO ex.
            }
            $this->ParseParameterValues($mDocComment);
        }
        $mType = $this->FParametersInfo[0];
        TClass::PrepareGeneric(['T' => $mType]);
        return new TClass();
    }

    /**
     * descHere
     * @param	\FrameworkDSW\System\IInterface	$Object
     * @return	\FrameworkDSW\System\IInterface
     */
    public function GetValue($Object) {
        TType::Object($Object, 'FrameworkDSW\System\IInterface');
        try {
            $mRaw = $this->FMetaInfo->getValue($Object);
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
                    $mResult = new TArray();
                    $mResult->Array = $mRaw;
                    return $mResult;
                }
                return $mRaw;
                break;
            }
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter(); //TODO Not accessible.
        }
    }

    /**
     * descHere
     * @param	\FrameworkDSW\System\IInterface	$Object
     * @param	\FrameworkDSW\System\IInterface	$Value
     */
    public function SetValue($Object, $Value) {
        TType::Object($Object, 'FrameworkDSW\System\IInterface');
        TType::Object($Value, 'FrameworkDSW\System\IInterface');
        try {
            $mType = $this->getType();
            switch ($mType->getName()) {
            case 'boolean':
                TType::Object($Value, 'FrameworkDSW\System\TBoolean');
                $this->FMetaInfo->setValue($Object, $Value->Unbox());
                break;
            case 'integer':
                TType::Object($Value, 'FrameworkDSW\System\TInteger');
                $this->FMetaInfo->setValue($Object, $Value->Unbox());
                break;
            case 'float':
                TType::Object($Value, 'FrameworkDSW\System\TFloat');
                $this->FMetaInfo->setValue($Object, $Value->Unbox());
                break;
            case 'string':
                TType::Object($Value, 'FrameworkDSW\System\TString');
                $this->FMetaInfo->setValue($Object, $Value->Unbox());
                break;
            default:
                if ($this->getType()->IsArray()) {
                    $mResult = new TArray();
                    TType::Object($Value, 'FrameworkDSW\Reflection\TArray');
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
            throw new EInvalidParameter(); //TODO Not accessible.
        }
    }

}

/**
 * \FrameworkDSW\Reflection\TConstructor
 * params <T: ?>
 * @author	许子健
 */
class TConstructor extends TAbstractMember implements IMember {
    /**
     * descHere
     * @var	\ReflectionMethod
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

        $mTemp = $this->GenericArg('T');
        if (is_array($mTemp)) {
            $mTemp = array_keys($mTemp)[0];
        }

        if (strrpos($mTemp, ']', -1) !== false) {
            throw new EBadGenericArgsStructure(); //TODO non-class.
        }

        if (($mTemp == 'boolean') || ($mTemp == 'integer') || ($mTemp == 'float') || ($mTemp == 'string')) {
            throw new EBadGenericArgsStructure(); //TODO non-class.
        }

        if (isset(class_implements($mTemp)['FrameworkDSW\System\IDelegate'])) {
            throw new EBadGenericArgsStructure(); //TODO non-class.
        }

        $mRawParentClass = (string) get_parent_class($mTemp);
        switch ($mRawParentClass) {
        case 'FrameworkDSW\System\TRecord':
            throw new EBadGenericArgsStructure(); //TODO non-class.
        case 'FrameworkDSW\TEnum':
            throw new EBadGenericArgsStructure(); //TODO non-class.
        case 'FrameworkDSW\TSet':
            throw new EBadGenericArgsStructure(); //TODO non-class.
        default:
            if ((class_exists($mTemp, true) === false) || (interface_exists($mTemp, true) === false)) {
                throw new EBadGenericArgsStructure();
                //TODO throw class not exists.
            }
            break;
        }
        $this->FClassName = $mTemp;
        $this->FName = '__construct';
        $this->FMetaInfo = new \ReflectionMethod($this->FClassName, '__construct');
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TClass <T: T>
     */
    public function getDeclaringClass() {
        TPrimitiveParam::PrepareGeneric($this->GenericArgs());
        return new TClass();
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TModifiers
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
     * @return	\FrameworkDSW\Reflection\TClass <T>[]
     */
    public function getParameterTypes() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
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
     * @param	\FrameworkDSW\Reflection\IInterface[]	$Parameters
     * @return	T
     */
    public function NewInstance($Parameters) {
        $mMetaClass = new \ReflectionClass($this->FClassName);
        $mGenericArgs = $this->GenericArg('T')[$this->FClassName];
        $mRawParameters = [];
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
        }
        $this->ParseParameterTypes($mDocComment);
        foreach ($this->FParameterValues as $mIndex => $mType) {
            switch ($mType) {
            case 'boolean':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TBoolean');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'integer':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TInteger');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'float':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TFloat');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'string':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TString');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'array':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\Reflection\TArray');
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
 * @author	许子健
 */
final class TMethod extends TAbstractMember implements IMember {
    /**
     * @var \ReflectionMethod
     */
    private $FMetaInfo = null;

    /**
     * descHere
     * @param	\FrameworkDSW\Reflection\TClass	$Class <T: ?>
     * @param	string	$Name
     */
    public function __construct($Class, $Name) {
        parent::__construct();
        TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => 'mixed']]);
        TType::String($Name);

        $mTemp = $this->SetClass($Class);
        $this->FName = $Name;
        try {
            $this->FMetaInfo = new \ReflectionMethod($mTemp, $Name);
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter(); //TODO
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TModifiers
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
     * @return	\FrameworkDSW\Reflection\TClass[] <T: ?>
     */
    public function getParameterTypes() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
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
     * @return	\FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getReturnType() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
        }
        $this->ParseParameterTypes($mDocComment);
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
     * @param	\FrameworkDSW\System\IInterface	$Object
     * @param	\FrameworkDSW\System\IInterface[]	$Parameters
     * @return	\FrameworkDSW\System\IInterface
     */
    public function Invoke($Object, $Parameters) {
        TType::PrepareGeneric($Object, 'FrameworkDSW\System\IInterface');
        TType::Arr($Parameters);

        $mRawParameters = [];
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
        }
        array_shift($this->ParseParameterTypes($mDocComment));
        foreach ($this->FParameterValues as $mIndex => $mType) {
            switch ($mType) {
            case 'boolean':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TBoolean');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'integer':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TInteger');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'float':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TFloat');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'string':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TString');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'array':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\Reflection\TArray');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Array;
                break;
            default:
                TType::Object($Parameters[$mIndex], $mType);
                $mRawParameters[$mIndex] = $Parameters[$mIndex];
                break;
            }
        }
        //TODO methods call with method-generics

        try {
            $mRawResult = $this->FMetaInfo->invokeArgs($Object, $mRawParameters);
            $mType = $this->getReturnType()->getName();
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
                if ($this->getType()->IsArray()) {
                    $mResult = new TArray();
                    $mResult->Array = $mRawResult;
                    return $mResult;
                }
                return $mRawResult;
                break;
            }
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter(); //TODO Not accessible.
        }
    }

}

/**
 * \FrameworkDSW\Reflection\TSignal
 * @author	许子健
 */
final class TSignal extends TAbstractMember implements IMember {
    /**
     * @var \ReflectionMethod
     */
    private $FMetaInfo = null;

    /**
     * descHere
     * @param	\FrameworkDSW\Reflection\TClass	$Class <T: ?>
     * @param	string	$Name
     */
    public function __construct($Class, $Name) {
        parent::__construct();
        TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => 'mixed']]);
        TType::String($Name);

        $mClassName = $this->SetClass($Class);
        $this->FName = $Name;
        try {
            $this->FMetaInfo = new \ReflectionMethod($mClassName, "{singal}$Name");
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter(); //TODO
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TModifiers
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
     * @return	\FrameworkDSW\Reflection\TClass[] <T: ?>
     */
    public function getParametersTypes() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
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
     * @param	\FrameworkDSW\System\IInterface	$Object
     * @param	\FrameworkDSW\System\IInterface[]	$Parameters
     */
    public function Dispatch($Object, $Parameters) {
        TType::PrepareGeneric($Object, 'FrameworkDSW\System\IInterface');
        TType::Arr($Parameters);

        $mRawParameters = [];
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
        }
        array_shift($this->ParseParameterTypes($mDocComment));
        foreach ($this->FParameterValues as $mIndex => $mType) {
            switch ($mType) {
            case 'boolean':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TBoolean');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'integer':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TInteger');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'float':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TFloat');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'string':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TString');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'array':
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\Reflection\TArray');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Array;
                break;
            default:
                TType::Object($Parameters[$mIndex], $mType);
                $mRawParameters[$mIndex] = $Parameters[$mIndex];
                break;
            }
        }
        //TODO methods call with method-generics
        TObject::Dispatch(array($Object, $this->FName), $mRawParameters);
    }

}

/**
 * \FrameworkDSW\Reflection\TSlot
 * @author	许子健
 */
final class TSlot extends TAbstractMember implements IMember {
    /**
     * @var \ReflectionMethod
     */
    private $FMetaInfo = null;

    /**
     * descHere
     * @param	\FrameworkDSW\Reflection\TClass	$Class <T>
     * @param	string	$Name
     */
    public function __construct($Class, $Name) {
        parent::__construct();
        TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => 'mixed']]);
        TType::String($Name);

        $mClassName = $this->SetClass($Class);
        $this->FName = $Name;
        try {
            $this->FMetaInfo = new \ReflectionMethod($mClassName, "{slot}$Name");
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter(); //TODO
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TModifiers
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
     * @return	\FrameworkDSW\Reflection\TClass[] <T: ?>
     */
    public function getParameterTypes() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
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
