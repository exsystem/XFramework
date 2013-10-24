<?php
/**
 * \FrameworkDSW\Linq\Expressions
 * @author    许子健
 * @version    $Id$
 * @since    separate file since reversion 30
 */
namespace FrameworkDSW\Linq\Expressions;

use FrameworkDSW\Containers\IIteratorAggregate;
use FrameworkDSW\Containers\IList;
use FrameworkDSW\Containers\TList;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\TBoolean;
use FrameworkDSW\System\TEnum;
use FrameworkDSW\System\TFloat;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TString;
use FrameworkDSW\Utilities\TType;

// FIXME: 去除PHP表達式不需要的符號。
// FIXME: 暫不支持數組（需要等待反射架構完成<type, dimensions>, int => <int, 0>）

/**
 * \FrameworkDSW\Linq\Expressions\TExpressionType
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
     * 強制轉換，例如：“(integer) $a”。不會檢測轉換是否有意義。
     *
     * @var integer
     */
    const eConvert = 10;
    /**
     * 強制轉換，例如：“(integer) $a”。檢測轉換是否有意義。
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
    const eListInit        = 22;
    /**
     * 成員調用，例如：“$a->b”。
     *
     * @var integer
     */
    const eMemberAccess = 23;
    const eMemberInit   = 24;
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
    const eNew            = 31;
    const eNewArrayInit   = 32;
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
    const ePower     = 39;
    const eQuote     = 40;
    /**
     * 算術右移運算符，例如：“$a >> $b”。
     *
     * @var integer
     */
    const eRightShift            = 41;
    const eSubtract              = 42;
    const eSubtractChecked       = 43;
    const eTypeAs                = 44;
    const eTypeIs                = 45;
    const eAssign                = 46;
    const eBlock                 = 47;
    const eDebugInfo             = 48;
    const eDecrement             = 49;
    const eDynamic               = 50;
    const eDefault               = 51;
    const eExtension             = 52;
    const eGoto                  = 53;
    const eIncrement             = 54;
    const eIndex                 = 55;
    const eLabel                 = 56;
    const eRuntimeVariables      = 57;
    const eLoop                  = 58;
    const eSwitch                = 59;
    const eThrow                 = 60;
    const eTry                   = 61;
    const eUnbox                 = 62;
    const eAddAssign             = 63;
    const eAndAssign             = 64;
    const eDivideAssign          = 65;
    const eExclusiveOrAssign     = 66;
    const eLeftShiftAssign       = 67;
    const eModuloAssign          = 68;
    const eMultiplyAssign        = 69;
    const eOrAssign              = 70;
    const ePowerAssign           = 71;
    const eRightShiftAssign      = 72;
    const eSubtractAssign        = 73;
    const eAddAssignChecked      = 74;
    const eMultiplyAssignChecked = 75;
    const eSubtractAssignChecked = 76;
    const ePreIncrementAssign    = 77;
    const ePreDecrementAssign    = 78;
    const ePostIncrementAssign   = 79;
    const ePostDecrementAssign   = 80;
    const eTypeEqual             = 81;
    const eOnesComplement        = 82;
    const eIsTrue                = 83;
    const eIsFalse               = 84;
}

/**
 * \FrameworkDSW\Linq\Expressions\TExpression
 * 表示一个表达式的结点。
 *
 * @author 许子健
 */
abstract class TExpression extends TObject {

    /**
     * 表达式结点的类型。
     *
     * @var \FrameworkDSW\Linq\Expressions\TExpressionType
     */
    protected $FNodeType = null;

    /**
     * 移进该结点。
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionVisitor $Visitor
     * @return \FrameworkDSW\Linq\Expressions\TExpression 表达式本身，或者替代自己的表达式。
     */
    public function Accept($Visitor) {
        $mExpression = $this->VisitChildren($Visitor);

        return $Visitor->Visit($mExpression);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function Add($Left, $Right) {
        return self::MakeBinary(TExpressionType::eAdd(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function AddAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAddAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function AddAssignChecked($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAddAssignChecked(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function AddChecked($Left, $Right, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAddChecked(), $Left, $Right, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function AndBitwise($Left, $Right) {
        return self::MakeBinary(TExpressionType::eAnd(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function AndAlso($Left, $Right) {
        return self::MakeBinary(TExpressionType::eAndAlso(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function AndAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eAndAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Array \FrameworkDSW\Linq\Expressions\TExpression
     * @param $Index \FrameworkDSW\Linq\Expressions\TExpression
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function ArrayIndexInOneDimensional($Array, $Index) {
        return self::MakeBinary(TExpressionType::eArrayIndex(), $Array, $Index);
    }

    /**
     * descHere
     *
     * @param $Array \FrameworkDSW\Linq\Expressions\TExpression
     * @return TUnaryExpression
     */
    public static function ArrayLength($Array) {
        return self::MakeUnary(TExpressionType::eArrayLength(), $Array);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function Assign($To, $By) {
        return self::MakeBinary(TExpressionType::eAssign(), $To, $By);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IList $Expressions <T: \FrameworkDSW\Linq\Expressions\TExpression>
     * @return \FrameworkDSW\Linq\Expressions\TBlockExpression
     */
    public static function Block($Expressions) {
        TType::Object($Expressions, [
            IList::class => ['T' => TExpression::class]]);

        TList::PrepareGeneric(['T' => TExpression::class]);
        $mExpressions = new TList(5, true);
        foreach ($Expressions as $mExpression) {
            $mExpressions->Add($mExpression);
        }

        return new TBlockExpression($Expressions);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Instance
     * @param array $Method
     * @param \FrameworkDSW\Containers\IIteratorAggregate $Arguments <T: \FrameworkDSW\Linq\Expressions\TExpression>
     * @param mixed $ReturnType
     * @return \FrameworkDSW\Linq\Expressions\TMethodCallExpression
     */
    public static function Call($Instance, $Method, $Arguments, $ReturnType = null) {
        TType::Object($Instance, TExpression::class);
        TType::Arr($Method);
        TType::Object($Arguments, [
            IIteratorAggregate::class => ['T' => TExpression::class]]);

        TList::PrepareGeneric(['T' => TExpression::class]);
        $mArguments = new TList(5, true);
        foreach ($Arguments as $mArgument) {
            $mArguments->Add($mArgument);
        }

        return new TMethodCallExpression($Instance, $Method, $mArguments, $ReturnType);

    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Test
     * @param \FrameworkDSW\Linq\Expressions\TExpression $IfNotNull
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function Coalesce($Test, $IfNotNull, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eCoalesce(), $Test, $IfNotNull, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param $Test \FrameworkDSW\Linq\Expressions\TExpression
     * @param $IfTrue \FrameworkDSW\Linq\Expressions\TExpression
     * @param $IfFalse \FrameworkDSW\Linq\Expressions\TExpression
     * @param $Type mixed
     * @return \FrameworkDSW\Linq\Expressions\TConditionalExpression
     */
    public static function Condition($Test, $IfTrue, $IfFalse, $Type = null) {
        TType::Object($Test, TExpression::class);
        TType::Object($IfTrue, TExpression::class);
        TType::Object($IfFalse, TExpression::class);

        return new TConditionalExpression($Test, $IfTrue, $IfFalse, $Type);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\System\TObject $Value
     * @param mixed $Type
     * @return \FrameworkDSW\Linq\Expressions\TConstantExpression
     */
    public static function Constant($Value, $Type = null) {
        TType::Object($Value, TObject::class);

        return new TConstantExpression($Value, $Type);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @param mixed $Type
     * @return \FrameworkDSW\Linq\Expressions\TUnaryExpression
     */
    public static function Convert($Expression, $Type) {
        return self::MakeUnary(TExpressionType::eConvert(), $Expression, $Type);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @param mixed $Type
     * @return \FrameworkDSW\Linq\Expressions\TUnaryExpression
     */
    public static function ConvertChecked($Expression, $Type) {
        return self::MakeUnary(TExpressionType::eConvertChecked(), $Expression, $Type);
    }

    /**
     * descHere
     *
     * @param mixed $Type
     * @return \FrameworkDSW\Linq\Expressions\TDefaultExpression
     */
    public static function DefaultValue($Type) {
        return new TDefaultExpression($Type);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function Divide($Left, $Right) {
        return self::MakeBinary(TExpressionType::eDivide(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function DivideAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eDivideAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function DoReduce() {
        throw new EInvalidParameter();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param boolean $LiftToNull
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function Equal($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function ExclusiveOr($Left, $Right) {
        return self::MakeBinary(TExpressionType::eExclusiveOr(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
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
     * @return \FrameworkDSW\Linq\Expressions\TExpressionType
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
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param boolean $LiftToNull
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function GreaterThan($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eGreaterThan(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param boolean $LiftToNull
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function GreaterThanOrEqual($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eGreaterThanOrEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Test
     * @param \FrameworkDSW\Linq\Expressions\TExpression $IfTrue
     * @return TConditionalExpression
     */
    public static function IfThen($Test, $IfTrue) {
        TType::Object($Test, TExpression::class);
        TType::Object($IfTrue, TExpression::class);

        return new TConditionalExpression($Test, $IfTrue, null);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Test
     * @param \FrameworkDSW\Linq\Expressions\TExpression $IfTrue
     * @param \FrameworkDSW\Linq\Expressions\TExpression $IfFalse
     * @return \FrameworkDSW\Linq\Expressions\TConditionalExpression
     */
    public static function IfThenElse($Test, $IfTrue, $IfFalse) {
        TType::Object($Test, TExpression::class);
        TType::Object($IfTrue, TExpression::class);
        TType::Object($IfFalse, TExpression::class);

        return new TConditionalExpression($Test, $IfTrue, $IfFalse);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Body
     * @param \FrameworkDSW\Containers\IList $Parameters <T: \FrameworkDSW\Linq\Expressions\TParameterExpression>
     * @param string $Name
     * @param mixed $DelegateType
     * @return \FrameworkDSW\Linq\Expressions\TLambdaExpression
     */
    public static function Lambda($Body, $Parameters, $Name = '', /** @noinspection PhpUnusedParameterInspection */
                                  $DelegateType = null) {
        return new TLambdaExpression($Name, $Body, $Parameters, $Body->getType());
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function LeftShift($Left, $Right) {
        return self::MakeBinary(TExpressionType::eLeftShift(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function LeftShiftAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eLeftShiftAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param boolean $LiftToNull
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function LessThan($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eLessThan(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param boolean $LiftToNull
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function LessThanOrEqual($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eLessThanOrEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionType $ExpressionType
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param boolean $LiftToNull
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function MakeBinary($ExpressionType, $Left, $Right, $LiftToNull = false, $Conversion = null) {
        return new TBinaryExpression($Left, $Right, $ExpressionType, $Conversion, $LiftToNull);
    }

    /**
     *
     * @param $Expression \FrameworkDSW\Linq\Expressions\TExpression
     * @param string $MemberName
     * @param mixed $Type
     * @return \FrameworkDSW\Linq\Expressions\TMemberExpression
     */
    public static function MakeMember($Expression, $MemberName, $Type) {
        return new TMemberExpression($Expression, $MemberName, $Type);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionType $ExpressionType
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Operand
     * @param mixed $Type
     * @return \FrameworkDSW\Linq\Expressions\TUnaryExpression
     */
    public static function MakeUnary($ExpressionType, $Operand, $Type = null) {
        return new TUnaryExpression($ExpressionType, $Operand, $Type);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function Modulo($Left, $Right) {
        return self::MakeBinary(TExpressionType::eModulo(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function ModuloAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eModuloAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function Multiply($Left, $Right) {
        return self::MakeBinary(TExpressionType::eMultiply(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function MultiplyAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eMultiplyAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function MultiplyAssignChecked($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eMultiplyAssignChecked(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function MultiplyChecked($Left, $Right) {
        return self::MakeBinary(TExpressionType::eMultiplyChecked(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @return TUnaryExpression
     */
    public static function Negate($Expression) {
        return self::MakeUnary(TExpressionType::eNegate(), $Expression);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @return TUnaryExpression
     */
    public static function NegateChecked($Expression) {
        return self::MakeUnary(TExpressionType::eNegateChecked(), $Expression);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @return TUnaryExpression
     */
    public static function Not($Expression) {
        return self::MakeUnary(TExpressionType::eNot(), $Expression);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param boolean $LiftToNull
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function NotEqual($Left, $Right, $LiftToNull = false) {
        return self::MakeBinary(TExpressionType::eNotEqual(), $Left, $Right, $LiftToNull);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function OrBitwise($Left, $Right) {
        return self::MakeBinary(TExpressionType::eOr(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function OrAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eOrAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function OrElse($Left, $Right) {
        return self::MakeBinary(TExpressionType::eOrElse(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param string $Name
     * @param mixed $Type
     * @param boolean $IsByRef
     * @return \FrameworkDSW\Linq\Expressions\TParameterExpression
     */
    public static function Parameter($Name, $Type, $IsByRef = false) {
        return new TParameterExpression($Name, $Type, $IsByRef);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function Power($Left, $Right) {
        return self::MakeBinary(TExpressionType::ePower(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function PowerAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::ePowerAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TUnaryExpression
     */
    public static function Quote($Expression) {
        return self::MakeUnary(TExpressionType::eQuote(), $Expression);
    }

    /**
     * descHere
     *
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return \FrameworkDSW\Linq\Expressions\TExpression
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function ReduceAndCheck() {
        throw new EInvalidParameter();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function ReferenceEqual($Left, $Right) {
        return self::MakeBinary(TExpressionType::eEqual(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function ReferenceNotEqual($Left, $Right) {
        return self::MakeBinary(TExpressionType::eNotEqual(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function RightShift($Left, $Right) {
        return self::MakeBinary(TExpressionType::eRightShift(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function RightShiftAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eRightShiftAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function Substract($Left, $Right) {
        return self::MakeBinary(TExpressionType::eSubtract(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function SubstractAssign($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eSubtractAssign(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $To
     * @param \FrameworkDSW\Linq\Expressions\TExpression $By
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function SubstractAssignChecked($To, $By, $Conversion = null) {
        return self::MakeBinary(TExpressionType::eSubtractAssignChecked(), $To, $By, false, $Conversion);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public static function SubstractChecked($Left, $Right, /** @noinspection PhpUnusedParameterInspection */
                                            $Conversion = null) {
        return self::MakeBinary(TExpressionType::eSubtractChecked(), $Left, $Right);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @param mixed $Type
     * @return \FrameworkDSW\Linq\Expressions\TUnaryExpression
     */
    public static function TypeAs($Expression, $Type) {
        return self::MakeUnary(TExpressionType::eTypeAs(), $Expression, $Type);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Body
     * @param \FrameworkDSW\Containers\IList $Parameters <T: \FrameworkDSW\Linq\Expressions\TParameterExpression>
     * @param string $Name
     * @return \FrameworkDSW\Linq\Expressions\TTypedExpression <T: T>
     */
    public static function TypedLambda($Body, $Parameters, $Name = '') {
        TTypedExpression::PrepareGeneric(array(
            'T' => self::StaticGenericArg('T')));

        return new TTypedExpression($Name, $Body, $Parameters, $Body->getType());
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TUnaryExpression
     */
    public static function UnaryPlus($Expression) {
        return self::MakeUnary(TExpressionType::eUnaryPlus(), $Expression);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionVisitor $Visitor
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitChildren(/** @noinspection PhpUnusedParameterInspection */
        $Visitor) {
        return $this;
    }

}

/**
 * \FrameworkDSW\Linq\Expressions\TConstantExpression
 *
 * @author 许子健
 */
final class TConstantExpression extends TExpression {

    /**
     *
     * @var \FrameworkDSW\System\TObject
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
     * @param \FrameworkDSW\System\TObject $Value
     * @param mixed $Type
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($Value, $Type = null) {
        parent::__construct();

        if (($Type === null) && ($Value !== null)) {
            $this->FType = $Value->ObjectType();
        }
        elseif (($Value === null) || $Value->IsInstanceOf($Type)) {
            $this->FType = $Type;
        }
        else {
            throw new EInvalidParameter();
        }
        $this->FValue    = $Value;
        $this->FNodeType = TExpressionType::eConstant();
    }

    /**
     * descHere
     */
    public function Destroy() {
        Framework::Free($this->FValue);
        parent::Destroy();
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
     * @return \FrameworkDSW\System\TObject
     */
    public function getValue() {
        return $this->FValue;
    }

}

/**
 * \FrameworkDSW\Linq\Expressions\TUnaryExpression
 *
 * @author 许子健
 */
final class TUnaryExpression extends TExpression {

//    /**
//     *
//     * @var boolean
//     */
//    private $FLiftToNull = false;
    /**
     *
     * @var \FrameworkDSW\Linq\Expressions\TExpression
     */
    private $FOperand = null;
    /**
     *
     * @var mixed
     */
    private $FType = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionType $ExpressionType
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Operand
     * @param mixed $Type
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($ExpressionType, $Operand, $Type = null) {
        parent::__construct();
        TType::Object($ExpressionType, TExpressionType::class);
        TType::Object($Operand, TExpression::class);

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
        $this->FOperand  = $Operand;
        $this->FType     = $Type;
    }

    /**
     * descHere
     */
    public function Destroy() {
        Framework::Free($this->FOperand);

        parent::Destroy();
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function DoReduce() {
        // reduce unary plus expression
        return $this->FOperand;
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCanReduce() {
        return ($this->FNodeType == TExpressionType::eUnaryPlus());
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getIsLifted() {
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getIsLiftedToNull() {
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getOperand() {
        return $this->FOperand;
    }

    /**
     * descHere
     *
     * @return mixed
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
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
                    return ''; // FIXME: impl.
            }
        }
        else {
            return $this->FType;
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Operand
     * @return \FrameworkDSW\Linq\Expressions\TUnaryExpression
     */
    public function Update($Operand) {
        TType::Object($Operand, TExpression::class);

        return self::MakeUnary($this->FNodeType, $Operand, $this->FType);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionVisitor $Visitor
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitChildren($Visitor) {
        $this->FOperand = $this->FOperand->Accept($Visitor);

        return $this;
    }
}

/**
 * \FrameworkDSW\Linq\Expressions\TBinaryExpression
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
     * @var \FrameworkDSW\Linq\Expressions\TExpression
     */
    private $FLeft = null;
    /**
     *
     * @var \FrameworkDSW\Linq\Expressions\TExpression
     */
    private $FRight = null;
    /**
     *
     * @var \FrameworkDSW\Linq\Expressions\TLambdaExpression
     */
    private $FConversion = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param \FrameworkDSW\Linq\Expressions\TExpressionType $NodeType
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @param boolean $LiftToNull
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($Left, $Right, $NodeType, $Conversion = null, $LiftToNull = false) {
        parent::__construct();

        TType::Object($NodeType, TExpressionType::class);
        TType::Object($Left, TExpression::class);
        TType::Object($Right, TExpression::class);
        TType::Object($Conversion, TLambdaExpression::class);
        TType::Bool($LiftToNull);

        $this->FNodeType   = $NodeType;
        $this->FConversion = $Conversion;

        switch ($this->FNodeType) {
            case TExpressionType::eAdd() :
            case TExpressionType::eAddChecked() :
            case TExpressionType::eSubtract() :
            case TExpressionType::eSubtractChecked() :
            case TExpressionType::eMultiply() :
            case TExpressionType::eMultiplyChecked() :
            case TExpressionType::eDivide() :
            case TExpressionType::eGreaterThan() :
            case TExpressionType::eLessThan() :
            case TExpressionType::eGreaterThanOrEqual() :
            case TExpressionType::eLessThanOrEqual() :
                if (($Left->getType() !== TInteger::class) || ($Left->getType() !== TFloat::class)) {
                    throw new EInvalidParameter();
                }
                if (($Right->getType() !== TInteger::class) || ($Right->getType() !== TFloat::class)) {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::eModulo() :
            case TExpressionType::eAnd() :
            case TExpressionType::eOr() :
            case TExpressionType::eExclusiveOr() :
            case TExpressionType::eLeftShift() :
            case TExpressionType::eRightShift() :
                if ($Left->getType() !== TInteger::class) {
                    throw new EInvalidParameter();
                }
                if ($Right->getType() !== TInteger::class) {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::ePower() :
                if (($Left->getType() !== TInteger::class) || ($Left->getType() !== TFloat::class)) {
                    throw new EInvalidParameter();
                }
                if ($Right->getType() !== TInteger::class) {
                    throw new EInvalidParameter();
                }
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case TExpressionType::eAndAlso() :
                /** @noinspection PhpMissingBreakStatementInspection */
            case TExpressionType::eOrElse() :
                if (($Left->getType() !== TBoolean::class) || ($Right->getType() !== TBoolean::class)) {
                    throw new EInvalidParameter();
                }
            case TExpressionType::eAssign() :
                if ($Left->getNodeType() == TExpressionType::eConstant()) {
                    throw new EInvalidParameter();
                }
                break;
            case TExpressionType::eArrayIndex() :
                if (($Left->getType() !== 'array') || ($Right->getType() !== TInteger::class)) {
                    throw new EInvalidParameter();
                }
                break;
        }

        $this->FLeft  = $Left;
        $this->FRight = $Right;

        switch ($this->FNodeType) {
            case TExpressionType::eAdd() :
            case TExpressionType::eAddChecked() :
            case TExpressionType::eSubtract() :
            case TExpressionType::eSubtractChecked() :
            case TExpressionType::eMultiply() :
            case TExpressionType::eMultiplyChecked() :
            case TExpressionType::eDivide() :
            case TExpressionType::eModulo() :
            case TExpressionType::eAnd() :
            case TExpressionType::eOr() :
            case TExpressionType::eExclusiveOr() :
            case TExpressionType::eLeftShift() :
            case TExpressionType::eRightShift() :
            case TExpressionType::ePower() :
            case TExpressionType::eAndAlso() :
            case TExpressionType::eOrElse() :
            case TExpressionType::eAssign() :
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
     *
     * @see TObject::Destroy()
     */
    public function Destroy() {
        Framework::Free($this->FConversion);
        Framework::Free($this->FLeft);
        Framework::Free($this->FRight);

        parent::Destroy(); //TODO delete me
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function DoReduce() {
        $mLeftValue  = $this->FLeft->getValue();
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
                    return TExpression::Constant(new TInteger($mReducedNumber), TInteger::class);
                }
                else {
                    return TExpression::Constant(new TInteger($mReducedNumber), TFloat::class);
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

                /** @noinspection PhpUndefinedVariableInspection */

                return TExpression::Constant(new TBoolean($mReducedBoolean), TBoolean::class);
                break;
        }

        if (($mLeftValue->IsInstanceOf(TInteger::class)) || ($mRightValue->IsInstanceOf(TFloat::class))) {
            TType::Float($mReducedNumber);

            return TExpression::Constant(new TFloat($mReducedNumber), TFloat::class);
        }
        else {
            TType::Int($mReducedNumber);

            return TExpression::Constant(new TInteger($mReducedNumber), TInteger::class);
        }
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getCanReduce() {
        return (($this->FLeft->getNodeType() == TExpressionType::eConstant()) && ($this->FRight->getNodeType() == TExpressionType::eConstant()) && ($this->FNodeType != TExpressionType::eArrayIndex()) && ($this->FNodeType != TExpressionType::eAssign()) && ($this->FLeft->getValue() !== null) && ($this->FRight->getValue() !== null));
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TLambdaExpression
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
        return $this->FLiftToNull && $this->getIsLifted();
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getLeft() {
        return $this->FLeft;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getRight() {
        return $this->FRight;
    }

    /**
     * descHere
     *
     * @return mixed
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function getType() {
        switch ($this->FNodeType) {
            case TExpressionType::eAdd() :
            case TExpressionType::eAddChecked() :
            case TExpressionType::eSubtract() :
            case TExpressionType::eSubtractChecked() :
            case TExpressionType::eMultiply() :
            case TExpressionType::eMultiplyChecked() :
            case TExpressionType::eDivide() :
                if (($this->FLeft->getType() == TFloat::class) || ($this->FRight->getType() == TFloat::class)) {
                    return TFloat::class;
                }
                else {
                    return TInteger::class;
                }
            case TExpressionType::eModulo() :
            case TExpressionType::eAnd() :
            case TExpressionType::eOr() :
            case TExpressionType::eExclusiveOr() :
            case TExpressionType::eLeftShift() :
            case TExpressionType::eRightShift() :
                return TInteger::class;
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
                return TBoolean::class;
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Left
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Right
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Conversion
     * @return \FrameworkDSW\Linq\Expressions\TBinaryExpression
     */
    public function Update($Left, $Right, $Conversion) {
        if ($Left == $this->FLeft && $Right == $this->FRight && $Conversion == $this->FConversion) {
            return $this;
        }
        else {
            return TExpression::MakeBinary($this->FNodeType, $Left, $Right, $this->FLiftToNull, $Conversion);
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionVisitor $Visitor
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitChildren($Visitor) {
        $this->FLeft  = $Visitor->Visit($this->FLeft);
        $this->FRight = $Visitor->Visit($this->FRight);

        return $this;
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
     * @var \FrameworkDSW\Linq\Expressions\TExpression
     */
    private $FIfFalse = null;
    /**
     *
     * @var \FrameworkDSW\Linq\Expressions\TExpression
     */
    private $FIfTrue = null;
    /**
     *
     * @var \FrameworkDSW\Linq\Expressions\TExpression
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
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Test
     * @param \FrameworkDSW\Linq\Expressions\TExpression $IfTrue
     * @param \FrameworkDSW\Linq\Expressions\TExpression $IfFalse
     * @param mixed $Type
     */
    public function __construct($Test, $IfTrue, $IfFalse, $Type = null) {
        parent::__construct();
        TType::Object($Test, TExpression::class);
        TType::Object($IfTrue, TExpression::class);
        TType::Object($IfFalse, TExpression::class);

        if ($Type === null) {
            $this->FType = '';
        }
        else {
            $this->FType = $Type;
        }

        $this->FNodeType = TExpressionType::eConditional();
        $this->FTest     = $Test;
        $this->FIfTrue   = $IfTrue;
        $this->FIfFalse  = $IfFalse;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getIfFalse() {
        return $this->FIfFalse;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getIfTrue() {
        return $this->FIfTrue;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
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
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Test
     * @param \FrameworkDSW\Linq\Expressions\TExpression $IfTrue
     * @param \FrameworkDSW\Linq\Expressions\TExpression $IfFalse
     * @return TConditionalExpression
     */
    public function Update($Test, $IfTrue, $IfFalse) {
        TType::Object($Test, TExpression::class);
        TType::Object($IfTrue, TExpression::class);
        TType::Object($IfFalse, TExpression::class);

        if (($Test == $this->FTest) && ($IfTrue == $this->FIfTrue) && ($IfFalse == $this->FIfFalse)) {
            return $this;
        }
        else {
            return TExpression::Condition($Test, $IfTrue, $IfFalse);
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionVisitor $Visitor
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitChildren($Visitor) {
        $this->FTest    = $this->FTest->Accept($Visitor);
        $this->FIfTrue  = $this->FIfTrue->Accept($Visitor);
        $this->FIfFalse = $this->FIfFalse->Accept($Visitor);

        return $this;
    }
}

/**
 * \FrameworkDSW\Linq\Expressions\TDefaultExpression
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
     * @param mixed $Type
     */
    public function __construct($Type) {
        parent::__construct();
        $this->FNodeType = TExpressionType::eDefault();
        $this->FType     = $Type;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function DoReduce() {
        switch ($this->FType) {
            case TBoolean::class :
                return TExpression::Constant(new TBoolean());
            case TInteger::class :
                return TExpression::Constant(new TInteger());
            case TFloat::class :
                return TExpression::Constant(new TFloat());
            case TString::class :
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
 * \FrameworkDSW\Linq\Expressions\TParameterExpression
 *
 * @author 许子健
 */
final class TParameterExpression extends TExpression {

    /**
     *
     * @var string
     */
    private $FName = '';
    /**
     *
     * @var mixed
     */
    private $FType = null;
    /**
     *
     * @var boolean
     */
    private $FIsByRef = false;

    /**
     * descHere
     *
     * @param string $Name
     * @param mixed $Type
     * @param boolean $IsByRef
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($Name, $Type, $IsByRef = false) {
        TType::String($Name);
        TType::Bool($IsByRef);

        if (preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $Name) !== 1) {
            throw new EInvalidParameter();
        }

        $this->FNodeType = TExpressionType::eParameter();
        $this->FName     = $Name;
        $this->FType     = $Type;
        $this->FIsByRef  = $IsByRef;
    }

    public function Destroy() {
        parent::Destroy();
        // TODO delete me!
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getIsByRef() {
        return $this->FIsByRef;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getName() {
        return $this->FName;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpressionType
     */
    public function getNodeType() {
        return TExpressionType::eParameter(); // ?
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
 * \FrameworkDSW\Linq\Expressions\TMemberExpression
 *
 * @author 许子健
 */
final class TMemberExpression extends TExpression {

    /**
     *
     * @var \FrameworkDSW\Linq\Expressions\TExpression
     */
    private $FExpression = null;
    /**
     *
     * @var string
     */
    private $FMemberName = '';
    /**
     *
     * @var mixed
     */
    private $FType = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @param string $MemberName
     * @param mixed $Type
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function __construct($Expression, $MemberName, $Type) {
        TType::Type($Expression, TExpression::class);
        TType::String($MemberName);

        if (preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $MemberName) !== 1) {
            throw new EInvalidParameter();
        }

        $this->FNodeType   = TExpressionType::eMemberAccess();
        $this->FExpression = $Expression;
        $this->FMemberName = $MemberName;
        $this->FType       = $Type;
    }

    /**
     * descHere
     */
    public function Destroy() {
        Framework::Free($this->FExpression);
        parent::Destroy();
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getExpression() {
        return $this->FExpression;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getMember() {
        return $this->FMemberName;
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
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @param string $MemberName
     * @param mixed $Type
     * @return \FrameworkDSW\Linq\Expressions\TMemberExpression
     */
    public function Update($Expression, $MemberName, $Type) {
        TType::Object($Expression, TExpression::class);
        TType::String($MemberName);

        if (($Expression == $this->FExpression) && ($MemberName == $this->FMemberName) && ($Type == $this->FType)) {
            return $this;
        }
        else {
            return TExpression::MakeMember($Expression, $MemberName, $Type);
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionVisitor $Visitor
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitChildren($Visitor) {
        $this->FExpression = $this->FExpression->Accept($Visitor);

        return $this;
    }
}

/**
 * \FrameworkDSW\Linq\Expressions\TMethodCallExpression
 *
 * @author 许子健
 */
final class TMethodCallExpression extends TExpression {

    /**
     *
     * @var \FrameworkDSW\Containers\IList <T: \FrameworkDSW\Linq\Expressions\TExpression>
     */
    private $FArguments = null;
    /**
     *
     * @var array
     */
    private $FMethod = array();
    /**
     *
     * @var \FrameworkDSW\Linq\Expressions\TExpression
     */
    private $FObject = null;
    /**
     *
     * @var mixed
     */
    private $FType = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Object
     * @param array $Method
     * @param \FrameworkDSW\Containers\IList $Arguments <T: \FrameworkDSW\Linq\Expressions\TExpression>
     * @param mixed $ReturnType
     */
    public function __construct($Object, $Method, $Arguments, $ReturnType = null) {
        parent::__construct();

        TType::Object($Object, TExpression::class);
        TType::Arr($Method);
        TType::Object($Arguments, [
            IList::class => ['T' => TExpression::class]]);

        $this->FNodeType  = TExpressionType::eCall();
        $this->FObject    = $Object;
        $this->FMethod    = $Method;
        $this->FArguments = $Arguments;
        $this->FType      = $ReturnType;
    }

    /**
     * (non-PHPdoc)
     *
     * @see TObject::Destroy()
     */
    public function Destroy() {
        Framework::Free($this->FObject);
        Framework::Free($this->FArguments);
        parent::Destroy();
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IList <T: \FrameworkDSW\Linq\Expressions\TExpression>
     */
    public function getArguments() {
        return $this->FArguments;
    }

    /**
     * descHere
     *
     * @return array
     */
    public function getMethod() {
        return $this->FMethod;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getObject() {
        return $this->FObject;
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
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Object
     * @param \FrameworkDSW\Containers\IList $Arguments <T: \FrameworkDSW\Linq\Expressions\TParameterExpression>
     * @return \FrameworkDSW\Linq\Expressions\TMethodCallExpression
     */
    public function Update($Object, $Arguments) {
        TType::Object($Object, TExpression::class);
        TType::Object($Arguments, [
            IList::class => ['T' => TParameterExpression::class]]);

        return TExpression::Call($Object, $this->FMethod, $Arguments, $this->FType);
    }

}

/**
 * \FrameworkDSW\Linq\Expressions\TLambdaExpression
 *
 * @author 许子健
 */
class TLambdaExpression extends TExpression {

    /**
     *
     * @var \FrameworkDSW\Linq\Expressions\TExpression
     */
    private $FBody = null;
    /**
     *
     * @var string
     */
    private $FName = '';
    /**
     *
     * @var \FrameworkDSW\Containers\IList <T: \FrameworkDSW\Linq\Expressions\TParameterExpression>
     */
    private $FParameters = null;
    /**
     *
     * @var mixed
     */
    private $FReturnType = null;

    /**
     * descHere
     *
     * @param string $Name
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Body
     * @param \FrameworkDSW\Containers\IList $Parameters <T: \FrameworkDSW\Linq\Expressions\TParameterExpression>
     * @param mixed $ReturnType
     */
    public function __construct($Name, $Body, $Parameters, $ReturnType) {
        parent::__construct();
        TType::String($Name);
        TType::Object($Body, TExpression::class);
        TType::Object($Parameters, [
            IList::class => ['T' => TParameterExpression::class]]);

        $this->FNodeType   = TExpressionType::eLambda();
        $this->FName       = $Name; // TODO: check if already exists, and if the name is valid.
        $this->FBody       = $Body;
        $this->FParameters = $Parameters;
        $this->FReturnType = $ReturnType;
    }

    /**
     * descHere
     */
    public function Destroy() {
        Framework::Free($this->FParameters);
        Framework::Free($this->FBody);
        parent::Destroy();
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\System\IDelegate
     */
    public function Compile() {

    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getBody() {
        return $this->FBody;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getName() {
        return $this->FName;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IList <T: \FrameworkDSW\Linq\Expressions\TParameterExpression>
     */
    public function getParameters() {
        return $this->FParameters;
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public function getReturnType() {
        return $this->FReturnType;
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public function getType() {
        return $this->FNodeType;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionVisitor $Visitor
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitChildren($Visitor) {
        $this->FBody = $this->FBody->Accept($Visitor);

        return $this;
    }
}

/**
 * \FrameworkDSW\Linq\Expressions\TTypedExpression
 * params <T: \FrameworkDSW\System\IDelegate>
 *
 * @author 许子健
 */
final class TTypedExpression extends TLambdaExpression {

    /**
     * descHere
     *
     * @return T
     */
    public function TypedCompile() {
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public function getType() {
        // TODO: Implement getType() method.
    }
}

/**
 * \FrameworkDSW\Linq\Expressions\TBlockExpression
 *
 * @author 许子健
 */
final class TBlockExpression extends TExpression {

    /**
     *
     * @var \FrameworkDSW\Containers\TList <T: \FrameworkDSW\Linq\Expressions\TExpression>
     */
    private $FExpressions = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\TList $Expressions <T: \FrameworkDSW\Linq\Expressions\TExpression>
     */
    public function __construct($Expressions) {
        parent::__construct();
        TType::Object($Expressions, [
            TList::class => ['T' => TExpression::class]]);
        $this->FExpressions = $Expressions;
        $this->FNodeType    = TExpressionType::eBlock();
    }

    /**
     * descHere
     */
    public function Destroy() {
        Framework::Free($this->FExpressions);
        parent::Destroy();
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IList <T: \FrameworkDSW\Linq\Expressions\TExpression>
     */
    public function getExpressions() {
        return $this->FExpressions;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getResult() {
        return $this->FExpressions->Last();
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public function getType() {
        return $this->FExpressions->Last()->getType();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IList $Expressions <T: \FrameworkDSW\Linq\Expressions\TExpression>
     * @return \FrameworkDSW\Linq\Expressions\TBlockExpression
     */
    public function Update($Expressions) {
        TType::Object($Expressions, [
            IList::class => ['T' => TExpression::class]]);

        return TExpression::Block($Expressions);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpressionVisitor $Visitor
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitChildren($Visitor) {
        foreach ($this->FExpressions as $mExpression) {
            $Visitor->Visit($mExpression);
        }

        return $this;
    }
}

/**
 * \FrameworkDSW\Linq\Expressions\TExpressionVisitor
 *
 * @author 许子健
 */
abstract class TExpressionVisitor extends TObject {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function Visit($Expression) {
        TType::Object($Expression, TExpression::class);

        if ($Expression->getCanReduce()) {
            $Expression = $Expression->Reduce();
        }
        $mObjectType = $Expression->ObjectType();

        switch ($mObjectType) {
            case TBinaryExpression::class :
                return $this->VisitBinary($Expression);
                break;
            case TBlockExpression::class :
                return $this->VisitBlock($Expression);
                break;
            case TConditionalExpression::class :
                return $this->VisitConditional($Expression);
                break;
            case TConstantExpression::class :
                return $this->VisitConstant($Expression);
                break;
            case TDefaultExpression::class :
                return $this->VisitDefault($Expression);
                break;
            case TLambdaExpression::class :
                return $this->VisitLambda($Expression);
                break;
            case TMemberExpression::class :
                return $this->VisitMember($Expression);
                break;
            case TMethodCallExpression::class :
                return $this->VisitMethodCall($Expression);
                break;
            case TParameterExpression::class :
                return $this->VisitParameter($Expression);
                break;
            case TUnaryExpression::class :
                return $this->VisitUnary($Expression);
                break;
            default : // case array('TTypedExpression' => array ('T' => ...)):
                return $this->VisitLambda($Expression);
                break;
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TBinaryExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitBinary($Expression) {
        return $Expression;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TBlockExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitBlock($Expression) {
        return $Expression;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TConditionalExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitConditional($Expression) {
        return $Expression;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TConstantExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitConstant($Expression) {
        return $Expression;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TDefaultExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitDefault($Expression) {
        return $Expression;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TLambdaExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitLambda($Expression) {
        return $Expression;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TMemberExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitMember($Expression) {
        return $Expression;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TMethodCallExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitMethodCall($Expression) {
        return $Expression;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TParameterExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitParameter($Expression) {
        return $Expression;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TUnaryExpression $Expression
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    protected function VisitUnary($Expression) {
        return $Expression;
    }

}