<?php
require '../FrameworkDSW/Containers.php';

TMap::PrepareGeneric(array('K'=>'string', 'V'=>'TObject'));
$map=new TMap();
$map->Put('a', new TObject());
$map->Put('b', new TObject());

foreach ($map as $key => $value) {
    echo $key;
    echo "\n\r";
    var_dump($value);
    echo "\n\r";
}



die();
TList::PrepareGeneric(array ('T' => 'integer'));
$obj = new TList(10000);

function foo($x, $y) {
    return 1;
}

function test0() {
    $i = 10000;
    while ($i--) {
        foo(10, 20);
    }
}

function test1() {
    global $obj;
    $i = 10000;
    while ($i--) {
        $obj->Add(1);
    }
}

function test2() {
    global $obj;
    $i = 10000;
    while ($i--) {
        $arr[] = 10;
    }
}

function test3() {
    global $obj;
    $i = 10000;
    $spl = new SplFixedArray(10000);
    while ($i--) {
        $spl[$i] = 10;
    }
}

function test4() {
    global $obj;
    $i = 10000;
    $spl = new SplFixedArray(10000);
    while ($i--) {
        $spl->offsetSet($i, 10);
    }
}

function test5() {
    global $obj;
    $i = 10000;
    while ($i--) {
        $obj[$i] = 10;
    }
}

function test6() {
    global $obj;
    $i = 10000;
    while ($i--) {
        $obj->offsetSet($i, 11);
    }
}

function test7() {
    $i = 10000;
}


test0();
test1();
test2();
test3();
test4();
test5();
test6();
test7();