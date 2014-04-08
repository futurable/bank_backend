<?php
namespace common\commands;

use common\models\BankAccount;
use yii\validators\NumberValidator;
/**
 *  BBANComponent.php
 *
 *  Copyright information
 *
 *      Copyright (C) 2012 Jarmo Kortetjärvi <jarmo.kortetjarvi@futurable.fi>
 *
 *  License
 *
 *		This program is free software: you can redistribute it and/or modify
 *		it under the terms of the GNU General Public License as published by
 * 		the Free Software Foundation, either version 3 of the License, or
 *		(at your option) any later version.
 *
 *		This program is distributed in the hope that it will be useful,
 *		but WITHOUT ANY WARRANTY; without even the implied warranty of
 *		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *		GNU General Public License for more details.
 *
 *		You should have received a copy of the GNU General Public License
 *		along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * BBANComponent for various BBAN-related tasks
 * 
 * @package   IBANComponent
 * @author    Jarmo Kortetjärvi
 * @copyright 2012 <jarmo.kortetjarvi@futurable.fi>
 * @license   GPLv3 or any later version
 * @version   2012-09-11
 */
 
Class BBANComponent{
	
	/**
	 * Generate finnish BBAN code validation bit from account number
	 * 
	 * @access  public
	 * @param	int		$branchCode
	 * @param	int		$accountNumber
	 * @return  int(14) $bankAccount
	 */
	public static function generateFinnishBBANaccount($branchCode, $accountNumber = false){
		$bankAccount = false;
        
        if($accountNumber == false){
           	$lastIban = BankAccount::find()
           	->select('iban')
           	->orderBy('id DESC')
           	->one()
           	->iban;
           
       		$accountNumber = substr($lastIban, -8, -2) + 1;
       	}

		// If branchcode and account number are valid 
		if( strlen($branchCode) == 6 AND strlen($accountNumber) > 0 ){
			// If account number is too short, fill with zeros from left
			$accountNumber = sprintf("%07s", $accountNumber);
			
			$tempAccount = $branchCode.$accountNumber;
			$sumOfDigits = 0;

			// Count the validation bit with Luhn algorithm
	        for ($pointer = 0;  $pointer < 14; $pointer++) {
	                // Factor is 1 for odd numbers, 2 for even numbers
	        		if ($pointer % 2 == 0) {
	                        $factor = 2;
	                }
	                else {
	                        $factor = 1;
	                }
	                
	                $result = $factor * substr($tempAccount, $pointer, 1);
	                // If >= 10, add both numbers together
	                if ($result >= 10) {
	                	$result = substr($result, 0, 1) + substr($result, 1, 1);
	                }
	                
	                $sumOfDigits += $result;
	        }
	        
	        $validationBit = substr( $sumOfDigits * 9, -1 );
	        
	  		if( ($sumOfDigits + $validationBit) % 10 == 0 ){
	  			$bankAccount = $branchCode.$accountNumber.$validationBit;
	  		}
	  		else $bankAccount = false;
		}

		return $bankAccount;
	}
	
	/**
	 * Validate Finnish BBAN account
	 * @param string 	$BBAN
	 * @return bool		$valid		true if valid, else false
	 */
	public function validateFinnishBBANaccount($BBAN){
		// Format account 
	}
}
?>