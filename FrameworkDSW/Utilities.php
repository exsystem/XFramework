<?php
/**
 * Utilities
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 1
 */
namespace FrameworkDSW\Utilities;
require_once 'FrameworkDSW/System.php';
use FrameworkDSW\System\ERuntimeException;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TDelegate;
use FrameworkDSW\System\TRecord;
/**
 *
 * @author 许子健
 */
class EInvalidTypeCasting extends ERuntimeException {}
/**
 *
 * @author 许子健
 */
class EInvalidBoolCasting extends EInvalidTypeCasting {}
/**
 *
 * @author 许子健
 */
class EInvalidIntCasting extends EInvalidTypeCasting {}
/**
 *
 * @author 许子健
 */
class EInvalidFloatCasting extends EInvalidTypeCasting {}
/**
 *
 * @author 许子健
 */
class EInvalidStringCasting extends EInvalidTypeCasting {}
/**
 *
 * @author 许子健
 */
class EInvalidArrayCasting extends EInvalidTypeCasting {}
/**
 *
 * @author 许子健
 */
class EInvalidRecordCasting extends EInvalidTypeCasting {}
/**
 *
 * @author 许子健
 */
class EInvalidObjectCasting extends EInvalidTypeCasting {}
/**
 *
 * @author 许子健
 */
class EInvalidClassCasting extends EInvalidTypeCasting {}
/**
 *
 * @author 许子健
 */
class EInvalidInterfaceCasting extends EInvalidTypeCasting {}
/**
 *
 * @author 许子健
 */
class EInvalidDelegateCasting extends EInvalidTypeCasting {}

/**
 *
 * @author 许子健
 */
class EBadProperties extends EException {}

/**
 *
 * @author 许子健
 */
class EInvalidFileName extends EException {}

/**
 *
 * @author 许子健
 */
final class TType extends TObject {

    /**
     *
     * @param mixed $Var
     */
    public static function Bool(&$Var) {
        $Var = (boolean) $Var;
    }

    /**
     *
     * @param mixed $Var
     */
    public static function Int(&$Var) {
        if (is_int($Var)) {
            return;
        }
        if (is_bool($Var) || is_float($Var) || is_string($Var)) {
            $Var = (integer) $Var;
            return;
        }
        throw new EInvalidIntCasting();
    }

    /**
     *
     * @param mixed $Var
     */
    public static function Float(&$Var) {
        if (is_object($Var)) {
            throw new EInvalidFloatCasting();
        }
        $Var = (float) $Var;
    }

    /**
     *
     * @param mixed $Var
     */
    public static function String(&$Var) {
        if (is_array($Var) || is_resource($Var)) {
            throw new EInvalidStringCasting();
        }
        $Var = (string) $Var;
    }

    /**
     *
     * @param mixed $Var
     */
    public static function Arr(&$Var) {
        if (is_null($Var)) {
            return;
        }
        $Var = (array) $Var;
    }

    /**
     *
     * @param object $Var
     * @param mixed $Type
     */
    public static function Object(&$Var, $Type = 'TObject') {
        if (is_null($Var)) {
            return;
        }

        if (is_string($Type) && (!($Var instanceof $Type))) {
            throw new EInvalidObjectCasting();
        }
        elseif (is_array($Type)) { // should be an array then.
            $mClass = array_keys($Type);
            $mClass = (string) $mClass[0];
            if (($mClass == 'TPair') && ($Var instanceof $mClass)) {
                return;
            }
            if (($Var instanceof $mClass) && ($Type[$mClass] + $Var->GenericArgs() != $Var->GenericArgs())) {
                $mTypeOfClass = $Type[$mClass];
                array_walk_recursive($mTypeOfClass, function ($mValue, $mIndex) use (&$mTypeOfClass) {
                    switch ($mValue) {
                        case 'boolean': break;
                        case 'integer': break;
                        case 'float': break;
                        case 'string': break;
                        case 'array': break;
                        default: $mTypeOfClass[$mIndex] = "\\{$mValue}"; break;
                    }
                });
                if ($mTypeOfClass + $Var->GenericArgs() != $Var->GenericArgs()) {
                    throw new EInvalidObjectCasting();
                }
            }
        }
    }

    /**
     *
     * @param string $Var
     */
    public static function MetaClass(&$Var) {
        $Var = (string) $Var;
        if (class_exists($Var)) {
            return;
        }
        throw new EInvalidClassCasting();
    }

    /**
     *
     * @param string $Var
     */
    public static function Intf(&$Var) {
        $Var = (string) $Var;
        if (interface_exists($Var)) {
            return;
        }
        throw new EInvalidInterfaceCasting();
    }

    /**
     *
     * @param TDelegate $Var
     */
    public static function Delegate($Var) {
        if (!($Var instanceof TDelegate || is_null($Var))) {
            throw new EInvalidDelegateCasting();
        }
    }

    /**
     *
     * @param mixed $Var
     * @param mixed $Type
     */
    public static function Type(&$Var, $Type) {
        switch ($Type) {
            case 'boolean' :
                TType::Bool($Var);
                break;
            case 'integer' :
                TType::Int($Var);
                break;
            case 'float' :
                TType::Float($Var);
                break;
            case 'string' :
                TType::String($Var);
                break;
            case 'array' :
                TType::Arr($Var);
                break;
            case 'TDelegate' :
                TType::Delegate($Var);
                break;
            case 'mixed':
                break;
            default : // an array or a compund type string
                if (is_string($Type) && !(class_exists($Type) || interface_exists($Type))) {
                    throw new EInvalidObjectCasting();
                }
                TType::Object($Var, $Type);
                break;
        }
    }

    /**
     *
     * @param mixed $Type
     * @return boolean
     */
    public static function IsTypePrimitive($Type) {
        return ($Type === 'boolean' || $Type === 'integer' || $Type === 'float' || $Type === 'string' || $Type === 'array');
    }
}

/**
 * TSize
 *
 * @author 许子健
 */
final class TSize extends TRecord {
    /**
     *
     * @var integer
     */
    public $Width = 0;
    /**
     *
     * @var integer
     */
    public $Height = 0;
}

/**
 * TPoint
 *
 * @author 许子健
 */
final class TPoint extends TRecord {
    /**
     *
     * @var integer
     */
    public $X = 0;
    /**
     *
     * @var integer
     */
    public $Y = 0;
}

/**
 * TFont
 *
 * @author 许子健
 */
final class TFont extends TObject { // TODO: impl TFont.
}

/**
 * TColor
 *
 * @author 许子健
 */
final class TColor extends TObject { // TODO: impl TColor.
}

/**
 * TVersion
 *
 * @author 许子健
 */
final class TVersion extends TRecord {
    /**
     *
     * @var integer
     */
    public $Build = 0;
    /**
     *
     * @var integer
     */
    public $MajorVersion = 0;
    /**
     *
     * @var integer
     */
    public $MinorVersion = 0;
    /**
     *
     * @var integer
     */
    public $Revision = 0;
}