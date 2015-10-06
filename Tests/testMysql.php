<?php
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Database\Mysql\TMysqlDriver;
use FrameworkDSW\Database\TConcurrencyType;
use FrameworkDSW\Database\TResultSetType;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\TBoolean;
use FrameworkDSW\System\TFloat;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TString;

require_once 'FrameworkDSW/Framework.php';


$mDriver = new TMysqlDriver();
TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
$mConfig                   = new TMap();
$mConfig['Username']       = 'root';
$mConfig['Password']       = '';
$mConfig['ConnectTimeout'] = '2';

$mConn = $mDriver->Connect('MySQL://localhost/test', $mConfig);

$mDropDDL     = <<<'EOD'
DROP TABLE IF EXISTS `tmysqlconnectiontest`
EOD;
$mCreateDDL   = <<<'EOD'
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

$mConn->Execute($mDropDDL);
$mConn->Execute($mCreateDDL);
$mConn->Execute($mTruncateDDL);

for ($i = 1; $i < 50; ++$i) {
    echo $mConn->Execute("insert into `tmysqlconnectiontest` values({$i}, 10, 1, '中国hi', 20.5)");
}


$mSt = $mConn->PrepareStatement(TResultSetType::eScrollSensitive(), TConcurrencyType::eReadOnly());
$mSt->setCommand('insert into tmysqlconnectiontest (_int, _bool, _vchar, _float) values (:a, :b, :c, :d)');
$mSt->BindParam(':a', new TInteger(10));
$mSt->BindParam(':b', new TBoolean(true));
$mSt->BindParam(':c', new TString('string'));
$mSt->BindParam(':d', new TFloat(1.5));
$rrr = $mSt->Execute();
echo $rrr, "\r\n";

Framework::Free($mSt);

echo $mConn->Execute("PREPARE sDel FROM 'DELETE FROM `tmysqlconnectiontest` WHERE `id`=?'"), "\t";
echo $mConn->Execute("SET @idd = 1"), "\t";
echo $mConn->Execute("EXECUTE sDel USING @idd"), "\t";
echo 'DONE!!', "\r\n";

$mStmt = $mConn->CreateStatement(TResultSetType::eScrollSensitive(), TConcurrencyType::eUpdatable());
$mRs   = $mStmt->Query('select id,_int,_bool,_vchar,_float from tmysqlconnectiontest order by id');
$mRow  = $mRs->current();

/** @var \FrameworkDSW\Database\IRow $mRow */
foreach ($mRs as $mRow) {
    echo $mRow['id']->Unbox(), "\t";
    echo $mRow['_int']->Unbox(), "\t";
    echo $mRow['_bool']->Unbox(), "\t";
    echo $mRow['_vchar']->Unbox(), "\t";
    echo $mRow['_float']->Unbox(), "\t";

    $mRow['_vchar'] = new TString('modified');
    $mRow->Update();
    echo $mRow['_vchar']->Unbox();
    echo "\r\n";
    try {
        $mRow->Delete();
    }
    catch (EException $Ex) {
        var_dump($Ex);
    }
}

Framework::Free($mRs);
Framework::Free($mStmt);
Framework::Free($mConn);
Framework::Free($mDriver);