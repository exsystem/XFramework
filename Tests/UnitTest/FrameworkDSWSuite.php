<?php
/**
 * FrameworkDSWSuite
 * @author  ExSystem
 * @version $Id: FrameworkDSWSuite.php 12 2010-05-20 02:54:05Z exsystemchina@gmail.com $
 * @since   separate file since reversion 23
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

