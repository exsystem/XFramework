<?php

require_once 'FrameworkDSW/Containers.php';

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Tests/UnitTest/helper.php';

/**
 * TLinkedList test case.
 */
class TLinkedListTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @var TLinkedList
     */
    private $TLinkedList;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        TLinkedList::PrepareGeneric(array ('T' => 'integer'));
        $this->TLinkedList = new TLinkedList();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        //Framework::Free($this->TLinkedList);
        $this->TLinkedList = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct() {
    }

    /**
     * Tests TLinkedList->__construct()
     */
    public function test__construct() {
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->TLinkedList->__construct(/* parameters */);
    
    }

    /**
     * Tests TLinkedList::FromArray()
     */
    public function testFromArray() {
        logStart('TLinkedList::FromArray()');
        TLinkedList::PrepareGeneric(array ('T' => 'integer'));
        $L = TLinkedList::FromArray(true, array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9), true);
        $str = '';
        foreach ($L as $value) {
            $str .= $value;
        }
        $this->assertEquals('0123456789', $str, 'foreach loop');
    }

    /**
     * Tests TLinkedList->Swap()
     */
    public function testSwap() {
        logStart('TLinkedList->Swap()');
        TLinkedList::PrepareGeneric(array ('T' => 'integer'));
        $L = new TLinkedList(true, array ('11', '22', '33'));
        $this->TLinkedList->Add(10);
        $this->TLinkedList->Add(20);
        $this->TLinkedList->Add(30);
        $this->TLinkedList->Add(40);
        $this->TLinkedList->Add(50);
        $this->TLinkedList->Swap($L);
        $str = '';
        foreach ($this->TLinkedList as $value) {
            $str .= $value;
        }
        $this->assertEquals('112233', $str);
        $str = '';
        foreach ($L as $value) {
            $str .= $value;
        }
        $this->assertEquals('1020304050', $str);
    
    }

    /**
     * Tests TLinkedList->ToArray()
     */
    public function testToArray() {
        $this->markTestIncomplete("ToArray test not implemented");
        
        $this->TLinkedList->ToArray(/* parameters */);
    
    }

    /**
     * 测试元素的批量尾端插入操作。
     */
    public function testInsertion() {
        logStart('Insertion');
        //下列代码已经通过测试，注释掉因为debug时候执行这个测试时间缓慢。
    /*
        log((float) memory_get_usage(true) / (1024.0 * 1024.0));
        $t = microtime(true);
        for ($i = 0; $i < 4000; ++$i) {
            $this->TLinkedList->Add($i);
        }
        $t = microtime(true) - $t;
        logging("TIME = {$t}");
        logging((float) memory_get_peak_usage(true) / (1024.0 * 1024.0));
    */
    }

    /**
     * 测试元素的批量尾端删除操作。
     */
    public function testDeletion() {
        logStart('Deletion');
        //下列代码已经通过测试，注释掉因为debug时候执行这个测试时间缓慢。
    /*
        for ($i = 0; $i < 1000; ++$i) {
            $this->TLinkedList->Add($i);
        }
        logging('The insertion done.');
        for ($i = 999; $i >= 0; --$i) {
            $this->TLinkedList->RemoveAt($i);
        }
        $this->assertEquals(0, $this->TLinkedList->Size(), 'not empty!');
    */
    }

    /**
     * Tests TLinkedList->InsertAll()
     */
    public function testInsertAll() {
        logStart('TLinkedList->InsertAll()');
        TList::PrepareGeneric(array ('T' => 'integer'));
        $c = new TList(10, true, array (1, 2, 3, 4, 5));
        $this->TLinkedList->InsertAll(0, $c);
        foreach ($this->TLinkedList as $value) {
            logging($value);
        }
    }

    /**
     * Tests TLinkedList->IndexOf()
     */
    public function testIndexOf() {
        logStart('TLinkedList->IndexOf()');
        logging('Start insertion...');
        for ($i = 0; $i < 100; ++$i) {
            $this->TLinkedList->Add($i);
            logging("A {$i} has been added at index {$i}, also for the next element.");
            $this->TLinkedList->Add($i++);
        }
        logging('Start searching...');
        for ($i = 0; $i < 100; $i += 2) {
            $this->assertEquals($i, $this->TLinkedList->IndexOf($i), $i);
            logging("Hits {$i}.");
        }
    }

    /**
     * Tests TLinkedList->LastIndexOf()
     */
    public function testLastIndexOf() {
        logStart('TLinkedList->LastIndexOf');
        logging('Start insertion...');
        for ($i = 0; $i < 100; ++$i) {
            $this->TLinkedList->Add($i);
            logging("A {$i} has been added at index {$i}, also for the next element.");
            $this->TLinkedList->Add($i++);
        }
        logging('Start searching...');
        for ($i = 0; $i < 100; $i += 2) {
            $this->assertEquals($i + 1, $this->TLinkedList->LastIndexOf($i), $i + 1);
            logging("Hits {$i}.");
        }
    }
}

