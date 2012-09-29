<?php
set_include_path(get_include_path() . ':/media/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW'); //LINUX
//set_include_path(get_include_path().';E:\\Documents\\ZendStudioWorkspace\\FrameworkDSW'); //WINDOWS
//set_include_path(get_include_path() . ':/Volumes/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW'); //MACOSX

require_once 'FrameworkDSW/Database_Mysql.php';

$mDriver = new TMysqlDriver();
TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'string'));
$mConfig = new TMap();
$mConfig['Username'] = 'root';
$mConfig['Password'] = '';
$mConfig['ConnectTimeout'] = '2';
$mConfig['Socket']='/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'; //MACOSX ONLY


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
TPrimitiveParam::PrepareGeneric(array ('T' => 'integer'));
$mStmt->BindParam('foo', new TPrimitiveParam(100));
$mRs = $mStmt->Query('call test_multi_sets(:foo)');
try {
    while (true) {
        $mRs = $mStmt->GetCurrentResult();
        foreach ($mRs as $mRow) {
            echo "\n";
            echo $mRow['id']->getValue(); //memory leak
        }
        echo "\n";
        $mStmt->NextResult(TCurrentResultOption::eCloseAllResults());
    }
}
catch (EFetchNextResultSetFailed $Ex) {
    echo "No more result sets!\n";
}
echo $mStmt->GetParam('foo')->getValue();
Framework::Free($mRs);
Framework::Free($mStmt);
Framework::Free($mConn);
Framework::Free($mDriver);

echo "\n\n";
echo 'END';