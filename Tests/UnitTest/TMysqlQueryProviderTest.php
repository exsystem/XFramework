<?php

require_once 'FrameworkDSW\Linq_LinqToMysql.php';
require_once 'Tests/helperForLinqToMysql.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * TMysqlQueryProvider test case.
 */
class TMysqlQueryProviderTest extends PHPUnit_Framework_TestCase {

    /**
     *
     * @var TMysqlQueryProvider
     */
    private $TMysqlQueryProvider;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();

        // TODO Auto-generated TMysqlQueryProviderTest::setUp()

        $this->TMysqlQueryProvider = new TMysqlQueryProvider(/* parameters */);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        // TODO Auto-generated TMysqlQueryProviderTest::tearDown()

        $this->TMysqlQueryProvider = null;

        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct() {
        // TODO Auto-generated constructor
    }

    /**
     * Tests TMysqlQueryProvider->__construct()
     */
    public function test__construct() {

        $c = new TTestContext($this->TMysqlQueryProvider);
        $c->PrepareMethodGeneric(array ('T' => 'TStudent'));

        $q = $c->CreateQuery();

        TList::PrepareGeneric(array ('T' => 'TParameterExpression'));

        $params = new TList();
        $params->Add(TExpression::Parameter('t', 'TStudent'));
        $expr = TExpression::NotEqual(TExpression::MakeMember(TExpression::Parameter('t', 'TStudent'), 'FGender', 'TBoolean'), TExpression::Constant(null, 'TBoolean'));
        TExpression::PrepareGeneric(array (
            'T' => array (
                'TPredicateDelegate' => array (
                    'E' => array (
                        'TPair' => array ('K' => 'integer', 'V' => 'TStudent'))))));
        $expr = TExpression::TypedLambda($expr, $params);

        $selector = TExpression::Parameter('t', 'TStudent');
        TExpression::PrepareGeneric(array (
            'T' => array (
                'TSelectorDelegate' => array (
                    'S' => array (
                        'TPair' => array ('K' => 'integer', 'V' => 'TStudent')),
                    'D' => 'TStudent'))));
        $selector = TExpression::TypedLambda($selector, $params);

        $orderby = TExpression::MakeMember(TExpression::Parameter('t', 'TStudent'), 'FName', 'string');
        TExpression::PrepareGeneric(array (
            'T' => array (
                'TSelectorDelegate' => array ('S' => 'TStudent',
                    'D' => 'string'))));
        $orderby = TExpression::TypedLambda($orderby, $params);

        $q->PrepareMethodGeneric(array ('R' => 'TStudent', 'K' => 'string'));
        foreach ($q->Select($selector)->Where($expr)->OrderByDescending($orderby) as $s) {
            echo $s->getName()->getValue();
        }

        return;
        // TODO Auto-generated TMysqlQueryProviderTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");

        $this->TMysqlQueryProvider->__construct(/* parameters */);

    }

    /**
     * Tests TMysqlQueryProvider->Destroy()
     */
    public function testDestroy() {
        // TODO Auto-generated TMysqlQueryProviderTest->testDestroy()
        $this->markTestIncomplete("Destroy test not implemented");

        $this->TMysqlQueryProvider->Destroy(/* parameters */);

    }

    /**
     * Tests TMysqlQueryProvider->CreateQuery()
     */
    public function testCreateQuery() {
        // TODO Auto-generated TMysqlQueryProviderTest->testCreateQuery()
        $this->markTestIncomplete("CreateQuery test not implemented");

        $this->TMysqlQueryProvider->CreateQuery(/* parameters */);

    }

    /**
     * Tests TMysqlQueryProvider->Execute()
     */
    public function testExecute() {
        // TODO Auto-generated TMysqlQueryProviderTest->testExecute()
        $this->markTestIncomplete("Execute test not implemented");

        $this->TMysqlQueryProvider->Execute(/* parameters */);

    }

    /**
     * Tests TMysqlQueryProvider->UseConnection()
     */
    public function testUseConnection() {
        // TODO Auto-generated TMysqlQueryProviderTest->testUseConnection()
        $this->markTestIncomplete("UseConnection test not implemented");

        $this->TMysqlQueryProvider->UseConnection(/* parameters */);

    }

}

