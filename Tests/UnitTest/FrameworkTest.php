<?php
/**
 * FrameworkTest
 * @author	ExSystem
 * @version	$Id$
 * @since	separate file since reversion 1
 */

require_once 'FrameworkDSW/Framework.php';
require_once 'classForTesting.php';

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Tests/UnitTest/helper.php';

/**
 * Framework test case.
 */
class FrameworkTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @var Framework
     */
    private $Framework;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct() {
    }

    public function testDeepSerialize() {
        logging('======TEST DEEP SERIALIZE=====');
        
        $obj = new TTest2();
        $obj2 = new TTest();
        $obj2->FPublic1 = 'OBJ2_public_1';
        
        $obj->FPublic1 = $obj;
        $obj->setPrivate(1, $obj);
        $obj->setPrivate(2, $obj2);
        TTest2::$FSPublic1 = $obj;
        
        $str = Framework::Serialize($obj);
        logging($str);
        logging('======TEST DEEP SERIALIZE====ended=');
    }

    public function testComlpexSerialize() {
        //$this->markTestIncomplete("Last test not implemented");
        logging('======TEST COMPLX SERIALIZE=====');
        
        $obj = new TTest();
        $obj2 = new TTest();
        $obj2->FPublic1 = 'OBJ2_public_1';
        
        $obj->FPublic1 = $obj;
        $obj->setPrivate(1, $obj);
        $obj->setPrivate(2, $obj2);
        TTest::$FSPublic1 = $obj;
        TTest::setSPrivate(1, 'helloworld');
        
        $i = 100;
        $a = microtime(true);
        while ($i--) {
            serialize($obj);
        }
        $b = microtime(true);
        $b = $b - $a;
        logging("TIME_PHP = {$b}");
        logging(serialize($obj));
        
        $i = 100;
        $a = microtime(true);
        while ($i--) {
            Framework::Serialize($obj);
        }
        $b = microtime(true);
        $b = $b - $a;
        logging("\nTIME_FRAMEWORK = {$b}");
        logging(Framework::Serialize($obj));
        
        $data = Framework::Unserialize(Framework::Serialize($obj));
        logging(print_r($data, true));
        
        logging('======TEST COMPLX SERIALIZE====ended=');
    
     //**"!" is for the "\0" character, this is well-formatted.******************
    //a:1:{
    //    i:0;a:2:{
    //        i:0;a:10:{
    //            s:7:"TRecord";s:56:"E:\Works\SourceOnly\FrameworkDSW\FrameworkDSW\System.php";
    //            s:5:"TType";s:59:"E:\Works\SourceOnly\FrameworkDSW\FrameworkDSW\Utilities.php";
    //            s:5:"TPair";s:60:"E:\Works\SourceOnly\FrameworkDSW\FrameworkDSW\Containers.php";
    //            s:16:"TStdListIterator";s:60:"E:\Works\SourceOnly\FrameworkDSW\FrameworkDSW\Containers.php";
    //            s:20:"TStdListListIterator";s:60:"E:\Works\SourceOnly\FrameworkDSW\FrameworkDSW\Containers.php";
    //            s:19:"TAbstractCollection";s:60:"E:\Works\SourceOnly\FrameworkDSW\FrameworkDSW\Containers.php";
    //            s:13:"TAbstractList";s:60:"E:\Works\SourceOnly\FrameworkDSW\FrameworkDSW\Containers.php";
    //            s:5:"TList";s:60:"E:\Works\SourceOnly\FrameworkDSW\FrameworkDSW\Containers.php";
    //            s:5:"TTest";s:61:"E:\Works\SourceOnly\FrameworkDSW\UnitTest\classForTesting.php";
    //            s:6:"TTest2";s:61:"E:\Works\SourceOnly\FrameworkDSW\UnitTest\classForTesting.php";
    //        }
    //        i:1;s:351:"
    //            a:2:{
    //                i:0;O:5:"TTest":4:{
    //                        s:16:"!TTest!FPrivate1";r:2;
    //                        s:16:"!TTest!FPrivate2";O:5:"TTest":4:{
    //                                            s:16:"!TTest!FPrivate1";N;
    //                                            s:16:"!TTest!FPrivate2";N;
    //                                            s:8:"FPublic1";s:13:"OBJ2_public_1";
    //                                            s:8:"FPublic2";N;
    //                        }
    //                        s:8:"FPublic1";r:2;
    //                        s:8:"FPublic2";N;
    //                }
    //                i:1;a:4:{
    //                    s:15:"TTest.FSPublic1";r:2;
    //                    s:15:"TTest.FSPublic2";N;
    //                    s:16:"TTest2.FSPublic1";r:2;
    //                    s:16:"TTest2.FSPublic2";N;
    //                }
    //            }
    //        ";
    //    }
    //}
    //**************************************************************************
    }

    /**
     * Tests Framework::Serialize()
     */
    public function testSerialize() {
        $obj = new TTest();
        $obj->FPublic1 = 'public_value_1';
        $obj->FPublic2 = 'public_value_2';
        TTest::$FSPublic1 = 'static_public_1';
        TTest::$FSPublic2 = 'static_public_2';
        $obj->setPrivate(1, 'private_1');
        $obj->setPrivate(2, 'private_2');
        TTest::setSPrivate(1, 'static_private_1');
        TTest::setSPrivate(2, 'static_private_2');
        
        $str = Framework::Serialize($obj);
        logging($str);
        
        logging(var_export(Framework::Unserialize($str), 1));
    }

    /**
     * Tests Framework::Unserialize()
     */
    public function testUnserialize() {
        logging('======TEST COMPLX UNSERIALIZE=====');
        
        $obj = new TTest();
        $obj2 = new TTest();
        $obj2->FPublic1 = 'OBJ2_public_1';
        
        $obj->FPublic1 = $obj;
        $obj->setPrivate(1, $obj);
        $obj->setPrivate(2, $obj2);
        TTest::$FSPublic1 = $obj;
        
        $i = 100; //10000
        $str = serialize($obj);
        $a = microtime(true);
        while ($i--) {
            unserialize($str);
        }
        $b = microtime(true);
        $b = $b - $a;
        logging("TIME_PHP = {$b}");
        
        $i = 100; //10000
        $str = Framework::Serialize($obj);
        $a = microtime(true);
        while ($i--) {
            Framework::Unserialize($str);
        }
        $b = microtime(true);
        $b = $b - $a;
        logging("TIME_FRAMEWORK = {$b}");
        
        logging('======TEST COMPLX UNSERIALIZE====ended=');
    }
}

