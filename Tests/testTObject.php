<?php
include 'FrameworkDSW/System.php';

class TTest extends TObject {

    protected function signalProtected($a, $b, $c) {
    }

    public function signalPublic($a, $b) {
    }

    protected function slotProtected($a, $b, $c) {
        var_dump($a, $b, $c);
    }

    public function slotPublic($a, $b) {
        var_dump($a, $b);
    }
}

class TChild extends TTest {

}

$obj = new TTest();
$obj2 = new TChild();

TObject::Link(array ($obj, 'Protected'), array ($obj, 'Protected'));
TObject::Unlink(array ($obj, 'Protected'), array ($obj, 'Protected'));

TObject::Link(array ($obj, 'Protected'), array ($obj, 'Protected'));
TObject::Link(array ($obj, 'Protected'), array ($obj, 'Public'));
TObject::Dispatch(array ($obj, 'Protected'), array (10, 'text', $obj));

TObject::Link(array ($obj2, 'Public'), array ($obj, 'Protected'));
TObject::Dispatch(array ($obj2, 'Public'), array (1, 2, 3));

TObject::Unlink(array ($obj, 'Protected'), array ($obj, 'Protected'));
TObject::Link(array ($obj, 'Protected'), array ($obj, 'Protected'));
TObject::Unlink(array ($obj, 'Protected'), array ($obj, 'Protected'));
TObject::Link(array ($obj, 'Protected'), array ($obj, 'Protected'));
TObject::Link(array ($obj, 'Protected'), array ($obj, 'Protected'));
TObject::Link(array ($obj, 'Protected'), array ($obj, 'Protected'));
TObject::Link(array ($obj, 'Protected'), array ($obj, 'Protected'));
TObject::Link(array ($obj, 'Protected'), array ($obj, 'Public'));
TObject::Dispatch(array ($obj, 'Protected'), array (10, 'text', $obj));

TObject::Link(array ($obj2, 'Public'), array ($obj, 'Protected'));
TObject::Dispatch(array ($obj2, 'Public'), array (1, 2, 3));