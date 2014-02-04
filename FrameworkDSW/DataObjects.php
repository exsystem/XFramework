<?php
/**
 * \FrameworkDSW\DataObjects
 * @author    许子健
 * @version    $Id$
 * @since    separate file since reversion 30
 */
namespace FrameworkDSW\DataObjects;

use FrameworkDSW\Containers\EIndexOutOfBounds;
use FrameworkDSW\Containers\ENoSuchElement;
use FrameworkDSW\Containers\IIterator;
use FrameworkDSW\Containers\IIteratorAggregate;
use FrameworkDSW\Containers\TList;
use FrameworkDSW\Containers\TPair;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Linq\Expressions\TExpression;
use FrameworkDSW\Linq\Expressions\TParameterExpression;
use FrameworkDSW\Linq\Expressions\TTypedExpression;
use FrameworkDSW\Linq\IExpressibleOrderedQueryable;
use FrameworkDSW\Linq\IQueryable;
use FrameworkDSW\Linq\IQueryProvider;
use FrameworkDSW\Linq\TAggregateCallbackDelegate;
use FrameworkDSW\Linq\TAggregateDelegate;
use FrameworkDSW\Linq\TGroupByCallbackDelegate;
use FrameworkDSW\Linq\TJoinCallbackDelegate;
use FrameworkDSW\Linq\TPredicateDelegate;
use FrameworkDSW\Linq\TSelectorDelegate;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\EInvalidTypeCasting;
use FrameworkDSW\Utilities\TType;

/**
 * \FrameworkDSW\DataObjects\IEntity
 *
 * @author 许子健
 */
interface IEntity extends IInterface {

    /**
     * descHere
     *
     * @return \FrameworkDSW\DataObjects\TObjectContext
     */
    public function getContext();

    /**
     *
     * @return string[] TODO {string}
     */
    public static function getPrimaryKeys();

    /**
     *
     * @return array TODO {<string, string>}
     */
    public static function getColumns();

    /**
     * @return array TODO {<string, string>}
     */
    public static function getColumnsType();

    /**
     *
     * @return string
     */
    public static function getTableName();
}

/**
 * \FrameworkDSW\DataObjects\TObjectContext
 *
 * @author 许子健
 */
abstract class TObjectContext extends TObject {

    /**
     *
     * @var \FrameworkDSW\Linq\IQueryProvider
     */
    protected $FProvider = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\DataObjects\IEntity $Entity
     */
    protected abstract function DoAddObject($Entity);

    /**
     * descHere
     *
     * @param \FrameworkDSW\DataObjects\IEntity $Entity
     */
    protected abstract function DoDeleteObject($Entity);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\IQueryProvider $QueryProvider
     */
    public function __construct($QueryProvider) {
        parent::__construct();
        TType::Object($QueryProvider, IQueryProvider::class);
        $this->FProvider = $QueryProvider;
    }

    /**
     * descHere
     *
     *
     * @param \FrameworkDSW\DataObjects\IEntity $Entity
     */
    public function AddObject($Entity) {
        TType::Object($Entity, IEntity::class);

        TObject::Dispatch([$this, 'PreAdd'], [$Entity]);
        $this->DoAddObject($Entity);
        TObject::Dispatch([$this, 'PostAdd'], [$Entity]);
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function CreateQuery() {
        $this->FProvider->PrepareMethodGeneric([
            'T' => $this->GenericArg('T')]);

        return $this->FProvider->CreateQuery($this);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\DataObjects\IEntity $Entity
     */
    public function DeleteObject($Entity) {
        TType::Object($Entity, IEntity::class);

        TObject::Dispatch([$this, 'PreDelete'], [$Entity]);
        $this->DoDeleteObject($Entity);
        TObject::Dispatch([$this, 'PostDelete'], [$Entity]);
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryProvider
     */
    public function getQueryProvider() {
        return $this->FProvider;
    }

    /**
     * descHere
     */
    public abstract function SaveChanges();

    /**
     * descHere
     *
     * @param \FrameworkDSW\DataObjects\IEntity $Entity
     */
    public function signalPostAdd($Entity) {
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\DataObjects\IEntity $Entity
     * @param array $Members
     */
    public function signalPostChange($Entity, $Members) {
    }

    /**
     * descHere
     *
     * @param  \FrameworkDSW\DataObjects\IEntity $Entity
     */
    public function signalPostDelete($Entity) {
    }

    /**
     * descHere
     *
     * @param  \FrameworkDSW\DataObjects\IEntity $Entity
     */
    public function signalPreAdd($Entity) {
    }

    /**
     * descHere
     *
     * @param  \FrameworkDSW\DataObjects\IEntity $Entity
     * @param $Members array
     */
    public function signalPreChange($Entity, $Members) {
    }

    /**
     * descHere
     *
     * @param  \FrameworkDSW\DataObjects\IEntity $Entity
     */
    public function signalPreDelete($Entity) {
    }

}

/**
 * \FrameworkDSW\DataObjects\TObjectQuery
 * param <T: ?>
 * extends \FrameworkDSW\Linq\IExpressibleOrderedQueryable<T: T>
 *
 * @author 许子健
 */
class TObjectQuery extends TObject implements IExpressibleOrderedQueryable {

    /**
     *
     * @var \FrameworkDSW\DataObjects\TObjectContext
     */
    private $FContext = null;

    /**
     *
     * @var \FrameworkDSW\Linq\Expressions\TTypedExpression <T: T>
     */
    private $FExpression = null;

    /**
     *
     * @var \FrameworkDSW\Containers\TList <T: \FrameworkDSW\Expressions\TExpression>
     */
    private $FArguments = null;

    /**
     * desc
     */
    private function PrepareArguments() {
        if ($this->FArguments === null) {
            TList::PrepareGeneric(['T' => TExpression::class]);
            $this->FArguments = new TList(5);
        }
        else {
            $this->FArguments->Clear();
        }
    }

    /**
     *
     * @param array $Method
     * @param mixed $ReturnType
     * @param boolean $UseArguments
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    private function MakeCallExpression($Method, /** @noinspection PhpUnusedParameterInspection */
                                        $ReturnType, $UseArguments = false) {
        $this->EnsureExpression();
        if ($UseArguments) {
            $mExpression = TExpression::Call($this->FExpression->getBody(), $Method, $this->FArguments, [
                IQueryable::class => ['T' => $this->GenericArg('T')]]);
        }
        else {
            $this->FArguments->Clear();
            $mExpression = TExpression::Call($this->FExpression->getBody(), $Method, null, [
                IQueryable::class => ['T' => $this->GenericArg('T')]]);
        }

        TList::PrepareGeneric(['T' => TParameterExpression::class]);
        $mParameters = new TList();
        $mParameters->Add(TExpression::Parameter('t', $this->GenericArg('T')));
        TExpression::PrepareGeneric([
            'T' => [IIterator::class => ['T' => $this->GenericArg('T')]]]);
        $this->FExpression = TExpression::TypedLambda($mExpression, $mParameters);

        return $this;
    }

    /**
     *
     * @throws \FrameworkDSW\System\EException
     */
    private function EnsureExpression() {
        if ($this->FExpression === null) {
            TList::PrepareGeneric(['T' => TParameterExpression::class]);
            $mParameters = new TList();
            $mParameters->Add(TExpression::Parameter('t', $this->ObjectType()));
            TExpression::PrepareGeneric([
                'T' => [
                    IIterator::class => ['T' => $this->GenericArg('T')]]]);
            $this->FExpression = TExpression::TypedLambda(TExpression::Parameter('t', $this->ObjectType()), $mParameters);
        }
    }

    /**
     * desc
     *
     * @return T
     */
    private function MakeDefault() {
        switch ($this->GenericArg('T')) {
            case Framework::Integer :
                return 0;
            case Framework::Float :
                return 0.0;
            case Framework::String :
                return '';
            case Framework::Boolean :
                return false;
            default :
                if (strrpos($this->GenericArg('T'), ']', -1) !== false) {
                    return null;
                }
                else {
                    return null;
                }
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\DataObjects\TObjectContext $Context
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Expression <T: T>
     */
    public function __construct($Context, $Expression = null) {
        parent::__construct();

        TType::Object($Context, TObjectContext::class);
        TType::Object($Expression, [
            TTypedExpression::class => ['T' => $this->GenericArg('T')]]);

        $this->FContext    = $Context;
        $this->FExpression = $Expression;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \FrameworkDSW\System\TObject::Destroy()
     */
    public function Destroy() {
        Framework::Free($this->FArguments); //FIXME !!!!!!!!!!
        Framework::Free($this->FExpression);
        parent::Destroy();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Expression <T: \FrameworkDSW\Linq\TAggregateDelegate<A: A, N: T>>
     * @param A $Seed
     * @return A
     */
    public function Aggregate($Expression, $Seed = null) {
        TType::Object($Expression, [
            TTypedExpression::class => [
                'T' => [
                    TAggregateDelegate::class => [
                        'A' => $this->GenericArg('A'),
                        'N' => $this->GenericArg('T')]]]]);
        TType::Object($Seed, $this->GenericArg('A'));

        $this->EnsureExpression();

        /**@var $mDelegate callback* */
        $mDelegate = $Expression->TypedCompile();
        foreach ($this->FExpression as $mElement) {
            $Seed = $mDelegate($Seed, $mElement);
        }

        return $Seed;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return boolean
     */
    public function All($Predicate) {
        TType::Object($Predicate, [
            TTypedExpression::class => [
                'T' => [
                    TPredicateDelegate::class => [
                        'E' => $this->GenericArg('T')]]]]);

        $this->EnsureExpression();

        /**@var $mDelegate callback* */
        $mDelegate = $Predicate->TypedCompile();
        foreach ($this->FExpression as $mElement) {
            if (!$mDelegate($mElement)) {
                return false;
            }
        }

        return true;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return boolean
     */
    public function Any($Predicate = null) {
        TType::Object($Predicate, [
            TTypedExpression::class => [
                'T' => [
                    TPredicateDelegate::class => [
                        'E' => $this->GenericArg('T')]]]]);

        $this->EnsureExpression();

        if ($Predicate === null) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ($this->FExpression as $mElement) {
                return true;
            }

            return false;
        }
        else {
            /**@var $mDelegate callback* */
            $mDelegate = $Predicate->TypedCompile();
            foreach ($this->FExpression as $mElement) {
                if ($mDelegate($mElement)) {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Selector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: D>>
     * @return D
     */
    public function Average($Selector = null) {
        TType::Object($Selector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('T'),
                                                 'D' => $this->GenericArg('D')]]]]);

        $this->EnsureExpression();

        $mResult = 0;
        $mCount  = 0;
        if ($Selector === null) {
            foreach ($this->FExpression as $mElement) {
                $mResult += $mElement;
                ++$mCount;
            }
        }
        else {
            /**@var $mDelegate callback* */
            $mDelegate = $Selector->TypedCompile();
            foreach ($this->FExpression as $mElement) {
                $mResult += $mDelegate($mElement);
                ++$mCount;
            }
        }
        $mResult /= $mCount;
        TType::Type($mResult, $this->GenericArg('D'));

        return $mResult;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: T>
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Concat($With) {
        TType::Object($With, [
            IIteratorAggregate::class => ['T' => $this->GenericArg('T')]]);

        $this->PrepareArguments();
        $this->FArguments->Add(TExpression::Constant($With));

        return $this->MakeCallExpression([TObjectQuery::class, 'Concat'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]], true);
    }

    /**
     * descHere
     *
     * @param T $Item
     * @return boolean
     */
    public function Contains($Item) {
        TType::Object($Item, $this->GenericArg('T'));

        $this->EnsureExpression();

        foreach ($this->FExpression as $mElement) {
            if ($mElement === $Item) {
                return true;
            }
        }

        return false;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return integer
     */
    public function Count($Predicate = null) {
        TType::Object($Predicate, [
            TTypedExpression::class => [
                'T' => [
                    TPredicateDelegate::class => [
                        'E' => $this->GenericArg('T')]]]]);

        $this->EnsureExpression();

        $mCount = 0;
        if ($Predicate === null) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ($this->FExpression as $mElement) {
                ++$mCount;
            }
        }
        else {
            /**@var $mDelegate callback* */
            $mDelegate = $Predicate->TypedCompile();
            foreach ($this->FExpression as $mElement) {
                if ($mDelegate($mElement)) {
                    ++$mCount;
                }
            }
        }

        return $mCount;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function DefaultIfEmpty() {
        return $this->MakeCallExpression([TObjectQuery::class,
            'DefaultIfEmpty'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]]);
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Distinct() {
        return $this->MakeCallExpression([TObjectQuery::class, 'Distinct'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]]);
    }

    /**
     * descHere
     *
     *
     * @param integer $Index
     * @throws \FrameworkDSW\Containers\EIndexOutOfBounds
     * @return T
     */
    public function ElementAt($Index) {
        TType::Int($Index);

        $this->EnsureExpression();
        $mCount = 0;
        foreach ($this->FExpression as $mElement) {
            if ($Index == $mCount) {
                return $mElement;
            }
            ++$mCount;
        }
        throw new EIndexOutOfBounds(); // TODO detail desc needed.
    }

    /**
     * descHere
     *
     * @param integer $Index
     * @return T
     */
    public function ElementAtOrDefault($Index) {
        TType::Int($Index);

        $this->EnsureExpression();
        $mCount = 0;
        foreach ($this->FExpression as $mElement) {
            if ($Index == $mCount) {
                return $mElement;
            }
            ++$mCount;
        }

        return $this->MakeDefault();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: T>
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Except($With) {
        TType::Object($With, [
            IIteratorAggregate::class => ['T' => $this->GenericArg('T')]]);

        $this->PrepareArguments();
        $this->FArguments->Add($With);

        return $this->MakeCallExpression([TObjectQuery::class, 'Except'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @throws \FrameworkDSW\Containers\ENoSuchElement
     * @return T
     */
    public function First($Predicate = null) {
        TType::Object($Predicate, [
            TTypedExpression::class => [
                'T' => [
                    TPredicateDelegate::class => [
                        'E' => $this->GenericArg('T')]]]]);

        $this->EnsureExpression();

        if ($Predicate === null) {
            foreach ($this->FExpression as $mElement) {
                return $mElement;
            }
        }
        else {
            /**@var $mDelegate callback* */
            $mDelegate = $Predicate->TypedCompile();
            foreach ($this->FExpression as $mElement) {
                if ($mDelegate($mElement)) {
                    return $mElement;
                }
            }
        }
        throw new ENoSuchElement(); // TODO detail exception type needed.
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return T
     */
    public function FirstOrDefault($Predicate = null) {
        try {
            return $this->First($Predicate);
        }
        catch (ENoSuchElement $Ex) { // TODO sync type with the method First.
            return $this->MakeDefault();
        }
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getCommandText() {
    }

    /**
     * descHere
     *
     * @return TObjectContext
     */
    public function getContext() {
        return $this->FContext;
    }

    /**
     * descHere
     *
     * @return mixed
     */
    public function getElementType() {
        return $this->GenericArg('T');
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getExpression() {
        $this->EnsureExpression();

        return $this->FExpression;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryProvider
     */
    public function getProvider() {
        return $this->FContext->getQueryProvider();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $KeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ElementSelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: E>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ResultSelector <T: \FrameworkDSW\Linq\TGroupByCallbackDelegate: <K: K, C: \FrameworkDSW\Containers\IIteratorAggregate<T: E>, R: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function GroupBy($KeySelector, $ElementSelector = null, $ResultSelector = null) {
        TType::Object($KeySelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('S'),
                                                 'D' => $this->GenericArg('K')]]]]);
        TType::Object($ElementSelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('S'),
                                                 'D' => $this->GenericArg('E')]]]]);
        TType::Object($ResultSelector, [
            TTypedExpression::class => [
                'T' => [
                    TGroupByCallbackDelegate::class => [
                        'K' => $this->GenericArg('K'),
                        'C' => [
                            IIteratorAggregate::class => [
                                'T' => $this->GenericArg('E')]]]],
                'R' => $this->GenericArg('R')]]);

        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);
        $this->FArguments->Add($ElementSelector);
        $this->FArguments->Add($ResultSelector);

        return $this->MakeCallExpression([TObjectQuery::class, 'GroupBy'], [
            IQueryable::class => ['T' => $this->GenericArg('R')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $Inner <T: I>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $OuterKeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $InnerKeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: I, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ResultSelector <T: \FrameworkDSW\Linq\TGroupByCallbackDelegate<K: T, C: \FrameworkDSW\Containers\IIteratorAggregate<T: I>, R: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function GroupJoin($Inner, $OuterKeySelector, $InnerKeySelector, $ResultSelector) {
        TType::Object($Inner, [
            IIteratorAggregate::class => ['T' => $this->GenericArg('I')]]);
        TType::Object($OuterKeySelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('T'),
                                                 'D' => $this->GenericArg('K')]]]]);
        TType::Object($InnerKeySelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('I'),
                                                 'D' => $this->GenericArg('K')]]]]);
        TType::Object($ResultSelector, [
            TTypedExpression::class => [
                'T' => [
                    TGroupByCallbackDelegate::class => [
                        'K' => $this->GenericArg('K'),
                        'C' => [
                            IIteratorAggregate::class => [
                                'T' => $this->GenericArg('I')]],
                        'R' => $this->GenericArg('R')]]]]);

        $this->PrepareArguments();
        $this->FArguments->Add($Inner);
        $this->FArguments->Add($OuterKeySelector);
        $this->FArguments->Add($InnerKeySelector);
        $this->FArguments->Add($ResultSelector);

        return $this->MakeCallExpression([TObjectQuery::class, 'GroupJoin'], [
            IQueryable::class => ['T' => $this->GenericArg('R')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: T>
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Intersect($With) {
        TType::Object($With, [
            IIteratorAggregate::class => ['T' => $this->GenericArg('T')]]);
        $this->PrepareArguments();
        $this->FArguments->Add($With);

        return $this->MakeCallExpression([TObjectQuery::class, 'Intersect'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]], true);
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Containers\IIterator <T: T>
     */
    public function Iterator() {
        $this->EnsureExpression();
        $this->FContext->getQueryProvider()->PrepareMethodGeneric([
            'R' => [IIterator::class => ['T' => $this->GenericArg('T')]]]);

        return $this->FContext->getQueryProvider()->Execute($this->FExpression);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $Inner <T: I>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $OuterKeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $InnerKeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: I, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ResultSelector <T: \FrameworkDSW\Linq\TJoinCallbackDelegate<O: O, I: T, R: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function Join($Inner, $OuterKeySelector, $InnerKeySelector, $ResultSelector = null) {
        TType::Object($Inner, [
            IIteratorAggregate::class => ['T' => $this->GenericArg('I')]]);
        TType::Object($OuterKeySelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('T'),
                                                 'D' => $this->GenericArg('K')]]]]);
        TType::Object($InnerKeySelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('I'),
                                                 'D' => $this->GenericArg('K')]]]]);
        TType::Object($ResultSelector, [
            TTypedExpression::class => [
                'T' => [
                    TJoinCallbackDelegate::class => [
                        'O' => $this->GenericArg('O'),
                        'I' => $this->GenericArg('T'),
                        'R' => $this->GenericArg('R')]]]]);

        $this->PrepareArguments();
        $this->FArguments->Add($Inner);
        $this->FArguments->Add($OuterKeySelector);
        $this->FArguments->Add($InnerKeySelector);
        $this->FArguments->Add($ResultSelector);

        return $this->MakeCallExpression([TObjectQuery::class, 'Join'], [
            IQueryable::class => ['T' => $this->GenericArg('R')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @throws \FrameworkDSW\Containers\ENoSuchElement
     * @throws \Exception|\FrameworkDSW\Containers\ENoSuchElement
     * @return T
     */
    public function Last($Predicate = null) {
        TType::Object($Predicate, [
            TTypedExpression::class => [
                'T' => [
                    TPredicateDelegate::class => [
                        'E' => $this->GenericArg('T')]]]]);

        $this->EnsureExpression();
        if ($Predicate === null) {
            try {
                $this->FExpression->getIterator()->rewind();
                $this->FExpression->getIterator()->next();
            }
            catch (ENoSuchElement $Ex) {
                throw $Ex;
            }
            foreach ($this->FExpression as $mElement) {
                $mResult = $mElement;
            }
        }
        else {
            /**@var $mDelegate callback* */
            $mDelegate = $Predicate->TypedCompile();
            $mFound    = false;
            foreach ($this->FExpression as $mElement) {
                if ($mDelegate($mElement)) {
                    $mResult = $mElement;
                    $mFound  = true;
                }
            }
            if (!$mFound) {
                throw new ENoSuchElement();
            }
        }

        /** @noinspection PhpUndefinedVariableInspection */

        return $mResult;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return T
     */
    public function LastOrDefault($Predicate = null) {
        try {
            return $this->Last($Predicate);
        }
        catch (ENoSuchElement $Ex) {
            return $this->MakeDefault();
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Selector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: R>>
     * @throws \FrameworkDSW\Utilities\EInvalidTypeCasting
     * @return R
     */
    public function Max($Selector = null) {
        TType::Object($Selector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('T'),
                                                 'D' => $this->GenericArg('R')]]]]);
        if ($this->GenericArg('R') != Framework::Integer || $this->GenericArg('R') != Framework::Float) {
            throw new EInvalidTypeCasting(); // TODO exception type needed.
        }

        $this->EnsureExpression();
        if ($Selector === null) {
            $mMax = $this->FExpression->getIterator()->current();
            foreach ($this->FExpression as $mElement) {
                if ($mElement > $mMax) {
                    $mMax = $mElement;
                }
            }
        }
        else {
            /**@var $mDelegate callback* */
            $mDelegate = $Selector->TypedCompile();
            $mMax      = $mDelegate($this->FExpression->getIterator()->current());
            foreach ($this->FExpression as $mElement) {
                if ($mDelegate($mElement) > $mMax) {
                    $mMax = $mElement;
                }
            }
        }

        return $mMax;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Selector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: R>>
     * @throws \FrameworkDSW\Utilities\EInvalidTypeCasting
     * @return R
     */
    public function Min($Selector = null) {
        TType::Object($Selector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('T'),
                                                 'D' => $this->GenericArg('R')]]]]);
        if ($this->GenericArg('R') != Framework::Integer || $this->GenericArg('R') != Framework::Float) {
            throw new EInvalidTypeCasting(); // TODO exception type needed.
        }

        $this->EnsureExpression();
        if ($Selector === null) {
            $mMin = $this->FExpression->getIterator()->current();
            foreach ($this->FExpression as $mElement) {
                if ($mElement < $mMin) {
                    $mMin = $mElement;
                }
            }
        }
        else {
            /**@var $mDelegate callback* */
            $mDelegate = $Selector->TypedCompile();
            $mMin      = $mDelegate($this->FExpression->getIterator()->current());
            foreach ($this->FExpression as $mElement) {
                if ($mDelegate($mElement) < $mMin) {
                    $mMin = $mElement;
                }
            }
        }

        return $mMin;
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function OfType() {
        return $this->MakeCallExpression([TObjectQuery::class, 'OfType'], [
            IQueryable::class => ['T' => $this->GenericArg('R')]]);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $KeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @return \FrameworkDSW\Linq\IExpressibleOrderedQueryable <T: T>
     */
    public function OrderBy($KeySelector) {
        TType::Object($KeySelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('T'),
                                                 'D' => $this->GenericArg('K')]]]]);
        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);

        return $this->MakeCallExpression([TObjectQuery::class, 'OrderBy'], [
            IExpressibleOrderedQueryable::class => [
                'T' => $this->GenericArg('T')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $KeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @return \FrameworkDSW\Linq\IExpressibleOrderedQueryable <T: T>
     */
    public function OrderByDescending($KeySelector) {
        TType::Object($KeySelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('T'),
                                                 'D' => $this->GenericArg('K')]]]]);
        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);

        return $this->MakeCallExpression([TObjectQuery::class,
            'OrderByDescending'], [
            IExpressibleOrderedQueryable::class => [
                'T' => $this->GenericArg('T')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Expression <T: \FrameworkDSW\Linq\TAggregateDelegate<A: A, N: T>>
     * @param A $Seed
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Callback <T: \FrameworkDSW\Linq\TAggregateCallbackDelegate<A: A, R: R>>
     * @return R
     */
    public function ProcessedAggregate($Expression, $Seed, $Callback) {
        TType::Object($Callback, [
            TTypedExpression::class => [
                TAggregateCallbackDelegate::class => [
                    'A' => $this->GenericArg('A'), 'R' => $this->GenericArg('R')]]]);
        /**@var $mDelegate callback* */
        $mDelegate = $Callback->TypedCompile();

        return $mDelegate($this->Aggregate($Expression, $Seed));
    }

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Reverse() {
        return $this->MakeCallExpression([TObjectQuery::class, 'Reverse'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]]);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Selector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: \FrameworkDSW\Containers\TPair<K: integer, V: T>, D: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function Select($Selector) {
        TType::Object($Selector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => [
                        'S' => [
                            TPair::class => ['K' => Framework::Integer,
                                             'V' => $this->GenericArg('T')]],
                        'D' => $this->GenericArg('R')]]]]);

        $this->PrepareArguments();
        $this->FArguments->Add($Selector);

        return $this->MakeCallExpression([TObjectQuery::class, 'Select'], [
            IQueryable::class => ['T' => $this->GenericArg('R')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $CollectionSelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: \FrameworkDSW\Containers\TPair<K: integer, V: T>, D: \FrameworkDSW\Containers\IIteratorAggregate<T: C>>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ResultSelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: \FrameworkDSW\Containers\TPair<K: T, V: C>, R: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function SelectMany($CollectionSelector, $ResultSelector) {
        TType::Object($CollectionSelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => [
                        'S' => [
                            TPair::class => ['K' => Framework::Integer,
                                             'V' => $this->GenericArg('T')]],
                        'D' => [
                            IIteratorAggregate::class => [
                                'T' => $this->GenericArg('C')]]]]]]);
        TType::Object($ResultSelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => [
                        'S' => [
                            TPair::class => ['K' => $this->GenericArg('T'),
                                             'V' => $this->GenericArg('C')]],
                        'R' => $this->GenericArg('R')]]]]);

        $this->PrepareArguments();
        $this->FArguments->Add($CollectionSelector);
        $this->FArguments->Add($ResultSelector);

        return $this->MakeCallExpression([TObjectQuery::class, 'SelectMany'], [
            IQueryable::class => ['T' => $this->GenericArg('R')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: T>
     * @return boolean
     */
    public function SequenceEqual($With) {
        TType::Object($With, [
            IIteratorAggregate::class => ['T' => $this->GenericArg('T')]]);

        $this->EnsureExpression();

        $mSource      = $this->FExpression->getIterator();
        $mDestination = $With->getIterator();

        $mSource->rewind();
        $mDestination->rewind();

        while ($mSource->valid() && $mDestination->valid()) {
            if ($mSource->current() !== $mDestination->current()) {
                return false;
            }
            $mSource->next();
            $mDestination->next();
        }
        if ($mSource->valid() || $mDestination->valid()) {
            return false;
        }

        return true;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @throws \FrameworkDSW\System\EException
     * @return T
     */
    public function Single($Predicate = null) {
        TType::Object($Predicate, [
            TTypedExpression::class => [
                'T' => [
                    TPredicateDelegate::class => [
                        'E' => $this->GenericArg('T')]]]]);

        $this->EnsureExpression();
        $mIterator = $this->FExpression->getIterator();
        $mIterator->rewind();

        $mResult = null;
        if ($Predicate === null) {
            $mIterator->next();
            $mResult = $mIterator->current();
            $mIterator->next();
            if ($mIterator->valid()) {
                throw new EException(); // TODO not a singleton collection!
            }
        }
        else {
            $mFound = false;
            /**@var $mDelegate callback* */
            $mDelegate = $Predicate->TypedCompile();
            while ($mIterator->valid()) {
                $mIterator->next();
                if ($mFound) {
                    throw new EException(); // not a singleton collection!
                }
                $mCurrent = $mIterator->current();
                if ($mDelegate($mCurrent)) {
                    $mResult = $mCurrent;
                    $mFound  = true;
                }
            }
        }

        return $mResult;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return T
     */
    public function SingleOrDefault($Predicate = null) {
        try {
            return $this->Single($Predicate);
        }
        catch (EException $Ex) {
            return $this->MakeDefault();
        }
    }

    /**
     * descHere
     *
     * @param integer $Count
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Skip($Count) {
        TType::Int($Count);

        $this->PrepareArguments();
        $this->FArguments->Add(TExpression::Constant(new TInteger($Count)));

        return $this->MakeCallExpression([TObjectQuery::class, 'Skip'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Selector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: R>>
     * @return R
     */
    public function Sum($Selector = null) {
        TType::Object($Selector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('T'),
                                                 'D' => $this->GenericArg('R')]]]]);

        $this->EnsureExpression();

        $mResult = 0;
        if ($Selector === null) {
            foreach ($this->FExpression as $mElement) {
                $mResult += $mElement;
            }
        }
        else {
            $mDelegate = $Selector->TypedCompile();
            foreach ($this->FExpression as $mElement) {
                /** @var \FrameworkDSW\System\TDelegate $mDelegate \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: R> */
                $mResult += $mDelegate($mElement);
            }
        }
        TType::Type($mResult, $this->GenericArg('R'));

        return $mResult;
    }

    /**
     * descHere
     *
     * @param $Count integer
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Take($Count) {
        TType::Int($Count);

        $this->PrepareArguments();
        $this->FArguments->Add(TExpression::Constant(new TInteger($Count)));

        return $this->MakeCallExpression([TObjectQuery::class, 'Take'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Condition <T: \FrameworkDSW\Linq\TPredicateDelegate<E: \FrameworkDSW\Containers\TPair<K: integer, V: T>>>
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function TakeWhile($Condition) {
        TType::Object($Condition, [
            TTypedExpression::class => [
                'T' => [
                    TPredicateDelegate::class => [
                        'E' => [
                            TPair::class => ['K' => Framework::Integer,
                                             'V' => $this->GenericArg('T')]]]]]]);

        $this->PrepareArguments();
        $this->FArguments->Add($Condition);

        return $this->MakeCallExpression([TObjectQuery::class, 'TakeWhile'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $KeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @return \FrameworkDSW\Linq\IExpressibleOrderedQueryable <T: T>
     */
    public function ThenBy($KeySelector) {
        TType::Object($KeySelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('S'),
                                                 'D' => $this->GenericArg('K')]]]]);

        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);

        return $this->MakeCallExpression([TObjectQuery::class, 'ThenBy'], [
            IExpressibleOrderedQueryable::class => ['T' => $this->GenericArg('T')]]);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $KeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @return \FrameworkDSW\Linq\IExpressibleOrderedQueryable <T: T>
     */
    public function ThenByDescending($KeySelector) {
        TType::Object($KeySelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => ['S' => $this->GenericArg('S'),
                                                 'D' => $this->GenericArg('K')]]]]);

        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);

        return $this->MakeCallExpression([TObjectQuery::class,
            'ThenByDescending'], [
            IExpressibleOrderedQueryable::class => ['T' => $this->GenericArg('T')]]);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: T>
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Union($With) {
        TType::Object($With, [
            IIteratorAggregate::class => ['T' => $this->GenericArg('T')]]);

        $this->PrepareArguments();
        $this->FArguments->Add($With);

        return $this->MakeCallExpression([TObjectQuery::class, 'Union'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]]);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Condition <T: \FrameworkDSW\Linq\TPredicateDelegate<E: \FrameworkDSW\Containers\TPair<K: integer, V: T>>>
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Where($Condition) {
        TType::Object($Condition, [
            TTypedExpression::class => [
                'T' => [
                    TPredicateDelegate::class => [
                        'E' => [
                            TPair::class => ['K' => Framework::Integer,
                                             'V' => $this->GenericArg('T')]]]]]]);

        $this->PrepareArguments();
        $this->FArguments->Add($Condition);

        return $this->MakeCallExpression([TObjectQuery::class, 'Where'], [
            IQueryable::class => ['T' => $this->GenericArg('T')]], true);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: Q>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ResultSelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: \FrameworkDSW\Containers\TPair<K: T, V: Q>, D: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function Zip($With, $ResultSelector) {
        TType::Object($With, [
            IIteratorAggregate::class => ['T' => $this->GenericArg('Q')]]);
        TType::Object($ResultSelector, [
            TTypedExpression::class => [
                'T' => [
                    TSelectorDelegate::class => [
                        'S' => [
                            TPair::class => ['K' => $this->GenericArg('T'),
                                             'V' => $this->GenericArg('Q')]],
                        'D' => $this->GenericArg('R')]]]]);

        $this->PrepareArguments();
        $this->FArguments->Add($With);
        $this->FArguments->Add($ResultSelector);

        return $this->MakeCallExpression([TObjectQuery::class, 'Zip'], [
            IQueryable::class => ['T' => $this->GenericArg('R')]]);
    }
}