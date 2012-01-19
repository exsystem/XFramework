<?php
/**
 * Linq_Expressions.php
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 30
 */

require_once 'FrameworkDSW/System.php';

// FIXME: 去除PHP表達式不需要的符號。
// FIXME: 暫不支持數組（需要等待反射架構完成<type, dimensions>, int => <int, 0>）


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
     * 移进该结点。
     *
     * @param $Visitor TExpressionVisitor           
     * @return TExpression 表达式本身，或者替代自己的表达式。
     */
    protected function Accept($Visitor) {
        return $Visitor->Visit($this);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function Add($Left, $Right) {
        return self::MakeBinary(TExpressionType::eAdd(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function AddAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAddAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function AddAssignChecked($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAddAssignChecked(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function AddChecked($Left, $Right, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAddChecked(), $Left, $Right, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function AndBitwise($Left, $Right) {
        return self::MakeBinary(TExpressionType::eAnd(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function AndAlso($Left, $Right) {
        return self::MakeBinary(TExpressionType::eAndAlso(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function AndAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAndAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Array TExpression           
     * @param $Index TExpression           
     * @return TBinaryExpression
     */
    public static function ArrayIndexInOneDimensional($Array, $Index) {
        return self::MakeBinary(TExpressionType::eArrayIndex(), $Array, $Index);
    }

    /**
     * descHere
     * @param	TExpression	$Array
     * @return	TUnaryExpression
     */
    public static static function ArrayLength($Array) {
        return self::MakeUnary(TExpressionType::eArrayLength(), $Array);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @return TBinaryExpression
     */
    public static function Assign($To, $By) {
        return self::MakeBinary(TExpressionType::eAssign(), $To, $By);
    }

    /**
     * descHere
     *
     * @param $Test TExpression           
     * @param $IfNotNull TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function Coalesce($Test, $IfNotNull, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eCoalesce(), $Test, $IfNotNull, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Test TExpression           
     * @param $IfTrue TExpression           
     * @param $IfFalse TExpression           
     * @param $Type mixed           
     * @return TConditionalExpression
     */
    public static function Condition($Test, $IfTrue, $IfFalse, $Type = null) {
        TType::Object($Test, 'TExpression');
        TType::Object($IfTrue, 'TExpression');
        TType::Object($IfFalse, 'TExpression');
        
        return new TConditionalExpression($Test, $IfTrue, $IfFalse, $Type);
    }

    /**
     * descHere
     *
     * @param $Value TObject           
     * @param $Type mixed           
     * @return TConstantExpression
     */
    public static function Constant($Value, $Type = null) {
        TType::Object($Value, 'TObject');
        
        return new TConstantExpression($Value, $Type);
    }

    /**
     * descHere
     * @param	TExpression	$Expression
     * @param	mixed	$Type
     * @return	TUnaryExpression
     */
    public static static function Convert($Expression, $Type) {
        return self::MakeUnary(TExpressionType::eConvert(), $Expression, $Type);
    }

    /**
     * descHere
     * @param	TExpression	$Expression
     * @param	mixed	$Type
     * @return	TUnaryExpression
     */
    public static static function ConvertChecked($Expression, $Type) {
        return self::MakeUnary(TExpressionType::eConvertChecked(), $Expression, $Type);
    }

    /**
     * descHere
     *
     * @param $Type mixed           
     * @return TDefaultExpression
     */
    public static function DefaultValue($Type) {
        return new TDefaultExpression($Type);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function Divide($Left, $Right) {
        return self::MakeBinary(TExpressionType::eDivide(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function DivideAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eDivideAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    protected function DoReduce() {
        throw new EInvalidParameter();
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @param $LiftToNull boolean           
     * @return TBinaryExpression
     */
    public static function Equal($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpresion           
     * @return TBinaryExpression
     */
    public static function ExclusiveOr($Left, $Right) {
        return self::MakeBinary(TExpressionType::eExclusiveOr(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function ExclusiveOrAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eExclusiveOrAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCanReduce() {
        return false;
    }

    /**
     * descHere
     *
     * @return TExpressionType
     */
    public function getNodeType() {
        return $this->FNodeType;
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public abstract function getType();

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @param $LiftToNull boolean           
     * @return TBinaryExpression
     */
    public static function GreaterThan($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eGreaterThan(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @param $LiftToNull boolean           
     * @return TBinaryExpression
     */
    public static function GreaterThanOrEqual($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eGreaterThanOrEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param $Test TExpression           
     * @param $IfTrue TExpression           
     * @return TConditionalExpression
     */
    public static function IfThen($Test, $IfTrue) {
        TType::Object($Test, 'TExpression');
        TType::Object($IfTrue, 'TExpression');
        
        return new TConditionalExpression($Test, $IfTrue, null);
    }

    /**
     * descHere
     *
     * @param $Test TExpression           
     * @param $IfTrue TExpression           
     * @param $IfFalse TExpression           
     * @return TConditionalExpression
     */
    public static function IfThenElse($Test, $IfTrue, $IfFalse) {
        TType::Object($Test, 'TExpression');
        TType::Object($IfTrue, 'TExpression');
        TType::Object($IfFalse, 'TExpression');
        
        return new TConditionalExpression($Test, $IfTrue, $IfFalse);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function LeftShift($Left, $Right) {
        return self::MakeBinary(TExpressionType::eLeftShift(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function LeftShiftAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eLeftShiftAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @param $LiftToNull boolean           
     * @return TBinaryExpression
     */
    public static function LessThan($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eLessThan(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @param $LiftToNull boolean           
     * @return TBinaryExpression
     */
    public static function LessThanOrEqual($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eLessThanOrEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param $ExpressionType TExpressionType           
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @param $LiftToNull boolean           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function MakeBinary($ExpressionType, $Left, $Right, $LiftToNull = false, $Conversion = null) {
        return new TBinaryExpression($Left, $Right, $ExpressionType, $Conversion, $LiftToNull);
    }

    /**
     * 
     * @param TExpression $Expression
     * @param string $MemberName
     * @param mixed $Type
     */
    public static function MakeMember($Expression, $MemberName, $Type) {
        return new TMemberExpression($Expression, $MemberName, $Type);
    }

    /**
     * descHere
     * @param	TExpressionType	$ExpressionType
     * @param	TExpression	$Operand
     * @param	mixed	$Type
     * @return	TUnaryExpression
     */
    public static static function MakeUnary($ExpressionType, $Operand, $Type) {
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function Modulo($Left, $Right) {
        return self::MakeBinary(TExpressionType::eModulo(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function ModuloAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eModuloAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function Multiply($Left, $Right) {
        return self::MakeBinary(TExpressionType::eMultiply(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function MultiplyAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eMultiplyAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function MultiplyAssignChecked($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eMultiplyAssignChecked(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function MultiplyChecked($Left, $Right) {
        return self::MakeBinary(TExpressionType::eMultiplyChecked(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$Expression
     * @return	TUnaryExpression
     */
    public static static function Negate($Expression) {
        return self::MakeUnary(TExpressionType::eNegate(), $Expression);
    }

    /**
     * descHere
     * @param	TExpression	$Expression
     * @return	TUnaryExpression
     */
    public static static function NegateChecked($Expression) {
        return self::MakeUnary(TExpressionType::eNegateChecked(), $Expression);
    }

    /**
     * descHere
     * @param	TExpression	$Expression
     * @return	TUnaryExpression
     */
    public static static function Not($Expression) {
        return self::MakeUnary(TExpressionType::eNot(), $Expression);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @param $LiftToNull boolean           
     * @return TBinaryExpression
     */
    public static function NotEqual($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eNotEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function OrBitwise($Left, $Right) {
        return self::MakeBinary(TExpressionType::eOr(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function OrAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eOrAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function OrElse($Left, $Right) {
        return self::MakeBinary(TExpressionType::eOrElse(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function Power($Left, $Right) {
        return self::MakeBinary(TExpressionType::ePower(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function PowerAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::ePowerAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     * @param	TExpression	$Expression
     * @return	TUnaryExpression
     */
    public static static function Quote($Expression) {
        return self::MakeUnary(TExpressionType::eQuote(), $Expression);
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    public function Reduce() {
        if ($this->getCanReduce()) {
            return $this->DoReduce();
        }
        else {
            throw new EInvalidParameter();
        }
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    public function ReduceAndCheck() {
        throw new EInvalidParameter();
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function ReferenceEqual($Left, $Right) {
        return self::MakeBinary(TExpressionType::eEqual(), $Left, $Right); // FIXME:
    // proper
    // type
    // is
    // what?
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function ReferenceNotEqual($Left, $Right) {
        return self::MakeBinary(TExpressionType::eNotEqual(), $Left, $Right); // FIXME:
    // proper
    // type
    // is
    // what?
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function RightShift($Left, $Right) {
        return self::MakeBinary(TExpressionType::eRightShift(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function RightShiftAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eRightShiftAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @return TBinaryExpression
     */
    public static function Substract($Left, $Right) {
        return self::MakeBinary(TExpressionType::eSubtract(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function SubstractAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eSubtractAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $To TExpression           
     * @param $By TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function SubstractAssignChecked($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eSubtractAssignChecked(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public static function SubstractChecked($Left, $Right, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eSubtractChecked(), $Left, $Right);
    }

    /**
     * descHere
     * @param	TExpression	$Expression
     * @param	mixed	$Type
     * @return	TUnaryExpression
     */
    public static static function TypeAs($Expression, $Type) {
        return self::MakeUnary(TExpressionType::eTypeAs(), $Expression, $Type());
    }

    /**
     * descHere
     * @param	TExpression	$Expression
     * @return	TUnaryExpression
     */
    public static static function UnaryPlus($Expression) {
        return self::MakeUnary(TExpressionType::eUnaryPlus(), $Expression);
    }

    /**
     * descHere
     *
     * @param $Visitor TExpressionVisitor           
     * @return TExpression
     */
    protected function VisitChildren($Visitor) {
    }

}

/**
 * TConstantExpression
 *
 * @author 许子健
 */
final class TConstantExpression extends TExpression {
    
    /**
     *
     * @var TObject
     */
    private $FValue = null;
    /**
     *
     * @var mixed
     */
    private $FType = null;

    /**
     * descHere
     *
     * @param $Value TObject      
     * @param $Type mixed           
     */
    protected function __construct($Value, $Type = null) {
        parent::__construct();
        
        if (($Type == null) && ($Value != null)) {
            $this->FType = $Value->ObjectType();
        }
        elseif (($Value == null) || $Value->IsInstanceOf($Type)) {
            $this->FType = $Type;
        }
        else {
            throw new EInvalidParameter();
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
     *
     * @param $Visitor TExpressionVisitor           
     * @return TExpression
     */
    protected function Accept($Visitor) {
        // TODO: visit constant.
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public function getType() {
        return $this->FType;
    }

    /**
     * descHere
     *
     * @return TObject
     */
    public function getValue() {
        return $this->FValue;
    }

}

/**
 * TUnaryExpression
 * @author	许子健
 */
final class TUnaryExpression extends TExpression {
    
    /**
     * @var	boolean
     */
    private $FLiftToNull = false;
    /**
     * @var	TExpression
     */
    private $FOperand = null;
    /**
     * @var	mixed
     */
    private $FType = null;

    /**
     * descHere
     * @param	TExpressionType	$ExpressionType
     * @param	TExpression	$Operand
     * @param	mixed	$Type
     */
    protected function __construct($ExpressionType, $Operand, $Type = null) {
        parent::__construct();
        TType::Object($ExpressionType, 'TExpressionType');
        TType::Object($Operand, 'TExpression');
        
        switch ($ExpressionType) {
            case TExpressionType::eArrayLength() :
            case TExpressionType::eNegate() :
            case TExpressionType::eNegateChecked() :
            case TExpressionType::eNot() :
            case TExpressionType::eQuote() :
            case TExpressionType::eUnaryPlus() :
                if ($Type !== null) {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::eConvert() :
            case TExpressionType::eConvertChecked() :
            case TExpressionType::eTypeAs() :
                if ($Type === null) {
                    throw new EInvalidParameter();
                }
                break;
            default :
                throw new EInvalidParameter();
                break;
        }
        $mOperandType = $Operand->getType();
        switch ($ExpressionType) {
            case TExpressionType::eArrayLength() :
                if ($mOperandType !== 'array') {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::eNegate() :
            case TExpressionType::eNegateChecked() :
            case TExpressionType::eUnaryPlus() :
                $mOperandType = $Operand->getType();
                if (($mOperandType !== 'integer') || ($mOperandType !== 'float')) {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::eNot() :
                if ($mOperandType !== 'boolean') {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::eConvert() :
            case TExpressionType::eConvertChecked() :
                if (($mOperandType !== 'boolean') || ($mOperandType !== 'integer') || ($mOperandType !== 'float') || ($mOperandType !== 'string')) {
                    throw new EInvalidParameter();
                }
                if (($Type !== 'boolean') || ($Type !== 'integer') || ($Type !== 'float') || ($Type !== 'string')) {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::eQuote() :
            case TExpressionType::eTypeAs() :
                if (($mOperandType === 'boolean') || ($mOperandType === 'integer') || ($mOperandType === 'float') || ($mOperandType === 'string')) {
                    throw new EInvalidParameter();
                }
                if (($Type === 'boolean') || ($Type === 'integer') || ($Type === 'float') || ($Type === 'string')) {
                    throw new EInvalidParameter();
                }
                break;
        }
        
        $this->FNodeType = $ExpressionType;
        $this->FOperand = $Operand;
        $this->FType = $Type;
    }

    /**
     * descHere
     */
    public function __destruct() {
        Framework::Free($this->FOperand);
        
        parent::__destruct();
    }

    /**
     * descHere
     * @return	TExpression
     */
    protected function DoReduce() {
        //reduce unary plus expression
        return $this->FOperand;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getCanReduce() {
        return ($this->FNodeType == TExpressionType::eUnaryPlus());
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
    }

    /**
     * descHere
     * @return	TExpression
     */
    public function getOperand() {
        return $this->FOperand;
    }

    /**
     * descHere
     * @return	mixed
     */
    public function getType() {
        if ($this->FType == null) {
            switch ($this->FNodeType) {
                case TExpressionType::eArrayLength() :
                    return 'integer';
                case TExpressionType::eNegate() :
                case TExpressionType::eNegateChecked() :
                case TExpressionType::eUnaryPlus() :
                    return $this->FOperand->getType();
                case TExpressionType::eNot() :
                    return 'boolean';
                case TExpressionType::eQuote() :
                    return ''; //FIXME: impl.
            }
        }
        else {
            return $this->FType;
        }
    }

    /**
     * descHere
     * @param	TExpression	$Operand
     * @return	TUnaryExpression
     */
    public function Update($Operand) {
        TType::Object($Operand, 'TExpression');
        return self::MakeUnary($this->FNodeType, $Operand, $this->FType);
    }

}

/**
 * TBinaryExpression
 *
 * @author 许子健
 */
final class TBinaryExpression extends TExpression {
    
    /**
     *
     * @var boolean
     */
    private $FLiftToNull = false;
    /**
     *
     * @var TExpression
     */
    private $FLeft = null;
    /**
     *
     * @var TExpression
     */
    private $FRight = null;
    /**
     *
     * @var TLambdaExpression
     */
    private $FConversion = null;

    /**
     * descHere
     *
     * @param $Left TExpression           
     * @param $Right TExpresion           
     * @param $NodeType TExpressionType           
     * @param $Conversion TLambdaExpression           
     * @param $LiftToNull boolean           
     */
    protected function __construct($Left, $Right, $NodeType, $Conversion = null, $LiftToNull = false) {
        parent::__construct();
        
        TType::Object($NodeType, 'TExpressionType');
        TType::Object($Left, 'TExpression');
        TType::Object($Right, 'TExpression');
        TType::Object($Conversion, 'TLambdaExpression');
        TType::Bool($LiftToNull);
        
        $this->FNodeType = $NodeType;
        $this->FConversion = $Conversion;
        
        switch ($this->FNodeType) {
            case TExpressionType::eAdd() :
            case TExpressionType::eAddChecked() :
            case TExpressionType::eSubtract() :
            case TExpressionType::eSubtractChecked() :
            case TExpressionType::eMultiply() :
            case TExpressionType::eMultiplyChecked() :
            case TExpressionType::eDivide() :
            case TExpressionType::eDivideChecked() :
            case TExpressionType::eGreaterThan() :
            case TExpressionType::eLessThan() :
            case TExpressionType::eGreaterThanOrEqual() :
            case TExpressionType::eLessThanOrEqual() :
                if (($Left->getType() != 'TInteger') || ($Left->getType() != 'TFloat')) {
                    throw new EInvalidParameter();
                }
                if (($Right->getType() != 'TInteger') || ($Right->getType() != 'TFloat')) {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::eModulo() :
            case TExpressionType::eAnd() :
            case TExpressionType::eOr() :
            case TExpressionType::eExclusiveOr() :
            case TExpressionType::eLeftShift() :
            case TExpressionType::eRightShift() :
                if ($Left->getType() != 'TInteger') {
                    throw new EInvalidParameter();
                }
                if ($Right->getType() != 'TInteger') {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::ePower() :
                if (($Left->getType() != 'TInteger') || ($Left->getType() != 'TFloat')) {
                    throw new EInvalidParameter();
                }
                if ($Right->getType() != 'TInteger') {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::eAndAlso() :
            case TExpressionType::eOrElse() :
                if (($Left->getType() != 'TBoolean') || ($Right->getType() != 'TBoolean')) {
                    throw new EInvalidParameter();
                }
            case TExpressionType::eAssign() : // FIXME: applicable?
                // TODO 'TParameterExpression'
                break;
            case TExpressionType::eArrayIndex() :
                if (($Left->getType() != 'array') || ($Right->getType() != 'TInteger')) {
                    throw new EInvalidParameter();
                }
                break;
        }
        
        $this->FLeft = $Left;
        $this->FRight = $Right;
        
        switch ($this->FNodeType) {
            case TExpressionType::eAdd() :
            case TExpressionType::eAddChecked() :
            case TExpressionType::eSubtract() :
            case TExpressionType::eSubtractChecked() :
            case TExpressionType::eMultiply() :
            case TExpressionType::eMultiplyChecked() :
            case TExpressionType::eDivide() :
            case TExpressionType::eDivideChecked() :
            case TExpressionType::eModulo() :
            case TExpressionType::eAnd() :
            case TExpressionType::eOr() :
            case TExpressionType::eExclusiveOr() :
            case TExpressionType::eLeftShift() :
            case TExpressionType::eRightShift() :
            case TExpressionType::ePower() :
            case TExpressionType::eAndAlso() :
            case TExpressionType::eOrElse() :
            case TExpressionType::eAssign() : // FIXME: applicable?
                $this->FLiftToNull = true;
                break;
            case TExpressionType::eGreaterThan() :
            case TExpressionType::eLessThan() :
            case TExpressionType::eGreaterThanOrEqual() :
            case TExpressionType::eLessThanOrEqual() :
            case TExpressionType::eEqual() :
            case TExpressionType::eNotEqual() :
                $this->FLiftToNull = $LiftToNull;
                break;
            case TExpressionType::eArrayIndex() :
                $this->FLiftToNull = false;
                break;
        }
    }

    /**
     * (non-PHPdoc)
     * @see TObject::__destruct()
     */
    public function __destruct() {
        Framework::Free($this->FConversion);
        Framework::Free($this->FLeft);
        Framework::Free($this->FRight);
        
        parent::__destruct();
    }

    /**
     * descHere
     *
     * @param $Visitor TExpressionVisitor           
     * @return TExpression
     */
    protected function Accept($Visitor) {
        return $Visitor->VisitBinary($this);
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    protected function DoReduce() {
        $mLeftValue = $this->FLeft->getValue();
        $mRightValue = $this->FRight->getValue();
        
        switch ($this->FNodeType) { // arithmetic and bitwise
            case TExpressionType::eAdd() :
            case TExpressionType::eAddChecked() :
                $mReducedNumber = $mLeftValue->Unbox() + $mRightValue->Unbox();
                break;
            case TExpressionType::eSubtract() :
            case TExpressionType::eSubtractChecked() :
                $mReducedNumber = $mLeftValue->Unbox() - $mRightValue->Unbox();
                break;
            case TExpressionType::eMultiply() :
            case TExpressionType::eMultiplyChecked() :
                $mReducedNumber = $mLeftValue->Unbox() * $mRightValue->Unbox();
                break;
            case TExpressionType::eDivide() :
            case TExpressionType::eDivideChecked() :
                $mReducedNumber = $mLeftValue->Unbox() / $mRightValue->Unbox();
                break;
            case TExpressionType::eModulo() :
                $mReducedNumber = $mLeftValue->Unbox() % $mRightValue->Unbox();
                break;
            case TExpressionType::eAnd() :
                $mReducedNumber = $mLeftValue->Unbox() & $mRightValue->Unbox();
                break;
            case TExpressionType::eOr() :
                $mReducedNumber = $mLeftValue->Unbox() | $mRightValue->Unbox();
                break;
            case TExpressionType::eExclusiveOr() :
                $mReducedNumber = $mLeftValue->Unbox() ^ $mRightValue->Unbox();
                break;
            case TExpressionType::eLeftShift() :
                $mReducedNumber = $mLeftValue->Unbox() << $mRightValue->Unbox();
                break;
            case TExpressionType::eRightShift() :
                $mReducedNumber = $mLeftValue->Unbox() >> $mRightValue->Unbox();
                break;
            case TExpressionType::ePower() :
                $mReducedNumber = pow($mLeftValue->Unbox(), $mRightValue->Unbox());
                if (is_int($mReducedNumber)) {
                    return TExpression::Constant(new TInteger($mReducedNumber), 'TInteger');
                }
                else {
                    return TExpression::Constant(new TInteger($mReducedNumber), 'TFloat');
                }
                break;
            default : // logical
                switch ($this->FNodeType) {
                    case TExpressionType::eAndAlso() :
                        $mReducedBoolean = ($mLeftValue->Unbox() && $mRightValue->Unbox());
                        break;
                    case TExpressionType::eOrElse() :
                        $mReducedBoolean = ($mLeftValue->Unbox() || $mRightValue->Unbox());
                        break;
                    case TExpressionType::eGreaterThan() :
                        $mReducedBoolean = ($mLeftValue->Unbox() > $mRightValue->Unbox());
                        break;
                    case TExpressionType::eLessThan() :
                        $mReducedBoolean = ($mLeftValue->Unbox() < $mRightValue->Unbox());
                        break;
                    case TExpressionType::eGreaterThanOrEqual() :
                        $mReducedBoolean = ($mLeftValue->Unbox() >= $mRightValue->Unbox());
                        break;
                    case TExpressionType::eLessThanOrEqual() :
                        $mReducedBoolean = ($mLeftValue->Unbox() <= $mRightValue->Unbox());
                        break;
                    case TExpressionType::eEqual() :
                        $mReducedBoolean = ($mLeftValue->Unbox() == $mRightValue->Unbox());
                        break;
                    case TExpressionType::eNotEqual() :
                        $mReducedBoolean = ($mLeftValue->Unbox() != $mRightValue->Unbox());
                        break;
                }
                return TExpression::Constant(new TBoolean($mReducedBoolean), 'TBoolean');
                break;
        }
        
        if (($mLeftValue->IsInstanceOf('TInteger')) || ($mRightValue->IsInstanceOf('TFloat'))) {
            TType::Float($mReducedNumber);
            return TExpression::Constant(new TFloat($mReducedNumber), 'TFloat');
        }
        else {
            TType::Int($mReducedNumber);
            return TExpression::Constant(new TInteger($mReducedNumber), 'TInteger');
        }
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCanReduce() {
        return (($this->FLeft->getNodeType() == TExpressionType::eConstant()) && ($this->FRight->getNodeType() == TExpressionType::eConstant()) && ($this->FNodeType != TExpressionType::eArrayIndex()) && ($this->FNodeType != TExpressionType::eAssign()) && ($this->FLeft->getValue() != null) && ($this->FRight->getValue() != null));
    }

    /**
     * descHere
     *
     * @return TLambdaExpression
     */
    public function getConversion() {
        return $this->FConversion;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getIsLifted() {
        return true;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getIsLiftedToNull() {
        return $this->$FLiftToNull && $this->getIsLifted();
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    public function getLeft() {
        return $this->FLeft;
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    public function getRight() {
        return $this->FRight;
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public function getType() {
        switch ($this->FNodeType) {
            case TExpressionType::eAdd() :
            case TExpressionType::eAddChecked() :
            case TExpressionType::eSubtract() :
            case TExpressionType::eSubtractChecked() :
            case TExpressionType::eMultiply() :
            case TExpressionType::eMultiplyChecked() :
            case TExpressionType::eDivide() :
            case TExpressionType::eDivideChecked() :
                if (($this->FLeft->getType() == 'TFloat') || ($this->FRight->getType() == 'TFloat')) {
                    return 'TFloat';
                }
                else {
                    return 'TInteger';
                }
            case TExpressionType::eModulo() :
            case TExpressionType::eAnd() :
            case TExpressionType::eOr() :
            case TExpressionType::eExclusiveOr() :
            case TExpressionType::eLeftShift() :
            case TExpressionType::eRightShift() :
                return 'TInteger';
            case TExpressionType::eAssign() :
            case TExpressionType::ePower() :
            case TExpressionType::eArrayIndex() :
                return $this->FLeft->getType();
            case TExpressionType::eAndAlso() :
            case TExpressionType::eOrElse() :
            case TExpressionType::eGreaterThan() :
            case TExpressionType::eLessThan() :
            case TExpressionType::eGreaterThanOrEqual() :
            case TExpressionType::eLessThanOrEqual() :
            case TExpressionType::eEqual() :
            case TExpressionType::eNotEqual() :
                return 'TBoolean';
        }
    }

    /**
     * descHere
     *
     * @param $Left TExpression      
     * @param $Right TExpression           
     * @param $Conversion TLambdaExpression           
     * @return TBinaryExpression
     */
    public function Update($Left, $Right, $Conversion) {
        if ($Left == $this->FLeft && $Right == $this->FRight && $Conversion == $this->FConversion) {
            return $this;
        }
        else {
            return TExpression::MakeBinary($this->FNodeType, $Left, $Right, $this->FLiftToNull, $Conversion);
        }
    }

}

/**
 * TConditionalExpression
 *
 * @author 许子健
 */
final class TConditionalExpression extends TExpression {
    
    /**
     *
     * @var TExpression
     */
    private $FIfFalse = null;
    /**
     *
     * @var TExpression
     */
    private $FIfTrue = null;
    /**
     *
     * @var TExpression
     */
    private $FTest = null;
    /**
     *
     * @var mixed
     */
    private $FType = null;

    /**
     * descHere
     *
     * @param $Test TExpression           
     * @param $IfTrue TExpression           
     * @param $IfFalse TExpression           
     * @param $Type mixed           
     */
    protected function __construct($Test, $IfTrue, $IfFalse, $Type = null) {
        parent::__construct();
        TType::Object($Test, 'TExpression');
        TType::Object($IfTrue, 'TExpression');
        TType::Object($IfFalse, 'TExpression');
        
        if ($Type === null) {
            $this->FType = '';
        }
        else {
            $this->FType = $Type;
        }
        
        $this->FTest = $Test;
        $this->FIfTrue = $IfTrue;
        $this->FIfFalse = $IfFalse;
    }

    /**
     * descHere
     *
     * @param $Visitor TExpressionVisitor           
     * @return TExpression
     */
    protected function Accept($Visitor) {
        TType::Object($Visitor, 'TExpressionVisitor');
        return null; //TODO impl.
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    public function getIfFlase() {
        return $this->FIfFalse;
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    public function getIfTrue() {
        return $this->FIfTrue;
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    public function getTest() {
        return $this->FTest;
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public function getType() {
        return $this->FType;
    }

    /**
     * descHere
     *
     * @param $Test TExpression           
     * @param $IfTrue TExpression           
     * @param $IfFalse TExpression           
     * @return TConditionalExpression
     */
    public function Update($Test, $IfTrue, $IfFalse) {
        TType::Object($Test, 'TExpression');
        TType::Object($IfTrue, 'TExpression');
        TType::Object($IfFalse, 'TExpression');
        
        if (($Test == $this->FTest) && ($IfTrue == $this->FIfTrue) && ($IfFalse == $this->FIfFalse)) {
            return $this;
        }
        else {
            return TExpression::Condition($Test, $IfTrue, $IfFalse);
        }
    }

}

/**
 * TDefaultExpression
 *
 * @author 许子健
 */
final class TDefaultExpression extends TExpression {
    
    /**
     *
     * @var mixed
     */
    private $FType = null;

    /**
     * descHere
     *
     * @param $Type mixed           
     */
    protected function __construct($Type) {
        parent::__construct();
        $this->FType = $Type;
    }

    /**
     * descHere
     *
     * @return TExpression
     */
    protected function DoReduce() {
        switch ($this->FType) {
            case 'TBoolean' :
                return TExpression::Constant(new TBoolean());
            case 'TInteger' :
                return TExpression::Constant(new TInteger());
            case 'TFloat' :
                return TExpression::Constant(new TFloat());
            case 'TString' :
                return TExpression::Constant(new TString());
            default :
                return TExpression::Constant(null, $this->FType);
        }
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public function getType() {
        return $this->FType;
    }

}

/**
 * TParameterExpression
 * @author	许子健
 */
final class TParameterExpression extends TExpression {
    
    /**
     * @var	string
     */
    private $FName = '';
    /**
     * @var	mixed
     */
    private $FType = null;
    /**
     * 
     * @var boolean
     */
    private $FIsByRef = false;

    /**
     * descHere
     * @param	string	$Name
     * @param	mixed	$Type
     * @param	boolean	$IsByRef
     */
    protected function __construct($Name, $Type, $IsByRef = false) {
        TType::String($Name);
        TType::Bool($IsByRef);
        
        if (preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $Name) !== 1) {
            throw new EInvalidParameter();
        }
        
        $this->FName = $Name;
        $this->FType = $Type;
        $this->FIsByRef = $IsByRef;
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
     * @return	boolean
     */
    public function getIsByRef() {
        return $this->FIsByRef;
    }

    /**
     * descHere
     * @return	string
     */
    public function getName() {
        return $this->FName;
    }

    /**
     * descHere
     * @return	TExpressionType
     */
    public function getNodeType() {
        return TExpressionType::eParameter(); //?
    }

    /**
     * descHere
     * @return	mixed
     */
    public function getType() {
        return $this->FType;
    }

}

/**
 * TMemberExpression
 * @author	许子健
 */
final class TMemberExpression extends TExpression {
    
    /**
     * @var	TExpression
     */
    private $FExpression = null;
    /**
     * @var	string
     */
    private $FMemberName = '';
    /**
     * @var	mixed
     */
    private $FType = null;

    /**
     * descHere
     * @param	TExpression	$Expression
     * @param	string	$MemberName
     * @param	mixed	$Type
     */
    protected function __construct($Expression, $MemberName, $Type) {
        TType::Type($Expression, 'TExpression');
        TType::String($MemberName);
        
        if (preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $MemberName) !== 1) {
            throw new EInvalidParameter();
        }
        
        $this->FExpression = $Expression;
        $this->FMemberName = $MemberName;
        $this->FType = $Type;
    }

    /**
     * descHere
     */
    public function __destruct() {
        Framework::Free($this->FExpression);
        parent::__destruct();
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
    public function getExpression() {
        return $this->FExpression;
    }

    /**
     * descHere
     * @return	string
     */
    public function getMember() {
        return $this->FMemberName;
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
     * @param	TExpression	$Expression
     * @param	string	$MemberName
     * @param	mixed	$Type
     * @return	TMemberExprsssion
     */
    public function Update($Expression, $MemberName, $Type) {
        TType::Object($Expression, 'TExpression');
        TType::String($MemberName);
        
        if (($Expression == $this->FExpression) && ($MemberName == $this->FMemberName) && ($Type == $this->FType)) {
            return $this;
        }
        else {
            return TExpression::MakeMember($Expression, $MemberName, $Type);
        }
    }

}

/**
 * TLambdaExpression
 * @author	许子健
 */
final class TLambdaExpression extends TExpression {
    
    /**
     * @var	TExpression
     */
    private $FBody = null;
    /**
     * @var	string
     */
    private $FName = '';
    /**
     * @var	IList <T: TParameterExpression>
     */
    private $FParameters = null;
    /**
     * @var	mixed
     */
    private $FReturnType = null;

    /**
     * descHere
     * @param	string	$Name
     * @param	TExpression	$Body
     * @param	IList<T: TParameterExpression>	$Parameters
     * @param	mixed	$ReturnType
     */
    protected function __construct($Name, $Body, $Parameters, $ReturnType) {
        TType::String($Name);
        TType::Object($Body, 'TExpression');
        TType::Object($Parameters, array (
            'IList' => array ('T' => 'TParameterExpression')));
        
        $this->FName = $Name; //TODO: check if already exists, and if the name is valid.
        $this->FBody = $Body;
        $this->FParameters = $Parameters;
        $this->FReturnType = $ReturnType;
    }

    /**
     * descHere
     */
    public function __destruct() {
        Framework::Free($this->FBody);
        parent::__destruct();
    }

    /**
     * descHere
     * @return	TDelegate
     */
    public function Compile() {
    }

    /**
     * descHere
     * @return	TExpression
     */
    public function getBody() {
        return $this->FBody;
    }

    /**
     * descHere
     * @return	string
     */
    public function getName() {
        return $this->FName;
    }

    /**
     * descHere
     * @return	IList<T: TParameterExpression>
     */
    public function getParameters() {
        return $this->FParameters;
    }

    /**
     * descHere
     * @return	mixed
     */
    public function getReturnType() {
        return $this->FReturnType;
    }

    /**
     * descHere
     * @return	mixed
     */
    public function getType() {
        return $this->FType;
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
        TType::Object($Expression, 'TExpression');
        
        $mObjectType = $Expression->ObjectType();
        
        switch ($mObjectType) {
            case 'TBinaryExpression' :
                return $this->VisitBinary($Expression);
            case 'TConditionalExpression' :
                return $this->VisitConditional($Expression);
            case 'TConstantExpression' :
                return $this->VisitConstant($Expression);
            case 'TDefaultExpression' :
                return $this->VisitDefault($Expression);
            case 'TLambdaExpression' :
                return $this->VisitLambda($Expression);
            case 'TMemberExpression' :
                return $this->VisitMember($Expression);
            case 'ParameterExpression' :
                return $this->VisitParameter($Expression);
            case 'TUnaryExpression' :
                return $this->VisitUnary($Expression);
        }
    }

    /**
     * descHere
     * @param	TBinaryExpression	$Expression
     * @return	TExpression
     */
    protected function VisitBinary($Expression) {
        return $Expression;
    }

    /**
     * descHere
     * @param	TConditional	$Expression
     * @return	TExpression
     */
    protected function VisitConditional($Expression) {
        return $Expression;
    }

    /**
     * descHere
     * @param	TConstantExpression	$Expression
     * @return	TExpression
     */
    protected function VisitConstant($Expression) {
        return $Expression;
    }

    /**
     * descHere
     * @param	TDefaultExpression	$Expression
     * @return	TExpression
     */
    protected function VisitDefault($Expression) {
        return $Expression;
    }

    /**
     * descHere
     * @param	TLambdaExpression	$Expression
     * @return	TExpression
     */
    protected function VisitLambda($Expression) {
        return $Expression;
    }

    /**
     * descHere
     * @param	TMemberExpression	$Expression
     * @return	TExpression
     */
    protected function VisitMember($Expression) {
        return $Expression;
    }

    /**
     * descHere
     * @param	TParameterExpression	$Expression
     * @return	TExpression
     */
    protected function VisitParameter($Expression) {
        return $Expression;
    }

    /**
     * descHere
     * @param	TUnaryExpression	$Expression
     * @return	TExpression
     */
    protected function VisitUnary($Expression) {
        return $Expression;
    }

}