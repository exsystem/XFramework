<?php
require_once 'FrameworkDSW/Database_Mysql.php';

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Tests/UnitTest/helper.php';

/**
 * TMysqlConnection test case.
 */
class TMysqlConnectionTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @var TMysqlConnection
     */
    private $TMysqlConnection;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        
        $mDriver = new TMysqlDriver();
        TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'string'));
        $mConfig = new TMap();
        $mConfig['Username'] = 'root';
        $mConfig['Password'] = '';
        $mConfig['ConnectTimeout'] = '2';
        
        $this->TMysqlConnection = $mDriver->Connect('MySQL://localhost/test', $mConfig);
        
        $mDropDDL = <<<'EOD'
DROP TABLE IF EXISTS `tmysqlconnectiontest`
EOD;
        $mCreateDDL = <<<'EOD'
CREATE TABLE IF NOT EXISTS `tmysqlconnectiontest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `_int` int(11) NOT NULL,
  `_bool` tinyint(1) NOT NULL,
  `_vchar` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `_float` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1
EOD;
        $mTruncateDDL = <<<'EOD'
TRUNCATE TABLE `tmysqlconnectiontest`
EOD;
        
        $this->TMysqlConnection->Execute($mDropDDL);
        $this->TMysqlConnection->Execute($mCreateDDL);
        $this->TMysqlConnection->Execute($mTruncateDDL);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        Framework::Free($this->TMysqlConnection);
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct() {
        ini_set('memory_limit', '256M');
    }

    /**
     * Tests TMysqlConnection::PushWarning()
     */
    public function testPushWarning() {
        // TODO Auto-generated TMysqlConnectionTest::testPushWarning()
        $this->markTestIncomplete("PushWarning test not implemented");
        
        TMysqlConnection::PushWarning(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->ClearWarnings()
     */
    public function testClearWarnings() {
        $this->TMysqlConnection->ClearWarnings();
        $mWarnings = $this->TMysqlConnection->getWarnings();
        $this->assertNull($mWarnings);
        for ($i = 0; $i < 10; ++$i) {
            try {
                $this->TMysqlConnection->Execute('ERROR TEST');
            }
            catch (EExecuteFailed $e) {
                $mContext = $e->getWarningContext();
                if ($mContext->IsInstanceOf('TMysqlWarningContext')) {
                    logging($mContext->getErrorMessage());
                }
            }
        }
        $mWarning = $this->TMysqlConnection->getWarnings()->getNextWarning();
        $this->TMysqlConnection->ClearWarnings();
        $this->assertNull($this->TMysqlConnection->getWarnings());
    
     //$this->assertNull($mWarning); this will lead into a dead loop, print_r($mWarning) is called by PHPUnit.
    }

    /**
     * Tests TMysqlConnection->Commit()
     */
    public function testCommit() {
        $this->TMysqlConnection->setAutoCommit(false);
        $mNum = $this->TMysqlConnection->Execute("insert into `tmysqlconnectiontest` values(10, 10, 1, '中国hi', 20.5)");
        logging($mNum);
        $this->TMysqlConnection->Commit();
        $mStmt = $this->TMysqlConnection->CreateStatement(TResultSetType::eForwardOnly(), TConcurrencyType::eReadOnly());
        $mStmt->setCommand("select `_vchar` from `tmysqlconnectiontest`");
        $mData = $mStmt->FetchAsScalar();
        $mResult = $mData->getValue();
        logging($mResult);
        
        $this->TMysqlConnection->setAutoCommit(false);
        $mNum = $this->TMysqlConnection->Execute("update `tmysqlconnectiontest` set `_vchar` = 'FAILED!' where `id` = 10");
        logging($mNum);
        $mStmt = $this->TMysqlConnection->CreateStatement(TResultSetType::eForwardOnly(), TConcurrencyType::eReadOnly());
        $mStmt->setCommand("select `_vchar` from `tmysqlconnectiontest`");
        $mData = $mStmt->FetchAsScalar();
        $mResult = $mData->getValue();
        logging($mResult);
        $this->TMysqlConnection->Rollback();
        $mStmt = $this->TMysqlConnection->CreateStatement(TResultSetType::eForwardOnly(), TConcurrencyType::eReadOnly());
        $mStmt->setCommand("select `_vchar` from `tmysqlconnectiontest`");
        $mData = $mStmt->FetchAsScalar();
        $mResult = $mData->getValue();
        logging($mResult);
    }

    /**
     * Tests TMysqlConnection->CreateSavepoint()
     */
    public function testCreateSavepoint() {
        $this->TMysqlConnection->setAutoCommit(false);
        $this->TMysqlConnection->Execute("insert into `tmysqlconnectiontest` values(10, 10, 1, '中國', 20.5)");
        $mSvpt = $this->TMysqlConnection->CreateSavepoint();
        $this->TMysqlConnection->Execute("update `tmysqlconnectiontest` set `_vchar`='CHINA' where `id`=10");
        $mSvpt2 = $this->TMysqlConnection->CreateSavepoint('NamedSvpt');
        $this->TMysqlConnection->Rollback($mSvpt);
        
        $mStmt = $this->TMysqlConnection->CreateStatement(TResultSetType::eForwardOnly(), TConcurrencyType::eReadOnly());
        $mStmt->setCommand("select `_vchar` from `tmysqlconnectiontest`");
        $mData = $mStmt->FetchAsScalar();
        $mResult = $mData->getValue();
        logging($mResult);
        Framework::Free($mStmt);
    }

    /**
     * Tests TMysqlConnection->CreateStatement()
     */
    public function testCreateStatement() {
        for ($i = 1; $i < 5; ++$i) {
            $this->TMysqlConnection->Execute("insert into `tmysqlconnectiontest` values({$i}, 10, 1, '中国hi', 20.5)");
        }
        
        $mStmt = $this->TMysqlConnection->CreateStatement(TResultSetType::eScrollSensitive(), TConcurrencyType::eUpdatable());
        $mRs = $mStmt->Query('select * from tmysqlconnectiontest');
        logging("<<<<");
        
        foreach ($mRs as $mRow) {
            $mRow['id']->getValue();
            $mRow['_int']->getValue();
            $mRow['_bool']->getValue();
            $mRow['_vchar']->getValue();
            $mRow['_float']->getValue();
            
            TPrimativeParam::PrepareGeneric(array ('T' => 'string'));
            $mRow['_vchar'] = new TPrimativeParam('修改过了！Modified');
            $mRow->Update();            
        /*
                logging($mRow['id']->getValue());
                logging($mRow['_int']->getValue());
                logging($mRow['_bool']->getValue());
                logging($mRow['_vchar']->getValue());
                logging(">>{$mRow['_vchar']->getValue()}<<");
                logging($mRow['_float']->getValue());
                */
        }
        logging(">>>>");
        Framework::Free($mRs);
        Framework::Free($mStmt);
    }

    /**
     * Tests TMysqlConnection->Disconnect()
     */
    public function testDisconnect() {
        // TODO Auto-generated TMysqlConnectionTest->testDisconnect()
        $this->markTestIncomplete("Disconnect test not implemented");
        
        $this->TMysqlConnection->Disconnect(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->Execute()
     */
    public function testExecute() {
        // TODO Auto-generated TMysqlConnectionTest->testExecute()
        $this->markTestIncomplete("Execute test not implemented");
        
        $this->TMysqlConnection->Execute(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->getAutoCommit()
     */
    public function testGetAutoCommit() {
        // TODO Auto-generated TMysqlConnectionTest->testGetAutoCommit()
        $this->markTestIncomplete("getAutoCommit test not implemented");
        
        $this->TMysqlConnection->getAutoCommit(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->getCatalog()
     */
    public function testGetCatalog() {
        // TODO Auto-generated TMysqlConnectionTest->testGetCatalog()
        $this->markTestIncomplete("getCatalog test not implemented");
        
        $this->TMysqlConnection->getCatalog(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->getHoldability()
     */
    public function testGetHoldability() {
        // TODO Auto-generated TMysqlConnectionTest->testGetHoldability()
        $this->markTestIncomplete("getHoldability test not implemented");
        
        $this->TMysqlConnection->getHoldability(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->getIsConnected()
     */
    public function testGetIsConnected() {
        // TODO Auto-generated TMysqlConnectionTest->testGetIsConnected()
        $this->markTestIncomplete("getIsConnected test not implemented");
        
        $this->TMysqlConnection->getIsConnected(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->getMetaData()
     */
    public function testGetMetaData() {
        // TODO Auto-generated TMysqlConnectionTest->testGetMetaData()
        $this->markTestIncomplete("getMetaData test not implemented");
        
        $this->TMysqlConnection->getMetaData(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->getReadOnly()
     */
    public function testGetReadOnly() {
        // TODO Auto-generated TMysqlConnectionTest->testGetReadOnly()
        $this->markTestIncomplete("getReadOnly test not implemented");
        
        $this->TMysqlConnection->getReadOnly(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->getTransactionIsolation()
     */
    public function testGetTransactionIsolation() {
        $mValue = $this->TMysqlConnection->getTransactionIsolation();
        logging(var_export($mValue, true));
    }

    /**
     * Tests TMysqlConnection->getWarnings()
     */
    public function testGetWarnings() {
        // TODO Auto-generated TMysqlConnectionTest->testGetWarnings()
        $this->markTestIncomplete("getWarnings test not implemented");
        
        $this->TMysqlConnection->getWarnings(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->PrepareStatement()
     */
    public function testPrepareStatement() {
        // TODO Auto-generated TMysqlConnectionTest->testPrepareStatement()
        $this->markTestIncomplete("PrepareStatement test not implemented");
        
        $this->TMysqlConnection->PrepareStatement(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->RemoveSavepoint()
     */
    public function testRemoveSavepoint() {
        // TODO Auto-generated TMysqlConnectionTest->testRemoveSavepoint()
        $this->markTestIncomplete("RemoveSavepoint test not implemented");
        
        $this->TMysqlConnection->RemoveSavepoint(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->Rollback()
     */
    public function testRollback() {
        // TODO Auto-generated TMysqlConnectionTest->testRollback()
        $this->markTestIncomplete("Rollback test not implemented");
        
        $this->TMysqlConnection->Rollback(/* parameters */);
    }

    /**
     * Tests TMysqlConnection->setAutoCommit()
     */
    public function testSetAutoCommit() {
        // TODO Auto-generated TMysqlConnectionTest->testSetAutoCommit()
        $this->markTestIncomplete("setAutoCommit test not implemented");
        
        $this->TMysqlConnection->setAutoCommit(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->setCatalog()
     */
    public function testSetCatalog() {
        // TODO Auto-generated TMysqlConnectionTest->testSetCatalog()
        $this->markTestIncomplete("setCatalog test not implemented");
        
        $this->TMysqlConnection->setCatalog(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->setHoldability()
     */
    public function testSetHoldability() {
        // TODO Auto-generated TMysqlConnectionTest->testSetHoldability()
        $this->markTestIncomplete("setHoldability test not implemented");
        
        $this->TMysqlConnection->setHoldability(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->setReadOnly()
     */
    public function testSetReadOnly() {
        // TODO Auto-generated TMysqlConnectionTest->testSetReadOnly()
        $this->markTestIncomplete("setReadOnly test not implemented");
        
        $this->TMysqlConnection->setReadOnly(/* parameters */);
    
    }

    /**
     * Tests TMysqlConnection->setTransactionIsolation()
     */
    public function testSetTransactionIsolation() {
        // TODO Auto-generated TMysqlConnectionTest->testSetTransactionIsolation()
        $this->markTestIncomplete("setTransactionIsolation test not implemented");
        
        $this->TMysqlConnection->setTransactionIsolation(/* parameters */);
    
    }

}

