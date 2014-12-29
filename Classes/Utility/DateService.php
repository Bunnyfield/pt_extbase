<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Daniel Lienert <lienert@punkt.de>, Joachim Mathes <mathes@punkt.de>
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
 * Date Service
 *
 * @package pt_extbase
 * @subpackage Utility
 */
class Tx_PtExtbase_Utility_DateService implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var integer
	 */
	protected $overrideCurrentYear = NULL;

	/**
	 * @param integer $overrideCurrentYear
	 */
	public function setOverrideCurrentYear($overrideCurrentYear) {
		$this->overrideCurrentYear = $overrideCurrentYear;
	}

	/**
	 * @return integer
	 */
	public function getCurrentYear() {
		if ($this->overrideCurrentYear === NULL) {
			return intval(date('Y'));
		} else {
			return intval($this->overrideCurrentYear);
		}
	}

}