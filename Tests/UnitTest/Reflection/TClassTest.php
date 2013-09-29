<?php

namespace UnitTest;
use FrameworkDSW\Containers\TList;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\IDelegate;
use FrameworkDSW\System\TDelegate;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TString;
use FrameworkDSW\Utilities\EInvalidObjectCasting;
use FrameworkDSW\Utilities\TType;

set_include_path(get_include_path() . PATH_SEPARATOR . '../../../');
require_once 'FrameworkDSW\Framework.php';

/**
 * Class TTesterDelegate
 * @package UnitTest
 */
interface TTesterDelegate extends IDelegate {
    /**
     * @param \FrameworkDSW\Reflection\TClass $Class <T: ?>
     * @param \ReflectionClass $RawClass
     * @param \PHPUnit_Framework_TestCase $TestCase
     * @param array $Test;
     */
    public function Invoke($Class, $RawClass, $TestCase, $Test);
}

/**
 * Class TClassTest
 * @package UnitTest
 */
class TClassTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \FrameworkDSW\Reflection\TClass <T: ?>
     */
    private $FClass = null;

    /**
     *
     */
    protected function setUp() {
        parent::setUp();
        TClass::PrepareGeneric(['T' => 'FrameworkDSW\System\TObject']);
        $this->FClass = new TClass();
    }

    /**
     *
     */
    protected function tearDown() {
        Framework::Free($this->FClass);
        parent::tearDown();
    }

    /**
     * @param array $Tests
     * @param \FrameworkDSW\System\TDelegate $Handler \UnitTest\TTesterDelegate
     */
    protected function ClassTester($Tests, $Handler) {
        foreach ($Tests as $mClassType => $mT) {
            Framework::Free($this->FClass);
            echo sprintf("\n=== 測試類型%s ===\n", $mClassType);
            TClass::PrepareGeneric(['T' => $mT]);
            $this->FClass = new TClass();
            try {
                $mRawClass = new \ReflectionClass($mClassType);
            }
            catch (\ReflectionException $Ex) {
                $mRawClass = null;
            }
            $Handler($this->FClass, $mRawClass, $this, $mT);
        }
    }

    /**
     *
     */
    public function testCast() {
        TClass::PrepareGeneric(['T' => ['FrameworkDSW\Containers\IList' => ['T' => 'string']]]);
        $mIListClass = new TClass();
        TList::PrepareGeneric(['T' => 'string']);
        $mList = new TList();
        /** @var \FrameworkDSW\Containers\IList $mCasted <T: string> */
        $mCasted = $mIListClass->Cast($mList);
        Framework::Free($mList);
        Framework::Free($mCasted);
    }

    /**
     *
     */
    public function testGetConstructor() {
        $mConstructor = $this->FClass->getConstructor();
        /** @var \FrameworkDSW\System\TObject $mInstance */
        $mInstance = $mConstructor->NewInstance([]);
        $this->assertEquals('FrameworkDSW\System\TObject', $mInstance->ClassType());
        Framework::Free($mInstance);
        Framework::Free($mConstructor);
    }

    /**
     *
     */
    public function testGetDeclaredField() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
        ];

        foreach ($mTests as $mClassType => $mT) {
            Framework::Free($this->FClass);
            echo sprintf("\n=== 測試類型%s ===\n", $mClassType);
            TClass::PrepareGeneric(['T' => $mT]);
            $this->FClass = new TClass();
            $mRawClass    = new \ReflectionClass($mClassType);
            $mRawFields   = $mRawClass->getProperties();
            $mField       = null;
            $mCount       = 0;
            foreach ($mRawFields as $mRawField) {
                ++$mCount;
                if ($mRawField->getDeclaringClass()->getName() == $mRawClass->getName()) {
                    $mField = $this->FClass->GetDeclaredField($mRawField->getName());
                    $this->assertEquals($mRawField->getName(), $mField->getName());
                    echo sprintf("%s由本類定義。\n", $mField->getName());
                    Framework::Free($mField);
                }
                else {
                    try {
                        $mField = $this->FClass->GetDeclaredField($mRawField->getName());
                        $this->fail($mRawField->getName());
                    }
                    catch (EInvalidParameter $Ex) {
                        echo sprintf("%s非本類定義。\n", $mRawField->getName());
                    }
                }
                Framework::Free($mField);
            }
            echo sprintf("共%s個數據成員。\n", $mCount);
        }

        try {
            $this->FClass->GetDeclaredField('FNotDefined');
        }
        catch (EInvalidParameter $Ex) {
            return;
        }
        $this->fail('FNotDefined應該沒有定義。');
    }

    /**
     *
     */
    public function testGetDeclaredFields() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
        ];

        foreach ($mTests as $mClassType => $mT) {
            Framework::Free($this->FClass);
            echo sprintf("\n=== 測試類型%s ===\n", $mClassType);
            TClass::PrepareGeneric(['T' => $mT]);
            $this->FClass    = new TClass();
            $mDeclaredFields = $this->FClass->getDeclaredFields();
            foreach ($mDeclaredFields as $mField) {
                echo sprintf("數據成員%s由本類定義。\n", $mField->getName());
            }
        }
    }

    /**
     *
     */
    public function testGetDeclaredMethod() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            foreach ($RawClass->getMethods() as $mRawMethod) {
                if ($mRawMethod->getName()[0] < 'A' || $mRawMethod->getName() > 'Z') {
                    echo sprintf("成員%s並非方法。\n", $mRawMethod->getName());
                }
                elseif ($mRawMethod->getDeclaringClass()->getName() == $RawClass->getName()) {
                    $mMethod = $Class->GetMethod($mRawMethod->getName());
                    echo sprintf("方法%s由本類定義。\n", $mMethod->getName());
                    Framework::Free($mMethod);
                }
                else {
                    try {
                        Framework::Free($Class->GetDeclaredMethod($mRawMethod->getName()));
                        $TestCase->fail(sprintf("方法%s非類型%s定義的成員。\n", $mRawMethod->getName(), $Class->getName()));
                    }
                    catch (EInvalidParameter $Ex) {
                        echo sprintf("方法%s非本類定義。\n", $mRawMethod->getName());
                    }
                }
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetDeclaredMethods() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            $mDeclaredMethods = $Class->getDeclaredMethods();
            $TestCase->assertNotEmpty($mDeclaredMethods);
            foreach ($RawClass->getMethods() as $mRawMethod) {
                if ($mRawMethod->getName()[0] < 'A' || $mRawMethod->getName() > 'Z') {
                    echo sprintf("成員%s並非方法。\n", $mRawMethod->getName());
                }
                elseif ($mRawMethod->getDeclaringClass()->getName() == $RawClass->getName()) {
                    $mFound = false;
                    echo sprintf("檢查方法%s是否為本類定義……\n", $mRawMethod->getName());
                    foreach ($mDeclaredMethods as $mMethod) {
                        if ($mMethod->getName() == $mRawMethod->getName()) {
                            $mFound = true;
                            break;
                        }
                    }
                    $TestCase->assertTrue($mFound);
                }
                else {
                    $mFound = false;
                    echo sprintf("檢查方法%s是否非本類定義……\n", $mRawMethod->getName());
                    foreach ($mDeclaredMethods as $mMethod) {
                        if ($mMethod->getName() == $mRawMethod->getName()) {
                            $mFound = true;
                            break;
                        }
                    }
                    $TestCase->assertFalse($mFound);
                }
            }
            foreach ($mDeclaredMethods as $mMethod) {
                Framework::Free($mMethod);
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetElementType() {
        $mTests = [
            'integer',
            'string[]',
            ['FrameworkDSW\Containers\IList[]' => ['T' => 'string']],
            'FrameworkDSW\System\TObject'
        ];

        foreach ($mTests as $mTest) {
            TClass::PrepareGeneric(['T' => $mTest]);
            $this->FClass = new TClass();
            echo $this->FClass->getElementType()->getName();
        }
    }

    /**
     *
     */
    public function testGetGenericValues() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            $mGenericsValues = $Class->getGenericsValues();
            /** @var string $mName */
            /** @var \FrameworkDSW\Reflection\TClass $mType <T: ?> */
            foreach ($mGenericsValues as $mName => $mType) {
                echo sprintf("<%s: %s>\n", $mName, $mType->getName());
            }
            $mValidation = [];
            foreach ($mGenericsValues as $mName => $mType) {
                $mValidation[$mName] = $mType->GenericArgs()['T'];
            }
            $this->assertEquals($Class->GenericArgs()['T'][$Class->getName()], $mValidation, sprintf('通過反射%s而獲取的泛型參數不正確！', $Class->getName()));
            Framework::Free($mGenericsValues);
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetInterfaces() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => ['string']
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            if ($RawClass === null) {
                try {
                    foreach ($Class->getInterfaces() as $i) {
                        Framework::Free($i);
                    }
                    $this->fail(sprintf('基礎類型%s不應該實現或繼承接口。', $Class->getName()));
                }
                catch (EInvalidObjectCasting $Ex) {
                    return;
                }
            }
            $mInterfaces = $Class->getInterfaces();
            foreach ($mInterfaces as $mInterface) {
                $this->assertContains($mInterface->getName(), $RawClass->getInterfaceNames(), sprintf('反射得到的接口%s不存在定義中！\n', $mInterface->getName()));
                echo sprintf("本類繼承或實現了%s。\n", $mInterface->getName());
                Framework::Free($mInterface);
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetField() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']]
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            $mRawFields = $RawClass->getProperties();
            foreach ($mRawFields as $mRawField) {
                echo sprintf("本類擁有數據成員%s。\n", $mRawField->getName());
                $mField = $Class->GetField($mRawField->getName());
                $this->assertEquals($mRawField->getName(), $mField->getName(), sprintf('反射得到的數據成員%s不相符于實際定義的%s！', $mField->getName(), $mRawField->getName()));
                Framework::Free($mField);
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetFields() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']]
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            $mRawFields = $RawClass->getProperties();
            foreach ($mRawFields as $mRawField) {
                echo sprintf("本類擁有數據成員%s。\n", $mRawField->getName());
                $mField = $Class->GetField($mRawField->getName());
                $TestCase->assertEquals($mRawField->getName(), $mField->getName(), sprintf('反射得到的數據成員%s不相符于實際定義的%s！', $mField->getName(), $mRawField->getName()));
                Framework::Free($mField);
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetMethod() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']]
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            $mRawMethods = $RawClass->getMethods();
            foreach ($mRawMethods as $mRawMethod) {
                if ($mRawMethod->getName()[0] < 'A' || $mRawMethod->getName() > 'Z') {
                    echo sprintf("成員%s並非方法。\n", $mRawMethod->getName());
                }
                else {
                    echo sprintf("本類擁有方法%s。\n", $mRawMethod->getName());
                    $mMethod = $Class->GetMethod($mRawMethod->getName());
                    $TestCase->assertEquals($mRawMethod->getName(), $mMethod->getName(), sprintf('反射得到的數據成員%s不相符于實際定義的%s！', $mMethod->getName(), $mRawMethod->getName()));
                    Framework::Free($mMethod);
                }
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetMethods() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']]
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            $mMethods        = $Class->getMethods();
            $mMethodsName    = [];
            $mRawMethodsName = [];
            foreach ($RawClass->getMethods() as $mRawMethod) {
                if ($mRawMethod->getName()[0] < 'A' || $mRawMethod->getName() > 'Z') {
                    echo sprintf("成員%s並非方法。\n", $mRawMethod->getName());
                }
                else {
                    $mRawMethodsName[] = $mRawMethod->getName();
                }
            }
            foreach ($mMethods as $mMethod) {
                $mMethodsName[] = $mMethod->getName();
                echo sprintf("本類擁有方法%s。\n", $mMethod->getName());
                Framework::Free($mMethod);
            }
            $this->assertEquals($mRawMethodsName, $mMethodsName, sprintf('反射得到的方法列表和定義的不一致！'));
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetName() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            if ($RawClass === null) {
                echo sprintf("類型名稱為%s。\n", $Class->getName());
            }
            else {
                $this->assertEquals($RawClass->getName(), $Class->getName(), sprintf('反射得到的類名稱和定義的不一致！'));
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetNamespace() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            if ($RawClass === null) {
                try {
                    $Class->getNamespace();
                    $this->fail(sprintf("類型%s不應該定義在命名空間中！", $Class->getName()));
                }
                catch (EInvalidParameter $Ex) {
                    echo sprintf("類型名稱為%s。\n", $Class->getName());
                }
            }
            else {
                $mNamespace=$Class->getNamespace();
                $this->assertEquals($RawClass->getNamespaceName(), $mNamespace->getName(), sprintf('反射得到的類名稱和定義的不一致！'));
                Framework::Free($mNamespace);
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetParentClass() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            if ($RawClass === null) {
                try {
                    $Class->getParentClass();
                    $this->fail(sprintf("類型%s不應該定義父類！", $Class->getName()));
                }
                catch (EInvalidObjectCasting $Ex) {
                    echo sprintf("類型名稱為%s，不可以獲取父類。\n", $Class->getName());
                }
            }
            else {
                $mParentClass = $Class->getParentClass();
                if ($mParentClass !== null) {
                    echo sprintf("父類名稱為%s。\n", $mParentClass->getName());
                    $this->assertEquals($RawClass->getParentClass()->getName(), $mParentClass->getName(), sprintf('反射得到的父類名稱和定義的不一致！'));
                    Framework::Free($mParentClass);
                }
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testGetSimpleName() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            if ($RawClass === null) {
                try {
                    $Class->getSimpleName();
                    $this->fail(sprintf("類型%s不應該定義短名稱！", $Class->getName()));
                }
                catch (EInvalidObjectCasting $Ex) {
                    echo sprintf("類型名稱為%s，不可以獲取短名稱。\n", $Class->getName());
                }
            }
            else {
                echo sprintf("短名稱為%s。\n", $Class->getSimpleName());
                $this->assertEquals($RawClass->getShortName(), $Class->getSimpleName(), sprintf('反射得到的短名稱和定義的不一致！'));
            }
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testIsArray() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            echo sprintf("本類%s數組。\n", $Class->IsArray()?'為':'非');
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testIsClass() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            echo sprintf("本類%s類。\n", $Class->IsClass()?'為':'非');
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testIsEnum() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            echo sprintf("本類%s枚舉。\n", $Class->IsEnum()?'為':'非');
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testIsInstance() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            //echo sprintf("本類%s類。\n", $Class->IsClass()?'為':'非');
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testIsInterface() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            echo sprintf("本類%s接口。\n", $Class->IsInterface()?'為':'非');
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testIsPrimitive() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            echo sprintf("本類%s基礎數據類型。\n", $Class->IsClass()?'為':'非');
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testIsSetStruct() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
            echo sprintf("本類%s集合。\n", $Class->IsSetStruct()?'為':'非');
        }, 'UnitTest\TTesterDelegate'));
    }

    /**
     *
     */
    public function testNewInstance() {
        $mTests = [
            'FrameworkDSW\Containers\IMap' => ['FrameworkDSW\Containers\IMap' => ['K' => 'string', 'V' => 'string']],
            TMap::ClassType()              => [TMap::ClassType() => ['K' => 'string', 'V' => 'string']],
            'string'                       => 'string'
        ];

        $this->ClassTester($mTests, new TDelegate(function ($Class, $RawClass, $TestCase, $Test) {
            /**@var \FrameworkDSW\Reflection\TClass $Class <T: ?> */
            /**@var \ReflectionClass $RawClass */
            /**@var \PHPUnit_Framework_TestCase $TestCase */
            /**@var array $Test */
            TType::Object($Class, ['FrameworkDSW\Reflection\TClass' => ['T' => null]]);
           // echo sprintf("本類%s類。\n", $Class->IsClass()?'為':'非');
        }, 'UnitTest\TTesterDelegate'));
    }
}
