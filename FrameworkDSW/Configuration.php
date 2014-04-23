<?php
/**
 * \FrameworkDSW\Configuration
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 91
 */
namespace FrameworkDSW\Configuration;

use FrameworkDSW\Containers\ECollectionIsReadOnly;
use FrameworkDSW\Containers\ENoSuchKey;
use FrameworkDSW\Containers\IArrayAccess;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\EInvalidObjectCasting;
use FrameworkDSW\Utilities\TType;

/**
 * Interface IConfiguration
 * extends FrameworkDSW\Containers\IArrayAccess<K: string, V: string>
 * @package FrameworkDSW\Configuration
 */
interface IConfiguration extends IArrayAccess {
    /**
     * param <V: IInterface>
     * @param string $Key
     * @return V
     */
    public function Get($Key);

    /**
     * param <V: IInterface>
     * @param string $Key
     * @param V $Default
     * @return V
     */
    public function GetOrDefault($Key, $Default = null);

    /**
     * param <V: IInterface>
     * @param string $Key
     * @param V $Value
     * @param boolean $Append
     * @throws ECollectionIsReadOnly
     */
    public function Set($Key, $Value, $Append = false);

    /**
     * @param string $Key
     * @return \FrameworkDSW\System\IInterface
     */
    public function GetObject($Key);

    /**
     * @param string $Key
     * @param \FrameworkDSW\System\IInterface $Default
     * @return \FrameworkDSW\System\IInterface
     */
    public function GetObjectOrDefault($Key, $Default = null);

    /**
     * @param string $Key
     * @param \FrameworkDSW\System\IInterface $Value
     * @param boolean $Append
     */
    public function SetObject($Key, $Value, $Append = false);

    /**
     * @param string $Key
     * @return boolean
     */
    public function GetBoolean($Key);

    /**
     * @param string $Key
     * @return integer
     */
    public function GetInteger($Key);

    /**
     * @param string $Key
     * @return float
     */
    public function GetFloat($Key);

    /**
     * @param string $Key
     * @return string
     */
    public function GetString($Key);

    /**
     * @param string $Key
     * @param boolean $Default
     * @return boolean
     */
    public function GetBooleanOrDefault($Key, $Default = false);

    /**
     * @param string $Key
     * @param integer $Default
     * @return integer
     */
    public function GetIntegerOrDefault($Key, $Default = 0);

    /**
     * @param string $Key
     * @param float $Default
     * @return float
     */
    public function GetFloatOrDefault($Key, $Default = 0.0);

    /**
     * @param string $Key
     * @param string $Default
     * @return string
     */
    public function GetStringOrDefault($Key, $Default = '');

    /**
     * @param string $Key
     * @param boolean $Value
     * @param boolean $Append
     * @throws ECollectionIsReadOnly
     */
    public function SetBoolean($Key, $Value, $Append = false);

    /**
     * @param string $Key
     * @param integer $Value
     * @param boolean $Append
     * @throws ECollectionIsReadOnly
     */
    public function SetInteger($Key, $Value, $Append = false);

    /**
     * @param string $Key
     * @param float $Value
     * @param boolean $Append
     * @throws ECollectionIsReadOnly
     */
    public function SetFloat($Key, $Value, $Append = false);

    /**
     * @param string $Key
     * @param string $Value
     * @param boolean $Append
     * @throws ECollectionIsReadOnly
     */
    public function SetString($Key, $Value, $Append = false);

    /**
     * @return boolean
     */
    public function getReadOnly();

    /**
     * @param string $Key
     * @throws ECollectionIsReadOnly
     */
    public function Remove($Key);

    /**
     * @param boolean $Value
     */
    public function setReadOnly($Value);

    /**
     * @param string $Prefix
     * @return string[]
     */
    public function GetKeys($Prefix = '');

    /**
     * @param string $Key
     * @return boolean
     */
    public function HasKey($Key);

    /**
     * @param string $Prefix
     * @return integer
     */
    public function Count($Prefix = '');

    /**
     * @return boolean
     */
    public function IsEmpty();

    /**
     * @throws ECollectionIsReadOnly
     */
    public function Clear();

    /**
     *
     */
    public function Flush();
}

/**
 * Interface IConfiguration
 * @package FrameworkDSW\Configuration
 */
interface IConfigurationStorage extends IInterface {
    /**
     * @return \FrameworkDSW\Containers\TMap <K: string, V: mixed>
     */
    public function Load();

    /**
     * @param \FrameworkDSW\Containers\TMap <K: string, V: mixed> $Config
     */
    public function Flush($Config);
}

/**
 * Class TConfiguration
 * @package FrameworkDSW\Configuration
 */
class TConfiguration extends TObject implements IConfiguration {
    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: mixed>
     */
    private $FConfig = null;

    /**
     * @var \FrameworkDSW\Configuration\IConfigurationStorage
     */
    private $FStorage = null;

    /**
     * @param \FrameworkDSW\Configuration\IConfigurationStorage $Storage
     */
    public function __construct($Storage) {
        TType::Object($Storage, IConfigurationStorage::class);
        parent::__construct();

        $this->FStorage = $Storage;
        $this->FConfig  = $Storage->Load();
    }

    /**
     *
     */
    public function Destroy() {
        Framework::Free($this->FStorage);
        Framework::Free($this->FConfig);
        parent::Destroy();
    }

    /**
     * param <V: IInterface>
     * @param string $Key
     * @param V $Default
     * @return V
     */
    public function GetOrDefault($Key, $Default = null) {
        TType::String($Key);
        TType::Object($Default, $this->GenericArg('V'));

        try {
            return $this->Get($Key);
        }
        catch (ENoSuchKey $Ex) {
            return $Default;
        }
    }

    /**
     * param <V: IInterface>
     * @param string $Key
     * @return V
     * @throws ENoSuchKey
     */
    public function Get($Key) {
        TType::String($Key);
        return $this->FConfig[$Key];
    }

    /**
     * param <V: IInterface>
     * @param string $Key
     * @param V $Value
     * @param boolean $Append
     * @throws \FrameworkDSW\Containers\ENoSuchKey
     * @throws ECollectionIsReadOnly
     */
    public function Set($Key, $Value, $Append = false) {
        TType::String($Key);
        TType::Object($Value, $this->GenericArg('V'));
        TType::Bool($Append);

        if ($Append || $this->FConfig->ContainsKey($Key)) {
            $this->FConfig[$Key] = $Value;
        }
        else {
            throw new ENoSuchKey();
        }
    }

    /**
     * @param string $Key
     * @param \FrameworkDSW\System\IInterface $Default
     * @return \FrameworkDSW\System\IInterface
     * @throws EInvalidObjectCasting
     */
    public function GetObjectOrDefault($Key, $Default = null) {
        TType::String($Key);
        TType::Object($Default, IInterface::class);

        try {
            return $this->GetObject($Key);
        }
        catch (ENoSuchKey $Ex) {
            return $Default;
        }
    }

    /**
     * @param string $Key
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @return \FrameworkDSW\System\IInterface
     */
    public function GetObject($Key) {
        TType::String($Key);

        $mResult = $this->FConfig[$Key];
        if ($mResult instanceof IInterface) {
            return $mResult;
        }
        else {
            throw new EInvalidObjectCasting();
        }
    }

    /**
     * @param string $Key
     * @param \FrameworkDSW\System\IInterface $Value
     * @param boolean $Append
     * @throws \FrameworkDSW\Containers\ENoSuchKey
     * @throws ECollectionIsReadOnly
     */
    public function SetObject($Key, $Value, $Append = false) {
        TType::String($Key);
        TType::Object($Value, IInterface::class);
        TType::Bool($Append);

        if ($Append || $this->FConfig->ContainsKey($Key)) {
            $this->FConfig[$Key] = $Value;
        }
        else {
            throw new ENoSuchKey();
        }
    }

    /**
     * @param string $Key
     * @param boolean $Default
     * @return boolean
     */
    public function GetBooleanOrDefault($Key, $Default = false) {
        TType::String($Key);
        TType::Bool($Default);

        try {
            return $this->GetBoolean($Key);
        }
        catch (ENoSuchKey $Ex) {
            return $Default;
        }
    }

    /**
     * @param string $Key
     * @return boolean
     */
    public function GetBoolean($Key) {
        TType::String($Key);
        return (boolean)$this->FConfig[$Key];
    }

    /**
     * @param string $Key
     * @param integer $Default
     * @return integer
     */
    public function GetIntegerOrDefault($Key, $Default = 0) {
        TType::String($Key);
        TType::Int($Default);

        try {
            return $this->GetInteger($Key);
        }
        catch (ENoSuchKey $Ex) {
            return $Default;
        }
    }

    /**
     * @param string $Key
     * @return integer
     */
    public function GetInteger($Key) {
        TType::String($Key);
        return (integer)$this->FConfig[$Key];
    }

    /**
     * @param string $Key
     * @param float $Default
     * @return float
     */
    public function GetFloatOrDefault($Key, $Default = 0.0) {
        TType::String($Key);
        TType::Float($Default);

        try {
            return $this->GetFloat($Key);
        }
        catch (ENoSuchKey $Ex) {
            return $Default;
        }
    }

    /**
     * @param string $Key
     * @return float
     */
    public function GetFloat($Key) {
        TType::String($Key);
        return (float)$this->FConfig[$Key];
    }

    /**
     * @param string $Key
     * @param string $Default
     * @return string
     */
    public function GetStringOrDefault($Key, $Default = '') {
        TType::String($Key);
        TType::String($Default);

        try {
            return $this->GetString($Key);
        }
        catch (ENoSuchKey $Ex) {
            return $Default;
        }
    }

    /**
     * @param string $Key
     * @return string
     */
    public function GetString($Key) {
        TType::String($Key);
        return (string)$this->FConfig[$Key];
    }

    /**
     * @param string $Key
     * @param boolean $Value
     * @param boolean $Append
     * @throws \FrameworkDSW\Containers\ENoSuchKey
     * @throws ECollectionIsReadOnly
     */
    public function SetBoolean($Key, $Value, $Append = false) {
        TType::String($Key);
        TType::Bool($Value);
        TType::Bool($Append);

        if ($Append || $this->FConfig->ContainsKey($Key)) {
            $this->FConfig[$Key] = $Value;
        }
        else {
            throw new ENoSuchKey();
        }
    }

    /**
     * @param string $Key
     * @param integer $Value
     * @param boolean $Append
     * @throws \FrameworkDSW\Containers\ENoSuchKey
     * @throws ECollectionIsReadOnly
     */
    public function SetInteger($Key, $Value, $Append = false) {
        TType::String($Key);
        TType::Int($Value);
        TType::Bool($Append);

        if ($Append || $this->FConfig->ContainsKey($Key)) {
            $this->FConfig[$Key] = $Value;
        }
        else {
            throw new ENoSuchKey();
        }
    }

    /**
     * @param string $Key
     * @param float $Value
     * @param boolean $Append
     * @throws \FrameworkDSW\Containers\ENoSuchKey
     * @throws ECollectionIsReadOnly
     */
    public function SetFloat($Key, $Value, $Append = false) {
        TType::String($Key);
        TType::Float($Value);
        TType::Bool($Append);

        if ($Append || $this->FConfig->ContainsKey($Key)) {
            $this->FConfig[$Key] = $Value;
        }
        else {
            throw new ENoSuchKey();
        }
    }

    /**
     * @return boolean
     */
    public function getReadOnly() {
        return $this->FConfig->getReadOnly();
    }

    /**
     * @param boolean $Value
     */
    public function setReadOnly($Value) {
        TType::Bool($Value);
        $this->FConfig->setReadOnly($Value);
    }

    /**
     * @param string $Prefix
     * @return string[]
     */
    public function GetKeys($Prefix = '') {
        // TODO: Implement GetKeys() method.
    }

    /**
     * @param string $Prefix
     * @return integer
     */
    public function Count($Prefix = '') {
        // TODO: Implement Count() method.
    }

    /**
     * @return boolean
     */
    public function IsEmpty() {
        return $this->FConfig->IsEmpty();
    }

    /**
     * @throws ECollectionIsReadOnly
     */
    public function Clear() {
        $this->FConfig->Clear();
    }

    /**
     * @throws ECollectionIsReadOnly
     */
    public function Flush() {
        if ($this->FConfig->getReadOnly()) {
            throw new ECollectionIsReadOnly();
        }
        $this->FStorage->Flush($this->FConfig);
    }

    /**
     *
     * @param string $offset
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset) {
        return $this->HasKey($offset);
    }

    /**
     * @param string $Key
     * @return boolean
     */
    public function HasKey($Key) {
        return $this->FConfig->ContainsKey($Key);
    }

    /**
     *
     * @param string $offset
     * @return string
     */
    public function offsetGet($offset) {
        return $this->GetString($offset);
    }

    /**
     *
     * @param string $offset
     * @param string $value
     */
    public function offsetSet($offset, $value) {
        $this->SetString($offset, $value);
    }

    /**
     * @param string $Key
     * @param string $Value
     * @param boolean $Append
     * @throws \FrameworkDSW\Containers\ENoSuchKey
     * @throws ECollectionIsReadOnly
     */
    public function SetString($Key, $Value, $Append = false) {
        TType::String($Key);
        TType::String($Value);
        TType::Bool($Append);

        if ($Append || $this->FConfig->ContainsKey($Key)) {
            $this->FConfig[$Key] = $Value;
        }
        else {
            throw new ENoSuchKey();
        }
    }

    /**
     *
     * @param string $offset
     */
    public function offsetUnset($offset) {
        $this->Remove($offset);
    }

    /**
     * @param string $Key
     * @throws ECollectionIsReadOnly
     * @throws ENoSuchKey
     */
    public function Remove($Key) {
        TType::String($Key);
        $this->FConfig->Delete($Key);
    }
}