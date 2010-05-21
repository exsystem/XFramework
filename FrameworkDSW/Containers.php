<?php
/**
 * Containers
 * @author  ExSystem
 * @version $Id: Containers.php 10 2010-05-19 03:47:02Z exsystemchina@gmail.com $
 * @since   separate file since reversion 16
 */
require_once 'FrameworkDSW/System.php';

/**
 *
 * @author  ExSystem
 */
class EIndexOutOfBounds extends ERuntimeException {}
/**
 *
 * @author  ExSystem
 */
class ENoKeyDefined extends ERuntimeException {}
/**
 *
 * @author  ExSystem
 */
class EIllegalState extends ERuntimeException {}
/**
 *
 * @author  ExSystem
 */
class EConcurrentModification extends ERuntimeException {}
/**
 *
 * @author  ExSystem
 */
class EContainerDataOprErr extends ERuntimeException {
    /**
     *
     * @var    integer
     */
    private $FEffected = -1;

    /**
     *
     * @param  string      $message
     * @param  integer     $code
     * @param  Exception   $previous
     * @param  integer     $EffectedCount
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
 * @author  ExSystem
 */
class EFailedToInsert extends EContainerDataOprErr {}
/**
 *
 * @author  ExSystem
 */
class EFailedToRemove extends EContainerDataOprErr {}

/**
 *
 * @author  ExSystem
 */
class EContainerException extends EException {}
/**
 *
 * @author  ExSystem
 */
class ECollectionIsReadOnly extends EContainerException {}
/**
 *
 * @author  ExSystem
 */
class EInvalidCapacity extends EContainerException {}
/**
 *
 * @author  ExSystem
 */
class ENoSuchElement extends EContainerException {}
/**
 *
 * @author  ExSystem
 */
class ECollectionNotExisted extends EContainerException {}

/**
 * IIterator
 * @param	<T>
 */
interface IIterator extends IInterface, Iterator {

    /**
     * Remove
     */
    public function Remove();
}

/**
 * IIteratorAggregate
 * @param	<T>
 */
interface IIteratorAggregate extends IInterface, IteratorAggregate {

    /**
     *
     * @return IIterator<T>
     */
    public function Iterator();
}

/**
 * IArrayAccess
 * @param	<T>
 */
interface IArrayAccess extends IInterface, ArrayAccess {}

//IListItrerator : {[node_0, node_1, node_2, ... ,node_n ,] tail_node}


/**
 * IListIterator
 * extends IIterator<T>
 * @param	<T>
 */
interface IListIterator extends IIterator {

    /**
     *
     * @param  T   $Element
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
     * @param  T
     */
    public function Set($Element);
}

/**
 * ICollection
 * extends IIteratorAggregate<T>
 * @param	<T>
 */
interface ICollection extends IIteratorAggregate {

    /**
     *
     * @param  T   $Element
     */
    public function Add($Element);

    /**
     *
     * @param   ICollection<T>	$Collection
     */
    public function AddAll($Collection);

    /**
     *
     * @return  boolean
     */
    public function Clear();

    /**
     *
     * @param  T		$Element
     * @return boolean
     */
    public function Contains($Element);

    /**
     *
     * @param   ICollection<T>	$Collection
     * @return  boolean
     */
    public function ContainsAll($Collection);

    /**
     *
     * @return  boolean
     */
    public function IsEmpty();

    /**
     *
     * @return integer
     */
    public function Size();

    /**
     *
     * @param  T   $Element
     */
    public function Remove($Element);

    /**
     *
     * @param   ICollection<T>	$Collection
     */
    public function RemoveAll($Collection);

    /**
     *
     * @param  ICollection<T>	$Collection
     * @return boolean
     */
    public function RetainAll($Collection);

    /**
     *
     * @return  T[]
     */
    public function ToArray();
}

/**
 * IList
 * extends ICollection<T>
 * @param	<T>
 */
interface IList extends ICollection {

    /**
     *
     * @param  integer	$Index
     * @param  T		$Element
     */
    public function Insert($Index, $Element);

    /**
     *
     * @param  integer			$Index
     * @param  ICollection<T>	$Collection
     */
    public function InsertAll($Index, $Collection);

    /**
     *
     * @param  integer $Index
     * @return T
     */
    public function Get($Index);

    /**
     *
     * @param  T		$Element
     * @return integer
     */
    public function IndexOf($Element);

    /**
     *
     * @param  T		$Element
     * @return integer
     */
    public function LastIndexOf($Element);

    /**
     *
     * @param  integer			$Index
     * @return IListIterator<T>
     */
    public function ListIterator($Index = 0);

    /**
     *
     * @param  integer $Index
     */
    public function RemoveAt($Index);

    /**
     *
     * @param  integer	$Index
     * @param  T		$Element
     * @return T
     */
    public function Set($Index, $Element);

    /**
     *
     * @param  integer	$FromIndex
     * @param  integer 	$ToIndex
     * @return IList<T>
     */
    public function SubList($FromIndex, $ToIndex);
}

/**
 * ISet
 * extends ICollection<T>
 * @param	<T>
 */
interface ISet extends ICollection {}

/**
 * IQueue
 * extends	ICollection<T>
 * @param	<T>
 */
interface IQueue extends ICollection {

    /**
     *
     * @param	T	$Element
     */
    public function Offer($Element);

    /**
     *
     * @return	T
     */
    public function Peek();

    /**
     *
     * @return	T
     */
    public function Poll();
}

/**
 * IMap
 * @param	<K, V>
 */
interface IMap extends IInterface {

    /**
     *
     * @param  K		$Key
     * @return boolean
     */
    public function ContainsKey($Key);

    /**
     *
     * @param  V	   $Value
     * @return boolean
     */
    public function ContainsValue($Value);

    /**
     *
     * @return ISet<TPair<K, V>>
     */
    public function PairSet();

    /**
     *
     * @return ISet<K>
     */
    public function KeySet();

    /**
     *
     * @param  K   $Key
     * @param  V   $Value
     */
    public function Put($Key, $Value);

    /**
     *
     * @param  IMap<K, V>    $Map
     */
    public function PutAll($Map);

    /**
     *
     * @return ICollection<V>
     */
    public function Values();
}

/**
 * TPair
 * @param	<K, V>
 */
final class TPair extends TRecord {
    /**
     * Key.
     * @var    K
     */
    public $Key;
    
    /**
     * Value.
     * @var    V
     */
    public $Value;
}

/**
 * The default iterator for TAbstractList.
 * extends IIterator<T>
 * @param	<T>
 * @author  ExSystem
 */
class TStdListIterator extends TObject implements IIterator {
    /**
     *
     * @var    TAbstractList<T>
     */
    protected $FList = null;
    /**
     *
     * @var    integer
     */
    protected $FCursor = 0;
    /**
     *
     * @var    integer
     */
    protected $FLastAt = -1;

    /**
     * @param  TAbstractList<T>		$List
     * @see    FrameworkDSW/TObject#Create()
     */
    public function __construct($List) {
        parent::__construct();
        TType::Type($List, array ('TAbstractList' => array ('T' => $this->GenericArg('T'))));
        
        $this->FList = $List;
    }

    /**
     *
     */
    public function rewind() {
        $this->FCursor = 0;
        $this->FLastAt = -1;
    }

    /**
     * @return  integer|string
     */
    public function key() {
        throw new ENoKeyDefined();
    }

    /**
     * @return  T
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
     * @return  boolean
     */
    public function valid() {
        return $this->FCursor < $this->FList->Size();
    }

    /**
     *
     */
    public function next() {
        $this->FLastAt = $this->FCursor;
        ++$this->FCursor;
    }
}

/**
 *
 * @author  ExSystem
 */
class TStdListListIterator extends TStdListIterator implements IListIterator {

    /**
     *
     * @param  TAbstractList   $List
     * @param  integer         $StartAt
     * @see FrameworkDSW/TStdListIterator#Create($List)
     */
    public function __construct($List, $StartAt) {
        TType::Object($List, 'TAbstractList');
        TType::Int($StartAt);
        
        parent::__construct($List);
        $this->FCursor = $StartAt;
    }

    /**
     *
     * @return integer
     * @see    FrameworkDSW/TStdListIterator#key()
     */
    public function key() {
        return $this->FCursor;
    }

    /**
     *
     * @param   mixed   $Element
     * @see     FrameworkDSW/IListIterator#Add($Element)
     */
    public function Add($Element) {
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
     * @return  boolean
     * @see     FrameworkDSW/IListIterator#HasPrevious()
     */
    public function HasPrevious() {
        return $this->FCursor != 0;
    }

    /**
     *
     * (non-PHPdoc)
     * @see FrameworkDSW/IListIterator#Previous()
     */
    public function Previous() {
        $this->FLastAt = $this->FCursor;
        --$this->FCursor;
    }

    /**
     *
     * @param   mixed   $Element
     * @see FrameworkDSW/IListIterator#Set($Element)
     */
    public function Set($Element) {
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
 *
 * @author ExSystem
 */
abstract class TAbstractCollection extends TObject implements ICollection {
    /**
     *
     * @var    boolean
     */
    private $FReadOnly = false;

    protected function CheckReadOnly() {
        if ($this->FReadOnly) {
            throw new ECollectionIsReadOnly();
        }
    }

    /**
     *
     * @param   ICollection $Collection
     */
    public function AddAll($Collection) {
        TType::Object($Collection, 'ICollection');
        $this->CheckReadOnly();
        
        foreach ($Collection as $mElement) {
            $this->Add($mElement);
        }
    }

    /**
     * (non-PHPdoc)
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
     * @param  mixed   $Element
     * @return boolean
     */
    public function Contains($Element) {
        $mItr = $this->Iterator();
        if ($Element instanceof TObject && $mItr->current() instanceof TObject) {
            while ($mItr->valid()) {
                if ($Element->Equals($mItr->current())) {
                    return true;
                }
                $mItr->next();
            }
        }
        else {
            while ($mItr->valid()) {
                if ($Element === $mItr->current()) {
                    return true;
                }
                $mItr->next();
            }
        }
        
        return false;
    }

    /**
     *
     * @param   ICollection $Collection
     * @return  boolean
     */
    public function ContainsAll($Collection) {
        TType::Object($Collection, 'ICollection');
        
        foreach ($Collection as $mElement) {
            if (!$this->Contains($mElement)) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @return  boolean
     */
    public function IsEmpty() {
        return $this->Size() == 0;
    }

    /**
     *
     * @param  mixed   $Element
     */
    public function Remove($Element) {
        $this->CheckReadOnly();
        $mItr = $this->Iterator();
        
        if ($Element instanceof TObject && $mItr->current() instanceof TObject) {
            while ($mItr->Valid()) {
                if ($Element->Equals($mItr->current())) {
                    $mItr->Remove();
                    return;
                }
                $mItr->next();
            }
        }
        else {
            while ($mItr->valid()) {
                if ($Element === $mItr->current()) {
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
     * @param   ICollection $Collection
     */
    public function RemoveAll($Collection) {
        TType::Object($Collection, 'ICollection');
        
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
     * @param  ICollection $Collection
     * @return boolean
     */
    public function RetainAll($Collection) {
        TType::Object($Collection, 'ICollection');
        
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
     * @return  array
     */
    public function ToArray() {
        $mResult = array ();
        foreach ($this as $mValue) {
            $mResult[] = $mValue;
        }
        return $mResult;
    }

    /**
     * @return  IIterator
     */
    public function getIterator() {
        return $this->Iterator();
    }

    /**
     *
     * @return  boolean
     */
    public function getReadOnly() {
        return $this->FReadOnly;
    }

    /**
     *
     * @param   boolean $Value
     */
    public function setReadOnly($Value) {
        TType::Bool($Value);
        
        $this->FReadOnly = $Value;
    }
}

abstract class TAbstractList extends TAbstractCollection implements IList, IArrayAccess {
    /**
     *
     * @var    integer
     */
    protected $FSize = 0;

    /**
     *
     * @param  integer $Index
     */
    private function CheckIndexForAdd($Index) {
        if ($Index < 0 || $Index > $this->FSize) {
            throw new EIndexOutOfBounds();
        }
    }

    /**
     *
     * @param  integer $Index
     */
    private function CheckIndexForGet($Index) {
        if ($Index < 0 || $Index >= $this->FSize) {
            throw new EIndexOutOfBounds();
        }
    }

    /**
     *
     * @param  integer $Index
     * @param  mixed   $Element
     */
    protected abstract function DoInsert($Index, $Element);

    /**
     *
     * @param  integer $Index
     * @return mixed
     */
    protected abstract function DoGet($Index);

    /**
     *
     * @param  integer     $Index
     * @param  ICollection $Collection
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
     * @param  integer $Index
     */
    protected abstract function DoRemoveAt($Index);

    /**
     *
     * @param  integer $FromIndex
     * @param  integer $ToIndex
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
     * @param  integer $Index
     * @param  mixed   $Element
     */
    protected abstract function DoSet($Index, $Element);

    /**
     *
     * @param  integer $FromIndex
     * @param  integer $ToIndex
     * @return IList
     */
    protected abstract function DoSubList($FromIndex, $ToIndex);

    /**
     * (non-PHPdoc)
     * @param   mixed   $Element
     * @see     FrameworkDSW/AbstractCollection#Add($Element)
     */
    public final function Add($Element) {
        $this->Insert($this->Size(), $Element);
    }

    /**
     *
     * @param  integer $Index
     * @param  mixed   $Element
     */
    public final function Insert($Index, $Element) {
        TType::Int($Index);
        
        $this->CheckReadOnly();
        $this->CheckIndexForAdd($Index);
        $this->DoInsert($Index, $Element);
        ++$this->FSize;
    }

    /**
     *
     * @param  integer     $Index
     * @param  ICollection $Collection
     */
    public final function InsertAll($Index, $Collection) {
        TType::Int($Index);
        TType::Object($Collection, 'ICollection');
        
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
     * @param  integer $Index
     * @return mixed
     */
    public final function Get($Index) {
        TType::Int($Index);
        
        $this->CheckIndexForGet($Index);
        return $this->DoGet($Index);
    }

    /**
     *
     * @param  mixed   $Element
     * @return integer
     */
    public final function IndexOf($Element) {
        $mItr = $this->ListIterator();
        
        if ($Element instanceof TObject && $mItr->current() instanceof TObject) {
            while ($mItr->valid()) {
                if ($Element->Equals($mItr->current())) {
                    return $mItr->key();
                }
                $mItr->next();
            }
        }
        else {
            while ($mItr->valid()) {
                if ($Element === $mItr->current()) {
                    return $mItr->key();
                }
                $mItr->next();
            }
        }
        
        throw new ENoSuchElement();
    }

    /**
     *
     * @return  IIterator
     * @see FrameworkDSW/TAbstractCollection#Iterator()
     */
    public function Iterator() {
        return new TStdListIterator($this);
    }

    /**
     *
     * @param  mixed   $Element
     * @return integer
     */
    public final function LastIndexOf($Element) {
        $mItr = $this->ListIterator($this->FSize);
        
        if ($Element instanceof TObject && $mItr->current() instanceof TObject) {
            while ($mItr->HasPrevious()) {
                $mItr->Previous();
                if ($Element->Equals($mItr->current())) {
                    return $mItr->key();
                }
            }
        }
        else {
            while ($mItr->HasPrevious()) {
                $mItr->Previous();
                if ($Element === $mItr->current()) {
                    return $mItr->key();
                }
            }
        }
        
        throw new ENoSuchElement();
    }

    /**
     *
     * @param  integer         $Index
     * @return IListIterator
     */
    public function ListIterator($Index = 0) {
        TType::Int($Index);
        
        $this->CheckIndexForAdd($Index);
        return new TStdListListIterator($this, $Index);
    }

    /**
     *
     * @param  integer $Index
     */
    public final function RemoveAt($Index) {
        $this->CheckReadOnly();
        
        $this->CheckIndexForGet($Index);
        $this->DoRemoveAt($Index);
        --$this->FSize;
    }

    /**
     *
     * @param   integer $FromIndex
     * @param   integer $ToIndex
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
     * @param  integer $Index
     * @param  mixed   $Element
     * @return mixed
     */
    public final function Set($Index, $Element) {
        TType::Int($Index);
        
        $this->CheckReadOnly();
        $this->CheckIndexForGet($Index);
        $this->DoSet($Index, $Element);
    }

    /**
     *
     * @return  integer
     * @see     FrameworkDSW/TAbstractCollection#Size()
     */
    public final function Size() {
        return $this->FSize;
    }

    /**
     * @param	integer	$FromIndex
     * @param	integer	$ToIndex
     * @return	IList
     */
    public final function SubList($FromIndex, $ToIndex) {
        TType::Int($FromIndex);
        TType::Int($ToIndex);
        
        $this->CheckIndexForGet($FromIndex);
        $this->CheckIndexForGet($ToIndex);
    }

    /**
     * @param   integer $offset
     * @return  boolean
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
     * @param   integer $offset
     * @return  mixed
     */
    public final function offsetGet($offset) {
        return $this->Get($offset);
    }

    /**
     * @param   integer $offset
     * @param   mixed   $value
     */
    public final function offsetSet($offset, $value) {
        $this->Set($offset, $value);
    }

    /**
     * @param   integer $offset
     */
    public final function offsetUnset($offset) {
        $this->RemoveAt($offset);
    }
}

/**
 *
 * @author  ExSystem
 */
final class TList extends TAbstractList {
    /**
     * @var    SplFixedArray
     */
    private $FList = null;
    /**
     * @var    integer
     */
    private $FCapacity = 10;

    /**
     *
     * @param	integer	$Capacity
     * @param	array	$FromArray
     * @param	boolean	$KeepOrder
     * @see		FrameworkDSW/TObject#Create()
     */
    public function __construct($Capacity = 10, $FromArray = null, $KeepOrder = true) {
        TType::Int($Capacity);
        TType::Arr($FromArray);
        TType::Bool($KeepOrder);
        
        if (is_null($FromArray)) {
            $this->FList = new SplFixedArray($Capacity);
            $this->FCapacity = $Capacity;
            return;
        }
        
        $this->FList = SplFixedArray::fromArray($FromArray, $KeepOrder);
        $this->FSize = count($this->FList);
        $this->FCapacity = $this->FSize;
    }

    /**
     *
     * @param	integer	$Index
     * @return	mixed
     * @see		FrameworkDSW/TAbstractList#DoGet($Index)
     */
    protected function DoGet($Index) {
        return $this->FList[$Index];
    }

    /**
     *
     * @param  integer $Index
     * @param  mixed   $Element
     * @see    FrameworkDSW/TAbstractList#DoInsert($Index, $Element)
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
     * @param  integer     $Index
     * @param  ICollection $Collection
     * @return integer
     * @see    FrameworkDSW/TAbstractList#DoInsertAll($Index, $Collection)
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
        
        //else...
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
     * @param  integer $Index
     * @see    FrameworkDSW/TAbstractList#DoRemoveAt($Index)
     */
    protected function DoRemoveAt($Index) {
        $mTail = $this->FSize;
        --$mTail;
        while ($Index < $mTail) {
            $this->FList[$Index++] = $this->FList[$Index];
        }
        $this->FList->offsetUnset($Index);
    }

    /**
     *
     * @param  integer $FromIndex
     * @param  integer $ToIndex
     * @return integer
     * @see FrameworkDSW/TAbstractList#DoRemoveRange($FromIndex, $ToIndex)
     */
    protected function DoRemoveRange($FromIndex, $ToIndex) {
        $mDelta = $ToIndex - $FromIndex;
        while ($ToIndex < $this->FSize) {
            $this->FList[($ToIndex++) - $mDelta] = $this->FList[$ToIndex];
        }
        return ++$mDelta;
    }

    /**
     *
     * @param  integer $Index
     * @param  mixed   $Element
     * @see FrameworkDSW/TAbstractList#DoSet($Index, $Element)
     */
    protected function DoSet($Index, $Element) {
        $this->FList[$Index] = $Element;
    }

    /**
     *
     * @param  integer $FromIndex
     * @param  integer $ToIndex
     * @return IList
     * @see    FrameworkDSW/TAbstractList#DoSubList($FromIndex, $ToIndex)
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
     *
     * @param  array   $Array
     * @param  boolean $KeepOrder
     * @return TList
     */
    public static function FromArray($Array, $KeepOrder = true) {
        TType::Arr($Array);
        TType::Bool($KeepOrder);
        
        return new TList(10, $Array, $KeepOrder);
    }

    /**
     *
     * @return array
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
     * @return mixed
     */
    public function First() {
        return $this->FList[0];
    }

    /**
     *
     * @return mixed
     */
    public function Last() {
        return $this->FList[$this->FSize - 1];
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
     * @param  integer $Value
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

echo 'containers end<br/>';