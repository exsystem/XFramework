<?php
/**
 * Linq_Expressions.php
 *
 * @author 许子健
 * @version $Id$
 * @since separate file since reversion 43
 */

/**
 * TMysqlQueryProvider
 *
 * @author 许子健
 */
final class TMysqlQueryProvider extends TExpressionVisitor implements IQueryProvider {
    // PRINCIPLE
    // - quote everything by yourself.
    // - add spaces or anything as splits such as ',' and '`' by yourself so
    // that others do not need to add those splits before their output.
    // - in LinqToMysql: IIteratorAggregate<T> MUST BE IExpressibleQueryable<T>,
    // or at least IQueryable<T>, so you can handle methods like Concat.

    // TODO add private static method for generating IDs for different tables in
    // different object queries which may be combined, like t0, t1, t2 and so
    // on.

    /**
     *
     * @var IQueryable <T: T>
     */
    private $FQuery = null;
    /**
     *
     * @var TMap <K: string, V: string>
     */
    private static $FVarAliasMapping = null;
    /**
     *
     * @var TList <T: string>
     */
    private $FMembers = null;
    /**
     *
     * @var TMap <K: string, V: IParam<T: ?>>
     */
    private static $FParameters = null;
    /**
     *
     * @var integer
     */
    private static $FParameterNameCounter = 0;
    /**
     *
     * @var string
     */
    private $FSql = '';
    /**
     *
     * @var string
     */
    private $FAlias = '';

    /**
     *
     * @var string
     */
    private $FSelect = '';
    /**
     *
     * @var string
     */
    private $FFrom = '';
    /**
     *
     * @var string
     */
    private $FWhere = '';
    /**
     *
     * @var string
     */
    private $FGroupBy = '';
    /**
     *
     * @var string
     */
    private $FHaving = '';
    /**
     *
     * @var string
     */
    private $FOrderBy = '';
    /**
     *
     * @var string
     */
    private $FUnion = '';
    /**
     *
     * @var string
     */
    private $FLimit = '';
    /**
     *
     * @var boolean
     */
    private $FDefaultIfEmpty = false;
    /**
     *
     * @var boolean
     */
    private $FDistinct = false;

    /**
     * desc
     */
    public function __construct() {
        parent::__construct();

        if (self::$FParameters == null) {
            // TODO how to handle <T: ? > -- i can not put ? and > together
            // since
            // the combination is the ending mark of php. Or just omit it?
            TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'IParam'));
            self::$FParameters = new TMap(true);
        }

        if (self::$FVarAliasMapping == null) {
            TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'string'));
            self::$FVarAliasMapping = new TMap();
        }

        TList::PrepareGeneric(array ('T' => 'string'));
        $this->FMembers = new TList();
    }

    /**
     * (non-PHPdoc)
     *
     * @see TObject::__destruct()
     */
    public function __destruct() {
        Framework::Free($this->FMembers);

        parent::__destruct();
    }

    /**
     * descHere
     *
     * @param $Expression TTypedExpression
     *            <T: T>
     * @return IQueryable <T: T>
     */
    public function CreateQuery($Expression = null) {
        TObjectQuery::PrepareGeneric(array ('T' => $this->GenericArg('T')));
        return new TObjectQuery($Expression);
    }

    /**
     * descHere
     *
     * @param $Expression TTypedExpression
     *            <T: R>
     * @return R
     */
    public function Execute($Expression) {
        TType::Object($Expression, array (
            'TTypedExpression' => array ('T' => $this->GenericArg('R'))));

        $this->Visit($Expression);
        // TODO execute the query...dont forget param binding.

        self::$FParameters->Clear();
        self::$FParameterNameCounter = 0;
        // ...generating result here.
        // ...binding variables with aliases according to FVarAliasMapping.
        self::$FVarAliasMapping->Clear();
        $this->FMembers->Clear();
        // ...returing the result object.
    }

    /**
     * descHere
     *
     * @param $Expression TBinaryExpression
     * @return TExpression
     */
    protected function VisitBinary($Expression) {
        if ($Expression->getNodeType() == TExpressionType::ePower()) {
            $this->FSql .= 'POW(';
            $this->Visit($Expression->getLeft());
            $this->FSql .= ', ';
            $this->Visit($Expression->getRight());
            $this->FSql .= ')';
            return $Expression;
        }
        else {
            $this->FSql .= '(';
            $this->Visit($Expression->getLeft());
            switch ($Expression->getNodeType()) {
                case TExpressionType::eAdd() :
                case TExpressionType::eAddChecked() :
                    $this->FSql .= ' + ';
                    break;
                case TExpressionType::eSubtract() :
                case TExpressionType::eSubtractChecked() :
                    $this->FSql .= ' - ';
                    break;
                case TExpressionType::eMultiply() :
                case TExpressionType::eMultiplyChecked() :
                    $this->FSql .= ' * ';
                    break;
                case TExpressionType::eDivide() :
                    $this->FSql .= ' / ';
                    break;
                case TExpressionType::eGreaterThan() :
                    $this->FSql .= ' > ';
                    break;
                case TExpressionType::eLessThan() :
                    $this->FSql .= ' < ';
                    break;
                case TExpressionType::eGreaterThanOrEqual() :
                    $this->FSql .= ' >= ';
                    break;
                case TExpressionType::eLessThanOrEqual() :
                    $this->FSql .= ' <= ';
                    break;
                case TExpressionType::eModulo() :
                    $this->FSql .= ' % ';
                    break;
                case TExpressionType::eAnd() :
                    $this->FSql .= ' & ';
                    break;
                case TExpressionType::eOr() :
                    $this->FSql .= ' | ';
                    break;
                case TExpressionType::eExclusiveOr() :
                    $this->FSql .= ' ^ ';
                    break;
                case TExpressionType::eLeftShift() :
                    $this->FSql .= ' << ';
                    break;
                case TExpressionType::eRightShift() :
                    $this->FSql .= ' >> ';
                    break;
                case TExpressionType::eAndAlso() :
                    $this->FSql .= ' AND ';
                    break;
                case TExpressionType::eOrElse() :
                    $this->FSql .= ' OR ';
                    break;
                case TExpressionType::eAssign() :
                    $this->FSql .= ' = ';
                    break;
            }
            $this->Visit($Expression->getRight());
            $this->FSql .= ')';
            return $Expression;
        }

        throw new EException(); // TODO wrong type such as arrayIndex nodes.
    }

    /**
     * descHere
     *
     * @param $Expression TConditionalExpression
     * @return TExpression
     */
    protected function VisitConditional($Expression) {
        $this->FSql .= 'IF(';
        $this->Visit($Expression->getTest());
        $this->FSql .= ', ';
        $this->Visit($Expression->getIfTrue());
        $this->FSql .= ', ';
        $this->Visit($Expression->getIfFlase());
        $this->FSql .= ')';

        return $Expression;
    }

    /**
     * descHere
     *
     * @param $Expression TConstantExpression
     * @return TExpression
     */
    protected function VisitConstant($Expression) {
        $this->FSql .= ":param_{$this->FParameterNameCounter}";

        TPrimativeParam::PrepareGeneric(array ('T' => $Expression->getType()));
        $mParameter = new TPrimativeParam($Expression->getValue());
        self::$FParameters->Put(":param_{$this->FParameterNameCounter}", $mParameter);
        self::$FParameterNameCounter++;

        return $Expression;
    }

    /**
     * descHere
     *
     * @param $Expression TDefaultExpression
     * @return TExpression
     */
    protected function VisitDefault($Expression) {
        $this->FSql .= ":param_{$this->FParameterNameCounter}";

        TPrimativeParam::PrepareGeneric(array ('T' => $Expression->getType()));
        $mValues = array ('boolean' => false, 'integer' => 0, 'float' => 0.0,
            'string' => '');
        $mParameter = new TPrimativeParam($mValues[$Expression->getType()]);
        self::$FParameters->Put(":param_{$this->FParameterNameCounter}", $mParameter);
        self::$FParameterNameCounter++;

        return $Expression;
    }

    /**
     * descHere
     *
     * @param $Expression TLambdaExpression
     * @return TExpression
     */
    protected function VisitLambda($Expression) {
        // TODO ...things to do.
        return $Expression;
    }

    /**
     * descHere
     *
     * @param $Expression TMemberExpression
     * @return TExpression
     */
    protected function VisitMember($Expression) {
        $this->Visit($Expression->getExpression());

        if ($this->FAlias == '') { // sql
            $this->FSql .= ".`{$Expression->getMember()}`";
            $this->FMembers->Add("`{$Expression->getMember()}`");
        }
        else { // php
            self::$FVarAliasMapping[$this->FAlias] .= "->{$Expression->getMember()}";
        }

        return $Expression;
    }

    /**
     * descHere
     *
     * @param $Expression TMethodCallExpression
     * @return TExpression
     */
    protected function VisitMethodCall($Expression) {
        $mMethod = $Expression->getMethod();
        $mArgs = $Expression->getArguments();

        if ($mMethod === array ('TObjectQuery', 'Average')) {
            $this->FAlias = "col{$this->FParameterNameCounter}";
            ++self::$FParameterNameCounter;
            self::$FVarAliasMapping->Put($this->FAlias, '');
            $this->FSql = 'AVG(';
            $this->Visit($mArgs[0]);
            $this->FSql .= ") AS `{$this->FAlias}`";
            $this->FSelect = $this->FSql;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Concat')) { // UNION ALL
            $this->FSql = 'ALL ';
            $this->Visit($mArgs[0]); // TODO [solved--using static] how to sync
                                     // parameters and aliases & variables
                                     // infomation?
            $this->FUnion = $this->FSql;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Count')) {
            $this->FAlias = 'col' . self::$FParameterNameCounter;
            ++self::$FParameterNameCounter;
            self::$FVarAliasMapping->Put($this->FAlias, '');
            if ($this->FDistinct) {
                $this->FSql = 'COUNT(DISTINCT ';
            }
            else {
                $this->FSql = 'COUNT(';
            }
            $this->Visit($mArgs[0]);
            $this->FSql .= ") AS `{$this->FAlias}`";
            $this->FSelect = $this->FSql;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'DefaultIfEmpty')) {
            $this->FDefaultIfEmpty = true;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Distinct')) {
            $this->FDistinct = true;
            return $Expression;
        }

        // SELECT DISTINCT t.key, ... FROM T AS t LEFT JOIN S AS s USING (key,
        // ...) WHERE s.key IS NULL
        if ($mMethod === array ('TObjectQuery', 'Except')) {
            $this->FDistinct = true;
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            $mMembers = $this->FMembers->ToArray();
            $mFirstMember = $mMembers[0];
            $mMembers = implode(', ', $mMembers);
            $mCounter = self::$FParameterNameCounter;
            ++self::$FParameterNameCounter;
            $this->FFrom = "LEFT JOIN ({$this->FSql}) AS `i{$mCounter}` USING ({$mMembers})";
            if ($this->FWhere != '') {
                $this->FWhere .= ' AND ';
            }
            $this->FWhere .= "(`i{$mCounter}`.`{$mFirstMember}` IS NULL)";
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'First')) {
            $this->FLimit = '1';
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            if ($this->FWhere != '') {
                $this->FWhere .= ' AND ';
            }
            $this->FWhere .= "({$this->FSql})";
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'FirstOrDefault')) {
            $this->FDefaultIfEmpty = true;
            $this->FLimit = '1';
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            if ($this->FWhere != '') {
                $this->FWhere = "({$this->FSql}) ";
            }
            else {
                $this->FWhere .= "AND ({$this->FSql})";
            }
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'GroupBy')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            $this->FGroupBy = $this->FSql;

            $this->FSql = '';
            $this->Visit($mArgs[1]);
            if ($this->FWhere != '') {
                $this->FWhere = "({$this->FSql}) ";
            }
            else {
                $this->FWhere .= "AND ({$this->FSql})";
            }

            $this->FSql = '';
            $this->Visit($mArgs[2]);
            $this->FHaving = $this->FSql;

            return $Expression;
        }

        // SELECT ... FROM T AS t JOIN S AS s ON t.key = s.key GROUP BY t.key
        // HAVING result_selector
        if ($mMethod === array ('TObjectQuery', 'GroupJoin')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]); // Inner --yields something such as
                                     // '(SELECT ...) AS s' or 'S AS s'
            $this->FFrom = "JOIN {$this->FSql} ON ";
            $this->FSql = '';
            $this->Visit($mArgs[1]); // OuterKeySelector
            $this->FGroupBy = $this->FSql;
            $this->FSql .= ' = ';
            $this->Visit($mArgs[2]); // InnerKeySelector
            $this->FFrom .= "{$this->FSql} ";
            $this->FSql = '';
            $this->Visit($mArgs[3]); // ResultKeySelector
            $this->FHaving = $this->FSql;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Intersect')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            $mMembers = implode(', ', $this->FMembers->ToArray());
            $this->FFrom = "INNER JOIN ({$this->FSql}) USING ({$mMembers})";
            return $Expression;
        }

        // SELECT ... FROM T as t JOIN S AS s ON t.key = s.key HAVING
        // result_selector
        if ($mMethod === array ('TObjectQuery', 'Join')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            $this->FFrom = "JOIN {$this->FSql} ON ";
            $this->FSql = '';
            $this->Visit($mArgs[1]); // OuterKeySelector
            $this->FSql .= ' = ';
            $this->Visit($mArgs[2]); // InnerKeySelector
            $this->FFrom .= "{$this->FSql} ";
            $this->FSql = '';
            $this->Visit($mArgs[3]); // ResultKeySelector
            $this->FHaving = $this->FSql;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Last')) {
            throw new EException(); // TODO unsupported --Last
        }

        if ($mMethod === array ('TObjectQuery', 'LastOrDefault')) {
            throw new EException(); // TODO unsupported --LastOrDefault
        }

        if ($mMethod === array ('TObjectQuery', 'Max')) {
            $this->FAlias = 'col' . self::$FParameterNameCounter;
            ++self::$FParameterNameCounter;
            self::$FVarAliasMapping->Put($this->FAlias, '');
            $this->FSql = 'MAX(';
            $this->Visit($mArgs[0]);
            $this->FSql .= ") AS `{$this->FAlias}`";
            $this->FSelect = $this->FSql;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Min')) {
            $this->FAlias = 'col' . self::$FParameterNameCounter;
            ++self::$FParameterNameCounter;
            self::$FVarAliasMapping->Put($this->FAlias, '');
            $this->FSql = 'MIN(';
            $this->Visit($mArgs[0]);
            $this->FSql .= ") AS `{$this->FAlias}`";
            $this->FSelect = $this->FSql;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'OfType')) {
            throw new EException(); // TODO unsupported --OfType
        }

        if ($mMethod === array ('TObjectQuery', 'OrderBy')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            $this->FOrderBy = $this->FSql;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'OrderByDescending')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            $this->FOrderBy = "{$this->FSql} DESC";
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Reverse')) {
            throw new EException(); // TODO unsupported. --Reverse
        }

        if ($mMethod === array ('TObjectQuery', 'Select')) {
            if ($this->FDistinct) {
                $this->FSql = 'DISTINCT ';
            }
            else {
                $this->FSql = '';
            }
            $this->Visit($mArgs[0]);
            $this->FSelect = $this->FSql;
            return $Expression;
        }

        // SELECT s.key, ... FROM T AS t INNER JOIN S AS s ON T.s = s.key
        if ($mMethod === array ('TObjectQuery', 'SelectMany')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]); // modify FSelect directly.
            $this->FFrom = $this->FSql;
            $this->FSql = '';
            $this->Visit($mArgs[1]);
            if ($this->FWhere != '') {
                $this->FWhere = "({$this->FSql}) ";
            }
            else {
                $this->FWhere .= "AND ({$this->FSql})";
            }

            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Skip')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            $this->FLimit = "{$this->FSql}, -1";
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Sum')) {
            $this->FAlias = 'col' . self::$FParameterNameCounter;
            ++self::$FParameterNameCounter;
            self::$FVarAliasMapping->Put($this->FAlias, '');
            $this->FSql = 'SUM(';
            $this->Visit($mArgs[0]);
            $this->FSql .= ") AS `{$this->FAlias}`";
            $this->FSelect = $this->FSql;
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Take')) {
            $this->FSql = '';
            $mTemp = strstr($this->FLimit, ' ', true); // search ' ' for
                                                       // including ','
            if ($mTemp !== false) {
                $this->FLimit = "{$mTemp} ";
            }
            else {
                $this->FLimit = '';
            }
            $this->Visit($mArgs[0]);
            $this->FLimit .= "{$this->FSql}";
            return $Expression;
        }

        if (($mMethod === array ('TObjectQuery', 'TakeWhile') || ($mMethod === array (
            'TObjectQuery', 'Where')))) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            if ($this->FWhere != '') {
                $this->FWhere = "({$this->FSql}) ";
            }
            else {
                $this->FWhere .= "AND ({$this->FSql})";
            }

            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'ThenBy')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            $this->FOrderBy .= ", {$this->FSql}";
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'OrderByDescending')) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            $this->FOrderBy .= ", {$this->FSql} DESC";
            return $Expression;
        }

        if ($mMethod === array ('TObjectQuery', 'Zip')) {
            throw new EException(); // TODO unsupported. --Zip
        }

        throw new EException(); // TODO unsupported.
    }

    /**
     * descHere
     *
     * @param $Expression TParameterExpression
     * @return TExpression
     */
    protected function VisitParameter($Expression) {
        if ($this->FAlias == '') { // sql
            $this->FSql .= "`{$Expression->getName()}`";
        }
        else { // php
            self::$FVarAliasMapping[$this->FAlias] .= "${$Expression->getName()}";
        }

        return $Expression;
    }

    /**
     * descHere
     *
     * @param $Expression TUnaryExpression
     * @return TExpression
     */
    protected function VisitUnary($Expression) {
        if ($this->FAlias == '') { // sql
            switch ($Expression->getNodeType()) {
                case TExpressionType::eArrayLength() :
                    throw new EException(); // TODO unsupported operation.
                    break;
                case TExpressionType::eConvert() :
                case TExpressionType::eConvertChecked() :
                    $mMapping = array ('string' => 'CHAR', 'float' => 'DECIMAL',
                        'integer' => 'SIGNED');
                    if (array_key_exists($Expression->getType(), $mMapping)) {
                        throw new EException(); // TODO unsupported operation.
                    }

                    $this->FSql .= 'CONVERT (';
                    $this->Visit($Expression->getOperand());
                    $this->FSql .= ", {$mMapping[$Expression->getType()]})";
                    break;
                case TExpressionType::eNegate() :
                case TExpressionType::eNegateChecked() :
                    $this->FSql .= '(-';
                    $this->Visit($Expression->getOperand());
                    $this->FSql .= ')';
                    break;
                case TExpressionType::eNot() :
                    $this->FSql .= '(!';
                    $this->Visit($Expression->getOperand());
                    $mRightParenthesisSection->Data = ')';
                    break;
                case TExpressionType::eTypeAs() :
                    throw new EException(); // TODO unsupported operation.
                case TExpressionType::eUnaryPlus() :
                    $this->Visit($Expression->getOperand());
                    break;
            }
        }
        else { // php
            switch ($Expression->getNodeType()) {
                case TExpressionType::eArrayLength() :
                    self::$FVarAliasMapping[$this->FAlias] .= 'count(';
                    $this->Visit($Expression->getOperand());
                    self::$FVarAliasMapping[$this->FAlias] .= ')';
                    break;
                case TExpressionType::eConvert() :
                case TExpressionType::eConvertChecked() :
                    self::$FVarAliasMapping[$this->FAlias] .= "({$Expression->getType()}) (";
                    $this->Visit($Expression->getOperand());
                    self::$FVarAliasMapping[$this->FAlias] .= ') ';
                    break;
                case TExpressionType::eNegate() :
                case TExpressionType::eNegateChecked() :
                    self::$FVarAliasMapping[$this->FAlias] .= '(-';
                    $this->Visit($Expression->getOperand());
                    self::$FVarAliasMapping[$this->FAlias] .= ')';
                    break;
                case TExpressionType::eNot() :
                    self::$FVarAliasMapping[$this->FAlias] .= '(!';
                    $this->Visit($Expression->getOperand());
                    self::$FVarAliasMapping[$this->FAlias] .= ')';
                    break;
                case TExpressionType::eTypeAs() :
                case TExpressionType::eUnaryPlus() :
                    $this->Visit($Expression->getOperand());
                    break;
            }
        }

        return $Expression;
    }
}