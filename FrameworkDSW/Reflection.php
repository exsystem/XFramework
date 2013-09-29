<?php
/**
 * \FrameworkDSW\Reflection
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
    public function __construct($Namespace) {
        parent::__construct();
        TType::String($Namespace);
        parent::EnsureSingleton();

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
     * @param    string $Namespace
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TNamespace
     */
    public static function getNamespace($Namespace) {
        TType::String($Namespace);
        if (self::$FNamespaces === null) {
            TMap::PrepareGeneric(['K' => 'string', 'V' => '\FrameworkDSW\Reflection\TNamespace']);
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
                if (isset($mDocComment[3]) && substr($mDocComment[3] = trim($mDocComment[3]), 0, 10) === '* extends ') {
                    $mDocComment = substr($mDocComment[3], 10);
                    $mTokens = preg_split('/([a-zA-Z_\x7f-\xff\x5c][a-zA-Z0-9_\x7f-\xff\x5c\[\]]*)|(<)|(: )|(>)|(\?)|(\, )/', $mDocComment, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
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
                            if ($mToken[0]=='\\') {
                                $mToken = substr($mToken, 1);
                            }
                            $mToken = "'{$mToken}'";
                            break;
                        }
                    }
                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $mGenericsExpression = implode('', $mTokens);
                    $mExtends = eval("return [{$mGenericsExpression}];");
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
            $this->FIsArray = true;
            $this->FClassName = $mTemp;
        }
        elseif (($mTemp == 'boolean') || ($mTemp == 'integer') || ($mTemp == 'float') || ($mTemp == 'string')) {
            $this->FIsPrimitive = true;
            $this->FClassName = $mTemp;
        }
        elseif(isset(class_implements($mTemp)['FrameworkDSW\System\IDelegate'])) {
            $this->FIsDelegate = true;
            $this->FClassName = $mTemp;
        }
        else {
            $mRawParentClass = (string) get_parent_class($mTemp);
            switch ($mRawParentClass) {
                case 'FrameworkDSW\System\TRecord':
                    $this->FIsRecord = true;
                    break;
                case 'FrameworkDSW\System\TEnum':
                    $this->FIsEnum = true;
                    break;
                case 'FrameworkDSW\System\TSet':
                    $this->FIsSet = true;
                    break;
                default:
                    if ((class_exists($mTemp, true) === false) && (interface_exists($mTemp, true) === false)) {
                        throw new EBadGenericArgsStructure();
                        //TODO throw class not exists.
                    }
                    $this->FIsType = true;
                    break;
            }
            $this->FClassName = $mTemp;
        }
        if ($this->FClassName[0]=='\\') {
            $this->FClassName = substr($this->FClassName, 1);
        }
    }

    /**
     * descHere
     * @param    \FrameworkDSW\System\IInterface $Object
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @return    T
     */
    public function Cast($Object) {
        TType::Object($Object, 'FrameworkDSW\System\IInterface');
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
     * @return	\FrameworkDSW\Reflection\TConstructor <T: T>
     */
    public function getConstructor() {
        //$this->EnsureType(); //LAZY CHECKING.
        TConstructor::PrepareGeneric($this->GenericArgs());
        return new TConstructor();
    }

    /**
     * descHere
     * @param    string $Name
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TField
     */
    public function GetDeclaredField($Name) {
        TType::String($Name);
        $this->EnsureType();

        try {
            $mRaw = $this->getMetaInfo()->getProperty($Name);
            if ($mRaw->getDeclaringClass()->getName() !== $this->getName()) {
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
            if ($mField->getDeclaringClass()->getName() === $this->getMetaInfo()->getName()) {
                $mResult[] = new TField($this, $mField->getName());
            }
        }
        return $mResult;
    }

    /**
     * descHere
     * @param    string $Name
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TMethod
     */
    public function GetDeclaredMethod($Name) {
        TType::String($Name);
        $this->EnsureType();
        if ((!$this->CheckMethodName($Name)) || ($this->getMetaInfo()->getMethod($Name)->getDeclaringClass()->getName() !== $this->FClassName)) {
            throw new EInvalidParameter();
        }

        try {
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
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @return    \FrameworkDSW\Reflection\TClass <T: ?>
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
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @return    \FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\IType>
     */
    public function getGenericsValues() {
        if (!($this->FIsType || $this->FIsArray)) {
            throw new EInvalidObjectCasting();//TODO
        }

        $mGenericArgs = $this->GenericArg('T');
        if (is_array($mGenericArgs)) {
            TMap::PrepareGeneric(['K' => 'string', 'V' => 'FrameworkDSW\Reflection\IType']);
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
     * @return	\FrameworkDSW\Reflection\TClass[] <T: ?>
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
        $mResult = [];
        foreach ($mFields as $mField) {
            $mName = $mField->getName();
            $mResult[] = new TField($this, $mName);
        }
        return $mResult;
    }

    /**
     * descHere
     * @param    string $Name
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TMethod
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TNamespace
     */
    public function getNamespace() {
        if ($this->FIsArray || $this->FIsPrimitive) {
            throw new EInvalidParameter(); //TODO
        }
        else {
            return TNamespace::getNamespace($this->getMetaInfo()->getNamespaceName());
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getParentClass() {
        $this->EnsureType();
        $mParentClassMetaInfo = $this->getMetaInfo()->getParentClass();
        if ($mParentClassMetaInfo === false) {
            return null;
        }
        else {
            TClass::PrepareGeneric(['T' => [$mParentClassMetaInfo->getName()=>$this->getExtendsInfo()[$mParentClassMetaInfo->getName()]]]);
            return new TClass();
        }
    }

    /**
     * descHere
     * @return	string
     */
    public function getSimpleName() {
        $this->EnsureType();
        return $this->getMetaInfo()->getShortName();
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
     * @param    \FrameworkDSW\System\IInterface[] $Parameters
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @return    T
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    protected function ParseParameterTypes($DocComment) {
        //TODO Support for [] - arrays

        $mLines = [];
        $mDocCommentLines = explode("\n", $DocComment);
        foreach ($mDocCommentLines as $mLine) {
            $mLine = trim($mLine);
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
            elseif (substr($mLine, 0, 10) === '* @return ') {
                $mLine = substr($mLine, 10);
                if ($mLine === false || $mLine === '') {
                    throw new EInvalidParameter();//TODO exception type properly, should be.
                }
                $mPos = strpos($mLine, ' ');
                if ($mPos === false) {
                    $mPos = strlen($mLine);
                }
                $mClassPartString = substr($mLine, 0, $mPos);
                $mLine = trim(substr($mLine, $mPos));
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
                    throw new EInvalidParameter();//TODO exception type properly, should be.
                }
                $mPos = strpos($mLine, ' ');
                if ($mPos === false) {
                    $mPos = strlen($mLine);
                }
                $mClassPartString = substr($mLine, 0, $mPos);
                $mLine = trim(substr($mLine, $mPos));
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
                $mLines[0] = [$mClassPartString, "{$mGenericsPartString}>"];
            }
        }

        $this->FParameterTypes = [];
        foreach ($mLines as $mLine) {
            if ($mLine[0][0]=='\\') {
                $mLine[0]=substr($mLine[0], 1);
            }
            if ($mLine[1] !== null) {
                $mTokens = preg_split('/([a-zA-Z_\x7f-\xff\x5c][a-zA-Z0-9_\x7f-\xff\x5c\[\]]*)|(<)|(: )|(>)|(\?)|(\, )/', $mLine[1], 0, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
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
                        if ($mToken[0]=='\\') {
                            $mToken = substr($mToken, 1);
                        }
                        $mToken = "'{$mToken}'";
                        break;
                    }
                }
                /** @noinspection PhpUnusedLocalVariableInspection */
                $mGenericsExpression = implode('', $mTokens);
                $mParameter = eval("return ['{$mLine[0]}' {$mGenericsExpression}];");
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
            //TODO additional generics args from methods/ctors.
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
     * @param    \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @throws \FrameworkDSW\System\EBadGenericArgsStructure
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
            if ((class_exists($mTemp, true) === false) && (interface_exists($mTemp, true) === false)) {
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
     * @param    \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @param    string $Name
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($Class, $Name) {
        TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T'=>null]]); //FIXME !!!!!
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\TClass<T: ?>>
     */
    public function getGenericsValues() {
        if ($this->FParameterValues == []) {
            $DocComment = $this->FMetaInfo->getDocComment();
            if ($DocComment === false) {
                throw new EInvalidParameter();//TODO ex.
            }
            $this->ParseParameterValues($DocComment);
        }
        $mType = $this->FParameterValues[0];
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getType() {
        if ($this->FParameterValues == []) {
            $mDocComment = $this->FMetaInfo->getDocComment();
            if ($mDocComment === false) {
                throw new EInvalidParameter();//TODO ex.
            }
            $this->ParseParameterValues($mDocComment);
        }
        $mType = $this->FParameterValues[0];
        TClass::PrepareGeneric(['T' => $mType]);
        return new TClass();
    }

    /**
     * descHere
     * @param    \FrameworkDSW\System\IInterface $Object
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\System\IInterface
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
     * @param    \FrameworkDSW\System\IInterface $Object
     * @param    \FrameworkDSW\System\IInterface $Value
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function SetValue($Object, $Value) {
        TType::Object($Object, 'FrameworkDSW\System\IInterface');
        TType::Object($Value, 'FrameworkDSW\System\IInterface');
        try {
            $mType = $this->getType();
            switch ($mType->getName()) {
            case 'boolean':
                /**@var $Value \FrameworkDSW\System\TBoolean**/
                TType::Object($Value, 'FrameworkDSW\System\TBoolean');
                $this->FMetaInfo->setValue($Object, $Value->Unbox());
                break;
            case 'integer':
                /**@var $Value \FrameworkDSW\System\TInteger**/
                TType::Object($Value, 'FrameworkDSW\System\TInteger');
                $this->FMetaInfo->setValue($Object, $Value->Unbox());
                break;
            case 'float':
                /**@var $Value \FrameworkDSW\System\TFloat**/
                TType::Object($Value, 'FrameworkDSW\System\TFloat');
                $this->FMetaInfo->setValue($Object, $Value->Unbox());
                break;
            case 'string':
                /**@var $Value \FrameworkDSW\System\TString**/
                TType::Object($Value, 'FrameworkDSW\System\TString');
                $this->FMetaInfo->setValue($Object, $Value->Unbox());
                break;
            default:
                if ($this->getType()->IsArray()) {
                    /**@var $Value \FrameworkDSW\Reflection\TArray**/
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
 * \FrameworkDSW\Reflection\TProperty
 * @author	许子健
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
     * @param    \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @param    string $Name
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($Class, $Name) {
        TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T'=>null]]); //FIXME !!!!!
        TType::String($Name);

        try {
            $mClassMetaInfo = new \ReflectionClass($Class->getName());
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter(); //TODO
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\Reflection\TClass<T: ?>>
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
                throw new EInvalidParameter();//TODO ex.
            }
            $this->ParseParameterValues($DocComment);
        }
        $mType = $this->FParameterValues[0];
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TClass <T: ?>
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
                throw new EInvalidParameter();//TODO ex.
            }
            $this->ParseParameterValues($mDocComment);
        }
        $mType = $this->FParameterValues[0];
        TClass::PrepareGeneric(['T' => $mType]);
        return new TClass();
    }

    /**
     * descHere
     * @param    \FrameworkDSW\System\IInterface $Object
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\System\IInterface
     */
    public function GetValue($Object) {
        TType::Object($Object, 'FrameworkDSW\System\IInterface');
        try {
            $mRaw = $this->FGetterMetaInfo->invoke($Object);
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
     * @param    \FrameworkDSW\System\IInterface $Object
     * @param    \FrameworkDSW\System\IInterface $Value
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function SetValue($Object, $Value) {
        TType::Object($Object, 'FrameworkDSW\System\IInterface');
        TType::Object($Value, 'FrameworkDSW\System\IInterface');
        try {
            $mType = $this->getType();
            switch ($mType->getName()) {
                case 'boolean':
                    /**@var $Value \FrameworkDSW\System\TBoolean**/
                    TType::Object($Value, 'FrameworkDSW\System\TBoolean');
                    $this->FSetterMetaInfo->invoke($Object, $Value->Unbox());
                    break;
                case 'integer':
                    /**@var $Value \FrameworkDSW\System\TInteger**/
                    TType::Object($Value, 'FrameworkDSW\System\TInteger');
                    $this->FSetterMetaInfo->invoke($Object, $Value->Unbox());
                    break;
                case 'float':
                    /**@var $Value \FrameworkDSW\System\TFloat**/
                    TType::Object($Value, 'FrameworkDSW\System\TFloat');
                    $this->FGetterMetaInfo->setValue($Object, $Value->Unbox());
                    break;
                case 'string':
                    /**@var $Value \FrameworkDSW\System\TString**/
                    TType::Object($Value, 'FrameworkDSW\System\TString');
                    $this->FSetterMetaInfo->invoke($Object, $Value->Unbox());
                    break;
                default:
                    if ($this->getType()->IsArray()) {
                        /**@var $Value \FrameworkDSW\Reflection\TArray**/
                        TType::Object($Value, 'FrameworkDSW\Reflection\TArray');
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

        TClass::PrepareGeneric($this->GenericArgs());
        $mClass = new TClass();

        $this->FClassName = $this->SetClass($mClass);
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TClass <T>[]
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
     * @param    \FrameworkDSW\System\IInterface[] $Parameters
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    T
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
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
        }
        $this->ParseParameterValues($mDocComment);
        foreach ($this->FParameterValues as $mIndex => $mType) {
            switch ($mType) {
            case 'boolean':
                /** @var $Parameters \FrameworkDSW\System\TBoolean[] **/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TBoolean');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'integer':
                /** @var $Parameters \FrameworkDSW\System\TInteger[] **/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TInteger');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'float':
                /** @var $Parameters \FrameworkDSW\System\TFloat[] **/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TFloat');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'string':
                /** @var $Parameters \FrameworkDSW\System\TString[] **/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TString');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'array':
                /** @var $Parameters \FrameworkDSW\Reflection\TArray[] **/
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
     * @param    \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @param    string $Name
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($Class, $Name) {
        parent::__construct();
        TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TClass[] <T: ?>
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public function getReturnType() {
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
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
     * @param    \FrameworkDSW\System\IInterface $Object
     * @param    \FrameworkDSW\System\IInterface[] $Parameters
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\System\IInterface
     */
    public function Invoke($Object, $Parameters) {
        TType::Object($Object, 'FrameworkDSW\System\IInterface');
        TType::Arr($Parameters);

        $mRawParameters = [];
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
        }
        $this->ParseParameterValues($mDocComment);
        array_shift($this->FParameterValues);
        foreach ($this->FParameterValues as $mIndex => $mType) {
            switch ($mType) {
            case 'boolean':
                /**@var $Parameters \FrameworkDSW\System\TBoolean[]**/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TBoolean');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'integer':
                /**@var $Parameters \FrameworkDSW\System\TInteger[]**/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TInteger');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'float':
                /**@var $Parameters \FrameworkDSW\System\TFloat[]**/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TFloat');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'string':
                /**@var $Parameters \FrameworkDSW\System\TString[]**/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TString');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'array':
                /**@var $Parameters \FrameworkDSW\Reflection\TArray[]**/
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
                if ($this->getReturnType()->IsArray()) {
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
     * @param    \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @param    string $Name
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($Class, $Name) {
        parent::__construct();
        TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => 'mixed']]);
        TType::String($Name);

        $mClassName = $this->SetClass($Class);
        $this->FName = $Name;
        try {
            $this->FMetaInfo = new \ReflectionMethod($mClassName, "signal{$Name}");
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TClass[] <T: ?>
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
     * @param    \FrameworkDSW\System\IInterface $Object
     * @param    \FrameworkDSW\System\IInterface[] $Parameters
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function DispatchSignal($Object, $Parameters) {
        TType::PrepareGeneric($Object, 'FrameworkDSW\System\IInterface');
        TType::Arr($Parameters);

        $mRawParameters = [];
        $mDocComment = $this->FMetaInfo->getDocComment();
        if ($mDocComment === false) {
            throw new EInvalidParameter(); //TODO
        }
        $this->ParseParameterValues($mDocComment);
        array_shift($this->FParameterValues);
        foreach ($this->FParameterValues as $mIndex => $mType) {
            switch ($mType) {
            case 'boolean':
                /**@var $Parameters \FrameworkDSW\System\TBoolean[]**/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TBoolean');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'integer':
                /**@var $Parameters \FrameworkDSW\System\TInteger[]**/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TInteger');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'float':
                /**@var $Parameters \FrameworkDSW\System\TFloat[]**/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TFloat');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'string':
                /**@var $Parameters \FrameworkDSW\System\TString[]**/
                TType::Object($Parameters[$mIndex], 'FrameworkDSW\System\TString');
                $mRawParameters[$mIndex] = $Parameters[$mIndex]->Unbox();
                break;
            case 'array':
                /**@var $Parameters \FrameworkDSW\Reflection\TArray[]**/
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
     * @param    \FrameworkDSW\Reflection\TClass $Class <T>
     * @param    string $Name
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($Class, $Name) {
        parent::__construct();
        TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => 'mixed']]);
        TType::String($Name);

        $mClassName = $this->SetClass($Class);
        $this->FName = $Name;
        try {
            $this->FMetaInfo = new \ReflectionMethod($mClassName, "slot{$Name}");
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return    \FrameworkDSW\Reflection\TClass[] <T: ?>
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
