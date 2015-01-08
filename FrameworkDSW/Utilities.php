<?php
/**
 * \FrameworkDSW\Utilities
 * @author    许子健
 * @version    $Id$
 * @since    separate file since reversion 1
 */
namespace FrameworkDSW\Utilities;

use FrameworkDSW\Containers\TPair;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\ERuntimeException;
use FrameworkDSW\System\IDelegate;
use FrameworkDSW\System\TDelegate;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TRecord;

/**
 *
 * @author 许子健
 */
class EInvalidTypeCasting extends ERuntimeException {
}

/**
 *
 * @author 许子健
 */
class EInvalidBoolCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EInvalidIntCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EInvalidFloatCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EInvalidStringCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EInvalidArrayCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EInvalidRecordCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EInvalidObjectCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EInvalidClassCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EInvalidInterfaceCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EInvalidDelegateCasting extends EInvalidTypeCasting {
}

/**
 *
 * @author 许子健
 */
class EBadProperties extends EException {
}

/**
 *
 * @author 许子健
 */
class EInvalidFileName extends EException {
}

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
        $Var = (boolean)$Var;
    }

    /**
     *
     * @param mixed $Var
     * @throws EInvalidIntCasting
     */
    public static function Int(&$Var) {
        if (is_int($Var)) {
            return;
        }
        if (is_bool($Var) || is_float($Var) || is_string($Var)) {
            $Var = (integer)$Var;

            return;
        }
        throw new EInvalidIntCasting();
    }

    /**
     *
     * @param mixed $Var
     * @throws EInvalidFloatCasting
     */
    public static function Float(&$Var) {
        if (is_object($Var)) {
            throw new EInvalidFloatCasting();
        }
        $Var = (float)$Var;
    }

    /**
     *
     * @param mixed $Var
     * @throws EInvalidStringCasting
     */
    public static function String(&$Var) {
        if (is_array($Var) || is_resource($Var)) {
            throw new EInvalidStringCasting();
        }
        $Var = (string)$Var;
    }

    /**
     *
     * @param mixed $Var
     */
    public static function Arr(&$Var) {
        if (is_null($Var)) {
            return;
        }
        $Var = (array)$Var;
    }

    /**
     *
     * @param object $Var
     * @param mixed $Type
     * @throws EInvalidObjectCasting
     */
    public static function Object(&$Var, $Type = TObject::class) {
        if (is_null($Var)) {
            return;
        }

        if (is_string($Type) && (!($Var instanceof $Type))) {
            throw new EInvalidObjectCasting();
        }
        elseif (is_array($Type)) { // should be an array then.
            $mClass = array_keys($Type);
            $mClass = (string)$mClass[0];
            if (($mClass == TPair::class) && ($Var instanceof $mClass)) {
                return;
            }
            /** @var \FrameworkDSW\System\TObject $Var */
            if ($Var instanceof $mClass) {
                $mVarGenericArgs = (array)$Var->GenericArgs();
                if ($Type[$mClass] + $mVarGenericArgs != $mVarGenericArgs) {
                    $mTypeOfClass = $Type[$mClass];
                    array_walk_recursive($mTypeOfClass, function ($mValue, $mIndex) use (&$mTypeOfClass) {
                        switch ($mValue) {
                            case Framework::Boolean:
                                break;
                            case Framework::Integer:
                                break;
                            case Framework::Float:
                                break;
                            case Framework::String:
                                break;
                            case null:
                                unset($mTypeOfClass[$mIndex]);
                                break;
                            default:
                                $mTypeOfClass[$mIndex] = "\\{$mValue}";
                                break;
                        }
                    });
                    if ($mTypeOfClass + $mVarGenericArgs != $mVarGenericArgs) {
                        throw new EInvalidObjectCasting();
                    }
                }
            }
            else {
                throw new EInvalidObjectCasting();
            }
        }
    }

    /**
     *
     * @param string $Var
     * @throws EInvalidInterfaceCasting
     */
    public static function Intf(&$Var) {
        $Var = (string)$Var;
        if (interface_exists($Var)) {
            return;
        }
        throw new EInvalidInterfaceCasting();
    }

    /**
     *
     * @param \FrameworkDSW\System\IDelegate $Var
     * @throws EInvalidDelegateCasting
     */
    public static function Delegate(&$Var) {
        if (is_null($Var)) {
            $Var = new TDelegate();
        }
        elseif (!($Var instanceof TDelegate)) {
            throw new EInvalidDelegateCasting();
        }
    }

    /**
     *
     * @param mixed $Var
     * @param mixed $Type
     * @throws EInvalidObjectCasting
     */
    public static function Type(&$Var, $Type) {
        switch ($Type) {
            case Framework::Boolean:
                TType::Bool($Var);
                break;
            case Framework::Integer:
                TType::Int($Var);
                break;
            case Framework::Float:
                TType::Float($Var);
                break;
            case Framework::String:
                TType::String($Var);
                break;
            case Framework::Variant:
                break;
            case null:
                break;
            default: // an array or a compound type string
                if (is_array($Type)) {
                    $mClass = array_keys($Type)[0];
                }
                else {
                    $mClass = (string)$Type;
                }

                if (strrpos($mClass, ']', -1) !== false) { //array
                    TType::Arr($Var);
                    return;
                }
                elseif (in_array(IDelegate::class, class_implements($mClass))) { //delegate
                    TType::Delegate($Var);
                }
                else { //object
                    if (is_string($Type) && !(class_exists($Type) || interface_exists($Type))) {
                        throw new EInvalidObjectCasting();
                    }
                    TType::Object($Var, $Type);
                }
                break;
        }
    }

    /**
     *
     * @param mixed $Type
     * @return boolean
     */
    public static function IsTypePrimitive($Type) {
        return ($Type === 'boolean' || $Type === 'integer' || $Type === 'float' || $Type === 'string' || (is_string($Type) && strrpos($Type, ']', -1) !== false));
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

/**
 *
 * @author 许子健
 */
class TUuid extends TRecord {
    /**
     *
     * @var string
     */
    public $Bytes = "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";

    /**
     *
     * @param \FrameworkDSW\Utilities\TUuid $Uuid
     * @return boolean
     */
    public static function Validate($Uuid) {
        TType::Object($Uuid, TUuid::class);
        return strlen($Uuid->Bytes) == 16;
    }

    /**
     * @return \FrameworkDSW\Utilities\TUuid
     */
    public static function Generate() {
        $mResult        = new TUuid();
        $mResult->Bytes = pack('n*', //16bytes
            mt_rand(0, 0xFFFF), mt_rand(0, 0xFFFF), //4bytes
            mt_rand(0, 0xFFFF), //2bytes
            mt_rand(0, 0x0FFF) | 0x4000, //2bytes
            mt_rand(0, 0x3FFF) | 0x4000, //2bytes
            mt_rand(0, 0xFFFF), mt_rand(0, 0xFFFF), mt_rand(0, 0xFFFF)); //6bytes
        return $mResult;
    }

    /**
     * @param \FrameworkDSW\Utilities\TUuid $Uuid
     * @param string $Delimiter
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return string
     */
    public static function ToString($Uuid, $Delimiter = '-') {
        TType::Object($Uuid, TUuid::class);
        TType::String($Delimiter);

        if (!self::Validate($Uuid)) {
            throw new EInvalidParameter();
        }

        $mResult = bin2hex(substr($Uuid->Bytes, 0, 4));
        $mResult .= $Delimiter;
        $mResult .= bin2hex(substr($Uuid->Bytes, 4, 2));
        $mResult .= $Delimiter;
        $mResult .= bin2hex(substr($Uuid->Bytes, 6, 2));
        $mResult .= $Delimiter;
        $mResult .= bin2hex(substr($Uuid->Bytes, 8, 2));
        $mResult .= $Delimiter;
        $mResult .= bin2hex(substr($Uuid->Bytes, 10, 6));
        return $mResult;
    }

    /**
     * @param string $String
     * @throws EInvalidParameter
     * @return \FrameworkDSW\Utilities\TUuid
     */
    public static function FromString($String) {
        TType::String($String);
        $mStartPos     = 0;
        $mLength       = 0;
        $mHexString    = '';
        $mStringLength = strlen($String);
        while ($mStartPos < $mStringLength) {
            $mFoundLength = strspn($String, '0123456789abcdefABCDEF', $mStartPos, 32);
            $mLength += $mFoundLength;
            $mHexString .= substr($String, $mStartPos++, $mFoundLength);
            $mStartPos += $mFoundLength;
        }
        if ($mLength != 32) {
            throw new EInvalidParameter();
        }
        $mResult        = new TUuid();
        $mResult->Bytes = pack('H*', $mHexString); //fixme hex2bin($mHexString) for php >=5.4
        return $mResult;
    }
}