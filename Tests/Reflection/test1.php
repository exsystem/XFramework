<?php
namespace Test;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Reflection\EIllegalAccess;
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\Reflection\TModifiers;
use FrameworkDSW\Reflection\TNamespace;
use FrameworkDSW\System\TBoolean;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TString;
use FrameworkDSW\Utilities\TType;

require_once 'FrameworkDSW/Framework.php';

/**
 * Class Foo
 * params <A: ?, B: \FrameworkDSW\System\TObject, C: \FrameworkDSW\Containers\TMap<K: string, V: string>>
 * extends \FrameworkDSW\Containers\TMap<K: integer, V: \FrameworkDSW\Containers\TList<T: integer>>
 * @package Test
 */
class Foo extends TMap {
    /**
     * @var string
     */
    private $FString = '';
    /**
     * @var \FrameworkDSW\System\TObject
     */
    private $FPrivateTObject = null;
    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\System\TObject> hello
     */
    private $FPublicTMap = null;

    /**
     * @param string $Param1
     * @param integer $Param2
     * @return boolean
     */
    public function PrivateMethod1($Param1, $Param2) {
        TType::String($Param1);
        TType::Int($Param2);
        return ($Param1 == 'true' && $Param2 == 1);
    }

    /**
     * @param \FrameworkDSW\Containers\IList  $Param1 <T: integer>
     */
    public function PublicMethod1($Param1) {
        echo 11;
    }
}

echo "==NAMESPACE==\n";
$ns = TNamespace::getNamespace('Test');
echo $ns->getName(), "\n";
Framework::Free($ns);

echo "==CLASS==\n";
TClass::PrepareGeneric(['T' => ['Test\Foo' => ['A' => 'string', 'B' => 'FrameworkDSW\System\TObject', 'C' => ['FrameworkDSW\Containers\TMap' => ['K' => 'string', 'V' => 'string']], 'K' => 'integer', 'V' => ['FrameworkDSW\Containers\TList' => ['T' => 'integer']]]]]);
$mFooClass = new TClass();
echo $mFooClass->getName(), "\n";
echo $mFooClass->getParentClass()->getGenericsValues()['K']->getName(), "\n";

echo "==FIELDS==\n";
$mFields = $mFooClass->getDeclaredFields();
foreach ($mFields as $mField) {
    if ($mField->getModifiers()->IsIn(TModifiers::ePrivate)) {
        echo 'PRIVATE ';
    }
    if ($mField->getModifiers()->IsIn(TModifiers::ePublic)) {
        echo 'PUBLIC ';
    }
    echo $mField->getName(), ': ';
    $mFieldType = $mField->getType();
    echo $mFieldType->getName();

    try {
        $mFieldTypeGenericsValue = $mFieldType->getGenericsValues();
        if ($mFieldTypeGenericsValue !== null) {
            /** @var \FrameworkDSW\Reflection\TClass $mType */
            echo '<';
            foreach ($mFieldTypeGenericsValue as $mName => $mType) {
                echo $mName, ': ', $mType->getName(), ', ';
                Framework::Free($mType);
            }
            echo '>';
            Framework::Free($mFieldTypeGenericsValue);
        }
    }
    catch (EIllegalAccess $Ex) {

    }

    echo "\n";
    Framework::Free($mFieldType);
    Framework::Free($mField);
}

echo "==METHOD==\n";
$mMethod = $mFooClass->GetMethod('PrivateMethod1');
$mReturnType = $mMethod->getReturnType();
echo $mReturnType->getName(), "\n";
Framework::Free($mReturnType);

echo "==DYNAMIC METHOD INVOCATION==\n";
$mInstance = $mFooClass->NewInstance([new TBoolean(true)]);
$mResultValue = $mMethod->Invoke($mInstance, [new TString('true'), new TInteger(1)]);
Framework::Free($mInstance);

TType::Object($mResultValue, 'FrameworkDSW\System\TBoolean');
/** @var \FrameworkDSW\System\TBoolean $mResultValue */
if ($mResultValue->Unbox()) {
    echo 'true';
}
else {
    echo 'false';
}
Framework::Free($mResultValue);

Framework::Free($mMethod);
Framework::Free($mFooClass);