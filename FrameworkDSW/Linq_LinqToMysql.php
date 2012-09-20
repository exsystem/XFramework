<?php
/**
 * Linq_Expressions.php
 *
 * @author 许子健
 * @version $Id$
 * @since separate file since reversion 43
 */

require_once 'FrameworkDSW/System.php';
require_once 'FrameworkDSW/Containers.php';
require_once 'FrameworkDSW/Linq.php';
require_once 'FrameworkDSW/Linq_Expressions.php';
require_once 'FrameworkDSW/Database.php';

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
     *
     * @var mixed
     */
    private $FEntityType = null;
    /**
     *
     * @var mixed
     */
    private $FResultType = null;
    /**
     *
     * @var string
     */
    private $FResultElementName = '';

    /**
     *
     * @var TMysqlConnection
     */
    private $FConnection = null;
    /**
     *
     * @var TObjectContext
     */
    private $FContext = null;

    /**
     *
     * @param TConstantExpression $SubQueryableExpression
     * @return string
     */
    private function GenerateSubQuerySql($SubQueryableExpression) {
        TType::Object($SubQueryableExpression, 'TConstantExpression');
        $mSubQueryProvider = new TMysqlQueryProvider();
        $mSubQuery = $SubQueryableExpression->getValue();
        TType::Object($mSubQuery, $this->FResultType);
        $mSubExpression = $mSubQuery->getExpression();

        $mDummy = $mSubExpression->getParameters();
        $mWithClass = $mDummy[0]->getType();
        if (!$mWithClass::InheritsFrom('IEntity')) {
            Framework::Free($mSubQueryProvider);
            throw new EInvalidClassCasting(); // TODO not an entity class
        }
        $mTableName = $mWithClass::getTableName(); // static string
                                                   // getTableName()
        $mSql = "`{$mTableName}` AS `{$mDummy[0]->getName()}`";

        if ($mSubExpression->getBody() !== null) {
            $mSubQueryProvider->Visit($mSubExpression);
            $mSql = "SELECT {$mSubQueryProvider->FSelect} FROM {$mSql}";
            if ($mSubQueryProvider->FWhere != '') {
                $mSql .= " WHERE {$mSubQueryProvider->FWhere}";
            }
            if ($mSubQueryProvider->FGroupBy != '') {
                $mSql .= " GROUP BY {$mSubQueryProvider->FGroupBy}";
            }
            if ($mSubQueryProvider->FHaving != '') {
                $mSql .= " HAVING {$mSubQueryProvider->FHaving}";
            }
            if ($mSubQueryProvider->FOrderBy != '') {
                $mSql .= " ORDER BY {$mSubQueryProvider->FOrderBy}";
            }
            if ($mSubQueryProvider->FLimit != '') {
                $mSql .= " LIMIT {$mSubQueryProvider->FLimit}";
            }
        }
        else {
            $mMembers = implode(', ', $this->FMembers->ToArray());
            $mSql = "SELECT {$mMembers} FROM {$mSql}";
        }
        Framework::Free($mSubQueryProvider);
        return "({$mSql})";
    }

    /**
     * desc
     */
    public function __construct() {
        parent::__construct();

        if (self::$FParameters === null) {
            // TODO how to handle <T: ? > -- i can not put ? and > together
            // since
            // the combination is the ending mark of php. Or just omit it?
            TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'IParam'));
            self::$FParameters = new TMap(true);
        }

        if (self::$FVarAliasMapping === null) {
            TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'string'));
            self::$FVarAliasMapping = new TMap();
        }

        TList::PrepareGeneric(array ('T' => 'string'));
        $this->FMembers = new TList();
    }

    /**
     * (non-PHPdoc)
     *
     * @see TObject::Destroy()
     */
    public function Destroy() {
        Framework::Free($this->FMembers);

        parent::Destroy();
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

        $mStmt = $this->FConnection->PrepareStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());

        $mDummy = $Expression->getParameters();
        $this->FEntityType = $mDummy[0]->getType();
        $mEntityType = $this->FEntityType;
        $mEntityName = $mEntityType::getTableName();

        $this->FResultType = $Expression->getReturnType();

        $mSql = "`{$mEntityName}` AS `{$mDummy[0]->getName()}`";
        if ($Expression->getBody() !== null) {
            $this->Visit($Expression);
            $mSql = "SELECT {$this->FSelect} FROM {$mSql}";
            if ($this->FWhere != '') {
                $mSql .= " WHERE {$this->FWhere}";
            }
            if ($this->FGroupBy != '') {
                $mSql .= " GROUP BY {$this->FGroupBy}";
            }
            if ($this->FHaving != '') {
                $mSql .= " HAVING {$this->FHaving}";
            }
            if ($this->FOrderBy != '') {
                $mSql .= " ORDER BY {$this->FOrderBy}";
            }
            if ($this->FLimit != '') {
                $mSql .= " LIMIT {$this->FLimit}";
            }
        }
        else {
            $mMembers = implode(', ', $this->FMembers->ToArray());
            $mSql = "SELECT {$mMembers} FROM {$mSql}";
        }

        $mStmt->setCommand($mSql);
        foreach (self::$FParameters as $mName => $mParameter) {
            $mStmt->BindParam($mName, $mParameter);
        }
        $mResultSet = $mStmt->Query();

        self::$FParameters->Clear();
        self::$FParameterNameCounter = 0;

        if (is_array($this->FResultType)) {
            TList::PrepareGeneric($this->FResultType['IQueryable']);
            $mClass = 'TList';
        }
        else {
            $mClass = $this->FResultType;
        }
        $mElementClass = $this->FResultType['IQueryable']['T'];
        $mResultCollection = new $mClass();

        if (in_array($mElementClass, array ('boolean', 'integer', 'float',
            'string'))) {
            foreach ($mResultSet as $mRow) {
                foreach (self::$FVarAliasMapping as $mAlias => $mVariable) {
                    $mResultCollection->Add($mRow[$mAlias]);
                }
            }
            if ($this->FDefaultIfEmpty && $mResultSet->getCount() == 0) {
                $mDefaults = array ('boolean' => false, 'integer' => 0,
                    'float' => 0.0, 'string' => '');
                $mResultCollection->Add($mDefaults[$mElementClass]);
            }
        }
        elseif (in_array('IEntity', class_implements($mElementClass))) {
            // TODO interface comparing is not supported -- InheritsFrom()
            foreach ($mResultSet as $mRow) {
                $mElement = new $mElementClass($this->FContext);
                $mResultElementName = $this->FResultElementName;
                $$mResultElementName = $mElement;
                foreach (self::$FVarAliasMapping as $mAlias => $mVariable) {
                    eval("{$mVariable} = \$mRow[\$mAlias];");
                }
                $mResultCollection->Add($mElement);
            }
            if ($this->FDefaultIfEmpty && $mResultSet->getCount() == 0) {
                $mResultCollection->Add(null);
            }
        }
        else {
            foreach ($mResultSet as $mRow) {
                $mElement = new $mElementClass();
                $mResultElementName = $this->FResultElementName;
                $$mResultElementName = $mElement;
                foreach (self::$FVarAliasMapping as $mAlias => $mVariable) {
                    eval("{$mVariable} = \$mRow[\$mAlias];");
                }
                $mResultCollection->Add($mElement);
            }
            if ($this->FDefaultIfEmpty && $mResultSet->getCount() == 0) {
                $mResultCollection->Add(null);
            }
        }

        self::$FVarAliasMapping->Clear();
        $this->FEntityType = null;
        $this->FResultType = null;
        $this->FResultElementName = '';
        $this->FMembers->Clear();
        $this->FConnection = null;
        $this->FContext = null;
        return $mResultCollection;
    }

    /**
     * descHere
     *
     * @param $Expression TBinaryExpression
     * @return TExpression
     */
    protected function VisitBinary($Expression) {
        if ($Expression->getNodeType() == TExpressionType::eAssign()) {
            $this->FSql .= '(';
            $this->Visit($Expression->getRight());
            $this->FAlias = (string) self::$FParameterNameCounter++;
            $this->FAlias = "col{$this->FAlias}";
            $this->FSql .= ") AS `{$this->FAlias}`";
            self::$FVarAliasMapping->Put($this->FAlias, '');
            $this->Visit($Expression->getLeft());
            $this->FAlias = '';
            return $Expression;
        }
        if ($Expression->getNodeType() == TExpressionType::eNotEqual()) {
            $this->FSql .= '(NOT (';
            $this->Visit($Expression->getLeft());
            $this->FSql .= ' <=> ';
            $this->Visit($Expression->getRight());
            $this->FSql .= '))';
            return $Expression;
        }
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
                case TExpressionType::eEqual() :
                    $this->FSql .= ' <=> ';
                    break;
                case TExpressionType::eNotEqual() :
                    $this->FSql .= ' <=> ';
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
     * @param $Expression TBlockExpression
     * @return TExpression
     */
    protected function VisitBlock($Expression) {
        TList::PrepareGeneric(array ('T' => 'string'));
        $mColumns = new TList();
        foreach ($Expression->getExpressions() as $mExpression) {
            // $this->FAlias = 'col' . self::$FParameterNameCounter;
            // ++self::$FParameterNameCounter;
            // self::$FVarAliasMapping->Put($this->FAlias, '');
            $this->FSql = ''; // '('
            $this->Visit($mExpression);
            // $this->FSql .= ") AS `{$this->FAlias}`";
            $mColumns->Add($this->FSql);
        }
        $this->FSql = implode(', ', $mColumns->ToArray());
        return $this;
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
        $this->FSql .= (':param_' . self::$FParameterNameCounter);

        TPrimativeParam::PrepareGeneric(array ('T' => $Expression->getType()));
        $mParameter = new TPrimativeParam($Expression->getValue());
        self::$FParameters->Put(':param_' . self::$FParameterNameCounter, $mParameter);
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
        ++self::$FParameterNameCounter;

        return $Expression;
    }

    /**
     * descHere
     *
     * @param $Expression TLambdaExpression
     * @return TExpression
     */
    protected function VisitLambda($Expression) {
        $this->Visit($Expression->getBody());
        // TODO ...any thing needs to do here?
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
            $mTable = $Expression->getExpression()->getType();
            $mColumn = $mTable::getColumns();
            $mColumn = $mColumn[$Expression->getMember()];
            $this->FSql .= ".`{$mColumn}`";
            $this->FMembers->Add("`{$mColumn}`");
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
        if ($Expression->getObject() !== null) {
            $this->Visit($Expression->getObject());
        }

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

        if ($mMethod === array ('TObjectQuery', 'Concat')) {
            // UNION ALL
            // TODO [solved--using static] how to sync parameters and aliases &
            // variables infomation?
            $this->FUnion = "ALL {$this->GenerateSubQuerySql($mArgs[0])}";
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
            $mMembers = $this->FMembers->ToArray();
            $mFirstMember = $mMembers[0];
            $mMembers = implode(', ', $mMembers);
            $mCounter = self::$FParameterNameCounter;
            ++self::$FParameterNameCounter;
            $this->FFrom = "LEFT JOIN ({$this->GenerateSubQuerySql($mArgs[0])}) AS `i{$mCounter}` USING ({$mMembers})";
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
            // Inner --yields something such as '(SELECT ...) AS s' or 'S AS s'
            $this->FFrom = "JOIN ({$this->GenerateSubQuerySql($mArgs[0])}) ON ";
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
            $mMembers = implode(', ', $this->FMembers->ToArray());
            $this->FFrom = "INNER JOIN {$this->GenerateSubQuerySql($mArgs[0])} USING ({$mMembers})";
            return $Expression;
        }

        // SELECT ... FROM T as t JOIN S AS s ON t.key = s.key HAVING
        // result_selector
        if ($mMethod === array ('TObjectQuery', 'Join')) {
            $this->FFrom = "JOIN {$this->GenerateSubQuerySql($mArgs[0])} ON ";
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
            if (get_class($mArgs[0]) == 'TTypedExpression' && $mArgs[0]->getBody()->ObjectType() === 'TParameterExpression') {
                $mParameter = $mArgs[0]->getBody();
                if ($mParameter instanceof TParameterExpression) {
                    $mEntityClass = $mParameter->getType();
                    $mMembers = $mEntityClass::getColumnsType();

                    TList::PrepareGeneric(array ('T' => 'TExpression'));
                    $mExpressions = new TList();
                    foreach ($mMembers as $mMember => $mType) {
                        // $mExpressions->Add(TExpression::MakeMember($mParameter,
                        // $mMember, $mType));
                        $mExpressions->Add(TExpression::Assign(TExpression::MakeMember($mParameter, $mMember, $mType), TExpression::MakeMember($mParameter, $mMember, $mType)));
                    }
                    $this->Visit(TExpression::Block($mExpressions));
                    $this->FResultElementName = $mParameter->getName();
                }
            }
            else {
                $this->Visit($mArgs[0]); // typed(param|member(param))
            }

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
            $mValue = $mArgs[0]->getValue()->UnboxToInteger(); // TConstantExpresion->TInteger
            $this->FLimit = "{$mValue}, -1";
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
            $mTemp = strstr($this->FLimit, ' ', true); // search ' ' for
                                                       // including ','
            if ($mTemp !== false) {
                $this->FLimit = "{$mTemp} ";
            }
            else {
                $this->FLimit = '';
            }
            $mValue = $mArgs[0]->getValue()->UnboxToInteger(); // TConstantExpresion->TInteger
            $this->FLimit .= "{$mValue}";
            return $Expression;
        }

        if (($mMethod === array ('TObjectQuery', 'TakeWhile') || ($mMethod === array (
            'TObjectQuery', 'Where')))) {
            $this->FSql = '';
            $this->Visit($mArgs[0]);
            if ($this->FWhere == '') {
                $this->FWhere = "{$this->FSql}";
            }
            else {
                $this->FWhere .= "AND {$this->FSql} ";
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
            self::$FVarAliasMapping[$this->FAlias] .= "\${$Expression->getName()}";
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
                    $this->FSql .= '(NOT';
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

    /**
     *
     * @param TMysqlConnection $Connection
     */
    public function UseConnection($Connection) {
        TType::Object($Connection, 'TMysqlConnection');
        $this->FConnection = $Connection;
    }

    /**
     *
     * @param TObjectContext $Context
     */
    public function UseContext($Context) {
        TType::Object($Context, 'TObjectContext');
        $this->FContext = $Context;
    }
}