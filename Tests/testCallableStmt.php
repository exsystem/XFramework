<?php
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Database\EFetchNextResultSetFailed;
use FrameworkDSW\Database\Mysql\TMysqlDriver;
use FrameworkDSW\Database\TConcurrencyType;
use FrameworkDSW\Database\TCurrentResultOption;
use FrameworkDSW\Database\TResultSetType;
use FrameworkDSW\System\TInteger;

require_once 'FrameworkDSW/Framework.php';

$mDriver = new TMysqlDriver();
TMap::PrepareGeneric(['K' => 'string', 'V' => 'string']);
$mConfig = new TMap();
$mConfig['Username'] = 'root';
$mConfig['Password'] = '';
$mConfig['ConnectTimeout'] = '2';

$mConn = $mDriver->Connect('MySQL://localhost/test', $mConfig);

$mDropProcedureCmd = <<<'EOD'
DROP PROCEDURE IF EXISTS test_multi_sets
EOD;
$mCreateProcedureCmd = <<<'EOD'
CREATE PROCEDURE test_multi_sets(INOUT p1 INT)
BEGIN
	SELECT p1*3 into p1;
	SELECT * from tb1;
	SELECT * from tb1;
	SELECT * from tb1;
END
EOD;

$mConn->Execute($mDropProcedureCmd);
$mConn->Execute($mCreateProcedureCmd);

$mStmt = $mConn->PrepareCall(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
$mStmt->BindParam('foo', new TInteger(100));
$mRs = $mStmt->Query('call test_multi_sets(:foo)');
try {
    while (true) {
        $mRs = $mStmt->GetCurrentResult();
        /** @var \FrameworkDSW\Database\IRow $mRow */
        foreach ($mRs as $mRow) {
            echo "\n";
            echo $mRow['id']->Unbox();
        }
        echo "\n";
        $mStmt->NextResult(TCurrentResultOption::eCloseAllResults());
    }
}
catch (EFetchNextResultSetFailed $Ex) {
    echo "No more result sets!\n";
}
echo $mStmt->GetParam('foo')->Unbox();
Framework::Free($mRs);
Framework::Free($mStmt);
Framework::Free($mConn);
Framework::Free($mDriver);

echo "\n\n";
echo 'END';