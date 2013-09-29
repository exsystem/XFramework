<?php
/**
 * \FrameworkDSW\Linq
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 30
 */
namespace FrameworkDSW\Linq;

use FrameworkDSW\System\IDelegate;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\Containers\IIteratorAggregate;
use FrameworkDSW\System\TObject;

/**
 * \FrameworkDSW\Linq\TAggregateDelegate
 * params <A: ?, N: ?>
 *
 * @author 许子健
 */
interface TAggregateDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param A $Accumulate
     * @param N $Next
     * @return A
     */
    public function Invoke($Accumulate, $Next);
}

/**
 * \FrameworkDSW\Linq\TAggregateCallbackDelegate
 * params <A: ?, R: ?>
 *
 * @author 许子健
 */
interface TAggregateCallbackDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param A $Accumulate
     * @return R
     */
    public function Invoke($Accumulate);
}

/**
 * \FrameworkDSW\Linq\TPredicateDelegate
 * params <E: ?>
 *
 * @author 许子健
 */
interface TPredicateDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param E $Element
     * @return boolean
     */
    public function Invoke($Element);
}

/**
 * \FrameworkDSW\Linq\TSelectorDelegate
 * params <S: ?, D: ?>
 *
 * @author 许子健
 */
interface TSelectorDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param S $Source
     * @return D
     */
    public function Invoke($Source);
}

/**
 * \FrameworkDSW\Linq\TGroupByCallbackDelegate
 * params <K: ?, C: ?, R: ?>
 *
 * @author 许子健
 */
interface TGroupByCallbackDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param K $Key
     * @param C $Collection
     * @return R
     */
    public function Invoke($Key, $Collection);

}

/**
 * \FrameworkDSW\Linq\TJoinCallbackDelegate
 * params <O: ?, I: ?, R: ?>
 *
 * @author 许子健
 */
interface TJoinCallbackDelegate extends IDelegate {

    /**
     * descHere
     *
     * @param O $Outer
     * @param I $Inner
     * @return R
     */
    public function Invoke($Outer, $Inner);
}

/**
 * \FrameworkDSW\Linq\IQueryProvider
 *
 * @author 许子健
 */
interface IQueryProvider extends IInterface {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Expression <T: T>
     * @return IQueryable <T: T>
     */
    public function CreateQuery($Expression = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Expression <T: R>
     * @return R
     */
    public function Execute($Expression);
}

/**
 * \FrameworkDSW\Linq\IQueryable
 * params <T: ?>
 * extends \FrameworkDSW\Containers\IIteratorAggregate<T: T>
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
     * @return \FrameworkDSW\Linq\Expressions\TExpression
     */
    public function getExpression();

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryProvider
     */
    public function getProvider();

}

/**
 * \FrameworkDSW\Linq\IExpressibleQueryable
 * params <T: ?>
 * extends \FrameworkDSW\Linq\IQueryable<T: T>
 *
 * @author 许子健
 */
interface IExpressibleQueryable extends IQueryable {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Expression <T: \FrameworkDSW\Linq\TAggregateDelegate<A: A, N: T>>
     * @param A $Seed
     * @return A
     */
    public function Aggregate($Expression, $Seed = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return boolean
     */
    public function All($Predicate);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return boolean
     */
    public function Any($Predicate = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Selector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: D>>
     * @return D
     */
    public function Average($Selector = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: T>
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Concat($With);

    /**
     * descHere
     *
     * @param T $Item
     * @return boolean
     */
    public function Contains($Item);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return integer
     */
    public function Count($Predicate = null);

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function DefaultIfEmpty();

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Distinct();

    /**
     * descHere
     *
     * @param integer $Index
     * @return T
     */
    public function ElementAt($Index);

    /**
     * descHere
     *
     * @param integer $Index
     * @return T
     */
    public function ElementAtOrDefault($Index);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: T>
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Except($With);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return T
     */
    public function First($Predicate = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return T
     */
    public function FirstOrDefault($Predicate = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $KeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ElementSelector <T: TSelectorDelegate<S: T, D: E>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ResultSelector <T: \FrameworkDSW\Linq\TGroupByCallbackDelegate: <K: K, C: \FrameworkDSW\Linq\IIteratorAggregate<T: E>, R: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function GroupBy($KeySelector, $ElementSelector = null, $ResultSelector = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $Inner <T: I>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $OuterKeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $InnerKeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: I, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ResultSelector \FrameworkDSW\Linq\TGroupByCallbackDelegate<K: T, C: \FrameworkDSW\Containers\IIteratorAggregate<T: I>, R: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function GroupJoin($Inner, $OuterKeySelector, $InnerKeySelector, $ResultSelector);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: T>
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Intersect($With);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $Inner <T: I>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $OuterKeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $InnerKeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: I, D: K>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ResultSelector <T: \FrameworkDSW\Linq\TJoinCallbackDelegate<O: O, I: T, R: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function Join($Inner, $OuterKeySelector, $InnerKeySelector, $ResultSelector = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return T
     */
    public function Last($Predicate = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
     * @return T
     */
    public function LastOrDefault($Predicate = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Selector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: R>>
     * @return R
     */
    public function Max($Selector = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Selector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: R>>
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
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $KeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function OrderBy($KeySelector);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $KeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @return \FrameworkDSW\Linq\IExpressibleOrderedQueryable <T: T>
     */
    public function OrderByDescending($KeySelector);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Expression <T: \FrameworkDSW\Linq\TAggregateDelegate<A: A, N: T>>
     * @param A $Seed
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Callback <T: \FrameworkDSW\Linq\TAggregateCallbackDelegate<A: A, R: R>>
     * @return R
     */
    public function ProcessedAggregate($Expression, $Seed, $Callback);

    /**
     * descHere
     *
     * @return \FrameworkDSW\Linq\IQueryable <T: T>
     */
    public function Reverse();

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Selector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: \FrameworkDSW\Containers\TPair<K: integer, V: T>, D: R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function Select($Selector);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $CollectionSelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: TPair<K: integer, V: T>, D: \FrameworkDSW\Containers\IIteratorAggregate<T: C>>>
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $ResultSelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: \FrameworkDSW\Containers\TPair<K: T, V: C>, R>>
     * @return \FrameworkDSW\Linq\IQueryable <T: R>
     */
    public function SelectMany($CollectionSelector, $ResultSelector);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Containers\IIteratorAggregate $With <T: T>
     * @return boolean
     */
    public function SequenceEqual($With);

    /**
     * descHere
     *
     * @param $Predicate \FrameworkDSW\Linq\Expressions\TTypedExpression
     *            <T: TPredicate<E: T>>
     * @return T
     */
    public function Single($Predicate = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Predicate <T: \FrameworkDSW\Linq\TPredicateDelegate<E: T>>
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
     * @param \FrameworkDSW\Linq\TSelectorDelegate $Selector <S: T, D: R>
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
     * @param  \FrameworkDSW\Linq\Expressions\TTypedExpression $Condition \FrameworkDSW\Linq\TPredicateDelegate<E: \FrameworkDSW\Containers\TPair<K: integer, V: T>>>
     * @return IQueryable <T: T>
     */
    public function TakeWhile($Condition);

    /**
     * descHere
     *
     * @param  \FrameworkDSW\Linq\Expressions\TTypedExpression $KeySelector <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
     * @return IExpressibleOrderedQueryable <T: T>
     */
    public function ThenBy($KeySelector);

    /**
     * descHere
     *
     * @param $KeySelector \FrameworkDSW\Linq\Expressions\TTypedExpression
     *            <T: \FrameworkDSW\Linq\TSelectorDelegate<S: T, D: K>>
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
     * @param \FrameworkDSW\Linq\Expressions\TTypedExpression $Condition <T: TPredicateDelegate<E: TPair<K: integer, V: T>>>
     * @return IQueryable <T: T>
     */
    public function Where($Condition);

    /**
     * descHere
     *
     * @param $With IIteratorAggregate
     *            <T: Q>
     * @param $ResultSelector \FrameworkDSW\Linq\Expressions\TTypedExpression
     *            <T: TSelector<S: TPair<K: T, V: Q>, D: R>>
     * @return IQueryable <T: R>
     */
    public function Zip($With, $ResultSelector);

}

/**
 * \FrameworkDSW\Linq\IExpressibleOrderedQueryable
 * params <T: ?>
 * extends IQueryable<T: T>
 *
 * @author 许子健
 */
interface IExpressibleOrderedQueryable extends IExpressibleQueryable {

}