<?php
/**
 * Linq.php
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 30
 */

/**
 * IQueryable
 * extends IIteratorAggregate<T>
 * param <T>
 * 
 * @author 许子健
 */
interface IQueryable extends IIteratorAggregate {

    /**
     *
     * @return mixed
     */
    public function getElementType();

    /**
     *
     * @return TExpression
     */
    public function getExpression();

    /**
     *
     * @return IQueryProvider
     */
    public function getProvider();

}