<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '../../');
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Database\Mysql\TMysqlDriver;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TVersion;

require_once 'FrameworkDSW/Framework.php';
require_once 'Tests/UnitTest/helper.php';

/**
 * TMysqlDriver test case.
 */
class TMysqlDriverTest extends PHPUnit_Framework_TestCase {

    /**
     * @var TMysqlDriver
     */
    private $TMysqlDriver;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();

        // TODO Auto-generated TMysqlDriverTest::setUp()


        $this->TMysqlDriver = new TMysqlDriver( /* parameters */);

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        // TODO Auto-generated TMysqlDriverTest::tearDown()
        $this->TMysqlDriver = null;

        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct() {
    }

    public function testValidateUrl() {
        $result = $this->TMysqlDriver->ValidateUrl("MySQL://localhost:3333/myDbName");
        $this->assertTrue($result, 'valiation');
        $this->assertFalse($this->TMysqlDriver->ValidateUrl(""), 'emptry string');
        $this->assertFalse($this->TMysqlDriver->ValidateUrl('MySQL://'), "2");
    }

    public function testConnect() {
        $mConfigs = array();

        $mConfigs[''] = null;

        TMap::PrepareGeneric(array('K' => 'string', 'V' => 'string'));
        $mConfigs['MySQL://localhost/test']             = new TMap();
        $mConfigs['MySQL://localhost/test']['Username'] = 'root';
        $mConfigs['MySQL://localhost/test']['Password'] = '';

        TMap::PrepareGeneric(array('K' => 'string', 'V' => 'string'));
        $mConfigs['MySQL://localhost/test']             = new TMap();
        $mConfigs['MySQL://localhost/test']['Username'] = 'root';

        TMap::PrepareGeneric(array('K' => 'string', 'V' => 'string'));
        $mConfigs['MySQL://localhost/test']             = new TMap();
        $mConfigs['MySQL://localhost/test']['Username'] = 'root';

        TMap::PrepareGeneric(['K' => TObject::class, 'V' => 'integer']);
        $mConfigs['MySQL://localhost/test']                 = new TMap();
        $mConfigs['MySQL://localhost/test'][new TVersion()] = 10;

        foreach ($mConfigs as $mUrl => $mProp) {
            try {
                $mConn = $this->TMysqlDriver->Connect($mUrl, $mProp);
                $mConn->Disconnect();
                $this->fail("failed for URL: [{$mUrl}].");
            }
            catch (EException $Ex) {
                logging("Expected exception thrown for URL: [{$mUrl}], [{$Ex->getMessage()}].");
            }
        }
    }

    public function testGetVersion() {
        TMap::PrepareGeneric(array('K' => 'string', 'V' => 'string'));
        $mProps             = new TMap();
        $mProps['Username'] = 'root';
        $mProps['Password'] = '';
        $mConn              = $this->TMysqlDriver->Connect('MySQL://127.0.0.1/test', $mProps);
        $mVersion           = $this->TMysqlDriver->getVersion();
        $mConn->Disconnect();
        logging("{$mVersion->MajorVersion}.{$mVersion->MinorVersion}.{$mVersion->Build} Rev.{$mVersion->Revision}");
    }

    public function testGetPropertyInfo() {
        $this->markTestIncomplete('incomplete');
    }

}

