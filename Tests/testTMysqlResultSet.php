<?php
//set_include_path(get_include_path() . ':/media/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW'); //LINUX
//set_include_path(get_include_path().';E:\\Documents\\ZendStudioWorkspace\\FrameworkDSW'); //WINDOWS
//set_include_path(get_include_path() . ':/Volumes/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW'); //MACOSX


require_once 'FrameworkDSW/Database_Mysql.php';

$mDriver = new TMysqlDriver();
TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'string'));
$mConfig = new TMap();
$mConfig['Username'] = 'root';
$mConfig['Password'] = '';
$mConfig['ConnectTimeout'] = '2';
//$mConfig['Socket']='/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'; //MACOSX ONLY


$mConn = $mDriver->Connect('MySQL://localhost/test', $mConfig);

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
$mDropProcedureCmd = <<<'EOD'
DROP PROCEDURE IF EXISTS testProcedure;
EOD;
$mCreateProcedureCmd = <<<'EOD'
CREATE PROCEDURE testProcedure()
BEGIN
  SELECT * FROM `tmysqlconnectiontest`;
  SELECT * FROM `tmysqlconnectiontest`;
  SELECT * FROM `tmysqlconnectiontest`;
END
EOD;

$mConn->Execute($mDropDDL);
$mConn->Execute($mCreateDDL);
$mConn->Execute($mTruncateDDL);
$mConn->Execute($mDropProcedureCmd);
$mConn->Execute($mCreateProcedureCmd);

for ($i = 1; $i < 50; ++$i) {
    echo $mConn->Execute("insert into `tmysqlconnectiontest` values({$i}, 10, 1, '中国hi', 20.5)");
}
$mStmt = $mConn->CreateStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
$mRs = $mStmt->Query('select * from tmysqlconnectiontest');
$mRs = $mStmt->Query('#call testProcedure()');
Framework::Free($mRs); //$mStmt->FCurrentResultSet was not destroyed, which lead calling released mysqli_stmt object, although $mRs has been destroyed.
$mStmt->NextResult(TCurrentResultOption::eCloseCurrentResult());
$mRs = $mStmt->GetCurrentResult();
$mRow = $mRs->current();
foreach ($mRs as $mRow) {
    echo $mRow['id']->getValue(); //memory leak
    echo $mRow['_int']->getValue(); //memory leak
    echo $mRow['_bool']->getValue(); //memory leak
    echo $mRow['_vchar']->getValue(); //memory leak
    echo $mRow['_float']->getValue(); //memory leak
}
Framework::Free($mRs);
Framework::Free($mStmt);
Framework::Free($mConn);
Framework::Free($mDriver);
echo 'END';