<?php

require_once 'FrameworkDSW\Containers.php';

require_once 'PHPUnit\Framework\TestCase.php';

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
     * 
     */
    public function testInsertion() {
        echo (float) memory_get_usage(true) / (1024.0 * 1024.0), "<br/>\n";
        $t = microtime(true);
        for ($i = 0; $i < 4000; ++$i) {
            $this->TLinkedList->Add($i);
        }
        $t = microtime(true) - $t;
        echo 'TIME=', $t, "<br/>\n";
        echo (float) memory_get_peak_usage(true) / (1024.0 * 1024.0), "<br/>\n";
    }

}

