<?php
include 'FrameworkDSW/System.php';

use System\TObject;
use System\Utilities\TType;
use System\Utilities\EInvalidTypeCasting;
use System\Utilities\EInvalidObjectCasting;

class TChild extends TObject {
    /**
     * @param	T	$p
     */
    public function Func($p) {
        try {
            TType::Type($p, $this->GenericArg('T'));
        }
        catch (EInvalidTypeCasting $e) {
            echo get_class($e) . ' ($p is <div style="border: 1px solid"> ' . var_export($p, true) . '</div>)<br/>';
        }
    }

    /**
     * @param	T	$t
     * @param	P	$p
     */
    public function FuncTwo($t, $p) {
        try {
            TType::Type($t, $this->GenericArg('T'));
        }
        catch (EInvalidTypeCasting $e) {
            echo get_class($e) . ' ($t is <div style="border: 1px solid"> ' . var_export($t, true) . '</div>)<br/>';
        }
        try {
            TType::Type($p, $this->GenericArg('P'));
        }
        catch (EInvalidTypeCasting $e) {
            echo get_class($e) . ' ($p is <div style="border: 1px solid"> ' . var_export($p, true) . '</div>)<br/>';
        }
    }
}
echo '<p>TEST 1</p>';
TChild::PrepareGeneric(array ('T' => 'integer'));
$obj = new TChild();
$obj->Func(0);
$obj->Func(new TObject()); //此处出错！不是integer
$obj->Func(true); //不出错，因为true是可以转换成1的，根据PHP的规则
$obj->Func('string'); //不出错，因为可以转换成0，根据PHP的规则

echo '<p>TEST 2</p>';
TChild::PrepareGeneric(array ('T' => 'integer', 'P' => 'System\TObject'));
$obj = new TChild();
$obj->FuncTwo(0, new TObject());
$obj->FuncTwo(new TObject(), 0); //出错，TObject不是integer，0不是TObject

class TComplex extends TObject {
    /**
     * 
     * @param	T	$t
     */
    public function Func($t) {
        TType::Type($t, $this->GenericArg('T'));
    }
}

echo '<p>TEST 3</p>';
TChild::PrepareGeneric(array ('T' => 'integer', 'P' => 'boolean'));
$c = new TChild();
TComplex::PrepareGeneric(array ('T' => array ('TChild' => array ('T' => 'integer', 'P' => 'boolean'))));
$obj = new TComplex();
$obj->Func($c);
$obj->Func(true);//出错：true不是TChild<T: integer, P: boolean>