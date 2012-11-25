<?php
require_once 'FrameworkDSW/View_Web.php';
require_once 'FrameworkDSW/Containers.php';
require_once 'PHPUnit\Framework\TestCase.php';
class TTestRecord extends \TRecord {
    /**
     *
     * @var string
     */
    public $Value = "漢字abc";
    /**
     *
     * @var \TInteger
     */
    public $Primitive = null;
    /**
     *
     * @var \TObject
     */
    public $Null = null;
    /**
     *
     * @var \TList <T: integer>
     */
    public $List = null;
    /**
     *
     * @var \TMap <K: string, V: integer>
     */
    public $Map = null;
    /**
     *
     * @var TTestRecord
     */
    public $Record = null;
    /**
     *
     * @var \TPair <K: string, V: string>
     */
    public $Pair = null;
}

/**
 * TJsonView test case.
 */
class TEkuJsonViewTest extends PHPUnit_Framework_TestCase {

    /**
	 * @var TEkuJsonView
	 */
    private $TJsonView;

    /**
	 * Prepares the environment before running a test.
	 */
    protected function setUp() {
        parent::setUp();

        // TODO Auto-generated TEkuJsonViewTest::setUp()


        $this->TJsonView = new TJsonView();
    }

    /**
	 * Cleans up the environment after running a test.
	 */
    protected function tearDown() {
        // TODO Auto-generated TEkuJsonViewTest::tearDown()
        $this->TJsonView = null;

        parent::tearDown();
    }

    /**
	 * Constructs the test case.
	 */
    public function __construct() {
        // TODO Auto-generated constructor
    }

    /**
	 * Tests TEkuJsonView->Update()
	 */
    public function testUpdate() {
        $mRecord = new TTestRecord();
        \TList::PrepareGeneric(array( 'T' => 'integer'));
        $mRecord->List = new TList(5, false, array (1, 2, 3, 4, 5));
        \TMap::PrepareGeneric(array( 'K' => 'string', 'V' => 'integer'));
        $mRecord->Map = new TMap();
        $mRecord->Map->Put('map_key', 123);
        $mRecord->Null = null;
        $mRecord->Pair = new TPair();
        $mRecord->Pair->Key = 'pair_key';
        $mRecord->Pair->Value = 'pair_value';
        $mRecord->Primitive = new TInteger(100);
        $mRecord->Record = new TTestRecord();
        $mRecord->Value = 'value_string';

        ob_start();
        \TMap::PrepareGeneric(array( 'K' => 'string', 'V' => 'IInterface'));
        $mResult = new TMap();
        $mResult->Put('data', $mRecord);
        $this->TJsonView->Update($mResult);
    }
}

