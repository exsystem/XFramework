<?php
/**
 * \FrameworkDSW\Containers
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 1
 */
namespace FrameworkDSW\Containers;

use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\ENotImplemented;
use FrameworkDSW\System\ERuntimeException;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TEnum;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TRecord;
use FrameworkDSW\Utilities\TType;

/**
 *
 * @author 许子健
 */
class EIndexOutOfBounds extends ERuntimeException {
}

/**
 *
 * @author 许子健
 */
class ENoKeyDefined extends ERuntimeException {
}

/**
 *
 * @author 许子健
 */
class EIllegalState extends ERuntimeException {
}

/**
 *
 * @author 许子健
 */
class EConcurrentModification extends ERuntimeException {
}

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
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param integer $EffectedCount
     */
    public function __construct($Message, $Previous = null, $EffectedCount) {
        $this->FEffected = (integer)$EffectedCount;
        parent::__construct($Message, $Previous);
    }

    /**
     *
     * @return integer
     */
    public function getEffectedCount() {
        return $this->FEffected;
    }
}

/**
 *
 * @author 许子健
 */
class EFailedToInsert extends EContainerDataOprErr {
}

/**
 *
 * @author 许子健
 */
class EFailedToRemove extends EContainerDataOprErr {
}

/**
 *
 * @author 许子健
 */
class EContainerException extends EException {
}

/**
 *
 * @author 许子健
 */
class ECollectionIsReadOnly extends EContainerException {
}

/**
 *
 * @author 许子健
 */
class EInvalidCapacity extends EContainerException {
}

/**
 *
 * @author 许子健
 */
class ENoSuchElement extends EContainerException {
}

/**
 *
 * @author 许子健
 */
class ENoSuchKey extends EContainerException {
}

/**
 * \FrameworkDSW\Containers\IIterator
 * param <T: ?>
 */
interface IIterator extends IInterface, \Iterator {

    /**
     * Remove
     */
    public function Remove();
}

/**
 * \FrameworkDSW\Containers\IIteratorAggregate
 * param <T: ?>
 */
interface IIteratorAggregate extends IInterface, \IteratorAggregate {

    /**
     *
     * @return \FrameworkDSW\Containers\IIterator <T: T>
     */
    public function Iterator();
}

/**
 * FrameworkDSW\Containers\IArrayAccess
 * param <K: ?, V: ?>
 */
interface IArrayAccess extends IInterface, \ArrayAccess {
}

// IListIterator : {[node_0, node_1, node_2, ... ,node_n ,] tail_node}

/**
 * \FrameworkDSW\Containers\IListIterator
 * param <T: ?>
 * extends \FrameworkDSW\Containers\IIterator<T: T>
 */
interface IListIterator extends IIterator {

    /**
     *
     * @param T $Element
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
     * @param T $Element
     */
    public function Set($Element);
}

/**
 * \FrameworkDSW\Containers\ICollection
 * param <T: ?>
 * extends \FrameworkDSW\Containers\IIteratorAggregate<T: T>
 */
interface ICollection extends IIteratorAggregate {

    /**
     *
     * @param T $Element
     */
    public function Add($Element);

    /**
     *
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
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
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
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
     * @param \FrameworkDSW\Containers\T $Element
     */
    public function Remove($Element);

    /**
     *
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     */
    public function RemoveAll($Collection);

    /**
     *
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
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
 * \FrameworkDSW\Containers\IList
 *  * param    <T: ?>
 * extends    \FrameworkDSW\Containers\ICollection<T: T>
 */
interface IList extends ICollection {

    /**
     *
     * @param integer $Index
     * @param T $Element
     */
    public function Insert($Index, $Element);

    /**
     *
     * @param integer $Index
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     */
    public function InsertAll($Index, $Collection);

    /**
     *
     * @param integer $Index
     * @return T
     */
    public function Get($Index);

    /**
     *
     * @param T $Element
     * @return integer
     */
    public function IndexOf($Element);

    /**
     *
     * @param T $Element
     * @return integer
     */
    public function LastIndexOf($Element);

    /**
     *
     * @param integer $Index
     * @return \FrameworkDSW\Containers\IListIterator <T: T>
     */
    public function ListIterator($Index = 0);

    /**
     *
     * @param integer $Index
     */
    public function RemoveAt($Index);

    /**
     *
     * @param integer $Index
     * @param T $Element
     * @return T
     */
    public function Set($Index, $Element);

    /**
     *
     * @param integer $FromIndex
     * @param integer $ToIndex
     * @return \FrameworkDSW\Containers\IList <T: T>
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
 * \FrameworkDSW\Containers\ISet
 * param <T: ?>
 * extends \FrameworkDSW\Containers\ICollection<T: T>
 */
interface ISet extends ICollection {
}

/**
 * \FrameworkDSW\Containers\IStack
 * param <T: ?>
 * extends \FrameworkDSW\Containers\ICollection<T: T>
 *
 * @author 许子健
 */
interface IStack extends ICollection {

    /**
     *
     * @param T $Element
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
 * \FrameworkDSW\Containers\IQueue
 * param <T: ?>
 * extends \FrameworkDSW\Containers\ICollection<T: T>
 */
interface IQueue extends ICollection {

    /**
     *
     * @param T $Element
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
 * \FrameworkDSW\Containers\TStdMapMapIterator
 * params <K: K, V: V>
 * extends \FrameworkDSW\Containers\IIterator<T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
 *
 * @author 许子健
 */
class TStdMapMapIterator extends TObject implements IIterator {

    /**
     *
     * @var \FrameworkDSW\Containers\TMapKeyType
     */
    private $FKeyType = null;
    /**
     *
     * @var array
     */
    private $FMapData = [];

    /**
     * descHere
     *
     * @param array $MapData
     * @param \FrameworkDSW\Containers\TMapKeyType $KeyType
     */
    public function __construct($MapData, $KeyType) {
        parent::__construct();
        TType::Arr($MapData);
        TType::Object($KeyType, TMapKeyType::class);

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
        throw new ENotImplemented('Not implemented: \FrameworkDSW\Containers\TStdMapMapIterator::Remove().');
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
 * \FrameworkDSW\Containers\IMap
 * params <K: ?, V: ?>
 * extends \FrameworkDSW\Containers\IArrayAccess<K: K, V: V>, \FrameworkDSW\Containers\ICollection<T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
 *
 * @author 许子健
 */
interface IMap extends IArrayAccess, ICollection {

    /**
     * descHere
     *
     * @param K $Key
     * @return boolean
     */
    public function ContainsKey($Key);

    /**
     * descHere
     *
     * @param V $Value
     * @return boolean
     */
    public function ContainsValue($Value);

    /**
     * descHere
     *
     * @param K $Key
     */
    public function Delete($Key);

    /**
     * descHere
     *
     * @param K $Key
     * @return V
     */
    public function Get($Key);

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ISet <T: K>
     */
    public function KeySet();

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ISet <T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
     */
    public function PairSet();

    /**
     * descHere
     *
     * @param K $Key
     * @param V $Value
     */
    public function Put($Key, $Value);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IMap $Map <K: K, V: V>
     */
    public function PutAll($Map);

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ICollection <T: V>
     */
    public function Values();

}

/**
 * \FrameworkDSW\Containers\TPair
 * param <K: ?, V: ?>
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
 * param <T: ?>
 * extends \FrameworkDSW\Containers\IIterator<T: T>
 *
 * @author 许子健
 */
class TStdListIterator extends TObject implements IIterator {
    /**
     *
     * @var \FrameworkDSW\Containers\TAbstractList <T: T>
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
     * @param \FrameworkDSW\Containers\TAbstractList $List <T: T>
     * @see FrameworkDSW/TObject#Create()
     */
    public function __construct($List) {
        parent::__construct();
        TType::Object($List, [
            TAbstractList::class => ['T' => $this->GenericArg('T')]]);

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
     * @throws ENoKeyDefined
     * @return integer string
     */
    public function key() {
        throw new ENoKeyDefined('No key defined: \FrameworkDSW\Containers\TStdListIterator::key().');
    }

    /**
     *
     * @throws ENoSuchElement
     * @return T
     */
    public function current() {
        try {
            return $this->FList->Get($this->FCursor);
        }
        catch (EIndexOutOfBounds $e) {
            throw new ENoSuchElement(sprintf('No such element: visiting current element of \FrameworkDSW\Containers\TStdListIterator at index %s.', (string)$this->FCursor), $e);
        }
    }

    /**
     *
     * @see FrameworkDSW/IIterator#Remove()
     */
    public function Remove() {
        if ($this->FLastAt < -1) {
            throw new EIllegalState(sprintf('Illegal state: the iterating element for removing at index %s might have been removed.', (string)$this->FCursor));
        }

        try {
            $this->FList->RemoveAt($this->FCursor);
            if ($this->FLastAt < $this->FCursor) {
                --$this->FCursor;
            }
            $this->FLastAt = -1;
        }
        catch (EIndexOutOfBounds $e) {
            throw new EConcurrentModification(sprintf('Concurrent modification: iterating and removing at index %s.', (string)$this->FCursor), $e);
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
 * \FrameworkDSW\Containers\TStdListListIterator
 * param <T: ?>
 * extends \FrameworkDSW\Containers\TStdListIterator<T: T>, \FrameworkDSW\Containers\IListIterator<T: T>
 *
 * @author 许子健
 */
class TStdListListIterator extends TStdListIterator implements IListIterator {

    /**
     *
     * @param \FrameworkDSW\Containers\TAbstractList $List <T: T>
     * @param integer $StartAt
     * @see FrameworkDSW/TStdListIterator#Create($List)
     */
    public function __construct($List, $StartAt) {
        TType::Object($List, [
            TAbstractList::class => ['T' => self::StaticGenericArg('T')]]);
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
     * @param T $Element
     * @throws EConcurrentModification
     * @see FrameworkDSW/IListIterator#Add($Element)
     */
    public function Add($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        try {
            $this->FList->Insert($this->FCursor, $Element);
            $this->FLastAt = -1;
        }
        catch (EIndexOutOfBounds $e) {
            throw new EConcurrentModification(sprintf('Concurrent modification: iterating and adding before index %s.', (string)$this->FCursor), $e);
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
     * @param T $Element
     * @throws EIllegalState
     * @throws EConcurrentModification
     * @see FrameworkDSW/IListIterator#Set($Element)
     */
    public function Set($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        if ($this->FLastAt < 0) {
            throw new EIllegalState(sprintf('Illegal state: the iterating element for setting at index %s might have been removed.', (string)$this->FCursor));
        }

        try {
            $this->FList->Set($this->FCursor, $Element);
        }
        catch (EIndexOutOfBounds $e) {
            throw new EConcurrentModification(sprintf('Concurrent modification: iterating and setting at index %s.', (string)$this->FCursor), $e);
        }
    }
}

/**
 * \FrameworkDSW\Containers\TAbstractCollection
 * param <T: ?>
 * extends \FrameworkDSW\Containers\ICollection<T: T>
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
            throw new ECollectionIsReadOnly('Read only collection.');
        }
    }

    /**
     *
     * @param boolean $ElementsOwned
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
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     */
    public function AddAll($Collection) {
        TType::Object($Collection, [
            ICollection::class => ['T' => $this->GenericArg('T')]]);
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
     * @param T $Element
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
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     * @return boolean
     */
    public function ContainsAll($Collection) {
        TType::Object($Collection, [
            ICollection::class => ['T' => $this->GenericArg('T')]]);

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
     * @param T $Element
     * @throws ENoSuchElement
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
            /**@var IInterface $Element */
            while ($mItr->Valid()) {
                if ($Element->Equals($mItr->current())) {
                    $mItr->Remove();

                    return;
                }
                $mItr->next();
            }
        }

        throw new ENoSuchElement('No such element: removing a non-existent element.');
    }

    /**
     *
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     */
    public function RemoveAll($Collection) {
        TType::Object($Collection, [
            ICollection::class => ['T' => $this->GenericArg('T')]]);

        $this->CheckReadOnly();
        $mItr = $this->Iterator();
        while ($mItr->valid()) {
            if ($Collection->Contains($mItr->current())) {
                $mItr->Remove();
            }
            $mItr->next();
        }
    }

    /**
     *
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     * @return boolean
     */
    public function RetainAll($Collection) {
        TType::Object($Collection, [
            ICollection::class => ['T' => $this->GenericArg('T')]]);

        $mModified = false;
        $mItr      = $this->Iterator();
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
        $mResult = [];
        foreach ($this as $mValue) {
            $mResult[] = $mValue;
        }

        return $mResult;
    }

    /**
     *
     * @return \FrameworkDSW\Containers\IIterator <T: T>
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
     * @param boolean $Value
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
 * \FrameworkDSW\Containers\TAbstractList
 * extends \FrameworkDSW\Containers\TAbstractCollection<T: T>, \FrameworkDSW\Containers\IList<T: T>, \FrameworkDSW\Containers\IArrayAccess<K: integer, V: T>
 * param <T: ?>
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
     * @param integer $Index
     * @throws EIndexOutOfBounds
     */
    private function CheckIndexForAdd($Index) {
        if ($Index < 0 || $Index > $this->FSize) {
            throw new EIndexOutOfBounds(sprintf('No such index: illegal index %s is not between %s and %s.'), (string)$Index, 0, (string)$this->FSize);
        }
    }

    /**
     *
     * @param integer $Index
     * @throws EIndexOutOfBounds
     */
    private function CheckIndexForGet($Index) {
        if ($Index < 0 || $Index >= $this->FSize) {
            throw new EIndexOutOfBounds(sprintf('No such index: illegal index %s is not between %s and %s.'), (string)$Index, 0, (string)($this->FSize - 1));
        }
    }

    /**
     *
     * @param integer $Index
     * @param T $Element
     */
    protected abstract function DoInsert($Index, $Element);

    /**
     *
     * @param integer $Index
     * @return T
     */
    protected abstract function DoGet($Index);

    /**
     *
     * @param integer $Index
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     * @throws EFailedToInsert
     * @return integer
     */
    protected function DoInsertAll($Index, $Collection) {
        $mItr    = $Collection->Iterator();
        $mBackup = $Index;

        while ($mItr->valid()) {
            try {
                $this->DoInsert($Index++, $mItr->current());
            }
            catch (EException $e) {
                $mEffected = $Index - $mBackup - 1;
                throw new EFailedToInsert(sprintf('Collection insertion failed: %s elements not inserted from element at index %s.', $mEffected, $Index), $e, $mEffected);
            }
            $mItr->next();
        }

        return $Index - $mBackup;
    }

    /**
     *
     * @param integer $Index
     */
    protected abstract function DoRemoveAt($Index);

    /**
     *
     * @param integer $FromIndex
     * @param integer $ToIndex
     * @throws EFailedToRemove
     * @return integer
     */
    protected function DoRemoveRange($FromIndex, $ToIndex) {
        $mItr     = $this->ListIterator($FromIndex);
        $mRemoved = 0;
        for ($mN = $ToIndex - $FromIndex; $mRemoved < $mN; ++$mRemoved) {
            try {
                $mItr->Remove();
            }
            catch (EException $e) {
                --$mRemoved;
                throw new EFailedToRemove(sprintf('Range move failed: only %s elements removed from element at index %s.', (string)$mRemoved, (string)$FromIndex), $e, $mRemoved);
            }
            $mItr->next();
        }

        return $mRemoved;
    }

    /**
     *
     * @param integer $Index
     * @param T $Element
     */
    protected abstract function DoSet($Index, $Element);

    /**
     *
     * @param integer $FromIndex
     * @param integer $ToIndex
     * @return \FrameworkDSW\Containers\IList <T: T>
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
     * @param T $Element
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
     * @param integer $Index
     * @param T $Element
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
     * @param integer $Index
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     * @throws \Exception|EFailedToInsert
     */
    public final function InsertAll($Index, $Collection) {
        TType::Int($Index);
        TType::Object($Collection, [
            ICollection::class => ['T' => $this->GenericArg('T')]]);

        $this->CheckReadOnly();
        $this->CheckIndexForAdd($Index);
        try {
            $this->FSize += $this->DoInsertAll($Index, $Collection);
        }
        catch (EFailedToInsert $e) {
            $this->FSize += $e->getEffectedCount();
            throw $e;
        }
    }

    /**
     *
     * @param integer $Index
     * @return T
     */
    public final function Get($Index) {
        TType::Int($Index);

        $this->CheckIndexForGet($Index);

        return $this->DoGet($Index);
    }

    /**
     *
     * @param T $Element
     * @throws ENoSuchElement
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
                /**@var IInterface $Element */
                if ($Element->Equals($mItr->current())) {
                    return $mItr->key();
                }
                $mItr->next();
            }
        }

        throw new ENoSuchElement('No such element.');
    }

    /**
     *
     * @return \FrameworkDSW\Containers\IIterator <T: T>
     * @see FrameworkDSW/TAbstractCollection#Iterator()
     */
    public function Iterator() {
        TStdListIterator::PrepareGeneric(['T' => $this->GenericArg('T')]);

        return new TStdListIterator($this);
    }

    /**
     *
     * @param T $Element
     * @throws ENoSuchElement
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
                /**@var IInterface $Element */
                if ($Element->Equals($mItr->current())) {
                    return $mItr->key();
                }
            }
        }

        throw new ENoSuchElement('No such element.');
    }

    /**
     *
     * @param integer $Index
     * @return \FrameworkDSW\Containers\IListIterator <T: T>
     */
    public function ListIterator($Index = 0) {
        TType::Int($Index);

        $this->CheckIndexForAdd($Index);
        TStdListListIterator::PrepareGeneric([
            'T' => $this->GenericArg('T')]);

        return new TStdListListIterator($this, $Index);
    }

    /**
     *
     * @param integer $Index
     */
    public final function RemoveAt($Index) {
        $this->CheckReadOnly();

        $this->CheckIndexForGet($Index);
        $this->DoRemoveAt($Index);
        --$this->FSize;
    }

    /**
     *
     * @param integer $FromIndex
     * @param integer $ToIndex
     * @throws \Exception|EFailedToRemove
     */
    public final function RemoveRange($FromIndex, $ToIndex) {
        TType::Int($FromIndex);
        TType::Int($ToIndex);

        $this->CheckReadOnly();

        $this->CheckIndexForGet($FromIndex);
        $this->CheckIndexForGet($ToIndex);
        if ($FromIndex > $ToIndex) {
            $mTemp     = $FromIndex;
            $FromIndex = $ToIndex;
            $ToIndex   = $mTemp;
        }

        try {
            $this->FSize -= $this->DoRemoveRange($FromIndex, $ToIndex);
        }
        catch (EFailedToRemove $e) {
            $this->FSize -= $e->getEffectedCount();
            throw $e;
        }
    }

    /**
     *
     * @param integer $Index
     * @param T $Element
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
     * @param integer $FromIndex
     * @param integer $ToIndex
     * @return \FrameworkDSW\Containers\IList <T: T>
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
     * @throws EIndexOutOfBounds
     * @return T
     */
    public final function First() {
        if ($this->FSize == 0) {
            throw new EIndexOutOfBounds('No such index: empty list.');
        }

        return $this->DoFirst();
    }

    /**
     *
     * @throws EIndexOutOfBounds
     * @return T
     */
    public final function Last() {
        if ($this->FSize == 0) {
            throw new EIndexOutOfBounds('No such index: empty list.');
        }

        return $this->DoLast();
    }

    /**
     *
     * @param integer $offset
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
     * @param integer $offset
     * @return T
     */
    public final function offsetGet($offset) {
        return $this->Get($offset);
    }

    /**
     *
     * @param integer $offset
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
     * @param integer $offset
     */
    public final function offsetUnset($offset) {
        $this->RemoveAt($offset);
    }
}

/**
 * \FrameworkDSW\Containers\TAbstractStack
 * param <T: ?>
 * extends \FrameworkDSW\Containers\TAbstractCollection<T: T>
 *
 * @author 许子健
 */
abstract class TAbstractStack extends TAbstractCollection {

    /**
     *
     * @param T $Element
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
     * @param T $Element
     */
    public final function Push($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        $this->CheckReadOnly();

        $this->DoPush($Element);
    }

    /**
     *
     * @param T $Element
     */
    public final function Add($Element) {
        $this->Push($Element);
    }

    /**
     *
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     */
    public final function AddAll($Collection) {
        TType::Object($Collection, [
            ICollection::class => ['T' => $this->GenericArg('T')]]);
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
     * @param T $Element
     * @throws EFailedToRemove
     */
    public final function Remove($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        throw new EFailedToRemove('Illegal operation: use pop instead of remove for stack.', null, -1);
    }

    /**
     *
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     * @throws EFailedToRemove
     */
    public final function RemoveAll($Collection) {
        TType::Object($Collection, [ICollection::class => ['T' => $this->GenericArg('T')]]);
        throw new EFailedToRemove('Illegal operation: only pop allowed for removing elements in stacks.', null, -1);
    }

    /**
     *
     * @throws ENoSuchElement
     * @return T
     */
    public final function Peek() {
        if ($this->IsEmpty()) {
            throw new ENoSuchElement('No such element: empty stack.');
        }

        return $this->DoPeek();
    }

    /**
     *
     * @throws ENoSuchElement
     * @return T
     */
    public final function Pop() {
        $this->CheckReadOnly();
        if ($this->IsEmpty()) {
            throw new ENoSuchElement('No such element: empty stack.');
        }

        return $this->DoPop();
    }

    /**
     *
     * @return \FrameworkDSW\Containers\IIterator <T: T>
     */
    public function Iterator() {
        // TODO: iterator
    }
}

/**
 * TAbstractMap
 * params <K: ?, V: ?, T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
 * extends \FrameworkDSW\Containers\IMap<K: K, V: V>, \FrameworkDSW\Containers\TAbstractCollection<T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
 *
 * @author 许子健
 */
abstract class TAbstractMap extends TAbstractCollection implements IMap {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\TPair $Element <K: K, V: V>
     */
    public function Add($Element) {
        TType::Type($Element, [
            TPair::class => ['K' => $this->GenericArg('K'),
                             'V' => $this->GenericArg('V')]]);
        $this->CheckReadOnly();
        $this->DoPut($Element->Key, $Element->Value);
    }

    /**
     * descHere
     *
     * @param K $Key
     * @return boolean
     */
    public function ContainsKey($Key) {
        TType::Type($Key, $this->GenericArg('K'));

        return $this->DoContainsKey($Key);
    }

    /**
     * descHere
     *
     * @param V $Value
     * @return boolean
     */
    public function ContainsValue($Value) {
        TType::Type($Value, $this->GenericArg('V'));

        return $this->DoContainsValue($Value);
    }

    /**
     * descHere
     *
     * @param K $Key
     * @throws ENoSuchKey
     */
    public function Delete($Key) {
        TType::Type($Key, $this->GenericArg('K'));
        $this->CheckReadOnly();
        if ($this->DoContainsKey($Key)) {
            $this->DoDelete($Key);
        }
        else {
            throw new ENoSuchKey('No such key: deletion failed.');
        }
    }

    /**
     * descHere
     *
     * @param K $Key
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
     * @param V $Value
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
     * @param K $Key
     */
    protected abstract function DoDelete($Key);

    /**
     * descHere
     *
     * @param K $Key
     * @return V
     */
    protected abstract function DoGet($Key);

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ISet <T: K>
     */
    protected function DoKeySet() {
        // TODO: DoKeySet.
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ISet <T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
     */
    protected function DoPairSet() {
        // TODO: DoPairSet.
    }

    /**
     * descHere
     *
     * @param K $Key
     * @param V $Value
     */
    protected abstract function DoPut($Key, $Value);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IMap $Map <K: K, V: V>
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
     * @return \FrameworkDSW\Containers\ICollection <T: V>
     */
    protected function DoValues() {
        $mItr    = $this->Iterator();
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
     * @param K $Key
     * @throws ENoSuchKey
     * @return V
     */
    public function Get($Key) {
        TType::Type($Key, $this->GenericArg('K'));
        if (!$this->DoContainsKey($Key)) {
            throw new ENoSuchKey('No such key: element not found.');
        }

        return $this->DoGet($Key);
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IIterator <T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
     */
    public function Iterator() {
        // TODO: Iterator.
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ISet <T: K>
     */
    public function KeySet() {
        // TODO: KeySet.
    }

    /**
     * descHere
     *
     * @param K $offset
     * @return boolean
     */
    public final function offsetExists($offset) {
        TType::Type($offset, $this->GenericArg('K'));

        return $this->DoContainsKey($offset);
    }

    /**
     * descHere
     *
     * @param K $offset
     * @throws ENoSuchKey
     * @return V
     */
    public final function offsetGet($offset) {
        TType::Type($offset, $this->GenericArg('K'));
        if (!$this->DoContainsKey($offset)) {
            throw new ENoSuchKey('No such key: element not found.');
        }

        return $this->DoGet($offset);
    }

    /**
     * descHere
     *
     * @param K $offset
     * @param V $value
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
     * @throws ENoSuchKey
     */
    public final function offsetUnset($offset) {
        TType::Type($offset, $this->GenericArg('K'));
        $this->CheckReadOnly();
        if (!$this->DoContainsKey($offset)) {
            throw new ENoSuchKey('No such key: deletion failed.');
        }
        $this->DoDelete($offset);
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ISet <T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
     */
    public function PairSet() {
        return $this->DoPairSet();
    }

    /**
     * descHere
     *
     * @param K $Key
     * @param V $Value
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
     * @param \FrameworkDSW\Containers\IMap $Map <K: K, V: V>
     */
    public function PutAll($Map) {
        TType::Object($Map, [
            IMap::class => ['K' => $this->GenericArg('K'),
                            'V' => $this->GenericArg('V')]]);
        $this->CheckReadOnly();
        $this->DoPutAll($Map);
    }

    /**
     * descHere
     *
     * @throws \FrameworkDSW\System\ENotImplemented
     * @return integer
     */
    public function Size() {
        throw new ENotImplemented();
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ICollection <T: V>
     */
    public function Values() {
        if ($this->Size() == 0) {
            return null;
        }

        return $this->DoValues();
    }

}

/**
 * \FrameworkDSW\Containers\TList
 * params <T: ?>
 * extends \FrameworkDSW\Containers\TAbstractList<T: T>
 *
 * @author 许子健
 */
final class TList extends TAbstractList {
    /**
     *
     * @var \SplFixedArray
     */
    private $FList = null;
    /**
     *
     * @var integer
     */
    private $FCapacity = 10;

    /**
     *
     * @param integer $Capacity
     * @param bool $ElementsOwned
     * @param T[] $FromArray
     * @param boolean $KeepOrder
     * @see FrameworkDSW/TObject#Create()
     */
    public function __construct($Capacity = 10, $ElementsOwned = false, $FromArray = null, $KeepOrder = true) {
        parent::__construct($ElementsOwned);

        TType::Int($Capacity);
        TType::Bool($ElementsOwned);
        TType::Arr($FromArray);
        TType::Bool($KeepOrder);

        if (is_null($FromArray)) {
            $this->FList     = new \SplFixedArray($Capacity);
            $this->FCapacity = $Capacity;

            return;
        }

        $this->FList     = \SplFixedArray::fromArray($FromArray, $KeepOrder);
        $this->FSize     = count($this->FList);
        $this->FCapacity = $this->FSize;
    }

    /**
     *
     * @param integer $Index
     * @return T
     * @see FrameworkDSW/TAbstractList#DoGet($Index)
     */
    protected function DoGet($Index) {
        return $this->FList[$Index];
    }

    /**
     *
     * @param integer $Index
     * @param T $Element
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
     * @param integer $Index
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     * @throws EFailedToInsert
     * @return integer
     * @see FrameworkDSW/TAbstractList#DoInsertAll($Index, $Collection)
     */
    protected function DoInsertAll($Index, $Collection) {
        $mSpace  = $Collection->Size();
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
                $mEffected = $Index - $mBackup;
                throw new EFailedToInsert(sprintf('List insertion failed: %s elements not inserted from element at index %s.', $mEffected, $Index), $e, $mEffected);
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
            throw new EFailedToInsert(sprintf('List insertion failed: %s elements not inserted from element at index %s.', $mSpace, $Index), $e, $mSpace);
        }

        return $mSpace;
    }

    /**
     *
     * @param integer $Index
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
     * @param integer $FromIndex
     * @param integer $ToIndex
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
     * @param integer $Index
     * @param T $Element
     * @see FrameworkDSW/TAbstractList#DoSet($Index, $Element)
     */
    protected function DoSet($Index, $Element) {
        $this->FList[$Index] = $Element;
    }

    /**
     *
     * @param integer $FromIndex
     * @param integer $ToIndex
     * @return \FrameworkDSW\Containers\IList <T: T>
     * @see FrameworkDSW/TAbstractList#DoSubList($FromIndex, $ToIndex)
     */
    protected function DoSubList($FromIndex, $ToIndex) {
        if ($FromIndex >= $ToIndex) {
            $mOffset = $FromIndex - $ToIndex + 1;
            $mList   = new TList($mOffset);
            while ($mOffset--) {
                $mList->Add($this[$FromIndex + $mOffset]);
            }

            return $mList;
        }

        $mOffset = $ToIndex - $FromIndex + 1;
        $mList   = new TList($mOffset);
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
     * @param \FrameworkDSW\Containers\TList $List <T: T>
     */
    public function Swap($List) {
        TType::Object($List, [
            TList::class => ['T' => $this->GenericArg('T')]]);

        $mTempList     = $this->FList;
        $mTempCapacity = $this->FCapacity;
        $mTempOwned    = $this->FElementsOwned;
        $mReadOnly     = $this->FReadOnly;
        $mSize         = $this->FSize;

        $this->FList          = $List->FList;
        $this->FCapacity      = $List->FCapacity;
        $this->FElementsOwned = $List->FElementsOwned;
        $this->FReadOnly      = $List->FReadOnly;
        $this->FSize          = $List->FSize;

        $List->FList          = $mTempList;
        $List->FCapacity      = $mTempCapacity;
        $List->FElementsOwned = $mTempOwned;
        $List->FReadOnly      = $mReadOnly;
        $List->FSize          = $mSize;
    }

    /**
     *
     * @param boolean $ElementsOwned
     * @param T[] $Array
     * @param boolean $KeepOrder
     * @return \FrameworkDSW\Containers\TList <T: T>
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
     * @param integer $Value
     * @throws EInvalidCapacity
     */
    public function setCapacity($Value) {
        TType::Int($Value);

        if ($Value < $this->FSize) {
            throw new EInvalidCapacity(sprintf('Invalid capacity: capacity must be at least %s.', $this->FSize));
        }
        if ($Value != $this->FCapacity) {
            $this->FList->setSize($Value);
            $this->FCapacity = $Value;
        }
    }
}

/**
 * \FrameworkDSW\Containers\TLinkedList
 * param <T: ?>
 * extends TAbstractList<T: T>
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
     * @var \SplFixedArray
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
     * @param integer $Index
     * @return array
     */
    private function GetNodeAddr($Index) {
        // decide from tail or from head.
        $mCurrAddr  = $this->FSize - 1; // from tail
        $mSteps     = $this->FSize - 1 - $Index;
        $mIsForward = false;
        if ($Index < $mSteps) {
            $mSteps     = $Index;
            $mCurrAddr  = $this->FHead; // from head
            $mIsForward = true;
        }

        // decide if should move from the current index.
        $mIsForward2 = true;
        $mDelta      = $Index - $this->FCurrIndex;
        if ($mDelta < 0) {
            $mDelta      = -$mDelta;
            $mIsForward2 = false;
        }
        if ($mDelta < $mSteps) {
            $mSteps     = $mDelta;
            $mCurrAddr  = $this->FCurrAddr; // from current
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
        $this->FCurrAddr  = $mCurrAddr;
        $this->FCurrIndex = $Index;

        return $mCurrAddr;
    }

    /**
     * descHere
     *
     * @param boolean $ElementsOwned
     * @param T[] $FromArray
     * @param boolean $KeepOrder
     */
    public function __construct($ElementsOwned = false, $FromArray = null, $KeepOrder = true) {
        parent::__construct($ElementsOwned);

        TType::Bool($ElementsOwned);
        TType::Arr($FromArray);
        TType::Bool($KeepOrder);

        if (is_null($FromArray) || count($FromArray) == 0) {
            $this->FSize     = 0;
            $this->FList     = new \SplFixedArray(30);
            $this->FAddrSize = 0;

            return;
        }
        $this->FList = \SplFixedArray::fromArray($FromArray, $KeepOrder);
        $this->FSize = count($this->FList);
        $this->FList->setSize(3 * $this->FSize);
        for ($mCurr = $this->FSize - 1; $mCurr >= 0; --$mCurr) {
            $this->FList[3 * $mCurr + self::CData] = $this->FList[$mCurr];
        }
        $this->FHead      = 0;
        $this->FTail      = $this->FSize - 1;
        $this->FCurrAddr  = ($this->FSize - 1) >> 1;
        $this->FCurrIndex = $this->FCurrAddr;
        $this->FAddrSize  = $this->FSize;

        for ($mIndex = 0; $mIndex < $this->FSize; ++$mIndex) {
            $this->FList[3 * $mIndex + self::CPrev] = $mIndex - 1;
            $this->FList[3 * $mIndex + self::CNext] = $mIndex + 1;
        }
        // $mTemp = $this->FList[$mIndex - 1];
        // $mTemp[self::CNext] = -1;
        // $this->FList[$mIndex - 1] = $mTemp;
        $this->FList[3 * ($mIndex - 1) + self::CNext] = -1;

        // This will not work since the [] operators in SPL are function calls
        // and returns a copy of the value, not the reference.
    }

    /**
     * descHere
     *
     * @param integer $Index
     * @return T
     */
    protected function DoGet($Index) {
        $mNode = $this->GetNodeAddr($Index);

        return $this->FList[3 * $mNode + self::CData];
    }

    /**
     * descHere
     *
     * @param integer $Index
     * @param T $Element
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
            $this->FCurrAddr  = $mNewHigh;
            $this->FCurrIndex = 0;
            $this->FHead      = $mNewHigh;
            $this->FTail      = $mNewHigh;

            return;
        } // else do the switch...
        if ($Index != $this->FSize) {
            $mNode = $this->GetNodeAddr($Index);
        }
        else {
            $mNode = $this->GetNodeAddr($Index - 1);
        }
        switch ($Index) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case 0 : // unshift
                $this->FList[3 * $mNode + self::CPrev]    = $mNewHigh;
                $this->FList[3 * $mNewHigh + self::CNext] = $mNode;
                $this->FHead                              = $mNewHigh;
                $this->FCurrAddr                          = $mNewHigh;
                $this->FCurrIndex                         = 0;
            case $this->FSize : // push
                $this->FList[3 * $mNode + self::CNext]    = $mNewHigh;
                $this->FList[3 * $mNewHigh + self::CPrev] = $mNode;
                $this->FTail                              = $mNewHigh;
                $this->FCurrAddr                          = $mNewHigh;
                $this->FCurrIndex                         = $Index;
                break;
            default : // insert
                $mPrevNode                                 = $this->FList[3 * $mNode + self::CPrev];
                $this->FList[3 * $mNode + self::CPrev]     = $mNewHigh;
                $this->FList[3 * $mNewHigh + self::CNext]  = $mNode;
                $this->FList[3 * $mPrevNode + self::CNext] = $mNewHigh;
                $this->FList[3 * $mNewHigh + self::CPrev]  = $mPrevNode;
                $this->FCurrAddr                           = $mNewHigh;
                $this->FCurrIndex                          = $Index;
                break;
        }
    }

    /**
     * descHere
     *
     * @param integer $Index
     * @param \FrameworkDSW\Containers\ICollection $Collection <T: T>
     * @return integer
     */
    protected function DoInsertAll($Index, $Collection) {
        if ($Collection->IsEmpty()) {
            return 0;
        }

        // TODO: 一个一个插入没有效率，应该先把$Collection拼接成链表，然后整体的一次插入
        --$Index;
        $mResult  = 0;
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
     * @param integer $Index
     */
    protected function DoRemoveAt($Index) {
        $mNode = $this->GetNodeAddr($Index);
        if ($this->FElementsOwned) {
            Framework::Free($this->FList[3 * $mNode + self::CData]);
        }

        switch ($Index) {
            /** @noinspection PhpMissingBreakStatementInspection */
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
                $mPrevNode                                 = $this->FList[3 * $mNode + self::CPrev];
                $mNextNode                                 = $this->FList[3 * $mNode + self::CNext];
                $this->FList[3 * $mPrevNode + self::CNext] = $mNextNode;
                $this->FList[3 * $mNextNode + self::CPrev] = $mPrevNode;
                $this->FList[3 * $mNode + self::CPrev]     = -1;
                $this->FList[3 * $mNode + self::CNext]     = -1;
                $this->FCurrAddr                           = $mNextNode;
                $this->FCurrIndex                          = $Index;

                return;
        }

        if ($this->FSize == 1) {
            $this->FCurrAddr  = -1;
            $this->FCurrIndex = -1;

            return;
        }
        $this->FCurrAddr  = $this->FHead;
        $this->FCurrIndex = 0;
    }

    /**
     * descHere
     *
     * @param integer $FromIndex
     * @param integer $ToIndex
     * @return integer
     */
    protected function DoRemoveRange($FromIndex, $ToIndex) {
    }

    /**
     * descHere
     *
     * @param integer $Index
     * @param T $Element
     */
    protected function DoSet($Index, $Element) {
        $this->FList[3 * $this->GetNodeAddr($Index) + self::CData] = $Element;
    }

    /**
     * descHere
     *
     * @param integer $FromIndex
     * @param integer $ToIndex
     * @return \FrameworkDSW\Containers\IList <T: T>
     */
    protected function DoSubList($FromIndex, $ToIndex) {
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Containers\TAbstractList::DoClear()
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

        $this->FList      = new \SplFixedArray(30);
        $this->FHead      = -1;
        $this->FTail      = -1;
        $this->FCurrAddr  = -1;
        $this->FCurrIndex = -1;
        $this->FAddrSize  = 0;
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
     * @param boolean $ElementsOwned
     * @param T[] $Array
     * @param boolean $KeepOrder
     * @return \FrameworkDSW\Containers\TLinkedList <T: T>
     */
    public static function FromArray($ElementsOwned = false, $Array, $KeepOrder) {
        TType::Bool($ElementsOwned);
        TType::Arr($Array);
        TType::Bool($KeepOrder);

        self::PrepareGeneric(['T' => self::StaticGenericArg('T')]);

        return new TLinkedList($ElementsOwned, $Array, $KeepOrder);
    }

    /**
     *
     * @param \FrameworkDSW\Containers\TLinkedList $LinkedList <T: T>
     */
    public function Swap($LinkedList) {
        TType::Object($LinkedList, [
            TLinkedList::class => ['T' => $this->GenericArg('T')]]);
        $this->CheckReadOnly();
        $LinkedList->CheckReadOnly();

        $mTempList     = $this->FList;
        $mTempHead     = $this->FHead;
        $mTempTail     = $this->FTail;
        $mTempReadOnly = $this->FReadOnly;
        $mTempSize     = $this->FSize;
        $mTempOwned    = $this->FElementsOwned;

        $this->FList          = $LinkedList->FList;
        $this->FHead          = $LinkedList->FHead;
        $this->FTail          = $LinkedList->FTail;
        $this->FCurrAddr      = $LinkedList->FCurrAddr;
        $this->FCurrIndex     = $LinkedList->FCurrIndex;
        $this->FReadOnly      = $LinkedList->FReadOnly;
        $this->FSize          = $LinkedList->FSize;
        $this->FElementsOwned = $LinkedList->FElementsOwned;

        $LinkedList->FList          = $mTempList;
        $LinkedList->FHead          = $mTempHead;
        $LinkedList->FTail          = $mTempTail;
        $LinkedList->FCurrAddr      = $mTempTail;
        $LinkedList->FCurrIndex     = $mTempTail;
        $LinkedList->FReadOnly      = $mTempReadOnly;
        $LinkedList->FSize          = $mTempSize;
        $LinkedList->FElementsOwned = $mTempOwned;
    }

    /**
     * descHere
     *
     * @return T[]
     */
    public function ToArray() {
        $mResult = [];
        foreach ($this as $mElement) {
            $mResult[] = $mElement;
        }

        return $mResult;
    }
}

/**
 * \FrameworkDSW\Containers\TStack
 * params <T: ?>
 * extends \FrameworkDSW\Containers\TAbstractStack<T: T>, \FrameworkDSW\Containers\IStack<T: T>
 *
 * @author 许子健
 */
final class TStack extends TAbstractStack implements IStack {
    /**
     *
     * @var \SplStack
     */
    private $FStack = null;

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Containers\TAbstractStack::DoClear()
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
     * @param T $Element
     */
    protected function DoPush($Element) {
        $this->FStack->push($Element);
    }

    /**
     *
     * @param boolean $ElementsOwned
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
        $mResult = [];
        foreach ($this->FStack as $mElement) {
            $mResult[] = $mElement;
        }

        return $mResult;
    }
}

/**
 * \FrameworkDSW\Containers\TMapKeyType
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
 * \FrameworkDSW\Containers\TMap
 * params <K: ?, V: ?>
 * extends \FrameworkDSW\Containers\TAbstractMap<K: K, V: V>
 *
 * @author 许子健
 */
class TMap extends TAbstractMap {
    /**
     *
     * @var array
     */
    private $FMap = [];
    /**
     *
     * @var boolean
     */
    private $FDirectKey = false;
    /**
     * 0: float, 1: array, 2: an object, -1: not related
     *
     * @var \FrameworkDSW\Containers\TMapKeyType
     */
    private $FKeyType = null;

    /**
     *
     * @param K $Key
     * @return string
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    private function HashKey($Key) {
        switch ($this->FKeyType) {
            case TMapKeyType::eObject() :
                return spl_object_hash($Key);
                break;
            case TMapKeyType::eFloat() :
                return (string)$Key;
                break;
            case TMapKeyType::eArray() :
            case TMapKeyType::eRecord() :
                return sha1(serialize((array)$Key), true);
                break;
            case TMapKeyType::eBoolean() :
                return $Key ? '1' : '0';
                break;
        }
    }

    /**
     * descHere
     *
     * @param boolean $ElementsOwned
     */
    public function __construct($ElementsOwned = false) {
        $this->PrepareMethodGeneric([
            'T' => [
                TPair::class => ['K' => self::StaticGenericArg('K'),
                                 'V' => self::StaticGenericArg('V')]]]);
        parent::__construct($ElementsOwned);
        TType::Bool($ElementsOwned);

        $mKClassType = $this->GenericArg('K');
        if (is_array($mKClassType)) {
            $mKClassType = array_keys($mKClassType);
            $mKClassType = $mKClassType[0];
        }
        if (class_exists($mKClassType, false) && is_subclass_of($mKClassType, TRecord::class)) {
            $this->FKeyType   = TMapKeyType::eRecord();
            $this->FDirectKey = false;

            return;
        }
        $this->FDirectKey = ($mKClassType == Framework::Float || $mKClassType == Framework::String);
        if ($this->FDirectKey) {
            $this->FKeyType = TMapKeyType::eDirectKey();

            return;
        }
        switch ($mKClassType) {
            case Framework::Float :
                $this->FKeyType = TMapKeyType::eFloat();
                break;
            case Framework::Boolean :
                $this->FKeyType = TMapKeyType::eBoolean();
                break;
            default :
                if (strrpos($mKClassType, ']', -1) !== false) {
                    $this->FKeyType = TMapKeyType::eObject();
                }
                else {
                    $this->FKeyType = TMapKeyType::eArray();
                }
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
        $this->FMap = [];
    }

    /**
     * descHere
     *
     * @param K $Key
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
     * @param V $Value
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
     * @param K $Key
     */
    protected function DoDelete($Key) {
        if (!TType::IsTypePrimitive($this->GenericArg('V'))) {
            Framework::Free($this->DoGet($Key));
        }
        if ($this->FDirectKey) {
            /** @noinspection PhpIllegalArrayKeyTypeInspection */
            unset($this->FMap[$Key]);
        }
        else {
            unset($this->FMap[$this->HashKey($Key)]);
        }
    }

    /**
     * descHere
     *
     * @param K $Key
     * @return V
     */
    protected function DoGet($Key) {
        if ($this->FDirectKey) {
            /** @noinspection PhpIllegalArrayKeyTypeInspection */
            return $this->FMap[$Key];
        }
        $mPair = $this->FMap[$this->HashKey($Key)];

        return $mPair->Value;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ISet <T: K>
     */
    protected function DoKeySet() {
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\ISet <T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
     */
    protected function DoPairSet() {
    }

    /**
     * descHere
     *
     * @param K $Key
     * @param V $Value
     */
    protected function DoPut($Key, $Value) {
        if ($this->FDirectKey) {
            /** @noinspection PhpIllegalArrayKeyTypeInspection */
            $this->FMap[$Key] = $Value;
        }
        elseif (($this->FKeyType == TMapKeyType::eObject()) || ($this->FKeyType == TMapKeyType::eRecord()) || ($this->FKeyType == TMapKeyType::eArray())) {
            $mPair                            = new TPair();
            $mPair->Key                       = $Key;
            $mPair->Value                     = $Value;
            $this->FMap[$this->HashKey($Key)] = $mPair;
        }
        else {
            $this->FMap[$this->HashKey($Key)] = $Value;
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IMap $Map <K: K, V: V>
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
                $mPair->Key                        = $mKey;
                $mPair->Value                      = $mValue;
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
     * @return \FrameworkDSW\Containers\ICollection <T: V>
     */
    protected function DoValues() {
        if (empty($this->FMap)) {
            return null;
        }
        TList::PrepareGeneric(['T' => $this->GenericArg('V')]);
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
     * @return integer
     */
    public function Size() {
        return count($this->FMap);
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IIterator <T: \FrameworkDSW\Containers\TPair<K: K, V: V>>
     */
    public function Iterator() {
        TStdMapMapIterator::PrepareGeneric([
            'K' => $this->GenericArg('K'), 'V' => $this->GenericArg('V'),
            'T' => [
                TPair::class => ['K' => $this->GenericArg('K'),
                                 'V' => $this->GenericArg('V')]]]);

        return new TStdMapMapIterator($this->FMap, $this->FKeyType);
    }
}
