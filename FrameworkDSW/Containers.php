<?php
/**
 * Containers
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 1
 */
namespace FrameworkDSW\Containers;
require_once 'FrameworkDSW/System.php';
use FrameworkDSW\System\ERuntimeException;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TType;
use FrameworkDSW\System\ENotImplemented;
use FrameworkDSW\System\TRecord;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\TEnum;

/**
 *
 * @author 许子健
 */
class EIndexOutOfBounds extends ERuntimeException {}
/**
 *
 * @author 许子健
 */
class ENoKeyDefined extends ERuntimeException {}
/**
 *
 * @author 许子健
 */
class EIllegalState extends ERuntimeException {}
/**
 *
 * @author 许子健
 */
class EConcurrentModification extends ERuntimeException {}
/**
 *
 * @author 许子健
 */
class EContainerDataOprErr extends ERuntimeException {
    /**
     *
     * @var integer
     */
    private $FEffected = -1;

    /**
     *
     * @param $message string
     * @param $code integer
     * @param $previous Exception
     * @param $EffectedCount integer
     */
    public function __construct($message, $code, $previous, $EffectedCount) {
        $this->FEffected = (integer) $EffectedCount;
        parent::__construct($message, $code, $previous);
    }

    /**
     *
     * @return integer
     */
    public function EffectedCount() {
        return $this->FEffected;
    }
}
/**
 *
 * @author 许子健
 */
class EFailedToInsert extends EContainerDataOprErr {}
/**
 *
 * @author 许子健
 */
class EFailedToRemove extends EContainerDataOprErr {}

/**
 *
 * @author 许子健
 */
class EContainerException extends EException {}
/**
 *
 * @author 许子健
 */
class ECollectionIsReadOnly extends EContainerException {}
/**
 *
 * @author 许子健
 */
class EInvalidCapacity extends EContainerException {}
/**
 *
 * @author 许子健
 */
class ENoSuchElement extends EContainerException {}
/**
 *
 * @author 许子健
 */
class ECollectionNotExisted extends EContainerException {}

/**
 * IIterator
 * param	<T>
 */
interface IIterator extends IInterface, \Iterator {

    /**
     * Remove
     */
    public function Remove();
}

/**
 * IIteratorAggregate
 * param	<T>
 */
interface IIteratorAggregate extends IInterface, \IteratorAggregate {

    /**
     *
     * @return IIterator <T>
     */
    public function Iterator();
}

/**
 * IArrayAccess
 * param	<K, V>
 */
interface IArrayAccess extends IInterface, \ArrayAccess {}

// IListItrerator : {[node_0, node_1, node_2, ... ,node_n ,] tail_node}

/**
 * IListIterator
 * extends	IIterator<T>
 * param	<T>
 */
interface IListIterator extends IIterator {

    /**
     *
     * @param $Element T
     */
    public function Add($Element);

    /**
     *
     * @return boolean
     */
    public function HasPrevious();

    /**
     *
     * @return T
     */
    public function Previous();

    /**
     *
     * @param
     *            T
     */
    public function Set($Element);
}

/**
 * ICollection
 * extends	IIteratorAggregate<T>
 * param	<T>
 */
interface ICollection extends IIteratorAggregate {

    /**
     *
     * @param $Element T
     */
    public function Add($Element);

    /**
     *
     * @param $Collection ICollection
     */
    public function AddAll($Collection);

    /**
     *
     * @return boolean
     */
    public function Clear();

    /**
     *
     * @param T $Element
     * @return boolean
     */
    public function Contains($Element);

    /**
     *
     * @param $Collection ICollection
     * @return boolean
     */
    public function ContainsAll($Collection);

    /**
     *
     * @return boolean
     */
    public function IsEmpty();

    /**
     *
     * @return integer
     */
    public function Size();

    /**
     *
     * @param $Element T
     */
    public function Remove($Element);

    /**
     *
     * @param $Collection ICollection
     */
    public function RemoveAll($Collection);

    /**
     *
     * @param $Collection ICollection
     * @return boolean
     */
    public function RetainAll($Collection);

    /**
     *
     * @return T[]
     */
    public function ToArray();

}

/**
 * IList
 * extends	ICollection<T>
 * param	<T>
 */
interface IList extends ICollection {

    /**
     *
     * @param $Index integer
     * @param $Element T
     */
    public function Insert($Index, $Element);

    /**
     *
     * @param $Index integer
     * @param $Collection ICollection
     *            <T>
     */
    public function InsertAll($Index, $Collection);

    /**
     *
     * @param $Index integer
     * @return T
     */
    public function Get($Index);

    /**
     *
     * @param $Element T
     * @return integer
     */
    public function IndexOf($Element);

    /**
     *
     * @param $Element T
     * @return integer
     */
    public function LastIndexOf($Element);

    /**
     *
     * @param $Index integer
     * @return IListIterator <T>
     */
    public function ListIterator($Index = 0);

    /**
     *
     * @param $Index integer
     */
    public function RemoveAt($Index);

    /**
     *
     * @param $Index integer
     * @param $Element T
     * @return T
     */
    public function Set($Index, $Element);

    /**
     *
     * @param $FromIndex integer
     * @param $ToIndex integer
     * @return IList <T>
     */
    public function SubList($FromIndex, $ToIndex);

    /**
     *
     * @return T
     */
    public function First();

    /**
     *
     * @return T
     */
    public function Last();
}

/**
 * ISet
 * extends ICollection<T>
 * param	<T>
 */
interface ISet extends ICollection {}

/**
 * IStack
 * param	<T>
 * extends	ICollection<T>
 *
 * @author 许子健
 */
interface IStack extends ICollection {

    /**
     *
     * @param $Element T
     */
    public function Push($Element);

    /**
     *
     * @return T
     */
    public function Pop();

    /**
     *
     * @return T
     */
    public function Peek();
}

/**
 * IQueue
 * extends	ICollection<T>
 * param	<T>
 */
interface IQueue extends ICollection {

    /**
     *
     * @param $Element T
     */
    public function Offer($Element);

    /**
     *
     * @return T
     */
    public function Peek();

    /**
     *
     * @return T
     */
    public function Poll();
}

/**
 * TStdMapMapIterator
 * params	<K, V>
 * extends	IIterator<T: TPair<K, V>>
 *
 * @author 许子健
 */
class TStdMapMapIterator extends TObject implements IIterator {

    /**
     *
     * @var TMapKeyType
     */
    private $FKeyType = null;
    /**
     *
     * @var array
     */
    private $FMapData = array ();

    /**
     * descHere
     *
     * @param $MapData array
     * @param $KeyType TMapKeyType
     */
    public function __construct($MapData, $KeyType) {
        parent::__construct();
        TType::Arr($MapData);
        TType::Object($KeyType, 'TMapKeyType');

        if ($KeyType == TMapKeyType::eRecord()) {
            $this->FKeyType = TMapKeyType::eObject();
        }
        else {
            $this->FKeyType = $KeyType;
        }
        $this->FMapData = $MapData;
    }

    /**
     * descHere
     *
     * @return V
     */
    public function current() {
        if ($this->FKeyType == TMapKeyType::eObject()) {
            $mCurrentPair = current($this->FMapData);
            return $mCurrentPair->Value;
        }
        return current($this->FMapData);
    }

    /**
     * descHere
     *
     * @return K
     */
    public function key() {
        switch ($this->FKeyType) {
            case TMapKeyType::eObject() :
                return current($this->FMapData)->Key;
            default :
                return key($this->FMapData);
        }

    }

    /**
     * descHere
     */
    public function next() {
        next($this->FMapData);
    }

    /**
     * descHere
     */
    public function Remove() {
        throw new ENotImplemented();
    }

    /**
     * descHere
     */
    public function rewind() {
        reset($this->FMapData);
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function valid() {
        return (key($this->FMapData) !== null);
    }

}

/**
 * IMap
 * params	<K, V>
 * extends	IArrayAccess<K, V>, ICollection<T: TPair<K, V>>
 *
 * @author 许子健
 */
interface IMap extends IArrayAccess, ICollection {

    /**
     * descHere
     *
     * @param $Key K
     * @return boolean
     */
    public function ContainsKey($Key);

    /**
     * descHere
     *
     * @param $Value V
     * @return boolean
     */
    public function ContainsValue($Value);

    /**
     * descHere
     *
     * @param $Key K
     */
    public function Delete($Key);

    /**
     * descHere
     *
     * @param $Key K
     * @return V
     */
    public function Get($Key);

    /**
     * descHere
     *
     * @return ISet <K>
     */
    public function KeySet();

    /**
     * descHere
     *
     * @return ISet <TPair<K, V>>
     */
    public function PairSet();

    /**
     * descHere
     *
     * @param $Key K
     * @param $Value V
     */
    public function Put($Key, $Value);

    /**
     * descHere
     *
     * @param $Map IMap
     *            <K, V>
     */
    public function PutAll($Map);

    /**
     * descHere
     *
     * @return ICollection <V>
     */
    public function Values();

}

/**
 * TPair
 * param	<K, V>
 */
final class TPair extends TRecord {
    /**
     * Key.
     *
     * @var K
     */
    public $Key;

    /**
     * Value.
     *
     * @var V
     */
    public $Value;
}

/**
 * The default iterator for TAbstractList.
 * extends IIterator<T>
 * param	<T>
 *
 * @author 许子健
 */
class TStdListIterator extends TObject implements IIterator {
    /**
     *
     * @var TAbstractList <T>
     */
    protected $FList = null;
    /**
     *
     * @var integer
     */
    protected $FCursor = 0;
    /**
     *
     * @var integer
     */
    protected $FLastAt = -1;

    /**
     *
     * @param $List TAbstractList
     *            <T>
     * @see FrameworkDSW/TObject#Create()
     */
    public function __construct($List) {
        parent::__construct();
        TType::Type($List, array (
            'TAbstractList' => array ('T' => $this->GenericArg('T'))));

        $this->FList = $List;
    }

    /**
     */
    public function rewind() {
        $this->FCursor = 0;
        $this->FLastAt = -1;
    }

    /**
     *
     * @return integer string
     */
    public function key() {
        throw new ENoKeyDefined();
    }

    /**
     *
     * @return T
     */
    public function current() {
        try {
            return $this->FList->Get($this->FCursor);
        }
        catch (EIndexOutOfBounds $e) {
            throw new ENoSuchElement();
        }
    }

    /**
     *
     * @see FrameworkDSW/IIterator#Remove()
     */
    public function Remove() {
        if ($this->FLastAt < 0) {
            throw new EIllegalState();
        }

        try {
            $this->FList->RemoveAt($this->FCursor);
            if ($this->FLastAt < $this->FCursor) {
                --$this->FCursor;
            }
            $this->FLastAt = -1;
        }
        catch (EIndexOutOfBounds $e) {
            throw new EConcurrentModification();
        }
    }

    /**
     *
     * @return boolean
     */
    public function valid() {
        return $this->FCursor < $this->FList->Size();
    }

    /**
     */
    public function next() {
        $this->FLastAt = $this->FCursor;
        ++$this->FCursor;
    }
}

/**
 * TStdListListIterator
 * extends	TStdListIterator<T>, IListIterator<T>
 * param	<T>
 *
 * @author 许子健
 */
class TStdListListIterator extends TStdListIterator implements IListIterator {

    /**
     *
     * @param $List TAbstractList
     *            <T>
     * @param $StartAt integer
     * @see FrameworkDSW/TStdListIterator#Create($List)
     */
    public function __construct($List, $StartAt) {
        TType::Type($List, array (
            'TAbstractList' => array ('T' => self::StaticGenericArg('T'))));
        TType::Int($StartAt);

        parent::__construct($List);
        $this->FCursor = $StartAt;
    }

    /**
     *
     * @return integer
     * @see FrameworkDSW/TStdListIterator#key()
     */
    public function key() {
        return $this->FCursor;
    }

    /**
     *
     * @param $Element T
     * @see FrameworkDSW/IListIterator#Add($Element)
     */
    public function Add($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        try {
            $this->FList->Insert($this->FCursor, $Element);
            $this->FLastAt = -1;
        }
        catch (EIndexOutOfBounds $e) {
            throw new EConcurrentModification();
        }
    }

    /**
     *
     * @return boolean
     * @see FrameworkDSW/IListIterator#HasPrevious()
     */
    public function HasPrevious() {
        return $this->FCursor != 0;
    }

    /**
     * (non-PHPdoc)
     *
     * @see FrameworkDSW/IListIterator#Previous()
     */
    public function Previous() {
        $this->FLastAt = $this->FCursor;
        --$this->FCursor;
    }

    /**
     *
     * @param $Element T
     * @see FrameworkDSW/IListIterator#Set($Element)
     */
    public function Set($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        if ($this->FLastAt < 0) {
            throw new EIllegalState();
        }

        try {
            $this->FList->Set($this->FCursor, $Element);
        }
        catch (EIndexOutOfBounds $e) {
            throw new EConcurrentModification();
        }
    }
}

/**
 * TAbstractCollection
 * extends	ICollection<T>
 * param	<T>
 *
 * @author 许子健
 */
abstract class TAbstractCollection extends TObject implements ICollection {
    /**
     *
     * @var boolean
     */
    protected $FReadOnly = false;
    /**
     *
     * @var boolean
     */
    protected $FElementsOwned = false;

    /**
     * dochere
     */
    protected function CheckReadOnly() {
        if ($this->FReadOnly) {
            throw new ECollectionIsReadOnly();
        }
    }

    /**
     *
     * @param $ElementsOwned boolean
     */
    public function __construct($ElementsOwned) {
        parent::__construct();
        TType::Bool($ElementsOwned);
        $this->FElementsOwned = $ElementsOwned && !TType::IsTypePrimitive($this->GenericArg('T'));
    }

    /**
     * (non-PHPdoc)
     *
     * @see TObject::Destroy()
     */
    public function Destroy() {
        $this->FReadOnly = false;
        $this->Clear();
        parent::Destroy();
    }

    /**
     *
     * @param $Collection ICollection
     *            <T>
     */
    public function AddAll($Collection) {
        TType::Type($Collection, array (
            'ICollection' => array ('T' => $this->GenericArg('T'))));
        $this->CheckReadOnly();

        foreach ($Collection as $mElement) {
            $this->Add($mElement);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see FrameworkDSW/ICollection#Clear()
     */
    public function Clear() {
        $this->CheckReadOnly();

        $mItr = $this->Iterator();
        while ($mItr->Valid()) {
            $mItr->Remove();
            $mItr->Next();
        }
    }

    /**
     *
     * @param $Element T
     * @return boolean
     */
    public function Contains($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        $mItr = $this->Iterator();

        if (TType::IsTypePrimitive($this->GenericArg('T'))) {
            while ($mItr->valid()) {
                if ($Element === $mItr->current()) {
                    return true;
                }
                $mItr->next();
            }
        }
        else {
            while ($mItr->valid()) {
                if ($Element->Equals($mItr->current())) {
                    return true;
                }
                $mItr->next();
            }
        }

        return false;
    }

    /**
     *
     * @param $Collection ICollection
     *            <T>
     * @return boolean
     */
    public function ContainsAll($Collection) {
        TType::Type($Collection, array (
            'ICollection' => array ('T' => $this->GenericArg('T'))));

        foreach ($Collection as $mElement) {
            if (!$this->Contains($mElement)) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function IsEmpty() {
        return $this->Size() == 0;
    }

    /**
     *
     * @param $Element T
     */
    public function Remove($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        $this->CheckReadOnly();
        $mItr = $this->Iterator();

        if (TType::IsTypePrimitive($this->GenericArg('T'))) {
            while ($mItr->valid()) {
                if ($Element === $mItr->current()) {
                    $mItr->Remove();
                    return;
                }
                $mItr->next();
            }
        }
        else {
            while ($mItr->Valid()) {
                if ($Element->Equals($mItr->current())) {
                    $mItr->Remove();
                    return;
                }
                $mItr->next();
            }
        }

        throw new ENoSuchElement();
    }

    /**
     *
     * @param $Collection ICollection
     *            <T>
     */
    public function RemoveAll($Collection) {
        TType::Type($Collection, array (
            'ICollection' => array ('T' => $this->GenericArg('T'))));

        $this->CheckReadOnly();
        $mItr = $this->Iterator();
        $mFlag = true;
        while ($mItr->valid()) {
            if ($Collection->Contains($mItr->current())) {
                $mItr->Remove();
                $mFlag = false;
            }
            $mItr->next();
        }

        if ($mFlag) {
            throw new ECollectionNotExisted();
        }
    }

    /**
     *
     * @param $Collection ICollection
     *            <T>
     * @return boolean
     */
    public function RetainAll($Collection) {
        TType::Type($Collection, array (
            'ICollection' => array ('T' => $this->GenericArg('T'))));

        $mModified = false;
        $mItr = $this->Iterator();
        while ($mItr->valid()) {
            if (!$Collection->Contains($mItr->current())) {
                $mItr->Remove();
                $mModified = true;
            }
            $mItr->next();
        }

        return $mModified;
    }

    /**
     *
     * @return T[]
     */
    public function ToArray() {
        $mResult = array ();
        foreach ($this as $mValue) {
            $mResult[] = $mValue;
        }
        return $mResult;
    }

    /**
     *
     * @return IIterator <T>
     */
    public function getIterator() {
        return $this->Iterator();
    }

    /**
     *
     * @return boolean
     */
    public function getReadOnly() {
        return $this->FReadOnly;
    }

    /**
     *
     * @param $Value boolean
     */
    public function setReadOnly($Value) {
        TType::Bool($Value);

        $this->FReadOnly = $Value;
    }

    /**
     *
     * @return boolean
     */
    public function getElementsOwned() {
        return $this->FElementsOwned;
    }
}

/**
 * TAbstractList
 * extends	TAbstractCollection<T>, IList<T>, IArrayAccess<K:integer, V = T>
 * param	<T>
 *
 * @author 许子健
 */
abstract class TAbstractList extends TAbstractCollection implements IList, IArrayAccess {
    /**
     *
     * @var integer
     */
    protected $FSize = 0;

    /**
     *
     * @param $Index integer
     */
    private function CheckIndexForAdd($Index) {
        if ($Index < 0 || $Index > $this->FSize) {
            throw new EIndexOutOfBounds();
        }
    }

    /**
     *
     * @param $Index integer
     */
    private function CheckIndexForGet($Index) {
        if ($Index < 0 || $Index >= $this->FSize) {
            throw new EIndexOutOfBounds();
        }
    }

    /**
     *
     * @param $Index integer
     * @param $Element T
     */
    protected abstract function DoInsert($Index, $Element);

    /**
     *
     * @param $Index integer
     * @return T
     */
    protected abstract function DoGet($Index);

    /**
     *
     * @param $Index integer
     * @param $Collection ICollection
     *            <T>
     * @return integer
     */
    protected function DoInsertAll($Index, $Collection) {
        $mItr = $Collection->Iterator();
        $mBackup = $Index;

        while ($mItr->valid()) {
            try {
                $this->DoInsert($Index++, $mItr->current());
            }
            catch (EException $e) {
                throw new EFailedToInsert('', 0, $e, $Index - $mBackup - 1);
            }
            $mItr->next();
        }
        return $Index - $mBackup;
    }

    /**
     *
     * @param $Index integer
     */
    protected abstract function DoRemoveAt($Index);

    /**
     *
     * @param $FromIndex integer
     * @param $ToIndex integer
     * @return integer
     */
    protected function DoRemoveRange($FromIndex, $ToIndex) {
        $mItr = $this->ListIterator($FromIndex);
        $mRemoved = 0;
        for ($mN = $ToIndex - $FromIndex; $mRemoved < $mN; ++$mRemoved) {
            try {
                $mItr->Remove();
            }
            catch (EException $e) {
                throw new EFailedToRemove('', 0, $e, --$mRemoved);
            }
            $mItr->next();
        }
        return $mRemoved;
    }

    /**
     *
     * @param $Index integer
     * @param $Element T
     */
    protected abstract function DoSet($Index, $Element);

    /**
     *
     * @param $FromIndex integer
     * @param $ToIndex integer
     * @return IList <T>
     */
    protected abstract function DoSubList($FromIndex, $ToIndex);

    /**
     *
     * @return T
     */
    protected abstract function DoFirst();

    /**
     *
     * @return T
     */
    protected abstract function DoLast();

    /**
     */
    protected abstract function DoClear();

    /**
     * (non-PHPdoc)
     *
     * @param $Element T
     * @see FrameworkDSW/AbstractCollection#Add($Element)
     */
    public final function Add($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        $this->Insert($this->Size(), $Element);
    }

    /**
     */
    public final function Clear() {
        if ($this->FSize != 0) {
            $this->CheckReadOnly();
            $this->DoClear();
            $this->FSize = 0;
        }
    }

    /**
     *
     * @param $Index integer
     * @param $Element T
     */
    public final function Insert($Index, $Element) {
        TType::Int($Index);
        TType::Type($Element, $this->GenericArg('T'));

        $this->CheckReadOnly();
        $this->CheckIndexForAdd($Index);
        $this->DoInsert($Index, $Element);
        ++$this->FSize;
    }

    /**
     *
     * @param $Index integer
     * @param $Collection ICollection
     *            <T>
     */
    public final function InsertAll($Index, $Collection) {
        TType::Int($Index);
        TType::Type($Collection, array (
            'ICollection' => array ('T' => $this->GenericArg('T'))));

        $this->CheckReadOnly();
        $this->CheckIndexForAdd($Index);
        try {
            $this->FSize += $this->DoInsertAll($Index, $Collection);
        }
        catch (EFailedToInsert $e) {
            $this->FSize += $e->EffectedCount();
            throw $e;
        }
    }

    /**
     *
     * @param $Index integer
     * @return T
     */
    public final function Get($Index) {
        TType::Int($Index);

        $this->CheckIndexForGet($Index);
        return $this->DoGet($Index);
    }

    /**
     *
     * @param $Element T
     * @return integer
     */
    public final function IndexOf($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        $mItr = $this->ListIterator();

        if (TType::IsTypePrimitive($this->GenericArg('T'))) {
            while ($mItr->valid()) {
                if ($Element === $mItr->current()) {
                    return $mItr->key();
                }
                $mItr->next();
            }
        }
        else {
            while ($mItr->valid()) {
                if ($Element->Equals($mItr->current())) {
                    return $mItr->key();
                }
                $mItr->next();
            }
        }

        throw new ENoSuchElement();
    }

    /**
     *
     * @return IIterator <T>
     * @see FrameworkDSW/TAbstractCollection#Iterator()
     */
    public function Iterator() {
        TStdListIterator::PrepareGeneric(array ('T' => $this->GenericArg('T')));
        return new TStdListIterator($this);
    }

    /**
     *
     * @param $Element T
     * @return integer
     */
    public final function LastIndexOf($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        $mItr = $this->ListIterator($this->FSize);

        if (TType::IsTypePrimitive($this->GenericArg('T'))) {
            while ($mItr->HasPrevious()) {
                $mItr->Previous();
                if ($Element === $mItr->current()) {
                    return $mItr->key();
                }
            }
        }
        else {
            while ($mItr->HasPrevious()) {
                $mItr->Previous();
                if ($Element->Equals($mItr->current())) {
                    return $mItr->key();
                }
            }
        }

        throw new ENoSuchElement();
    }

    /**
     *
     * @param $Index integer
     * @return IListIterator <T>
     */
    public function ListIterator($Index = 0) {
        TType::Int($Index);

        $this->CheckIndexForAdd($Index);
        TStdListListIterator::PrepareGeneric(array (
            'T' => $this->GenericArg('T')));
        return new TStdListListIterator($this, $Index);
    }

    /**
     *
     * @param $Index integer
     */
    public final function RemoveAt($Index) {
        $this->CheckReadOnly();

        $this->CheckIndexForGet($Index);
        $this->DoRemoveAt($Index);
        --$this->FSize;
    }

    /**
     *
     * @param $FromIndex integer
     * @param $ToIndex integer
     */
    public final function RemoveRange($FromIndex, $ToIndex) {
        TType::Int($FromIndex);
        TType::Int($ToIndex);

        $this->CheckReadOnly();

        $this->CheckIndexForGet($FromIndex);
        $this->CheckIndexForGet($ToIndex);
        if ($FromIndex > $ToIndex) {
            $mTemp = $FromIndex;
            $FromIndex = $ToIndex;
            $ToIndex = $mTemp;
        }

        try {
            $this->FSize -= $this->DoRemoveRange($FromIndex, $ToIndex);
        }
        catch (EFailedToRemove $e) {
            $this->FSize -= $e->EffectedCount();
            throw $e;
        }
    }

    /**
     *
     * @param $Index integer
     * @param $Element T
     * @return T
     */
    public final function Set($Index, $Element) {
        TType::Int($Index);
        TType::Type($Element, $this->GenericArg('T'));

        $this->CheckReadOnly();
        $this->CheckIndexForGet($Index);

        if ($this->FElementsOwned) {
            Framework::Free($this->DoGet($Index));
        }

        $this->DoSet($Index, $Element);
    }

    /**
     *
     * @return integer
     * @see FrameworkDSW/TAbstractCollection#Size()
     */
    public final function Size() {
        return $this->FSize;
    }

    /**
     *
     * @param $FromIndex integer
     * @param $ToIndex integer
     * @return IList <T>
     */
    public final function SubList($FromIndex, $ToIndex) {
        TType::Int($FromIndex);
        TType::Int($ToIndex);

        $this->CheckIndexForGet($FromIndex);
        $this->CheckIndexForGet($ToIndex);

        return $this->DoSubList($FromIndex, $ToIndex);
    }

    /**
     *
     * @return T
     */
    public final function First() {
        if ($this->FSize == 0) {
            throw new EIndexOutOfBounds();
        }
        return $this->DoFirst();
    }

    /**
     *
     * @return T
     */
    public final function Last() {
        if ($this->FSize == 0) {
            throw new EIndexOutOfBounds();
        }
        return $this->DoLast();
    }

    /**
     *
     * @param $offset integer
     * @return boolean
     */
    public final function offsetExists($offset) {
        TType::Int($offset);

        try {
            $this->CheckIndexForGet($offset);
        }
        catch (EIndexOutOfBounds $e) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param $offset integer
     * @return T
     */
    public final function offsetGet($offset) {
        return $this->Get($offset);
    }

    /**
     *
     * @param $offset integer
     * @param T $value
     */
    public final function offsetSet($offset, $value) {
        if ($offset === null) {
            $this->Add($value);
        }
        else {
            $this->Set($offset, $value);
        }
    }

    /**
     *
     * @param $offset integer
     */
    public final function offsetUnset($offset) {
        $this->RemoveAt($offset);
    }
}

/**
 * TAbstractStack
 * param	<T>
 * extends	TAbstractCollection<T>
 *
 * @author 许子健
 */
abstract class TAbstractStack extends TAbstractCollection {

    /**
     *
     * @param $Element T
     */
    protected abstract function DoPush($Element);

    /**
     *
     * @return T
     */
    protected abstract function DoPop();

    /**
     *
     * @return T
     */
    protected abstract function DoPeek();

    /**
     */
    protected abstract function DoClear();

    /**
     *
     * @param $Element T
     */
    public final function Push($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        $this->CheckReadOnly();

        $this->DoPush($Element);
    }

    /**
     *
     * @param $Element T
     */
    public final function Add($Element) {
        $this->Push($Element);
    }

    /**
     *
     * @param $Collection ICollection
     *            <T>
     */
    public final function AddAll($Collection) {
        TType::Object($Collection, array (
            'ICollection' => array ('T' => $this->GenericArg('T'))));
        $this->CheckReadOnly();

        foreach ($Collection as $Element) {
            $this->Push($Element);
        }
    }

    /**
     */
    public final function Clear() {
        if (!$this->IsEmpty()) {
            $this->DoClear();
        }
    }

    /**
     *
     * @param $Element T
     */
    public final function Remove($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        throw new EFailedToRemove();
    }

    /**
     *
     * @param $Collection ICollection
     *            <T>
     */
    public final function RemoveAll($Collection) {
        TType::Object('ICollection', array ('T' => $this->GenericArg('T')));
        throw new EFailedToRemove();
    }

    /**
     *
     * @return T
     */
    public final function Peek() {
        if ($this->IsEmpty()) {
            throw new ENoSuchElement();
        }

        return $this->DoPeek();
    }

    /**
     *
     * @return T
     */
    public final function Pop() {
        $this->CheckReadOnly();
        if ($this->IsEmpty()) {
            throw new ENoSuchElement();
        }

        return $this->DoPop();
    }

    /**
     *
     * @return IIterator <T>
     */
    public function Iterator() {
        // TODO: iterator
    }
}

/**
 * TAbstractMap
 * params	<K, V, T: TPair<K, V>>
 * extends	IMap<K, V>, TAbstractCollection<T: TPair<K, V>>
 *
 * @author 许子健
 */
abstract class TAbstractMap extends TAbstractCollection implements IMap {

    /**
     * descHere
     *
     * @param $Element TPair
     *            <K, V>
     */
    public function Add($Element) {
        TType::Type($Element, array (
            'TPair' => array ('K' => $this->GenericArg('K'),
                'V' => $this->GenericArg('V'))));
        $this->CheckReadOnly();
        $this->DoPut($Element->Key, $Element->Value);
    }

    /**
     * descHere
     *
     * @param $Key K
     * @return boolean
     */
    public function ContainsKey($Key) {
        TType::Type($Key, $this->GenericArg('K'));
        return $this->DoContainsKey($Key);
    }

    /**
     * descHere
     *
     * @param $Value V
     * @return boolean
     */
    public function ContainsValue($Value) {
        TType::Type($Value, $this->GenericArg('V'));
        return $this->DoContainsValue($Value);
    }

    /**
     * descHere
     *
     * @param $Key K
     */
    public function Delete($Key) {
        TType::Type($Key, $this->GenericArg('K'));
        $this->CheckReadOnly();
        if ($this->DoContainsKey($Key)) {
            $this->DoDelete($Key);
        }
        else {
            throw new EIndexOutOfBounds(); // TODO: define a new exception
                                               // class.
        }
    }

    /**
     * descHere
     *
     * @param $Key K
     * @return boolean
     */
    protected function DoContainsKey($Key) {
        $mItr = $this->Iterator();
        if (TType::IsTypePrimitive($this->GenericArg('K'))) {
            while ($mItr->valid()) {
                if ($mItr->current()->Key === $Key) {
                    return true;
                }
                $mItr->next();
            }
            return false;
        }
        else {
            while ($mItr->valid()) {
                if ($Key->Equals($mItr->current()->Key)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * descHere
     *
     * @param $Value V
     * @return boolean
     */
    protected function DoContainsValue($Value) {
        $mItr = $this->Iterator();
        if (TType::IsTypePrimitive($this->GenericArg('V'))) {
            while ($mItr->valid()) {
                if ($mItr->current()->Value === $Value) {
                    return true;
                }
                $mItr->next();
            }
            return false;
        }
        else {
            while ($mItr->valid()) {
                if ($Value->Equals($mItr->current()->Value)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * descHere
     *
     * @param $Key K
     */
    protected abstract function DoDelete($Key);

    /**
     * descHere
     *
     * @param $Key K
     * @return V
     */
    protected abstract function DoGet($Key);

    /**
     * descHere
     *
     * @return ISet <K>
     */
    protected function DoKeySet() {
        // TODO: DoKeySet.
    }

    /**
     * descHere
     *
     * @return ISet <TPair<K, V>>
     */
    protected function DoPairSet() {
        // TODO: DoPairSet.
    }

    /**
     * descHere
     *
     * @param $Key K
     * @param $Value V
     */
    protected abstract function DoPut($Key, $Value);

    /**
     * descHere
     *
     * @param $Map IMap
     *            <K, V>
     */
    protected function DoPutAll($Map) {
        $mItr = $Map->Iterator();
        while ($mItr->valid()) {
            $this->DoPut($mItr->current()->Key, $mItr->current()->Pair);
            $mItr->next();
        }
    }

    /**
     * descHere
     *
     * @return ICollection <V>
     */
    protected function DoValues() {
        $mItr = $this->Iterator();
        $mResult = new TList($this->Size());
        while ($mItr->valid()) {
            $mResult->Add($mItr->current()->Value);
            $mItr->next();
        }
        return $mResult;
    }

    /**
     * descHere
     *
     * @param $Key K
     * @return V
     */
    public function Get($Key) {
        TType::Type($Key, $this->GenericArg('K'));
        if (!$this->DoContainsKey($Key)) {
            throw new EIndexOutOfBounds();
        }
        return $this->DoGet($Key);
    }

    /**
     * descHere
     *
     * @return IIterator <TPair<K, V>>
     */
    public function Iterator() {
        // TODO: Iterator.
    }

    /**
     * descHere
     *
     * @return ISet <K>
     */
    public function KeySet() {
        // TODO: KeySet.
    }

    /**
     * descHere
     *
     * @param $offset K
     * @return boolean
     */
    public final function offsetExists($offset) {
        TType::Type($offset, $this->GenericArg('K'));
        return $this->DoContainsKey($offset);
    }

    /**
     * descHere
     *
     * @param $offset K
     * @return V
     */
    public final function offsetGet($offset) {
        TType::Type($offset, $this->GenericArg('K'));
        if (!$this->DoContainsKey($offset)) {
            throw new EIndexOutOfBounds();
        }
        return $this->DoGet($offset);
    }

    /**
     * descHere
     *
     * @param $offset K
     * @param $value V
     */
    public final function offsetSet($offset, $value) {
        TType::Type($offset, $this->GenericArg('K'));
        TType::Type($value, $this->GenericArg('V'));
        $this->CheckReadOnly();
        $this->DoPut($offset, $value);
    }

    /**
     * descHere
     *
     * @param K $offset
     */
    public final function offsetUnset($offset) {
        TType::Type($offset, $this->GenericArg('K'));
        $this->CheckReadOnly();
        if (!$this->DoContainsKey($offset)) {
            throw new EIndexOutOfBounds();
        }
        $this->DoDelete($offset);
    }

    /**
     * descHere
     *
     * @return ISet <TPair<K, V>>
     */
    public function PairSet() {
        return $this->DoPairSet();
    }

    /**
     * descHere
     *
     * @param $Key K
     * @param $Value V
     */
    public function Put($Key, $Value) {
        TType::Type($Key, $this->GenericArg('K'));
        TType::Type($Value, $this->GenericArg('V'));
        $this->CheckReadOnly();
        $this->DoPut($Key, $Value);
    }

    /**
     * descHere
     *
     * @param $Map IMap
     *            <K, V>
     */
    public function PutAll($Map) {
        TType::Type($Map, array (
            'IMap' => array ('K' => $this->GenericArg('K'),
                'V' => $this->GenericArg('V'))));
        $this->CheckReadOnly();
        $this->DoPutAll($Map);
    }

    /**
     * descHere
     *
     * @return int
     */
    public function Size() {
        throw new ENotImplemented();
    }

    /**
     * descHere
     *
     * @return ICollection <V>
     */
    public function Values() {
        if ($this->Size() == 0) {
            return null;
        }
        return $this->DoValues();
    }

}

/**
 * TList
 * extends	TAbstractList<T>
 *
 * @author 许子健
 */
final class TList extends TAbstractList {
    /**
     *
     * @var SplFixedArray
     */
    private $FList = null;
    /**
     *
     * @var integer
     */
    private $FCapacity = 10;

    /**
     *
     * @param $Capacity integer
     * @param $FromArray T[]
     * @param $KeepOrder boolean
     * @see FrameworkDSW/TObject#Create()
     */
    public function __construct($Capacity = 10, $ElementsOwned = false, $FromArray = null, $KeepOrder = true) {
        parent::__construct($ElementsOwned);

        TType::Int($Capacity);
        TType::Bool($ElementsOwned);
        TType::Arr($FromArray);
        TType::Bool($KeepOrder);

        if (is_null($FromArray)) {
            $this->FList = new \SplFixedArray($Capacity);
            $this->FCapacity = $Capacity;
            return;
        }

        $this->FList = \SplFixedArray::fromArray($FromArray, $KeepOrder);
        $this->FSize = count($this->FList);
        $this->FCapacity = $this->FSize;
    }

    /**
     *
     * @param $Index integer
     * @return T
     * @see FrameworkDSW/TAbstractList#DoGet($Index)
     */
    protected function DoGet($Index) {
        return $this->FList[$Index];
    }

    /**
     *
     * @param $Index integer
     * @param $Element T
     * @see FrameworkDSW/TAbstractList#DoInsert($Index, $Element)
     */
    protected function DoInsert($Index, $Element) {
        $mNewSize = $this->FSize;
        if ($mNewSize >= $this->FCapacity) {
            $mCapacity = $mNewSize + 11;
            $this->FList->setSize($mCapacity);
            $this->FCapacity = $mCapacity;
        }

        while ($mNewSize > $Index) {
            $this->FList[$mNewSize] = $this->FList[$mNewSize - 1];
            --$mNewSize;
        }
        $this->FList[$Index] = $Element;
    }

    /**
     *
     * @param $Index integer
     * @param $Collection ICollection
     *            <T>
     * @return integer
     * @see FrameworkDSW/TAbstractList#DoInsertAll($Index, $Collection)
     */
    protected function DoInsertAll($Index, $Collection) {
        $mSpace = $Collection->Size();
        $mBackup = $Index;

        $mCapacity = $this->FSize + $mSpace + 10;
        if ($mCapacity >= $this->FCapacity) {
            $this->FList->setSize($mCapacity);
            $this->FCapacity = $mCapacity;
        }

        if ($Index == $this->FSize) {
            try {
                foreach ($Collection as $mElement) {
                    $this->FList[$Index++] = $mElement;
                }
            }
            catch (EException $e) {
                throw new EFailedToInsert('', 0, $e, $Index - $mBackup);
            }
            return $Index - $mBackup;
        }

        // else...
        $mNewIndex = $this->FSize;
        while ((--$mNewIndex) > $mBackup) {
            $this->FList[$mNewIndex + $mSpace] = $this->FList[$mNewIndex];
        }

        try {
            foreach ($Collection as $mElement) {
                $this->FList[$Index] = $mElement;
                ++$Index;
            }
        }
        catch (EException $e) {
            while ($Index - $mBackup < $mSpace) {
                $this->FList[$Index++] = null;
            }
            throw new EFailedToInsert('', 0, $e, $mSpace);
        }

        return $mSpace;
    }

    /**
     *
     * @param $Index integer
     * @see FrameworkDSW/TAbstractList#DoRemoveAt($Index)
     */
    protected function DoRemoveAt($Index) {
        if ($this->FElementsOwned) {
            Framework::Free($this->FList[$Index]);
        }

        $mTail = $this->FSize;
        --$mTail;
        while ($Index < $mTail) {
            $this->FList[$Index++] = $this->FList[$Index];
        }
        $this->FList->offsetUnset($Index);
    }

    /**
     *
     * @param $FromIndex integer
     * @param $ToIndex integer
     * @return integer
     * @see FrameworkDSW/TAbstractList#DoRemoveRange($FromIndex, $ToIndex)
     */
    protected function DoRemoveRange($FromIndex, $ToIndex) {
        $mDelta = $ToIndex - $FromIndex;
        if ($this->FElementsOwned) {
            $mIndex = $FromIndex - 1;
            while (++$mIndex < $ToIndex) {
                Framework::Free($this->FList[$mIndex]);
            }
        }
        while ($ToIndex < $this->FSize) {
            $this->FList[($ToIndex++) - $mDelta] = $this->FList[$ToIndex];
        }
        return ++$mDelta;
    }

    /**
     *
     * @param $Index integer
     * @param $Element T
     * @see FrameworkDSW/TAbstractList#DoSet($Index, $Element)
     */
    protected function DoSet($Index, $Element) {
        $this->FList[$Index] = $Element;
    }

    /**
     *
     * @param $FromIndex integer
     * @param $ToIndex integer
     * @return IList <T>
     * @see FrameworkDSW/TAbstractList#DoSubList($FromIndex, $ToIndex)
     */
    protected function DoSubList($FromIndex, $ToIndex) {
        if ($FromIndex >= $ToIndex) {
            $mOffset = $FromIndex - $ToIndex + 1;
            $mList = new TList($mOffset);
            while ($mOffset--) {
                $mList->Add($this[$FromIndex + $mOffset]);
            }
            return $mList;
        }

        $mOffset = $ToIndex - $FromIndex + 1;
        $mList = new TList($mOffset);
        while ($mOffset--) {
            $mList->Add($this[$ToIndex + $mOffset]);
        }
        return $mList;
    }

    /**
     */
    protected function DoClear() {
        if ($this->FElementsOwned) {
            foreach ($this->FList as $mElement) {
                Framework::Free($mElement);
            }
        }

        $this->FList = new \SplFixedArray($this->FCapacity);
    }

    /**
     *
     * @return T
     */
    protected function DoFirst() {
        return $this->FList[0];
    }

    /**
     *
     * @return T
     */
    protected function DoLast() {
        return $this->FList[$this->FSize - 1];
    }

    /**
     *
     * @param $List TList
     *            <T>
     */
    public function Swap($List) {
        TType::Object($List, array (
            'TList' => array ('T' => $this->GenericArg('T'))));

        $mTempList = $this->FList;
        $mTempCapacity = $this->FCapacity;
        $mTempOwned = $this->FElementsOwned;
        $mReadOnly = $this->FReadOnly;
        $mSize = $this->FSize;

        $this->FList = $List->FList;
        $this->FCapacity = $List->FCapacity;
        $this->FElementsOwned = $List->FElementsOwned;
        $this->FReadOnly = $List->FReadOnly;
        $this->FSize = $List->FSize;

        $List->FList = $mTempList;
        $List->FCapacity = $mTempCapacity;
        $List->FElementsOwned = $mTempOwned;
        $List->FReadOnly = $mReadOnly;
        $List->FSize = $mSize;
    }

    /**
     *
     * @param $ElementsOwned boolean
     * @param $Array T[]
     * @param $KeepOrder boolean
     * @return TList <T>
     */
    public static function FromArray($ElementsOwned, $Array, $KeepOrder = true) {
        TType::Bool($ElementsOwned);
        TType::Arr($Array);
        TType::Bool($KeepOrder);

        return new TList(10, $ElementsOwned, $Array, $KeepOrder);
    }

    /**
     *
     * @return T[]
     * @see FrameworkDSW/TAbstractCollection#ToArray()
     */
    public function ToArray() {
        $this->FList->setSize($this->FSize);
        $mArr = $this->FList->toArray();
        $this->FList->setSize($this->FCapacity);
        return $mArr;
    }

    /**
     *
     * @return integer
     */
    public function getCapacity() {
        return $this->FCapacity;
    }

    /**
     *
     * @param $Value integer
     */
    public function setCapacity($Value) {
        TType::Int($Value);

        if ($Value < $this->FSize) {
            throw new EInvalidCapacity();
        }
        if ($Value != $this->FCapacity) {
            $this->FList->setSize($Value);
            $this->FCapacity = $Value;
        }
    }
}

/**
 * TLinkedList
 * param	<T>
 * extends	TAbstractList<T>
 *
 * @author 许子健
 */
final class TLinkedList extends TAbstractList {
    /**
     *
     * @var integer
     */
    const CPrev = 0;
    /**
     *
     * @var integer
     */
    const CData = 1;
    /**
     *
     * @var integer
     */
    const CNext = 2;

    /**
     *
     * @var SplFixedArray
     */
    private $FList = null;
    /**
     *
     * @var integer
     */
    private $FHead = -1;
    /**
     *
     * @var integer
     */
    private $FTail = -1;
    /**
     *
     * @var integer
     */
    private $FCurrIndex = -1;
    /**
     *
     * @var integer
     */
    private $FCurrAddr = -1;
    /**
     *
     * @var integer
     */
    private $FAddrSize = 0;

    /**
     *
     * @param $Index integer
     * @return array
     */
    private function GetNodeAddr($Index) {
        // decide from tail or from head.
        $mCurrAddr = $this->FSize - 1; // from tail
        $mSteps = $this->FSize - 1 - $Index;
        $mIsForward = false;
        if ($Index < $mSteps) {
            $mSteps = $Index;
            $mCurrAddr = $this->FHead; // from head
            $mIsForward = true;
        }

        // decide if should move from the current index.
        $mIsForward2 = true;
        $mDelta = $Index - $this->FCurrIndex;
        if ($mDelta < 0) {
            $mDelta = -$mDelta;
            $mIsForward2 = false;
        }
        if ($mDelta < $mSteps) {
            $mSteps = $mDelta;
            $mCurrAddr = $this->FCurrAddr; // from current
            $mIsForward = $mIsForward2;
        }

        // iterating.
        ++$mSteps;
        if ($mIsForward) {
            while (--$mSteps) {
                $mCurrAddr = $this->FList[3 * $mCurrAddr + self::CNext];
            }
        }
        else {
            while (--$mSteps) {
                $mCurrAddr = $this->FList[3 * $mCurrAddr + self::CPrev];
            }
        }
        $this->FCurrAddr = $mCurrAddr;
        $this->FCurrIndex = $Index;
        return $mCurrAddr;
    }

    /**
     * descHere
     *
     * @param $ElementsOwned boolean
     * @param $FromArray T[]
     * @param $KeepOrder boolean
     */
    public function __construct($ElementsOwned = false, $FromArray = null, $KeepOrder = true) {
        TType::Bool($ElementsOwned);
        TType::Arr($FromArray);
        TType::Bool($KeepOrder);

        parent::__construct($ElementsOwned);

        if (is_null($FromArray) || count($FromArray) == 0) {
            $this->FSize = 0;
            $this->FList = new \SplFixedArray(30);
            $this->FAddrSize = 0;
            return;
        }
        $this->FList = \SplFixedArray::fromArray($FromArray, $KeepOrder);
        $this->FSize = count($this->FList);
        $this->FList->setSize(3 * $this->FSize);
        for ($mCurr = $this->FSize - 1; $mCurr >= 0; --$mCurr) {
            $this->FList[3 * $mCurr + self::CData] = $this->FList[$mCurr];
        }
        $this->FHead = 0;
        $this->FTail = $this->FSize - 1;
        $this->FCurrAddr = ($this->FSize - 1) >> 1;
        $this->FCurrIndex = $this->FCurrAddr;
        $this->FAddrSize = $this->FSize;

        for ($mIndex = 0; $mIndex < $this->FSize; ++$mIndex) {
            $this->FList[3 * $mIndex + self::CPrev] = $mIndex - 1;
            $this->FList[3 * $mIndex + self::CNext] = $mIndex + 1;
        }
        // $mTemp = $this->FList[$mIndex - 1];
        // $mTemp[self::CNext] = -1;
        // $this->FList[$mIndex - 1] = $mTemp;
        $this->FList[3 * ($mIndex - 1) + self::CNext] = -1;

        // This will not work since the [] operators in SPL are function calls
        // and returns a copy of the value, not the refernce.
    }

    /**
     * descHere
     *
     * @param $Index integer
     * @return T
     */
    protected function DoGet($Index) {
        $mNode = $this->GetNodeAddr($Index);
        return $this->FList[3 * $mNode + self::CData];
    }

    /**
     * descHere
     *
     * @param $Index integer
     * @param $Element T
     */
    protected function DoInsert($Index, $Element) {
        $mNewHigh = $this->FAddrSize++;
        if (count($this->FList) < 3 * $this->FAddrSize) {
            $this->FList->setSize(3 * ($this->FAddrSize + 10));
        }
        $this->FList[3 * $mNewHigh + self::CPrev] = -1;
        $this->FList[3 * $mNewHigh + self::CData] = $Element;
        $this->FList[3 * $mNewHigh + self::CNext] = -1;

        if ($this->FSize == 0) { // if empty before insertion.
            $this->FCurrAddr = $mNewHigh;
            $this->FCurrIndex = 0;
            $this->FHead = $mNewHigh;
            $this->FTail = $mNewHigh;
            return;
        } // else do the switch...
        if ($Index != $this->FSize) {
            $mNode = $this->GetNodeAddr($Index);
        }
        else {
            $mNode = $this->GetNodeAddr($Index - 1);
        }
        switch ($Index) {
            case 0 : // unshift
                $this->FList[3 * $mNode + self::CPrev] = $mNewHigh;
                $this->FList[3 * $mNewHigh + self::CNext] = $mNode;
                $this->FHead = $mNewHigh;
                $this->FCurrAddr = $mNewHigh;
                $this->FCurrIndex = 0;
            case $this->FSize : // push
                $this->FList[3 * $mNode + self::CNext] = $mNewHigh;
                $this->FList[3 * $mNewHigh + self::CPrev] = $mNode;
                $this->FTail = $mNewHigh;
                $this->FCurrAddr = $mNewHigh;
                $this->FCurrIndex = $Index;
                break;
            default : // insert
                $mPrevNode = $this->FList[3 * $mNode + self::CPrev];
                $this->FList[3 * $mNode + self::CPrev] = $mNewHigh;
                $this->FList[3 * $mNewHigh + self::CNext] = $mNode;
                $this->FList[3 * $mPrevNode + self::CNext] = $mNewHigh;
                $this->FList[3 * $mNewHigh + self::CPrev] = $mPrevNode;
                $this->FCurrAddr = $mNewHigh;
                $this->FCurrIndex = $Index;
                break;
        }
    }

    /**
     * descHere
     *
     * @param $Index integer
     * @param $Collection ICollection
     *            <T>
     * @return integer
     */
    protected function DoInsertAll($Index, $Collection) {
        if ($Collection->IsEmpty()) {
            return 0;
        }

        // TODO: 一个一个插入没有效率，应该先把$Collection拼接成链表，然后整体的一次插入
        --$Index;
        $mResult = 0;
        $mOldSize = $this->FSize;
        foreach ($Collection as $mElement) {
            $this->DoInsert(++$Index, $mElement);
            ++$mResult;
            ++$this->FSize;
        }
        $this->FSize = $mOldSize;
        return $mResult;
    }

    /**
     * descHere
     *
     * @param $Index integer
     */
    protected function DoRemoveAt($Index) {
        $mNode = $this->GetNodeAddr($Index);
        if ($this->FElementsOwned) {
            Framework::Free($this->FList[3 * $mNode + self::CData]);
        }

        switch ($Index) {
            case 0 : // shift
                $this->FHead = $this->FList[3 * $mNode + self::CNext];
                if ($this->FHead != -1) {
                    $this->FList[3 * $this->FHead + self::CPrev] = -1;
                }
                $this->FList[3 * $mNode + self::CPrev] = -1;
                $this->FList[3 * $mNode + self::CNext] = -1;
            case $this->FSize - 1 : // pop
                $this->FTail = $this->FList[3 * $mNode + self::CPrev];
                if ($this->FTail != -1) {
                    $this->FList[3 * $this->FTail + self::CNext] = -1;
                }
                $this->FList[3 * $mNode + self::CPrev] = -1;
                $this->FList[3 * $mNode + self::CNext] = -1;
                break;
            default : // remove
                $mPrevNode = $this->FList[3 * $mNode + self::CPrev];
                $mNextNode = $this->FList[3 * $mNode + self::CNext];
                $this->FList[3 * $mPrevNode + self::CNext] = $mNextNode;
                $this->FList[3 * $mNextNode + self::CPrev] = $mPrevNode;
                $this->FList[3 * $mNode + self::CPrev] = -1;
                $this->FList[3 * $mNode + self::CNext] = -1;
                $this->FCurrAddr = $mNextNode;
                $this->FCurrIndex = $Index;
                return;
        }

        if ($this->FSize == 1) {
            $this->FCurrAddr = -1;
            $this->FCurrIndex = -1;
            return;
        }
        $this->FCurrAddr = $this->FHead;
        $this->FCurrIndex = 0;
    }

    /**
     * descHere
     *
     * @param $FromIndex integer
     * @param $ToIndex integer
     * @return integer
     */
    protected function DoRemoveRange($FromIndex, $ToIndex) {
    }

    /**
     * descHere
     *
     * @param $Index integer
     * @param $Element T
     */
    protected function DoSet($Index, $Element) {
        $this->FList[3 * $this->GetNodeAddr($Index) + self::CData] = $Element;
    }

    /**
     * descHere
     *
     * @param $FromIndex integer
     * @param $ToIndex integer
     * @return IList <T>
     */
    protected function DoSubList($FromIndex, $ToIndex) {
    }

    /**
     */
    protected function DoClear() {
        if ($this->FElementsOwned) {
            for ($mIndex = 0; $mIndex < $this->FSize; ++$mIndex) {
                $this->FList[3 * $mIndex + self::CData]->Destroy();
                $this->FList[3 * $mIndex + self::CData] = null;
                // Framework::Free($this->FList[3 * $mIndex + self::CData]);
                // //TODO: FIX ME!
            }
        }

        $this->FList = new \SplFixedArray(30);
        $this->FHead = -1;
        $this->FTail = -1;
        $this->FCurrAddr = -1;
        $this->FCurrIndex = -1;
        $this->FAddrSize = 0;
    }

    /**
     * descHere
     *
     * @return T
     */
    protected function DoFirst() {
        return $this->FList[3 * $this->FHead + self::CData];
    }

    /**
     * descHere
     *
     * @return T
     */
    protected function DoLast() {
        return $this->FList[3 * $this->FTail + self::CData];
    }

    /**
     * descHere
     *
     * @param $ElementsOwned boolean
     * @param $Array T[]
     * @param $KeepOrder boolean
     * @return TLinkedList
     */
    public static function FromArray($ElementsOwned = false, $Array, $KeepOrder) {
        TType::Bool($ElementsOwned);
        TType::Arr($Array);
        TType::Bool($KeepOrder);

        self::PrepareGeneric(array ('T' => self::StaticGenericArg('T')));
        return new TLinkedList($ElementsOwned, $Array, $KeepOrder);
    }

    /**
     *
     * @param $LinkedList TLinkedList
     *            <T>
     */
    public function Swap($LinkedList) {
        TType::Object($LinkedList, array (
            'TLinkedList' => array ('T' => $this->GenericArg('T'))));
        $this->CheckReadOnly();
        $LinkedList->CheckReadOnly();

        $mTempList = $this->FList;
        $mTempHead = $this->FHead;
        $mTempTail = $this->FTail;
        $mTempTail = $this->FCurrAddr;
        $mTempTail = $this->FCurrIndex;
        $mTempReadOnly = $this->FReadOnly;
        $mTempSize = $this->FSize;
        $mTempOwned = $this->FElementsOwned;

        $this->FList = $LinkedList->FList;
        $this->FHead = $LinkedList->FHead;
        $this->FTail = $LinkedList->FTail;
        $this->FCurrAddr = $LinkedList->FCurrAddr;
        $this->FCurrIndex = $LinkedList->FCurrIndex;
        $this->FReadOnly = $LinkedList->FReadOnly;
        $this->FSize = $LinkedList->FSize;
        $this->FElementsOwned = $LinkedList->FElementsOwned;

        $LinkedList->FList = $mTempList;
        $LinkedList->FHead = $mTempHead;
        $LinkedList->FTail = $mTempTail;
        $LinkedList->FCurrAddr = $mTempTail;
        $LinkedList->FCurrIndex = $mTempTail;
        $LinkedList->FReadOnly = $mTempReadOnly;
        $LinkedList->FSize = $mTempSize;
        $LinkedList->FElementsOwned = $mTempOwned;
    }

    /**
     * descHere
     *
     * @return T[]
     */
    public function ToArray() {
        $mResult = array ();
        foreach ($this as $mElement) {
            $mResult[] = $mElement;
        }
        return $mResult;
    }
}

/**
 * TStack
 * params	<T>
 * extends	TAbstractStack<T>, IStack<T>
 *
 * @author 许子健
 */
final class TStack extends TAbstractStack implements IStack {
    /**
     *
     * @var SplStack
     */
    private $FStack = null;

    /**
     */
    protected function DoClear() {
        if ($this->FElementsOwned) {
            foreach ($this->FStack as $mElement) {
                Framework::Free($mElement);
            }
        }
        Framework::Free($this->FStack);
        $this->FStack = new TStack();
    }

    /**
     *
     * @return T
     */
    protected function DoPeek() {
        return $this->FStack->top();
    }

    /**
     *
     * @return T
     */
    protected function DoPop() {
        return $this->FStack->pop();
    }

    /**
     *
     * @param $Element T
     */
    protected function DoPush($Element) {
        $this->FStack->push($Element);
    }

    /**
     *
     * @param $ElementsOwned boolean
     */
    public function __construct($ElementsOwned = false) {
        TType::Bool($ElementsOwned);
        parent::__construct($ElementsOwned);
        $this->FStack = new \SplStack();
    }

    /**
     *
     * @return integer
     */
    public function Size() {
        return count($this->FStack);
    }

    /**
     *
     * @return boolean
     */
    public function IsEmpty() {
        return (count($this->FStack) == 0);
    }

    /**
     *
     * @return T[]
     */
    public function ToArray() {
        $mResult = array ();
        foreach ($this->FStack as $mElement) {
            $mResult[] = $mElement;
        }
        return $mResult;
    }
}

/**
 *
 * @author 许子健
 *
 */
final class TMapKeyType extends TEnum {
    /**
     *
     * @var integer
     */
    const eDirectKey = -1;
    /**
     *
     * @var integer
     */
    const eFloat = 0;
    /**
     *
     * @var integer
     */
    const eArray = 1;
    /**
     *
     * @var integer
     */
    const eObject = 2;
    /**
     *
     * @var integer
     */
    const eBoolean = 3;
    /**
     *
     * @var integer
     */
    const eRecord = 4;
}

/**
 * TMap
 * params	<K, V>
 * extends	TAbstractMap<K, V>
 *
 * @author 许子健
 */
class TMap extends TAbstractMap {
    /**
     *
     * @var array
     */
    private $FMap = array ();
    /**
     *
     * @var boolean
     */
    private $FDirectKey = false;
    /**
     * 0: float, 1: array, 2: an object, -1: not related
     *
     * @var TMapKeyType
     */
    private $FKeyType = null;

    /**
     *
     * @param $Key K
     * @return string
     */
    private function HashKey($Key) {
        switch ($this->FKeyType) {
            case TMapKeyType::eObject() :
                return spl_object_hash($Key);
                break;
            case TMapKeyType::eFloat() :
                return (string) $Key;
                break;
            case TMapKeyType::eArray() :
            case TMapKeyType::eRecord() :
                return sha1(serialize((array) $Key), true);
                break;
            case TMapKeyType::eBoolean() :
                return $Key ? '1' : '0';
                break;
        }
    }

    /**
     * descHere
     *
     * @param $ElementsOwned boolean
     */
    public function __construct($ElementsOwned = false) {
        TType::Bool($ElementsOwned);
        $this->PrepareMethodGeneric(array (
                'T' => array (
                        'TPair' => array ('K' => $this->GenericArg('K'),
                                'V' => $this->GenericArg('V')))));
        parent::__construct($ElementsOwned);

        $mKClassType = $this->GenericArg('K');
        if (is_array($mKClassType)) {
            $mKClassType = array_keys($mKClassType);
            $mKClassType = $mKClassType[0];
        }
        if (class_exists($mKClassType) && is_subclass_of($mKClassType, 'TRecord')) {
            $this->FKeyType = TMapKeyType::eRecord();
            $this->FDirectKey = false;
            return;
        }
        $this->FDirectKey = ($mKClassType == 'integer' || $mKClassType == 'string');
        if ($this->FDirectKey) {
            $this->FKeyType = TMapKeyType::eDirectKey();
            return;
        }
        switch ($mKClassType) {
            case 'float' :
                $this->FKeyType = TMapKeyType::eFloat();
                break;
            case 'array' :
                $this->FKeyType = TMapKeyType::eArray();
                break;
            case 'boolean' :
                $this->FKeyType = TMapKeyType::eBoolean();
                break;
            default :
                $this->FKeyType = TMapKeyType::eObject();
                break;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see TAbstractCollection::Clear()
     */
    public function Clear() {
        $this->CheckReadOnly();

        if ($this->FElementsOwned) {
            if ($this->FKeyType == TMapKeyType::eObject() && TType::IsTypePrimitive($this->GenericArg('V'))) {
                foreach ($this->FMap as &$mValue) {
                    Framework::Free($mValue->Key);
                    Framework::Free($mValue->Value);
                }
            }
            elseif ($this->FKeyType != TMapKeyType::eObject() && TType::IsTypePrimitive($this->GenericArg('V'))) {
                foreach ($this->FMap as &$mValue) {
                    Framework::Free($mValue);
                }
            }
        }
        $this->FMap = array ();
    }

    /**
     * descHere
     *
     * @param $Key K
     * @return boolean
     */
    protected function DoContainsKey($Key) {
        switch ($this->FKeyType) {
            case TMapKeyType::eDirectKey() :
                return array_key_exists($Key, $this->FMap);
            default :
                return array_key_exists($this->HashKey($Key), $this->FMap);
        }
    }

    /**
     * descHere
     *
     * @param $Value V
     * @return boolean
     */
    protected function DoContainsValue($Value) {
        if (($this->FKeyType == TMapKeyType::eObject()) || ($this->FKeyType == TMapKeyType::eRecord())) {
            if (TType::IsTypePrimitive($this->GenericArg('V'))) {
                foreach ($this->FMap as $mPair) {
                    if ($mPair->Value === $Value) {
                        return true;
                    }
                }
            }
            else {
                foreach ($this->FMap as $mPair) {
                    if ($mPair->Value->Equals($Value)) {
                        return true;
                    }
                }
            }
            return false;
        }
        return in_array($Value, $this->FMap, true);
    }

    /**
     * descHere
     *
     * @param $Key K
     */
    protected function DoDelete($Key) {
        if (!TType::IsTypePrimitive($this->GenericArg('V'))) {
            Framework::Free($this->DoGet($Key));
        }
        if ($this->FDirectKey) {
            unset($this->FMap[$Key]);
        }
        else {
            unset($this->FMap[$this->HashKey($Key)]);
        }
    }

    /**
     * descHere
     *
     * @param $Key K
     * @return V
     */
    protected function DoGet($Key) {
        if ($this->FDirectKey) {
            return $this->FMap[$Key];
        }
        $mPair = $this->FMap[$this->HashKey($Key)];
        return $mPair->Value;
    }

    /**
     * descHere
     *
     * @return ISet <K>
     */
    protected function DoKeySet() {
    }

    /**
     * descHere
     *
     * @return ISet <TPair<K, V>>
     */
    protected function DoPairSet() {
    }

    /**
     * descHere
     *
     * @param $Key K
     * @param $Value V
     */
    protected function DoPut($Key, $Value) {
        if ($this->FDirectKey) {
            $this->FMap[$Key] = $Value;
        }
        elseif (($this->FKeyType == TMapKeyType::eObject()) || ($this->FKeyType == TMapKeyType::eRecord()) || ($this->FKeyType == TMapKeyType::eArray())) {
            $mPair = new TPair();
            $mPair->Key = $Key;
            $mPair->Value = $Value;
            $this->FMap[$this->HashKey($Key)] = $mPair;
        }
        else {
            $this->FMap[$this->HashKey($Key)] = $Value;
        }
    }

    /**
     * descHere
     *
     * @param $Map IMap
     *            <K, V>
     */
    protected function DoPutAll($Map) {
        if ($this->FDirectKey) {
            foreach ($Map as $mKey => $mValue) {
                $this->FMap[$mKey] = $mValue;
            }
        }
        elseif (($this->FKeyType == TMapKeyType::eObject()) || ($this->FKeyType == TMapKeyType::eRecord())) {
            $mPair = new TPair();
            foreach ($Map as $mKey => $mValue) {
                $mPair->Key = $mKey;
                $mPair->Value = $mValue;
                $this->FMap[$this->HashKey($mKey)] = $mPair->Duplicate();
            }
        }
        else {
            foreach ($Map as $mKey => $mValue) {
                $this->FMap[$this->HashKey($mKey)] = $mValue;
            }
        }
    }

    /**
     * descHere
     *
     * @return ICollection <V>
     */
    protected function DoValues() {
        if (empty($this->FMap)) {
            return null;
        }
        TList::PrepareGeneric(array ('T' => $this->GenericArg('V')));
        if (($this->FKeyType == TMapKeyType::eObject()) || ($this->FKeyType == TMapKeyType::eRecord())) {
            $mResult = new TList(count($this->FMap), $this->FElementsOwned);
            foreach ($this->FMap as $mValue) {
                $mResult->Add($mValue);
            }
            return $mResult;
        } // else ...
        return TList::FromArray($this->FElementsOwned, array_values($this->FMap));
    }

    /**
     * descHere
     *
     * @return boolean
     */
    public function getElementsOwned() {
        return $this->FElementsOwned;
    }

    /**
     * descHere
     *
     * @return int
     */
    public function Size() {
        return count($this->FMap);
    }

    /**
     * descHere
     *
     * @return IIterator <TPair<K, V>>
     */
    public function Iterator() {
        TStdMapMapIterator::PrepareGeneric(array (
            'K' => $this->GenericArg('K'), 'V' => $this->GenericArg('V'),
            'T' => array (
                'TPair' => array ('K' => $this->GenericArg('K'),
                    'V' => $this->GenericArg('V')))));
        return new TStdMapMapIterator($this->FMap, $this->FKeyType);
    }
}
