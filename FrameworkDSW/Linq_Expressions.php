<?php
/**
 * Linq_Expressions.php
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 30
 */

require_once 'FrameworkDSW/System.php';

// FIXME: 去除PHP表達式不需要的符號。


/**
 * TExpressionType
 * 表示在表達式樹結點的表達式類型的枚舉類型。
 *
 * @author 许子健
 */
final class TExpressionType extends TEnum {
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
     * 按位取反運算符，例如：“~$a”。
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
 * @author	许子健
 */
abstract class TExpression extends TObject {
    
    /**
     * 表达式结点的类型。
     * 
     * @var	TExpressionType
     */
    protected $FNodeType = null;

    /**
     * 移进该结点。
     * @param	TExpressionVisitor	$Visitor    表达式访问器。
     * @return	TExpression    表达式本身，或者替代自己的表达式。
     */
    protected function Accept($Visitor) {
        return $Visitor->Visit($this);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function Add($Left, $Right) {
        return self::MakeBinary(TExpressionType::eAdd(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function AddAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAddAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function AddAssignChecked($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAddAssignChecked(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function AddChecked($Left, $Right, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAddChecked(), $Left, $Right, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function AndBitwise($Left, $Right) {
        return self::MakeBinary(TExpressionType::eAnd(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function AndAlso($Left, $Right) {
        return self::MakeBinary(TExpressionType::eAndAlso(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function AndAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAndAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Array
     * @param	TExpression	$Index
     * @return	TBinaryExpression
     */
    public static function ArrayIndexInOneDimensional($Array, $Index) {
        return self::MakeBinary(TExpressionType::eArrayIndex(), $Array, $Index);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @return	TBinaryExpression
     */
    public static function Assign($To, $By) {
        return self::MakeBinary(TExpressionType::eAssign(), $To, $By);
    }

    /**
     * descHere
     * @param	TExpression	$Test
     * @param	TExpression	$IfNotNull
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function Coalesce($Test, $IfNotNull, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eCoalesce(), $Test, $IfNotNull, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Test
     * @param	TExpression	$IfTrue
     * @param	TExpression	$IfFalse
     * @param	mixed	$Type
     * @return	TConditionalExpression
     */
    public static function Condition($Test, $IfTrue, $IfFalse, $Type = null) {
        TType::Object($Test, 'TExpression');
        TType::Object($IfTrue, 'TExpression');
        TType::Object($IfFalse, 'TExpression');
        
        return new TConditionalExpression($Test, $IfTrue, $IfFalse, $Type);
    }

    /**
     * descHere
     * @param	TObject	$Value
     * @param	mixed	$Type
     * @return	TConstantExpression
     */
    public static function Constant($Value, $Type = null) {
        TType::Object($Value, 'TObject');
        
        return new TConstantExpression($Value, $Type);
    }

    /**
     * descHere
     * @param	mixed	$Type
     * @return	TDefaultExpression
     */
    public static function DefaultValue($Type) {
        return new TDefaultExpression($Type);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function Divide($Left, $Right) {
        return self::MakeBinary(TExpressionType::eDivide(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function DivideAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eDivideAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @return TExpression
     */
    protected function DoReduce() {
        throw new EInvalidParameter();
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @param	boolean	$LiftToNull
     * @return	TBinaryExpression
     */
    public static function Equal($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpresion	$Right
     * @return	TBinaryExpression
     */
    public static function ExclusiveOr($Left, $Right) {
        return self::MakeBinary(TExpressionType::eExclusiveOr(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function ExclusiveOrAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eExclusiveOrAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getCanReduce() {
        return false;
    }

    /**
     * descHere
     * @return	TExpressionType
     */
    public function getNodeType() {
        return $this->FNodeType;
    }

    /**
     * descHere
     * @return	mixed
     */
    public abstract function getType();

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @param	boolean	$LiftToNull
     * @return	TBinaryExpression
     */
    public static function GreaterThan($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eGreaterThan(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @param	boolean	$LiftToNull
     * @return	TBinaryExpression
     */
    public static function GreaterThanOrEqual($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eGreaterThanOrEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     * @param	TExpression	$Test
     * @param	TExpression	$IfTrue
     * @return	TConditionalExpression
     */
    public static function IfThen($Test, $IfTrue) {
        TType::Object($Test, 'TExpression');
        TType::Object($IfTrue, 'TExpression');
        
        return new TConditionalExpression($Test, $IfTrue, null);
    }

    /**
     * descHere
     * @param	TExpression	$Test
     * @param	TExpression	$IfTrue
     * @param	TExpression	$IfFalse
     * @return	TConditionalExpression
     */
    public static function IfThenElse($Test, $IfTrue, $IfFalse) {
        TType::Object($Test, 'TExpression');
        TType::Object($IfTrue, 'TExpression');
        TType::Object($IfFalse, 'TExpression');
        
        return new TConditionalExpression($Test, $IfTrue, $IfFalse);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function LeftShift($Left, $Right) {
        return self::MakeBinary(TExpressionType::eLeftShift(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function LeftShiftAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eLeftShiftAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @param	boolean	$LiftToNull
     * @return	TBinaryExpression
     */
    public static function LessThan($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eLessThan(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @param	boolean	$LiftToNull
     * @return	TBinaryExpression
     */
    public static function LessThanOrEqual($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eLessThanOrEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     * @param	TExpressionType	$ExpressionType
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @param	boolean	$LiftToNull
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function MakeBinary($ExpressionType, $Left, $Right, $LiftToNull = false, $Conversion = null) {
        return new TBinaryExpression($Left, $Right, $ExpressionType, $Conversion, $LiftToNull);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function Modulo($Left, $Right) {
        return self::MakeBinary(TExpressionType::eModulo(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function ModuloAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eModuloAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function Multiply($Left, $Right) {
        return self::MakeBinary(TExpressionType::eMultiply(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function MultiplyAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eMultiplyAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function MultiplyAssignChecked($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eMultiplyAssignChecked(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function MultiplyChecked($Left, $Right) {
        return self::MakeBinary(TExpressionType::eMultiplyChecked(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @param	boolean	$LiftToNull
     * @return	TBinaryExpression
     */
    public static function NotEqual($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eNotEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function OrBitwise($Left, $Right) {
        return self::MakeBinary(TExpressionType::eOr(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function OrAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eOrAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function OrElse($Left, $Right) {
        return self::MakeBinary(TExpressionType::eOrElse(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function Power($Left, $Right) {
        return self::MakeBinary(TExpressionType::ePower(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function PowerAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::ePowerAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @return	TExpression
     */
    public function Reduce() {
        return $this->DoReduce();
    }

    /**
     * descHere
     * @return	TExpression
     */
    public function ReduceAndCheck() {
        throw new EInvalidParameter();
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function ReferenceEqual($Left, $Right) {
        return self::MakeBinary(TExpressionType::eEqual(), $Left, $Right); //FIXME: proper type is what?
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function ReferenceNotEqual($Left, $Right) {
        return self::MakeBinary(TExpressionType::eNotEqual(), $Left, $Right); //FIXME: proper type is what?
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function RightShift($Left, $Right) {
        return self::MakeBinary(TExpressionType::eRightShift(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function RightShiftAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eRightShiftAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @return	TBinaryExpression
     */
    public static function Substract($Left, $Right) {
        return self::MakeBinary(TExpressionType::eSubtract(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function SubstractAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eSubtractAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$To
     * @param	TExpression	$By
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function SubstractAssignChecked($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eSubtractAssignChecked(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Left
     * @param	TExpression	$Right
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public static function SubstractChecked($Left, $Right, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eSubtractChecked(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpressionVisitor	$Visitor
     * @return	TExpression
     */
    protected function VisitChildren($Visitor) {
    }

}

/**
 * TConstantExpression
 * @author	许子健
 */
final class TConstantExpression extends TExpression {
    
    /**
     * @var	IPrimitive <T: ?>
     */
    private $FValue = null;
    /**
     * 
     * @var mixed
     */
    private $FType = null;

    /**
     * descHere
     * @param	TObject	$Value
     * @param	mixed	$Type
     */
    protected function __construct($Value, $Type = null) {
        parent::__construct();
        TType::Object($Value);
        if ($Type == null) {
            $this->FType = $Value->ObjectType();
        }
        else {
            $this->FType = $Type;
        }
        $this->FValue = $Value;
        $this->FNodeType = TExpressionType::eConstant();
    }

    /**
     * descHere
     */
    public function __destruct() {
        Framework::Free($this->FValue);
        parent::__destruct();
    }

    /**
     * descHere
     * @param	TExpressionVisitor	$Visitor
     * @return	TExpression
     */
    protected function Accept($Visitor) {
        //TODO: visit constant.
    }

    /**
     * descHere
     * @return	mixed
     */
    public function getType() {
        return $this->FType;
    }

    /**
     * descHere
     * @return	IPrimitive <T: ?>
     */
    public function getValue() {
        return $this->FValue;
    }

}

/**
 * TBinaryExpression
 * @author	许子健
 */
final class TBinaryExpression extends TExpression {
    
    /**
     * @var	boolean
     */
    private $FLiftToNull = false;
    /**
     * @var	TExpression
     */
    private $FLeft = null;
    /**
     * @var	TExpression
     */
    private $FRight = null;
    /**
     * 
     * @var TLamdaExpression
     */
    private $FConversion = null;

    /**
     * descHere
     * @param	TObject	$Left
     * @param	TObject	$Right
     * @param	TExpressionType	$NodeType
     * @param	TLamdaExpression	$Conversion
     * @param	boolean	$LiftToNull
     */
    protected function __construct($Left, $Right, $NodeType, $Conversion = null, $LiftToNull = false) {
        parent::__construct();
        
        TType::Object($NodeType, 'TExpressionType');
        TType::Object($Left, 'TExpression');
        TType::Object($Right, 'TExpression');
        TType::Object($Conversion, 'TLamdaExpression');
        TType::Bool($LiftToNull);
        
        $this->FLeft = $Left;
        $this->FRight = $Right;
        $this->FNodeType = $NodeType;
        $this->FConversion = $Conversion;
        $this->$FLiftToNull = $LiftToNull;
    }

    /**
     * descHere
     * @param	TExpressionVisitor	$Visitor
     * @return	TExpression
     */
    protected function Accept($Visitor) {
        return $Visitor->VisitBinary($this);
    }

    /**
     * descHere
     * @return    TExpression
     */
    protected function DoReduce() {
        if ($this->FLeft->IsInstanceOf('TConstantExpression')) {
            $mLeftValue = $this->FLeft->getValue();
        }
        if ($this->FRight->IsInstanceOf('TConstantExpression')) {
            $mRightValue = $this->FRight->getValue();
        }
        
        switch ($this->FNodeType) { //arithmetic and bitwise
            case TExpressionType::eAdd() :
            case TExpressionType::eAddChecked() :
                $mReducedValue = $mLeftValue->Unbox() + $mRightValue->Unbox();
                break;
            case TExpressionType::eSubtract() :
            case TExpressionType::eSubtractChecked() :
                $mReducedValue = $mLeftValue->Unbox() - $mRightValue->Unbox();
                break;
            case TExpressionType::eMultiply() :
            case TExpressionType::eMultiplyChecked() :
                $mReducedValue = $mLeftValue->Unbox() * $mRightValue->Unbox();
                break;
            case TExpressionType::eDivide() :
            case TExpressionType::eDivideChecked() :
                $mReducedValue = $mLeftValue->Unbox() / $mRightValue->Unbox();
                break;
            case TExpressionType::eModulo() :
                $mReducedValue = $mLeftValue->Unbox() % $mRightValue->Unbox();
                break;
            case TExpressionType::eAnd() :
                $mReducedValue = $mLeftValue->Unbox() & $mRightValue->Unbox();
                break;
            case TExpressionType::eOr() :
                $mReducedValue = $mLeftValue->Unbox() | $mRightValue->Unbox();
                break;
            case TExpressionType::eExclusiveOr() :
                $mReducedValue = $mLeftValue->Unbox() ^ $mRightValue->Unbox();
                break;
            case TExpressionType::eLeftShift() :
                $mReducedValue = $mLeftValue->Unbox() << $mRightValue->Unbox();
                break;
            case TExpressionType::eRightShift() :
                $mReducedValue = $mLeftValue->Unbox() >> $mRightValue->Unbox();
                break;
            case TExpressionType::ePower() :
                $mReducedValue = pow($mLeftValue->Unbox(), $mRightValue->Unbox());
                if (is_int($mReducedValue)) {
                    return TExpression::Constant(new TInteger($mReducedValue), 'integer');
                }
                else {
                    return TExpression::Constant(new TInteger($mReducedValue), 'float');
                }
                break;
            default : //logical
                switch ($this->FNodeType) {
                    case TExpressionType::eAndAlso() :
                        $mReducedValue = ($mLeftValue->Unbox() && $mRightValue->Unbox());
                        break;
                    case TExpressionType::eOrElse() :
                        $mReducedValue = ($mLeftValue->Unbox() || $mRightValue->Unbox());
                        break;
                    case TExpressionType::eGreaterThan() :
                        $mReducedValue = ($mLeftValue->Unbox() > $mRightValue->Unbox());
                        break;
                    case TExpressionType::eLessThan() :
                        $mReducedValue = ($mLeftValue->Unbox() < $mRightValue->Unbox());
                        break;
                    case TExpressionType::eGreaterThanOrEqual() :
                        $mReducedValue = ($mLeftValue->Unbox() >= $mRightValue->Unbox());
                        break;
                    case TExpressionType::eLessThanOrEqual() :
                        $mReducedValue = ($mLeftValue->Unbox() <= $mRightValue->Unbox());
                        break;
                    case TExpressionType::eEqual() :
                        $mReducedValue = ($mLeftValue->Unbox() == $mRightValue->Unbox());
                        break;
                    case TExpressionType::eNotEqual() :
                        $mReducedValue = ($mLeftValue->Unbox() != $mRightValue->Unbox());
                        break;
                }
                return TExpression::Constant(new TBoolean($mReducedValue), 'boolean');
                break;
        }
        
        if (($mLeftValue->GenericArg('T') == 'float') || ($mRightValue->GenericArg('T') == 'float')) {
            TType::Float($mReducedValue);
            return TExpression::Constant(new TFloat($mReducedValue), 'float');
        }
        else {
            TType::Int($mReducedValue);
            return TExpression::Constant(new TInteger($mReducedValue), 'integer');
        }
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getCanReduce() {
        return ($this->FLeft->getNodeType() == TExpressionType::eConstant()) && ($this->FRight->getNodeType() == TExpressionType::eConstant()) && ($this->FNodeType != TExpressionType::eArrayIndex()) && ($this->FNodeType != TExpressionType::eAssign());
    }

    /**
     * descHere
     * @return	TLamdaExpression
     */
    public function getConversion() {
        return $this->FConversion;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getIsLifted() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getIsLiftedToNull() {
        return $this->$FLiftToNull && $this->getIsLifted();
    }

    /**
     * descHere
     * @return	TExpression
     */
    public function getLeft() {
        return $this->FLeft;
    }

    /**
     * descHere
     * @return	TExpression
     */
    public function getRight() {
        return $this->FRight;
    }

    /**
     * descHere
     * @return	mixed
     */
    public function getType() {
    }

    /**
     * descHere
     * @param	TObject	$Left
     * @param	TObject	$Right
     * @param	TLamdaExpression	$Conversion
     * @return	TBinaryExpression
     */
    public function Update($Left, $Right, $Conversion) {
    }

}

/**
 * TConditionalExpression
 * @author	许子健
 */
final class TConditionalExpression extends TExpression {
    
    /**
     * @var	TExpression
     */
    private $FIfFalse = null;
    /**
     * @var	TExpression
     */
    private $FIfTrue = null;
    /**
     * @var	TExpression
     */
    private $FTest = null;
    /**
     * @var	mixed
     */
    private $FType = null;

    /**
     * descHere
     * @param	TExpression	$Test
     * @param	TExpression	$IfTrue
     * @param	TExpression	$IfFalse
     * @param	mixed	$Type
     */
    protected function __construct($Test, $IfTrue, $IfFalse, $Type = null) {
    }

    /**
     * descHere
     * @param	TExpressionVisitor	$Visitor
     * @return	TExpression
     */
    protected function Accept($Visitor) {
    }

    /**
     * descHere
     * @return	TExpression
     */
    public function getIfFlase() {
    }

    /**
     * descHere
     * @return	TExpression
     */
    public function getIfTrue() {
    }

    /**
     * descHere
     * @return	TExpression
     */
    public function getTest() {
    }

    /**
     * descHere
     * @return	mixed
     */
    public function getType() {
    }

    /**
     * descHere
     * @param	TExpression	$Test
     * @param	TExpression	$IfTrue
     * @param	TExpression	$IfFalse
     * @return	TConditionalExpression
     */
    public function Update($Test, $IfTrue, $IfFalse) {
    }

}

/**
 * TDefaultExpression
 * @author	许子健
 */
class TDefaultExpression extends TExpression {
    
    /**
     * @var	mixed
     */
    private $FType = null;

    /**
     * descHere
     * @param	mixed	$Type
     */
    protected function __construct($Type) {
    }

    /**
     * descHere
     * @return    TExpression
     */
    protected function DoReduce() {
    }

    /**
     * descHere
     * @return	mixed
     */
    public function getType() {
    }

}

/**
 * TExpressionVisitor
 * @author	许子健
 */
abstract class TExpressionVisitor extends TObject {

    /**
     * descHere
     * @param	TExpression	$Expression
     * @return	TExpression
     */
    public function Visit($Expression) {
    }

    /**
     * descHere
     * @return	int
     */
    public function VisitBinary() {
    }

    /**
     * descHere
     * @param	TConstantExpression	$Expression
     * @return	TExpression
     */
    protected function VisitConstant($Expression) {
    }

}