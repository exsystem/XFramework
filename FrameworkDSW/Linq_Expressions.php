<?php
/**
 * Linq_Expressions.php
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 30
 */

// FIXME: 去除PHP表達式不需要的符號。

/**
 * TExpressionType
 * 表示在表達式樹結點的表達式類型的枚舉類型。
 *
 * @author 许子健
 */
class TExpressionType extends TEnum {
    /**
     * 加法運算符，例如“$a+$b”。
     *
     * @var integer
     */
    const eAdd = 0;
    /**
     * 帶溢出檢測的加法運算符。例如“$a+$b”。
     *
     * @var integer
     */
    const eAddChecked = 1;
    /**
     * 位与運算符，例如“$a & $b”。
     *
     * @var integer
     */
    const eAnd = 2;
    /**
     * 邏輯与運算符，例如“$a && $b”。
     *
     * @var integer
     */
    const eAndAlso = 3;
    /**
     * 數組長度運算符，例如“count($array)”。
     *
     * @var integer
     */
    const eArrayLength = 4;
    /**
     * 數組取元素符，例如“$Array[$i]”。
     *
     * @var integer
     */
    const eArrayIndex = 5;
    /**
     * 方法調用，例如“$obj->method()”。
     *
     * @var integer
     */
    const eCall = 6;
    /**
     * 省略中間元的條件運算符，例如“$a ?: $b”。
     *
     * @var integer
     */
    const eCoalesce = 7;
    /**
     * 條件運算符，例如：“$a ? $b : $c”。
     *
     * @var integer
     */
    const eConditional = 8;
    /**
     * 常量。
     *
     * @var integer
     */
    const eConstant = 9;
    /**
     * 強制轉換，例如：“(inetger) $a”。不會檢測轉換是否有意義。
     *
     * @var integer
     */
    const eConvert = 10;
    /**
     * 強制轉換，例如：“(inetger) $a”。檢測轉換是否有意義。
     *
     * @var integer
     */
    const eConvertChecked = 11;
    /**
     * 除法運算符，例如“$a / $b”。
     *
     * @var integer
     */
    const eDivide = 12;
    /**
     * 相等比較運算符，例如：“$a == $b”。
     *
     * @var integer
     */
    const eEqual = 13;
    /**
     * 位異或運算符，例如：“$a ^ $b”。
     *
     * @var integer
     */
    const eExclusiveOr = 14;
    /**
     * 大於比較運算符，例如：“$a >= $b”。
     *
     * @var integer
     */
    const eGreaterThan = 15;
    /**
     * 不小於比較運算符，例如：“$a >= $b”。
     *
     * @var integer
     */
    const eGreaterThanOrEqual = 16;
    /**
     * 委托調用，例如：“$a()”。
     *
     * @var integer
     */
    const eInvoke = 17;
    const eLambda = 18;
    /**
     * 左移運算符，例如：“$a << $b”。
     *
     * @var integer
     */
    const eLeftShift = 19;
    /**
     * 小於運算符，例如：“$a < $b”。
     *
     * @var integer
     */
    const eLessThan = 20;
    /**
     * 不大於運算符，例如：“$a <= $b”。
     *
     * @var integer
     */
    const eLessThanOrEqual = 21;
    const eListInit = 22;
    /**
     * 成員調用，例如：“$a->b”。
     *
     * @var integer
     */
    const eMemberAccess = 23;
    const eMemberInit = 24;
    /**
     * 取餘運算符，例如：“$a % $b”。
     *
     * @var integer
     */
    const eModulo = 25;
    /**
     * 乘法運算符，例如：“$a * $b”。不進行溢出檢查。
     *
     * @var integer
     */
    const eMultiply = 26;
    /**
     * 帶有溢出檢查的乘法運算符，例如：“$a * $b”。
     *
     * @var integer
     */
    const eMultiplyChecked = 27;
    /**
     * 不帶溢出檢測的算術取反運算符，例如：“-$a”。
     *
     * @var integer
     */
    const eNegate = 28;
    /**
     * 一元加法運算符，例如：“+$a”。
     *
     * @var integer
     */
    const eUnaryPlus = 29;
    /**
     * 帶溢出檢測的算術取反運算符，例如：“-$a”。
     *
     * @var integer
     */
    const eNegateChecked = 30;
    /**
     * 構造方法調用符，例如：“new Foo()”。
     *
     * @var integer
     */
    const eNew = 31;
    const eNewArrayInit = 32;
    const eNewArrayBounds = 33;
    /**
     * 邏輯取反運算符，例如：“!$a”。
     *
     * @var integer
     */
    const eNot = 34;
    /**
     * 不等比較運算符，例如：“$a != $b”。
     *
     * @var integer
     */
    const eNotEqual = 35;
    /**
     * 位或運算符，例如：“$a | $b”。
     *
     * @var integer
     */
    const eOr = 36;
    /**
     * 邏輯或運算符，例如：“$a || $b”。
     *
     * @var integer
     */
    const eOrElse = 37;
    /**
     * 方法的參數。
     *
     * @var integer
     */
    const eParameter = 38;
    const ePower = 39;
    const eQuote = 40;
    /**
     * 算術右移運算符，例如：“$a >> $b”。
     *
     * @var integer
     */
    const eRightShift = 41;
    const eSubtract = 42;
    const eSubtractChecked = 43;
    const eTypeAs = 44;
    const eTypeIs = 45;
    const eAssign = 46;
    const eBlock = 47;
    const eDebugInfo = 48;
    const eDecrement = 49;
    const eDynamic = 50;
    const eDefault = 51;
    const eExtension = 52;
    const eGoto = 53;
    const eIncrement = 54;
    const eIndex = 55;
    const eLabel = 56;
    const eRuntimeVariables = 57;
    const eLoop = 58;
    const eSwitch = 59;
    const eThrow = 60;
    const eTry = 61;
    const eUnbox = 62;
    const eAddAssign = 63;
    const eAndAssign = 64;
    const eDivideAssign = 65;
    const eExclusiveOrAssign = 66;
    const eLeftShiftAssign = 67;
    const eModuloAssign = 68;
    const eMultiplyAssign = 69;
    const eOrAssign = 70;
    const ePowerAssign = 71;
    const eRightShiftAssign = 72;
    const eSubtractAssign = 73;
    const eAddAssignChecked = 74;
    const eMultiplyAssignChecked = 75;
    const eSubtractAssignChecked = 76;
    const ePreIncrementAssign = 77;
    const ePreDecrementAssign = 78;
    const ePostIncrementAssign = 79;
    const ePostDecrementAssign = 80;
    const eTypeEqual = 81;
    const eOnesComplement = 82;
    const eIsTrue = 83;
    const eIsFalse = 84;
}

/**
 * TExpression
 * 表示一个表达式的结点。
 *
 * @author 许子健
 */
abstract class TExpression extends TObject {
    /**
     * 表达式结点的类型。
     *
     * @var TExpressionType
     */
    protected $FNodeType = null;
    
    /**
     * 表达式得出的值的数据类型。
     *
     * @var mixed
     */
    protected $FType = null;

    /**
     * 移进该结点。
     *
     * @param $Visitor TExpressionVisitor
     *            表达式访问器。
     * @return TExpression 表达式本身，或者替代自己的表达式。
     */
    protected function Accept($Visitor) {
        return $Visitor->Visit($this);
    }

    /**
     * 访问该表达式结点的子结点。
     *
     * @param $Visitor TExpressionVisitor
     *            表达式子结点的访问器。
     * @return TExpression 表达式本身，或者替代自己的表达式。
     */
    protected function VisitChildren($Visitor) {
        return $Visitor->Visit($this);
    }

    /**
     * 检查表达式结点是否可规约。
     */
    protected function DoReduceAndCheck() {
    }

    /**
     * 是否能够规约。
     *
     * @return boolean 返回是否能够规约该表达式结点构成的子表达式。
     */
    public function getCanReduce() {
        return false;
    }

    /**
     * 获得该表达式结点的类型。
     *
     * @return TExpressionType 返回该表达式结点的类型。
     */
    public function getNodeType() {
        return $this->FNodeType;
    }

    /**
     * 获得表达式得出的值的数据类型。
     *
     * @return mixed 返回该表达式结果的数据类型。
     */
    public function getType() {
        return $this->FType;
    }

    /**
     * 规约该表达式结点构成的子表达式。
     *
     * @return TExpression 规约后的该表达式。
     */
    public function Reduce() {
        return $this;
    }

    /**
     * 规约该表达式结点构成的子表达式，并做检查。
     *
     * @return TExpression 规约后的该表达式。
     */
    public function ReduceAndCheck() {
        if (!$this->getCanReduce()) {
            throw new EInvalidParameter('The expression can not be reduced.');
        }
        $this->DoReduceAndCheck();
        return $this->Reduce();
    }
}

/**
 * TConstantExpression
 * 代表常量值的表达式。
 *
 * @author 许子健
 */
final class TConstantExpression extends TExpression {
    
    /**
     *
     * @var TObject 常量的值。基础类型需要转换成对象。
     */
    private $FValue = null;

    /**
     *
     * @see TExpression::Accept()
     * @param $Visitor TExpressionVisitor
     *            表达式访问器。
     * @return TExpression 表达式本身，或者替代自己的表达式。
     */
    protected function Accept($Visitor) {
        return $Visitor->VisitConstant($this);
    }

    /**
     * 构造方法。
     */
    public function __construct() {
        parent::__construct();
        $this->FNodeType = TExpressionType::eConstant();
    }

    /**
     * 析构方法。
     *
     * @see TObject::__destruct()
     */
    public function __destruct() {
        Framework::Free($this->FValue);
    }

    /**
     * 获得常量的值。
     *
     * @return TObject 当前常量的值。
     */
    public function getValue() {
        return $this->FValue;
    }

    /**
     * 设置常量的值。
     *
     * @param $Value TObject
     *            新常量的值。
     */
    public function setValue($Value) {
        TType::Object($Value);
        $this->FValue = $Value;
        $this->FType = $this->FValue->ObjectType();
    }
}

/**
 * TExpressionVisitor
 * TODO: complete this class.
 * 
 * @author 许子健
 */
abstract class TExpressionVisitor extends TObject {

    /**
     *
     * @param $Expression TConstantExpression           
     * @return TExpression
     */
    protected function VisitConstant($Expression) {
        return $Expression;
    }
}