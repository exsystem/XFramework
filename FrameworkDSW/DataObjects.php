<?php
/**
 * DataObjects.php
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 30
 */

/**
 * IEntity
 *
 * @author 许子健
 */
interface IEntity extends IInterface {

    /**
     * descHere
     *
     * @return TObjectContext
     */
    public function getContext();
}

/**
 * TObjectContext
 *
 * @author 许子健
 */
abstract class TObjectContext extends TObject {

    /**
     *
     * @var IQueryProvider
     */
    protected $FProvider = null;

    /**
     * descHere
     *
     * @param $Entity IEntity
     */
    protected abstract function DoAddObject($Entity);

    /**
     * descHere
     *
     * @param $Entity IEntity
     */
    protected abstract function DoDeleteObject($Entity);

    /**
     * descHere
     *
     * @param $QueryProvider IQueryProvider
     */
    public function __construct($QueryProvider) {
        parent::__construct();
        TType::Object($QueryProvider, 'IQueryProvider');
        $this->FProvider = $QueryProvider;
    }

    /**
     * descHere
     *
     *
     * @param $Entity IEntity
     */
    public function AddObject($Entity) {
        TType::Object($Entity, 'IEntity');

        TObject::Dispatch(array ($this, 'PreAdd'), array ($Entity));
        $this->DoAddObject($Entity);
        TObject::Dispatch(array ($this, 'PostAdd'), array ($Entity));
    }

    /**
     * descHere
     *
     * @return IQueryable <T: T>
     */
    public function CreateQuery() {
        $this->FProvider->PrepareMethodGeneric(array (
            'T' => $this->GenericArg('T')));
        return $this->FProvider->CreateQuery();
    }

    /**
     * descHere
     *
     * @param $Entity IEntity
     */
    public function DeleteObject($Entity) {
        TType::Object($Entity, 'IEntity');

        TObject::Dispatch(array ($this, 'PreDelete'), array ($Entity));
        $this->DoDeleteObject($Entity);
        TObject::Dispatch(array ($this, 'PostDelete'), array ($Entity));
    }

    /**
     * descHere
     *
     * @return IQueryProvider
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
     * @param $Entity IEntity
     */
    public function signalPostAdd($Entity) {
    }

    /**
     * descHere
     *
     * @param $Entity IEntity
     * @param $Members array
     */
    public function signalPostChange($Entity, $Members) {
    }

    /**
     * descHere
     *
     * @param $Entity IEntity
     */
    public function signalPostDelete($Entity) {
    }

    /**
     * descHere
     *
     * @param $Entity IEntity
     */
    public function signalPreAdd($Entity) {
    }

    /**
     * descHere
     *
     * @param $Entity IEntity
     * @param $Members array
     */
    public function signalPreChange($Entity, $Members) {
    }

    /**
     * descHere
     *
     * @param $Entity IEntity
     */
    public function signalPreDelete($Entity) {
    }

}

/**
 * TObjectQuery
 * param <T>
 * extends IExpressibleOrderedQueryable<T>
 *
 * @author 许子健
 */
class TObjectQuery extends TObject implements IExpressibleOrderedQueryable {

    /**
     *
     * @var TTypedExpression <T: T>
     */
    private $FExpression = null;

    /**
     *
     * @var TList <T: TExpression>
     */
    private $FArguments = null;

    /**
     * desc
     */
    private function PrepareArguments() {
        if ($this->FArguments == null) {
            TList::PrepareGeneric(array ('T' => 'TExpression'));
            $this->FArguments = new TList(5, true);
        }
        else {
            $this->FArguments->Clear();
        }
    }

    /**
     *
     * @param $Method array
     * @param $ReturnType mixed
     * @param $UseArguments boolean
     * @return IQueryable <T: T>
     */
    private function MakeCallExpression($Method, $ReturnType, $UseArguments = false) {
        $this->EnsureExpression();
        if ($UseArguments) {
            $mExpression = TExpression::Call($this->FExpression->getBody(), $Method, $this->FArguments, array (
                'IQueryable' => array ('T' => $this->GenericArg('T'))));
        }
        else {
            $this->FArguments->Clear();
            $mExpression = TExpression::Call($this->FExpression->getBody(), $Method, null, array (
                'IQueryable' => array ('T' => $this->GenericArg('T'))));
        }

        TList::PrepareGeneric(array ('T' => 'TParameterExpresion'));
        $mParameters = new TList();
        $mParameters->Add(TExpression::Parameter('t', $this->ObjectType()));
        TExpression::PrepareGeneric(array (
            'T' => array ('IList' => array ('T' => $this->GenericArg('T')))));
        $this->FExpression = TExpression::TypedLambda($mExpression, $mParameters);
        return $this;
    }

    /**
     *
     * @throws EException
     */
    private function EnsureExpression() {
        if ($this->FExpression == null) {
            TList::PrepareGeneric(array ('T' => 'TParameterExpresion'));
            $mParameters = new TList();
            $mParameters->Add(TExpression::Parameter('t', $this->ObjectType()));
            TExpression::PrepareGeneric(array (
                'T' => array ('IList' => array ('T' => $this->GenericArg('T')))));
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
            case 'integer' :
                return 0;
            case 'float' :
                return 0.0;
            case 'string' :
                return '';
            case 'boolean' :
                return false;
            case 'array' :
                return array ();
            default :
                return null;
        }
    }

    /**
     * descHere
     *
     * @param $Expression TTypedExpression
     *            <T: T>
     */
    public function __construct($Expression = null) {
        parent::__construct();

        TType::Object($Expression, array (
            'TTypedExpression' => array ('T' => $this->GenericArg('T'))));
        $this->FExpression = $Expression;
    }

    /**
     * (non-PHPdoc)
     *
     * @see TObject::__destruct()
     */
    public function __destruct() {
        Framework::Free($this->FArguments);
        Framework::Free($this->FExpression);
        parent::__destruct();
    }

    /**
     * descHere
     *
     * @param $Expression TTypedExpression
     *            <T: TAggregateDelegate<A: A, N: T>>
     * @param $Seed A
     * @return A
     */
    public function Aggregate($Expression, $Seed = null) {
        TType::Object($Expression, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TAggregateDelegate' => array (
                        'A' => $this->GenericArg('A'),
                        'N' => $this->GenericArg('T'))))));
        TType::Object($Seed, $this->GenericArg('A'));

        $this->EnsureExpression();

        $mDelegate = $Expression->TypedCompile();
        foreach ($this->FExpression as $mElement) {
            $Seed = $mDelegate($Seed, $mElement);
        }
        return $Seed;
    }

    /**
     * descHere
     *
     * @param $Perdicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return boolean
     */
    public function All($Perdicate) {
        TType::Object($Perdicate, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TPredicateDelegate' => array (
                        'E' => $this->GenericArg('T'))))));

        $this->EnsureExpression();

        $mDelegate = $Perdicate->TypedCompile();
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
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return boolean
     */
    public function Any($Predicate = null) {
        TType::Object($Predicate, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TPredicateDelegate' => array (
                        'E' => $this->GenericArg('T'))))));

        $this->EnsureExpression();

        if ($Predicate == null) {
            foreach ($this->FExpression as $mElement) {
                return true;
            }
            return false;
        }
        else {
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
     * @param $Selector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: D>>
     * @return D
     */
    public function Average($Selector = null) {
        TType::Object($Selector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('T'),
                        'D' => $this->GenericArg('D'))))));

        $this->EnsureExpression();

        $mResult = 0;
        $mCount = 0;
        if ($Selector == null) {
            foreach ($this->FExpression as $mElement) {
                $mResult += $mElement;
                ++$mCount;
            }
        }
        else {
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
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function Concat($With) {
        TType::Object($With, array (
            'IIteratorAggregate' => array ('T' => $this->GenericArg('T'))));

        $this->PrepareArguments();
        $this->FArguments->Add(TExpression::Constant($With));
        return $this->MakeCallExpression(array ('TObjectQuery', 'Concat'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))), true);
    }

    /**
     * descHere
     *
     * @param $Item T
     * @return boolean
     */
    public function Contains($Item) {
        TType::Object($Item, $this->GenericArg('T'));

        $this->EnsureExpression();

        foreach ($this->FExpression as $mElement) {
            if ($mElement == $Item) {
                return true;
            }
        }
        return false;
    }

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return integer
     */
    public function Count($Predicate = null) {
        TType::Object($Predicate, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TPredicateDelegate' => array (
                        'E' => $this->GenericArg('T'))))));

        $this->EnsureExpression();

        $mCount = 0;
        if ($Predicate == null) {
            foreach ($this->FExpression as $mElement) {
                ++$mCount;
            }
        }
        else {
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
     * @return IQueryable <T: T>
     */
    public function DefaultIfEmpty() {
        return $this->MakeCallExpression(array ('TObjectQuery',
            'DefaultIfEmpty'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))));
    }

    /**
     * descHere
     *
     * @return IQueryable <T: T>
     */
    public function Distinct() {
        return $this->MakeCallExpression(array ('TObjectQuery', 'Distinct'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))));
    }

    /**
     * descHere
     *
     *
     * @param $Index integer
     * @return T
     */
    public function ElementAt($Index) {
        TType::Object($Index, 'integer');

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
     * @param $Index integer
     * @return T
     */
    public function ElementAtOrDefault($Index) {
        TType::Object($Index, 'integer');

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
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function Except($With) {
        TType::Object($With, array (
            'IIteratorAggregate' => array ('T' => $this->GenericArg('T'))));

        $this->PrepareArguments();
        $this->FArguments->Add($With);
        return $this->MakeCallExpression(array ('TObjectQuery', 'Except'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))), true);
    }

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function First($Predicate = null) {
        TType::Object($Predicate, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TPredicateDelegate' => array (
                        'E' => $this->GenericArg('T'))))));

        $this->EnsureExpression();

        if ($Predicate == null) {
            foreach ($this->FExpression as $mElement) {
                return $mElement;
            }
        }
        else {
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
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function FirstOrDefault($Predicate = null) {
        try {
            $this->First($Predicate);
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
     * @return TExpression
     */
    public function getExpression() {
        $this->EnsureExpression();
        return $this->FExpression;
    }

    /**
     * descHere
     *
     * @return IQueryProvider
     */
    public function getProvider() {
        return $this->FContext->getQueryProvider();
    }

    /**
     * descHere
     *
     * @param $KeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @param $ElementSelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: E>>
     * @param $ResultSelector TTypedExpression
     *            <T: TGroupByCallbackDelegate: <K: K, C: IIteratorAggregate<T:
     *            E>, R: R>>
     * @return IQueryable <T: R>
     */
    public function GroupBy($KeySelector, $ElementSelector = null, $ResultSelector = null) {
        TType::Object($KeySelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('S'),
                        'D' => $this->GenericArg('K'))))));
        TType::Object($ElementSelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('S'),
                        'D' => $this->GenericArg('E'))))));
        TType::Object($ResultSelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TGroupByCallbackDelegate' => array (
                        'K' => $this->GenericArg('K'),
                        'C' => array (
                            'IIteratorAggregate' => array (
                                'T' => $this->GenericArg('E'))))),
                'R' => $this->GenericArg('R'))));

        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);
        $this->FArguments->Add($ElementSelector);
        $this->FArguments->Add($ResultSelector);
        return $this->MakeCallExpression(array ('TObjectQuery', 'GroupBy'), array (
            'IQueryable' => array ('T' => $this->GenericArg('R'))), true);
    }

    /**
     * descHere
     *
     * @param $Inner IIteratorAggregate
     *            <T: I>
     * @param $OuterKeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @param $InnerKeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: I, D: K>>
     * @param $ResultSelector TTypedExpression
     *            <T: TGroupByCallbackDelegate<K: T, C: IIteratorAggregate<T:
     *            I>, R: R>>
     * @return IQueryable <T: R>
     */
    public function GroupJoin($Inner, $OuterKeySelector, $InnerKeySelector, $ResultSelector) {
        TType::Object($Inner, array (
            'IIteratorAggregate' => array ('T' => $this->GenericArg('I'))));
        TType::Object($OuterKeySelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('T'),
                        'D' => $this->GenericArg('K'))))));
        TType::Object($InnerKeySelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('I'),
                        'D' => $this->GenericArg('K'))))));
        TType::Object($ResultSelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TGroupByCallbackDelegate' => array (
                        'K' => $this->GenericArg('K'),
                        'C' => array (
                            'IIteratorAggregate' => array (
                                'T' => $this->GenericArg('I'))),
                        'R' => $this->GenericArg('R'))))));

        $this->PrepareArguments();
        $this->FArguments->Add($Inner);
        $this->FArguments->Add($OuterKeySelector);
        $this->FArguments->Add($InnerKeySelector);
        $this->FArguments->Add($ResultSelector);
        return $this->MakeCallExpression(array ('TObjectQuery', 'GroupJoin'), array (
            'IQueryable' => array ('T' => $this->GenericArg('R'))), true);
    }

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function Intersect($With) {
        TType::Object($With, array (
            'IIteratorAggregate' => array ('T' => $this->GenericArg('T'))));
        $this->PrepareArguments();
        $this->FArguments->Add($With);
        return $this->MakeCallExpression(array ('TObjectQuery', 'Intersect'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))), true);
    }

    /**
     * descHere
     *
     * @return IIterator <T>
     */
    public function Iterator() {
        $this->EnsureExpression();
        $this->FContext->getQueryProvider()->PrepareGeneric(array (
            'R' => array ('IIterator' => array ('T' => $this->GenericArg('T')))));
        return $this->FContext->getQueryProvider()->Execute($this->FExpression);
    }

    /**
     * descHere
     *
     * @param $Inner IIteratorAggregate
     *            <T: I>
     * @param $OuterKeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @param $InnerKeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: I, D: K>>
     * @param $ResultSelector TTypedExpression
     *            <T: TJoinCallbackDelegate<O: O, I: T, R: R>>
     * @return IQueryable <T: R>
     */
    public function Join($Inner, $OuterKeySelector, $InnerKeySelector, $ResultSelector = null) {
        TType::Object($Inner, array (
            'IIteratorAggregate' => array ('T' => $this->GenericArg('I'))));
        TType::Object($OuterKeySelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('T'),
                        'D' => $this->GenericArg('K'))))));
        TType::Object($InnerKeySelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('I'),
                        'D' => $this->GenericArg('K'))))));
        TType::Object($ResultSelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TJoinCallbackDelegate' => array (
                        'O' => $this->GenericArg('O'),
                        'I' => $this->GenericArg('T'),
                        'R' => $this->GenericArg('R'))))));

        $this->PrepareArguments();
        $this->FArguments->Add($Inner);
        $this->FArguments->Add($OuterKeySelector);
        $this->FArguments->Add($InnerKeySelector);
        $this->FArguments->Add($ResultSelector);
        return $this->MakeCallExpression(array ('TObjectQuery', 'Join'), array (
            'IQueryable' => array ('T' => $this->GenericArg('R'))), true);
    }

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function Last($Predicate = null) {
        TType::Object($Predicate, array (
            'TTypedExpresion' => array (
                'T' => array (
                    'TPredicateDelegate' => array (
                        'E' => $this->GenericArg('T'))))));

        $this->EnsureExpression();
        if ($Predicate == null) {
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
            $mDelegate = $Predicate->TypedCompile();
            $mFound = false;
            foreach ($this->FExpression as $mElement) {
                if ($mDelegate($mElement)) {
                    $mResult = $mElement;
                    $mFound = true;
                }
            }
            if (!$mFound) {
                throw new ENoSuchElement();
            }
        }
        return $mResult;
    }

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function LastOrDefault($Predicate = null) {
        try {
            $this->Last($Predicate);
        }
        catch (ENoSuchElement $Ex) {
            return $this->MakeDefault();
        }
    }

    /**
     * descHere
     *
     * @param $Selector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: R>>
     * @return R
     */
    public function Max($Selector = null) {
        TType::Object($Selector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('T'),
                        'D' => $this->GenericArg('R'))))));
        if ($this->GenericArg('R') != 'integer' || $this->GenericArg('R') != 'float') {
            throw new EInvalidTypeCasting(); // TODO exception type needed.
        }

        $this->EnsureExpression();
        if ($Selector == null) {
            $mMax = $this->FExpression->getIterator()->current();
            foreach ($this->FExpression as $mElement) {
                if ($mElement > $mMax) {
                    $mMax = $mElement;
                }
            }
        }
        else {
            $mDelegate = $Selector->TypedCompile();
            $mMax = $mDelegate($this->FExpression->getIterator()->current());
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
     * @param $Selector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: R>>
     * @return R
     */
    public function Min($Selector = null) {
        TType::Object($Selector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('T'),
                        'D' => $this->GenericArg('R'))))));
        if ($this->GenericArg('R') != 'integer' || $this->GenericArg('R') != 'float') {
            throw new EInvalidTypeCasting(); // TODO exception type needed.
        }

        $this->EnsureExpression();
        if ($Selector == null) {
            $mMin = $this->FExpression->getIterator()->current();
            foreach ($this->FExpression as $mElement) {
                if ($mElement < $mMin) {
                    $mMin = $mElement;
                }
            }
        }
        else {
            $mDelegate = $Selector->TypedCompile();
            $mMin = $mDelegate($this->FExpression->getIterator()->current());
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
     * @return IQueryable <T: R>
     */
    public function OfType() {
        return $this->MakeCallExpression(array ('TObjectQuery', 'OfType'), array (
            'IQueryable' => array ('T' => $this->GenericArg('R'))));
    }

    /**
     * descHere
     *
     * @param $KeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function OrderBy($KeySelector) {
        TType::Object($KeySelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('T'),
                        'D' => $this->GenericArg('K'))))));
        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);
        return $this->MakeCallExpression(array ('TObjectQuery', 'OrderBy'), array (
            'IExpressibleOrderedQueryable' => array (
                'T' => $this->GenericArg('T'))), true);
    }

    /**
     * descHere
     *
     * @param $KeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function OrderByDescending($KeySelector) {
        TType::Object($KeySelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('T'),
                        'D' => $this->GenericArg('K'))))));
        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);
        return $this->MakeCallExpression(array ('TObjectQuery',
            'OrderByDescending'), array (
            'IExpressibleOrderedQueryable' => array (
                'T' => $this->GenericArg('T'))), true);
    }

    /**
     * descHere
     *
     * @param $Expression TTypedExpression
     *            <T: TAggregateDelegate<A: A, N: T>>
     * @param $Seed A
     * @param $Callback TTypedExpression
     *            <T: TAggregateCallbackDelegate<A: A, R: R>>
     * @return R
     */
    public function ProcessedAggregate($Expression, $Seed, $Callback) {
        TType::Object($Callback, array (
            'TTypedExpression' => array (
                'TAggregateCallbackDelegate' => array (
                    'A' => $this->GenericArg('A'), 'R' => $this->GenericArg('R')))));
        $mDelegate = $Callback->TypedCompile();
        return $mDelegate($this->Aggregate($Expression, $Seed));
    }

    /**
     * descHere
     *
     * @return IQueryable <T: T>
     */
    public function Reverse() {
        return $this->MakeCallExpression(array ('TObjectQuery', 'Reverse'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))));
    }

    /**
     * descHere
     *
     * @param $Selector TTypedExpression
     *            <T: TSelectorDelegate<S: TPair<K: integer, V: T>, D: R>>
     * @return IQueryable <T: R>
     */
    public function Select($Selector) {
        TType::Object($Selector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array (
                        'S' => array (
                            'TPair' => array ('K' => 'integer',
                                'V' => $this->GenericArg('T'))),
                        'D' => $this->GenericArg('R'))))));

        $this->PrepareArguments();
        $this->FArguments->Add($Selector);
        return $this->MakeCallExpression(array ('TObjectQuery', 'Select'), array (
            'IQueryable' => array ('T' => $this->GenericArg('R'))), true);
    }

    /**
     * descHere
     *
     * @param $CollectionSelector TTypedExpression
     *            <T: TSelectorDelegate<S: TPair<K: integer, V: T>, D:
     *            IIteratorAggregate<T: C>>>
     * @param $ResultSelector TTypedExpression
     *            <T: TSelectorDelegate<S: TPair<K: T, V: C>, R: R>>
     * @return IQueryable <T: R>
     */
    public function SelectMany($CollectionSelector, $ResultSelector) {
        TType::Object($CollectionSelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array (
                        'S' => array (
                            'TPair' => array ('K' => 'integer',
                                'V' => $this->GenericArg('T'))),
                        'D' => array (
                            'IIteratorAggregate' => array (
                                'T' => $this->GenericArg('C'))))))));
        TType::Object($ResultSelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array (
                        'S' => array (
                            'TPair' => array ('K' => $this->GenericArg('T'),
                                'V' => $this->GenericArg('C'))),
                        'R' => $this->GenericArg('R'))))));

        $this->PrepareArguments();
        $this->FArguments->Add($CollectionSelector);
        $this->FArguments->Add($ResultSelector);
        return $this->MakeCallExpression(array ('TObjectQuery', 'SelectMany'), array (
            'IQueryable' => array ('T' => $this->GenericArg('R'))), true);
    }

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return boolean
     */
    public function SequenceEqual($With) {
        TType::Object($With, array (
            'IIteratorAggregate' => array ('T' => $this->GenericArg('T'))));

        $this->EnsureExpression();

        $mSource = $this->FExpression->getIterator();
        $mDestination = $With->getIterator();

        $mSource->rewind();
        $mDestination->rewind();

        while ($mSource->valid() && $mDestination->valid()) {
            if ($mSource->current() != $mDestination->current()) {
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
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function Single($Predicate = null) {
        TType::Object($Predicate, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TPredicateDelegate' => array (
                        'E' => $this->GenericArg('T'))))));

        $this->EnsureExpression();
        $mIterator = $this->FExpression->getIterator();
        $mIterator->rewind();

        if ($Predicate == null) {
            $mIterator->next();
            $mResult = $mIterator->current();
            $mIterator->next();
            if ($mIterator->valid()) {
                throw new EException(); // TODO not a singleton collection!
            }
        }
        else {
            $mFound = false;
            $mDelegate = $Predicate->TypedCompile();
            while ($mIterator->valid()) {
                $mIterator->next();
                if ($mFound) {
                    throw new EException(); // not a singleton collection!
                }
                $mCurrent = $mIterator->current();
                if ($mDelegate($mCurrent)) {
                    $mResult = $mCurrent;
                    $mFound = true;
                }
            }
        }
    }

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function SingleOrDefault($Predicate = null) {
        try {
            $this->Single($Predicate);
        }
        catch (EException $Ex) {
            return $this->MakeDefault();
        }
    }

    /**
     * descHere
     *
     * @param $Count integer
     * @return IQueryable <T: T>
     */
    public function Skip($Count) {
        TType::Int($Count);

        $this->PrepareArguments();
        $this->FArguments->Add(TExpression::Constant(new TInteger($Count)));
        return $this->MakeCallExpression(array ('TObjectQuery', 'Skip'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))), true);
    }

    /**
     * descHere
     *
     * @param $Selector TSelectorDelegate
     *            <S: T, D: R>
     * @return R
     */
    public function Sum($Selector = null) {
        TType::Object($Selector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('T'),
                        'D' => $this->GenericArg('R'))))));

        $this->EnsureExpression();

        $mResult = 0;
        if ($Selector == null) {
            foreach ($this->FExpression as $mElement) {
                $mResult += $mElement;
            }
        }
        else {
            $mDelegate = $Selector->TypedCompile();
            foreach ($this->FExpression as $mElement) {
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
     * @return IQueryable <T: T>
     */
    public function Take($Count) {
        TType::Int($Count);

        $this->PrepareArguments();
        $this->FArguments->Add(TExpression::Constant(new TInteger($Count)));
        return $this->MakeCallExpression(array ('TObjectQuery', 'Take'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))), true);
    }

    /**
     * descHere
     *
     * @param $Condition TTypedExpression
     *            <T: TPredicate<E: TPair<K: integer, V: T>>>
     * @return IQueryable <T: T>
     */
    public function TakeWhile($Condition) {
        TType::Object($Condition, array (
            'TTypedExpresion' => array (
                'T' => array (
                    'TPredicateDelegate' => array (
                        'E' => array (
                            'TPair' => array ('K' => 'integer',
                                'V' => $this->GenericArg('T'))))))));

        $this->PrepareArguments();
        $this->FArguments->Add($Condition);
        return $this->MakeCallExpression(array ('TObjectQuery', 'TakeWhile'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))), true);
    }

    /**
     * descHere
     *
     * @param $KeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function ThenBy($KeySelector) {
        TType::Object($KeySelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('S'),
                        'D' => $this->GenericArg('K'))))));

        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);
        return $this->MakeCallExpression(array ('TObjectQuery', 'ThenBy'), array (
            'IExpressibleOrderedQuery' => array ('T' => $this->GenericArg('T'))));
    }

    /**
     * descHere
     *
     * @param $KeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function ThenByDescending($KeySelector) {
        TType::Object($KeySelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array ('S' => $this->GenericArg('S'),
                        'D' => $this->GenericArg('K'))))));

        $this->PrepareArguments();
        $this->FArguments->Add($KeySelector);
        return $this->MakeCallExpression(array ('TObjectQuery',
            'ThenByDescending'), array (
            'IExpressibleOrderedQuery' => array ('T' => $this->GenericArg('T'))));
    }

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function Union($With) {
        TType::Object($With, array (
            'IIteratorAggregate' => array ('T' => $this->GenericArg('T'))));

        $this->PrepareArguments();
        $this->FArguments->Add($With);
        return $this->MakeCallExpression(array ('TObjectQuery', 'Union'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))));
    }

    /**
     * descHere
     *
     * @param $Condition TTypedExpression
     *            <T: TPredicateDelegate<E: TPair<K: integer, V: T>>>
     * @return IQueryable <T: T>
     */
    public function Where($Condition) {
        TType::Object($Condition, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TPredicateDelegate' => array (
                        'E' => array (
                            'TPair' => array ('K' => 'integer',
                                'V' => $this->GenericArg('T'))))))));

        $this->PrepareArguments();
        $this->FArguments->Add($Condition);
        return $this->MakeCallExpression(array ('TObjectQuery', 'Where'), array (
            'IQueryable' => array ('T' => $this->GenericArg('T'))));
    }

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: Q>
     * @param $ResultSelector TTypedExpression<T:
     *            TSelectorDelegate<S: TPair<K: T, V: Q>, D: R>>
     * @return IQueryable <T: R>
     */
    public function Zip($With, $ResultSelector) {
        TType::Object($With, array (
            'IIteratorAggregate' => array ('T' => $this->GenericArg('Q'))));
        TType::Object($ResultSelector, array (
            'TTypedExpression' => array (
                'T' => array (
                    'TSelectorDelegate' => array (
                        'S' => array (
                            'TPair' => array ('K' => $this->GenericArg('T'),
                                'V' => $this->GenericArg('Q'))),
                        'D' => $this->GenericArg('R'))))));

        $this->PrepareArguments();
        $this->FArguments->Add($With);
        $this->FArguments->Add($ResultSelector);
        return $this->MakeCallExpression(array ('TObjectQuery', 'Zip'), array (
            'IQueryable' => array ('T' => $this->GenericArg('R'))));
    }

}
