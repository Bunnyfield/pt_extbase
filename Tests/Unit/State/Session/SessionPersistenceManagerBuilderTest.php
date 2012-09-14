<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Unit test for session persistence manager builder
 * 
 * @author Michael Knoll 
 * @package Tests
 * @subpackage State\Session
 * @see Tx_PtExtbase_State_Session_SessionPersistenceManagerBuilder
 */
class Tx_PtExtbase_Tests_Unit_State_Session_SessionPersistenceManagerBuilderTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/** @test */
	public function getInstanceReturnsSingletonInstanceOfLifecycleManager() {
		$firstInstance = Tx_PtExtbase_Lifecycle_ManagerFactory::getInstance();
		$this->assertTrue(is_a($firstInstance, 'Tx_PtExtbase_Lifecycle_Manager'));
		$firstInstance->updateState(100);
		$secondInstance = Tx_PtExtbase_Lifecycle_ManagerFactory::getInstance();
		$this->assertEquals($firstInstance, $secondInstance);
	}
	
}

?>