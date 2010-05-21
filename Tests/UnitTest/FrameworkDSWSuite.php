<?php
/**
 * FrameworkDSWSuite
 * @author  ExSystem
 * @version $Id$
 * @since   separate file since reversion 1
 */
 
require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * Static test suite.
 */
class FrameworkDSWSuite extends PHPUnit_Framework_TestSuite {

	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName('FrameworkDSWSuite');

	}

	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self();
	}
}

