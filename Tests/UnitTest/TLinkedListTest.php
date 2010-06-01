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
        Framework::Free($this->TLinkedList);
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
        // TODO Auto-generated TLinkedListTest->test__construct()
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
        // TODO Auto-generated TLinkedListTest->testSwap()
        $this->markTestIncomplete("Swap test not implemented");
        
        $this->TLinkedList->Swap(/* parameters */);
    
    }

    /**
     * Tests TLinkedList->ToArray()
     */
    public function testToArray() {
        // TODO Auto-generated TLinkedListTest->testToArray()
        $this->markTestIncomplete("ToArray test not implemented");
        
        $this->TLinkedList->ToArray(/* parameters */);
    
    }

}

