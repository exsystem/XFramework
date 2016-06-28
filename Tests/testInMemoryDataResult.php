<?php
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Database\TInMemoryResultSet;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TString;

require_once 'FrameworkDSW/Framework.php';

TMap::PrepareGeneric(['K' => Framework::String, 'V' => [TClass::class => ['T' => null]]]);
$mMeta = new TMap(true);
$mMeta->Put('A', Framework::Type(TString::class));
$mMeta->Put('B', Framework::Type(TString::class));
$mMeta->Put('C', Framework::Type(TInteger::class));

$mData = [];
if (true) {
    for ($i = 0; $i < 10; ++$i) {
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => IInterface::class]);
        $d = new TMap(true);
        $d->Put('A', new TString('mike'));
        $d->Put('B', new TString('orange'));
        $d->Put('C', new TInteger(100 + $i));
        $mData[] = $d;
    }
}
$rs = new TInMemoryResultSet(null, $mData, $mMeta);
$i  = 0;
foreach ($rs as $r) {
    echo $i++, ": ";
    echo $r['A']->Unbox(), "\t";
    echo $r['B']->Unbox(), "\t";
    echo $r['C']->Unbox(), "\t";
    $r['C'] = new TInteger(300);
    $r->Update();
    echo $r['C']->Unbox(), "\t";
    echo "\r\n";
}

$i  = 0;
foreach ($rs as $r) {
    $r->Delete();
}
echo $rs->getCount(), "\r\n";

try {
    $rs->getInsertRow()['A'] = new TString('newGirl');
    $rs->getInsertRow()['B'] = new TString('newGirl');
    $rs->getInsertRow()['C'] = new TInteger(200);
}
catch (\FrameworkDSW\Database\EUnsupportedDbFeature $e) {

}
echo 'END', "\r\n";
Framework::Free($rs);
foreach ($mData as $d) {
    Framework::Free($d);
}
