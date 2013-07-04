<?php
/**
 * Linq.php
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 30
 */
namespace FrameworkDSW\Linq;
require_once 'FrameworkDSW/System.php';
require_once 'FrameworkDSW/Containers.php';
use FrameworkDSW\System\IDelegate;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\Containers\IIteratorAggregate;
use FrameworkDSW\System\TObject;

/**
 * TAggregateDelegate
 * params <A, N>
 *
 * @author 许子健
 */
interface TAggregateDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param $Accumulate A
     * @param $Next N
     * @return A
     */
    public function Invoke($Accumulate, $Next);
}

/**
 * TAggregateCallbackDelegate
 * params <A, R>
 *
 * @author 许子健
 */
interface TAggregateCallbackDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param $Accumulate A
     * @return R
     */
    public function Invoke($Accumulate);
}

/**
 * TPredicateDelegate
 * params <E>
 *
 * @author 许子健
 */
interface TPredicateDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param $Element E
     * @return boolean
     */
    public function Invoke($Element);
}

/**
 * TSelectorDelegate
 * params <S, D>
 *
 * @author 许子健
 */
interface TSelectorDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param $Source S
     * @return D
     */
    public function Invoke($Source);
}

/**
 * TGroupByCallbackDelegate
 * params <K, C, R>
 *
 * @author 许子健
 */
interface TGroupByCallbackDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param $Key K
     * @param $Collection C
     * @return R
     */
    public function Invoke($Key, $Collection);

}

/**
 * TJoinCallbackDelegate
 * params <O, I, R>
 *
 * @author 许子健
 */
interface TJoinCallbackDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param $Outer O
     * @param $Inner I
     * @return R
     */
    public function Invoke($Outer, $Inner);
}

/**
 * IQueryProvider
 *
 * @author 许子健
 */
interface IQueryProvider extends IInterface {

    /**
     * descHere
     *
     * @param $Expression TTypedExpression
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function CreateQuery($Expression = null);

    /**
     * descHere
     *
     * @param $Expression TTypedExpression
     *            <T: R>
     * @return R
     */
    public function Execute($Expression);
}

/**
 * IQueryable
 * params <T>
 * extends IIteratorAggregate<T>
 *
 * @author 许子健
 */
interface IQueryable extends IIteratorAggregate {

    /**
     * descHere
     *
     * @return mixed
     */
    public function getElementType();

    /**
     * descHere
     *
     * @return TExpression
     */
    public function getExpression();

    /**
     * descHere
     *
     * @return IQueryProvider
     */
    public function getProvider();

}

/**
 * IExpressibleQueryable
 * params <T>
 * extends IQueryable<T>
 *
 * @author 许子健
 */
interface IExpressibleQueryable extends IQueryable {

    /**
     * descHere
     *
     * @param $Expression TTypedExpression
     *            <T: TAggregateDelegate<A: A, N: T>>
     * @param $Seed A
     * @return A
     */
    public function Aggregate($Expression, $Seed = null);

    /**
     * descHere
     *
     * @param $Perdicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return boolean
     */
    public function All($Perdicate);

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return boolean
     */
    public function Any($Predicate = null);

    /**
     * descHere
     *
     * @param $Selector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: D>>
     * @return D
     */
    public function Average($Selector = null);

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function Concat($With);

    /**
     * descHere
     *
     * @param $Item T
     * @return boolean
     */
    public function Contains($Item);

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return integer
     */
    public function Count($Predicate = null);

    /**
     * descHere
     *
     * @return IQueryable <T: T>
     */
    public function DefaultIfEmpty();

    /**
     * descHere
     *
     * @return IQueryable <T: T>
     */
    public function Distinct();

    /**
     * descHere
     *
     * @param $Index integer
     * @return T
     */
    public function ElementAt($Index);

    /**
     * descHere
     *
     * @param $Index integer
     * @return T
     */
    public function ElementAtOrDefault($Index);

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function Except($With);

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function First($Predicate = null);

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function FirstOrDefault($Predicate = null);

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
    public function GroupBy($KeySelector, $ElementSelector = null, $ResultSelector = null);

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
     *            TGroupByCallbackDelegate<K: T, C: IIteratorAggregate<T: I>, R:
     *            R>>
     * @return IQueryable <T: R>
     */
    public function GroupJoin($Inner, $OuterKeySelector, $InnerKeySelector, $ResultSelector);

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function Intersect($With);

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
    public function Join($Inner, $OuterKeySelector, $InnerKeySelector, $ResultSelector = null);

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function Last($Predicate = null);

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicateDelegate<E: T>>
     * @return T
     */
    public function LastOrDefault($Predicate = null);

    /**
     * descHere
     *
     * @param $Selector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: R>>
     * @return R
     */
    public function Max($Selector = null);

    /**
     * descHere
     *
     * @param $Selector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: R>>
     * @return R
     */
    public function Min($Selector = null);

    /**
     * descHere
     *
     * @return IQueryable <T: R>
     */
    public function OfType();

    /**
     * descHere
     *
     * @param $KeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function OrderBy($KeySelector);

    /**
     * descHere
     *
     * @param $KeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function OrderByDescending($KeySelector);

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
    public function ProcessedAggregate($Expression, $Seed, $Callback);

    /**
     * descHere
     *
     * @return IQueryable <T: T>
     */
    public function Reverse();

    /**
     * descHere
     *
     * @param $Selector TTypedExpression<T:
     *            TSelectorDelegate<S: TPair<K: integer, V: T>, D: R>>
     * @return IQueryable <T: R>
     */
    public function Select($Selector);

    /**
     * descHere
     *
     * @param $CollectionSelector TTypedExpression
     *            <T: TSelectorDelegate<S: TPair<K: integer, V: T>, D:
     *            IIteratorAggregate<T: C>>>
     * @param $ResultSelector TTypedExpression<T:
     *            TSelectorDelegate<S: TPair<K: T, V: C>, R>>
     * @return IQueryable <T: R>
     */
    public function SelectMany($CollectionSelector, $ResultSelector);

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return boolean
     */
    public function SequenceEqual($With);

    /**
     * descHere
     *
     * @param $Predicate TTypedExpression
     *            <T: TPredicate<E: T>>
     * @return T
     */
    public function Single($Predicate = null);

    /**
     * descHere
     *
     * @param $Predicate TypedExpression
     *            <T: TPredicate<E: T>>
     * @return T
     */
    public function SingleOrDefault($Predicate = null);

    /**
     * descHere
     *
     * @param $Count integer
     * @return IQueryable <T: T>
     */
    public function Skip($Count);

    /**
     * descHere
     *
     * @param $Selector TSelector
     *            <S: T, D: R>
     * @return R
     */
    public function Sum($Selector = null);

    /**
     * descHere
     *
     * @param $Count integer
     * @return IQueryable <T: T>
     */
    public function Take($Count);

    /**
     * descHere
     *
     * @param $Condition TTypedExpression
     *            TPredicate<E: TPair<K: integer, V: T>>>
     * @return IQueryable <T: T>
     */
    public function TakeWhile($Condition);

    /**
     * descHere
     *
     * @param $KeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function ThenBy($KeySelector);

    /**
     * descHere
     *
     * @param $KeySelector TTypedExpression
     *            <T: TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function ThenByDescending($KeySelector);

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function Union($With);

    /**
     * descHere
     *
     * @param $Condition TTypedExpression<T:
     *            TPredicate<E: TPair<K: integer, V: T>>>
     * @return IQueryable <T: T>
     */
    public function Where($Condition);

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: Q>
     * @param $ResultSelector TTypedExpression
     *            <T: TSelector<S: TPair<K: T, V: Q>, D: R>>
     * @return IQueryable <T: R>
     */
    public function Zip($With, $ResultSelector);

}

/**
 * IExpressibleOrderedQueryable
 * params <T>
 * extends IQueryable<T>
 *
 * @author 许子健
 */
interface IExpressibleOrderedQueryable extends IExpressibleQueryable {

}