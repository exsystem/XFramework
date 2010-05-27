<?php
/**
 * Containers
 * @author  ExSystem
 * @version $Id$
 * @since   separate file since reversion 1
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
 * param	<T>
 */
interface IIterator extends IInterface, Iterator {

    /**
     * Remove
     */
    public function Remove();
}

/**
 * IIteratorAggregate
 * param	<T>
 */
interface IIteratorAggregate extends IInterface, IteratorAggregate {

    /**
     *
     * @return IIterator <T>
     */
    public function Iterator();
}

/**
 * IArrayAccess
 * param	<T>
 */
interface IArrayAccess extends IInterface, ArrayAccess {}

//IListItrerator : {[node_0, node_1, node_2, ... ,node_n ,] tail_node}


/**
 * IListIterator
 * extends	IIterator<T>
 * param	<T>
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
 * extends	IIteratorAggregate<T>
 * param	<T>
 */
interface ICollection extends IIteratorAggregate {

    /**
     *
     * @param  T   $Element
     */
    public function Add($Element);

    /**
     *
     * @param   ICollection	$Collection	<T>
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
     * @param   ICollection	$Collection	<T>
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
     * @param   ICollection	$Collection	<T>
     */
    public function RemoveAll($Collection);

    /**
     *
     * @param  ICollection	$Collection	<T>
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
 * extends	ICollection<T>
 * param	<T>
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
     * @param  ICollection		$Collection <T>
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
     * @return IListIterator <T>
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
     * @return IList <T>
     */
    public function SubList($FromIndex, $ToIndex);

    /**
     * @return	T
     */
    public function First();

    /**
     * @return	T 
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
 * IQueue
 * extends	ICollection<T>
 * param	<T>
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
 * param	<K, V>
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
     * @return ISet <TPair<K, V>>
     */
    public function PairSet();

    /**
     *
     * @return ISet <K>
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
     * @param  IMap	$Map <K, V>
     */
    public function PutAll($Map);

    /**
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
 * param	<T>
 * @author  ExSystem
 */
class TStdListIterator extends TObject implements IIterator {
    /**
     *
     * @var    TAbstractList <T>
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
     * @param  TAbstractList	$List <T>
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
 * TStdListListIterator
 * extends	TStdListIterator<T>, IListIterator<T>
 * param	<T>
 * @author  ExSystem
 */
class TStdListListIterator extends TStdListIterator implements IListIterator {

    /**
     *
     * @param  TAbstractList	$List <T>
     * @param  integer			$StartAt
     * @see FrameworkDSW/TStdListIterator#Create($List)
     */
    public function __construct($List, $StartAt) {
        TType::Type($List, array ('TAbstractList' => array ('T' => $this->GenericArg('T'))));
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
     * @param   T   $Element
     * @see     FrameworkDSW/IListIterator#Add($Element)
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
     * @param   T   $Element
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
 * @author ExSystem
 */
abstract class TAbstractCollection extends TObject implements ICollection {
    /**
     *
     * @var    boolean
     */
    private $FReadOnly = false;
    /**
     * 
     * @var	boolean
     */
    private $FElementsOwned = false;

    /**
     * 
     */
    protected function CheckReadOnly() {
        if ($this->FReadOnly) {
            throw new ECollectionIsReadOnly();
        }
    }

    /**
     * 
     * @param	boolean	$ElementsOwned
     */
    public function __construct($ElementsOwned) {
        TType::Bool($ElementsOwned);
        parent::__construct();
        $this->FElementsOwned = $ElementsOwned && !TType::IsTypePrimitive($this->GenericArg('T'));
    }

    /**
     *
     * @param   ICollection	$Collection <T>
     */
    public function AddAll($Collection) {
        TType::Type($Collection, $this->GenericArg('T'));
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
     * @param  T		$Element
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
     * @param   ICollection	$Collection <T>
     * @return  boolean
     */
    public function ContainsAll($Collection) {
        TType::Type($Collection, array ('ICollection' => array ('T' => $this->GenericArg('T'))));
        
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
     * @param  T	$Element
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
     * @param   ICollection	$Collection <T>
     */
    public function RemoveAll($Collection) {
        TType::Type($Collection, array ('ICollection' => array ('T' => $this->GenericArg('T'))));
        
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
     * @param  ICollection	$Collection <T>
     * @return boolean
     */
    public function RetainAll($Collection) {
        TType::Type($Collection, array ('ICollection' => array ('T' => $this->GenericArg('T'))));
        
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
     * @return  T[]
     */
    public function ToArray() {
        $mResult = array ();
        foreach ($this as $mValue) {
            $mResult[] = $mValue;
        }
        return $mResult;
    }

    /**
     * @return  IIterator <T>
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

    /**
     * @return	boolean
     */
    public function getElementsOwned() {
        return $this->FElementsOwned;
    }
}

/**
 * TAbstractList
 * extends	TAbstractCollection<T>, IList<T>, IArrayAccess<T>
 * param	<T>
 * @author 许子健
 */
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
     * @param  integer	$Index
     * @param  T		$Element
     */
    protected abstract function DoInsert($Index, $Element);

    /**
     *
     * @param  integer $Index
     * @return T
     */
    protected abstract function DoGet($Index);

    /**
     *
     * @param  integer		$Index
     * @param  ICollection	$Collection <T>
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
     * @param  integer	$Index
     * @param  T		$Element
     */
    protected abstract function DoSet($Index, $Element);

    /**
     *
     * @param  integer		$FromIndex
     * @param  integer		$ToIndex
     * @return IList <T>
     */
    protected abstract function DoSubList($FromIndex, $ToIndex);

    /**
     * @return	T
     */
    protected abstract function DoFirst();

    /**
     * @return	T
     */
    protected abstract function DoLast();

    /**
     * 
     */
    protected abstract function DoClear();

    /**
     * (non-PHPdoc)
     * @param   T   $Element
     * @see     FrameworkDSW/AbstractCollection#Add($Element)
     */
    public final function Add($Element) {
        TType::Type($Element, $this->GenericArg('T'));
        $this->Insert($this->Size(), $Element);
    }

    /**
     * 
     */
    public final function Clear() {
        if ($this->FSize == 0) {
            $this->CheckReadOnly();
            $this->DoClear();
            $this->FSize = 0;
        }
    }

    /**
     *
     * @param  integer	$Index
     * @param  T		$Element
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
     * @param  integer		$Index
     * @param  ICollection	$Collection <T>
     */
    public final function InsertAll($Index, $Collection) {
        TType::Int($Index);
        TType::Type($Collection, array ('ICollection' => array ('T' => $this->GenericArg('T'))));
        
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
     * @return T
     */
    public final function Get($Index) {
        TType::Int($Index);
        
        $this->CheckIndexForGet($Index);
        return $this->DoGet($Index);
    }

    /**
     *
     * @param  T		$Element
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
     * @return  IIterator <T>
     * @see FrameworkDSW/TAbstractCollection#Iterator()
     */
    public function Iterator() {
        TStdListIterator::PrepareGeneric(array ('T' => $this->GenericArg('T')));
        return new TStdListIterator($this);
    }

    /**
     *
     * @param  T		$Element
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
     * @param  integer         		$Index
     * @return IListIterator <T>
     */
    public function ListIterator($Index = 0) {
        TType::Int($Index);
        
        $this->CheckIndexForAdd($Index);
        TStdListListIterator::PrepareGeneric(array ('T' => $this->GenericArg('T')));
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
     * @param  integer	$Index
     * @param  T		$Element
     * @return T
     */
    public final function Set($Index, $Element) {
        TType::Int($Index);
        TType::Type($Element, $this->GenericArg('T'));
        
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
     * @param	integer		$FromIndex
     * @param	integer		$ToIndex
     * @return	IList <T>
     */
    public final function SubList($FromIndex, $ToIndex) {
        TType::Int($FromIndex);
        TType::Int($ToIndex);
        
        $this->CheckIndexForGet($FromIndex);
        $this->CheckIndexForGet($ToIndex);
        
        return $this->DoSubList($FromIndex, $ToIndex);
    }

    /**
     * @return	T
     */
    public final function First() {
        if ($this->FSize == 0) {
            throw new EIndexOutOfBounds();
        }
        return $this->DoFirst();
    }

    /**
     * @return	T
     */
    public final function Last() {
        if ($this->FSize == 0) {
            throw new EIndexOutOfBounds();
        }
        return $this->DoLast();
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
     * @return  T
     */
    public final function offsetGet($offset) {
        return $this->Get($offset);
    }

    /**
     * @param   integer $offset
     * @param   T		$value
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
 * TList
 * extends	TAbstractList<T>
 * @author  ExSystem
 */
final class TList extends TAbstractList {
    /**
     * @var SplFixedArray
     */
    private $FList = null;
    /**
     * @var	integer
     */
    private $FCapacity = 10;

    /**
     *
     * @param	integer	$Capacity
     * @param	T[]		$FromArray
     * @param	boolean	$KeepOrder
     * @see		FrameworkDSW/TObject#Create()
     */
    public function __construct($Capacity = 10, $ElementsOwned = false, $FromArray = null, $KeepOrder = true) {
        TType::Int($Capacity);
        TType::Bool($ElementsOwned);
        TType::Arr($FromArray);
        TType::Bool($KeepOrder);
        
        parent::__construct($ElementsOwned);
        
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
     * @return	T
     * @see		FrameworkDSW/TAbstractList#DoGet($Index)
     */
    protected function DoGet($Index) {
        return $this->FList[$Index];
    }

    /**
     *
     * @param  integer	$Index
     * @param  T		$Element
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
     * @param  integer			$Index
     * @param  ICollection 		$Collection <T>
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
     * @param  integer $FromIndex
     * @param  integer $ToIndex
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
     * @param  integer	$Index
     * @param  T		$Element
     * @see FrameworkDSW/TAbstractList#DoSet($Index, $Element)
     */
    protected function DoSet($Index, $Element) {
        $this->FList[$Index] = $Element;
    }

    /**
     *
     * @param  integer		$FromIndex
     * @param  integer		$ToIndex
     * @return IList <T>
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
     */
    protected function DoClear() {
        if ($this->FElementsOwned) {
            foreach ($this->FList as &$mElement) {
                Framework::Free($mElement);
            }
        }
        
        Framework::Free($this->FList);
        $this->FList = new SplFixedArray($this->FCapacity);
    }

    /**
     * @return	T
     */
    protected function DoFirst() {
        return $this->FList[0];
    }

    /**
     * @return	T
     */
    protected function DoLast() {
        return $this->FList[$this->FSize - 1];
    }

    /**
     * 
     * @param	TList	$List <T>
     */
    public function Swap($List) {
        TType::Object($List, array ('TList' => array ('T' => $this->GenericArg('T'))));
        
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
     * @param  T[]			$Array
     * @param  boolean		$KeepOrder
     * @return TList <T>
     */
    public static function FromArray($Array, $KeepOrder = true) {
        TType::Arr($Array);
        TType::Bool($KeepOrder);
        
        return new TList(10, $Array, $KeepOrder);
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

/**
 * TLinkedList
 * param	<T>
 * extends	TAbstractList<T>
 * @author	许子健
 */
final class TLinkedList extends TAbstractList {
    /**
     * 
     * @var	integer
     */
    const CPrev = 0;
    /**
     * 
     * @var	integer
     */
    const CData = 1;
    /**
     * 
     * @var	integer
     */
    const CNext = 2;
    
    /**
     * @var	SplFixedArray
     */
    private $FList = null;
    /**
     * 
     * @var	integer
     */
    private $FHead = -1;
    /**
     * 
     * @var	integer
     */
    private $FTail = -1;
    /**
     * 
     * @var	integer
     */
    private $FCurrIndex = -1;
    /**
     * 
     * @var	integer
     */
    private $FCurrAddr = -1;

    /**
     * 
     * @param	integer	$Index
     * @return	array
     */
    private function GetNodeAddr($Index) {
        $mFrom = $this->FSize - 1; //from tail
        $mSteps = $this->FSize - 1 - $Index;
        $mIsForward = false;
        if ($Index < $mSteps) {
            $mSteps = $Index;
            $mFrom = $this->FHead; //from head
            $mIsForward = true;
        }
        $mDelta = $Index - $this->FCurrIndex;
        $mIsForward = true;
        if ($mDelta < 0) {
            $mDelta = -$mDelta;
            $mIsForward = false;
        }
        if ($mDelta < $mSteps) {
            $mSteps = $mDelta;
            $mFrom = $this->FCurrAddr; //from current
        }
        
        $mCurrAddr = $mFrom;
        ++$mSteps;
        if ($mIsForward) {
            while (--$mSteps) {
                $mCurrAddr = $this->FList[$mCurrAddr][self::CNext];
            }
        }
        else {
            while (--$mSteps) {
                $mCurrAddr = $this->FList[$mCurrAddr][self::CPrev];
            }
        }
        $this->FCurrAddr = $mCurrAddr;
        $this->FCurrIndex = $Index;
        return $mCurrAddr;
    }

    /**
     * descHere
     * @param	T[]		$FromArray
     * @param	boolean	$KeepOder
     */
    public function __construct($ElementsOwned = false, $FromArray = null, $KeepOder = true) {
        TType::Bool($ElementsOwned);
        TType::Arr($FromArray);
        TType::Bool($KeepOder);
        
        parent::__construct($ElementsOwned);
        
        if (is_null($FromArray) || count($FromArray) == 0) {
            $this->FSize = 0;
            $this->FList = new SplFixedArray();
            return;
        }
        $this->FList = SplFixedArray::fromArray($FromArray, $KeepOder);
        $this->FSize = count($this->FList);
        $this->FHead = 0;
        $this->FTail = $this->FSize - 1;
        $this->FCurrAddr = ($this->FSize - 1) >> 1;
        $this->FCurrIndex = $this->FCurrAddr;
        
        $mIndex = 0;
        while ($mIndex < $this->FSize) {
            $this->FList[$mIndex] = array ($mIndex - 1, $this->FList[$mIndex], ++$mIndex);
        }
        $this->FList[$mIndex - 1][self::CNext] = -1;
    }

    /**
     * descHere
     * @param	integer	$Index
     * @return	T
     */
    protected function DoGet($Index) {
        $mNode = $this->GetNodeAddr($Index);
        return $this->FList[$mNode][self::CData];
    }

    /**
     * descHere
     * @param	integer	$Index
     * @param	T	$Element
     */
    protected function DoInsert($Index, $Element) {
        $mNewHigh = count($this->FList);
        $this->FList->setSize($mNewHigh + 1);
        $this->FList[$mNewHigh] = array (-1, $Element, -1);
        
        if ($this->FSize == 0) { //if empty before insertion.
            $this->FCurrAddr = $mNewHigh;
            $this->FCurrIndex = 0;
            $this->FHead = $mNewHigh;
            $this->FTail = $mNewHigh;
            return;
        } //else do the switch...
        $mNode = $this->GetNodeAddr($Index);
        switch ($Index) {
            case 0 : //unshift               
                $this->FList[$mNode][self::CPrev] = $mNewHigh;
                $this->FList[$mNewHigh][self::CNext] = $mNode;
                $this->FHead = $mNewHigh;
                $this->FCurrAddr = $mNewHigh;
                $this->FCurrIndex = 0;
            case $this->FSize : //push
                $this->FList[$mNode][self::CNext] = $mNewHigh;
                $this->FList[$mNewHigh][self::CPrev] = $mNode;
                $this->FTail = $mNewHigh;
                $this->FCurrAddr = $mNewHigh;
                $this->FCurrIndex = $Index;
                break;
            default : //insert
                $mPrevNode = $this->FList[$mNode][self::CPrev];
                $this->FList[$mNode][self::CPrev] = $mNewHigh;
                $this->FList[$mNewHigh][self::CNext] = $mNode;
                $this->FList[$mPrevNode][self::CNext] = $mNewHigh;
                $this->FList[$mNewHigh][self::CPrev] = $mPrevNode;
                $this->FCurrAddr = $mNewHigh;
                $this->FCurrIndex = $Index;
                break;
        }
    }

    /**
     * descHere
     * @param	integer		$Index
     * @param	ICollection	$Collection <T>
     * @return	integer
     */
    protected function DoInsertAll($Index, $Collection) {
        if ($Collection->IsEmpty()) {
            return 0;
        }
        
        //TODO: 一个一个插入没有效率，应该先把$Collection拼接成链表，然后整体的一次插入
        --$Index;
        $mResult = 0;
        foreach ($Collection as $mElement) {
            $this->DoInsert(++$Index, $mElement);
            ++$mResult;
        }
        return $mResult;
    }

    /**
     * descHere
     * @param	integer	$Index
     */
    protected function DoRemoveAt($Index) {
        $mNode = $this->GetNodeAddr($Index);
        if ($this->FElementsOwned) {
            Framework::Free($this->FList[$mNode][self::CData]);
        }
        
        switch ($Index) {
            case 0 : //shift               
                $this->FHead = $this->FList[$mNode][self::CNext];
                $this->FList[$this->FHead][self::CPrev] = -1;
                $this->FList[$mNode][self::CPrev] = -1;
                $this->FList[$mNode][self::CNext] = -1;
            case $this->FSize - 1 : //pop
                $this->FTail = $this->FList[$mNode][self::CPrev];
                $this->FList[$this->FTail][self::CNext] = -1;
                $this->FList[$mNode][self::CPrev] = -1;
                $this->FList[$mNode][self::CNext] = -1;
                break;
            default : //remove
                $mPrevNode = $this->FList[$mNode][self::CPrev];
                $mNextNode = $this->FList[$mNode][self::CNext];
                $this->FList[$mPrevNode][self::CNext] = $mNextNode;
                $this->FList[$mNextNode][self::CPrev] = $mPrevNode;
                $this->FList[$mNode][self::CPrev] = -1;
                $this->FList[$mNode][self::CNext] = -1;
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
     * @param	integer	$FromIndex
     * @param	integer	$ToIndex
     * @return	integer
     */
    protected function DoRemoveRange($FromIndex, $ToIndex) {
    }

    /**
     * descHere
     * @param	integer	$Index
     * @param	T	$Element
     */
    protected function DoSet($Index, $Element) {
        $this->FList[$this->GetNodeAddr($Index)][self::CData] = $Element;
    }

    /**
     * descHere
     * @param	integer		$FromIndex
     * @param	integer		$ToIndex
     * @return	IList <T>
     */
    protected function DoSubList($FromIndex, $ToIndex) {
    }

    /**
     * 
     */
    protected function DoClear() {
        if ($this->FElementsOwned) {
            foreach ($this->FList as &$mElement) {
                Framework::Free($mElement);
            }
        }
        
        Framework::Free($this->FList);
        $this->FList = new SplFixedArray(0);
        $this->FHead = -1;
        $this->FTail = -1;
        $this->FCurrAddr = -1;
        $this->FCurrIndex = -1;
    }

    /**
     * descHere
     * @return	T
     */
    protected function DoFirst() {
        return $this->FList[$this->FHead][self::CData];
    }

    /**
     * descHere
     * @return	T
     */
    protected function DoLast() {
        return $this->FList[$this->FTail][self::CData];
    }

    /**
     * descHere
     * @param	boolean			$ElementsOwned
     * @param	T[]				$Array
     * @param	boolean			$KeepOrder
     * @return	TLinkedList	<T>
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
     * @param	TLinkedList	$LinkedList <T>
     */
    public function Swap($LinkedList) {
        TType::Object($LinkedList, array ('TLinkedList' => array ('T' => $this->GenericArg('T'))));
        
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
     * @return	T[]
     */
    public function ToArray() {
        //把链表导出成array。
    }
}