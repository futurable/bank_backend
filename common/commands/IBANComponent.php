<?php
namespace common\commands;

/**
 *  IBANComponent.php
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
 * IBANComponent for various IBAN-related tasks
 * 
 * @package   IBANComponent
 * @author    Jarmo Kortetjärvi
 * @copyright 2012 <jarmo.kortetjarvi@futurable.fi>
 * @license   GPLv3 or any later version
 * @version   2012-09-11
 */
 
Class IBANComponent{
	
	/**
	 * Generate IBAN code from bank/branch code
	 * 
	 * @access  public
	 * @param	int		$branchCode
	 * @param	int		$accountNumber
	 * @return  string 	$IBAN
	 */
	public static function generateFinnishIBANaccount($branchCode, $accountNumber){
		$iban = false;
		
		// Branch code is valid
		if( strlen($branchCode) == 6 AND strlen($accountNumber) > 0 AND strlen($accountNumber) <= 7){ 
			// Add zero-padding to the account number
     		$BBANAccount = BBANComponent::generateFinnishBBANaccount((int)$branchCode, (int)$accountNumber);

			// Country code
   			$countryCode  = 'FI';

			// Temporary validation bits
      		$dummyDigits = '00';

			$dummyIBAN = $countryCode.$dummyDigits.$BBANAccount;

			$checkDigits = self::generateIBANcheckDigits($dummyIBAN);

			$iban = $dummyIBAN = $countryCode.$checkDigits.$BBANAccount;
		}
		
		return $iban;
	}
	/**
	 * Generate IBAN check digits from IBAN
	 * 
	 * @access  public
	 * @param	string	$dummyIBAN
	 * @return  string 	$checkDigits
	 */
	private static function generateIBANcheckDigits($dummyIBAN){
		// Convert to machine format (strip spaces
		$iban = self::iban_to_machine_format($dummyIBAN);
		
		// Move first 4 chars (country code and checksum) to the end of the string
		$tempiban = substr($iban, 4).substr($iban, 0, 4);
		
		// Subsitutute letters with according numbers
		$tempiban = self::iban_checksum_string_replace($tempiban);
		
		// Calculate Mod-97
		$check = bcmod("$tempiban", "97");
		
		// Get check digits
		$checkDigits = 98 - $check;
		$checkDigits = sprintf("%02s", $checkDigits);
		
		return $checkDigits;
	}
        
        public static function getBranchCode($IBAN){
            if(self::verify_iban($IBAN)){
                return self::iban_get_branch_part($IBAN);
            }
        }
	
	# PHP IBAN - http://code.google.com/p/php-iban - LGPLv3
	
	# Verify an IBAN number.  Returns true or false.
	#  NOTE: Input can be printed 'IBAN xx xx xx...' or machine 'xxxxx' format.
	public static function verify_iban($iban) {
		self::_iban_load_registry();
		
		# First convert to machine format.
		$iban = self::iban_to_machine_format($iban);
	
		# Get country of IBAN
		$country = self::iban_get_country_part($iban);
	
		# Get length of IBAN
		if(strlen($iban)!= self::iban_country_get_iban_length($country)) { return false; }
	
		# Get checksum of IBAN
		$checksum = self::iban_get_checksum_part($iban);
	
		# Get country-specific IBAN format regex
		$regex = '/'.self::iban_country_get_iban_format_regex($country).'/';
	
		# Check regex
		if(preg_match($regex,$iban)) {
			# Regex passed, check checksum
			if(!self::iban_verify_checksum($iban)) { 
				return false;
			}
		}
		else {
			return false;
		}
	
		# Otherwise it 'could' exist
		return true;
	}
	
	# Convert an IBAN to machine format.  To do this, we
	# remove IBAN from the start, if present, and remove
	# non basic roman letter / digit characters
	public static function iban_to_machine_format($iban) {
		# Uppercase and trim spaces from left
		$iban = ltrim(strtoupper($iban));
		# Remove IBAN from start of string, if present
		$iban = preg_replace('/^IBAN/','',$iban);
		# Remove all non basic roman letter / digit characters
		$iban = preg_replace('/[^A-Z0-9]/','',$iban);
		return $iban;
	}
	
	# Get the country part from an IBAN
	public static function iban_get_country_part($iban) {
	 	$iban = self::iban_to_machine_format($iban);
	 	return substr($iban,0,2);
	}
	
	# Get the checksum part from an IBAN
	public static function iban_get_checksum_part($iban) {
	 	$iban = self::iban_to_machine_format($iban);
		return substr($iban,2,2);
	}
	
	# Get the BBAN part from an IBAN
	public static function iban_get_bban_part($iban) {
	 	$iban = self::iban_to_machine_format($iban);
	 	return substr($iban,4);
	}
	
	# Check the checksum of an IBAN - code modified from Validate_Finance PEAR class
	public static function iban_verify_checksum($iban) {
		# convert to machine format
		$iban = self::iban_to_machine_format($iban);
		# move first 4 chars (countrycode and checksum) to the end of the string
		$tempiban = substr($iban, 4).substr($iban, 0, 4);
		# subsitutute chars
		$tempiban = self::iban_checksum_string_replace($tempiban);
		# mod97-10
		$result = self::iban_mod97_10($tempiban);
		# checkvalue of 1 indicates correct IBAN checksum
		if ($result != 1) {
			return false;
		}
		return true;
	}
	
	# Find the correct checksum for an IBAN
	#  $iban  The IBAN whose checksum should be calculated
	public static function iban_find_checksum($iban) {
		$iban = self::iban_to_machine_format($iban);
		# move first 4 chars to right
		$left = substr($iban,0,2) . '00'; # but set right-most 2 (checksum) to '00'
		$right = substr($iban,4);
		# glue back together
		$tmp = $right . $left;
		# convert letters using conversion table
		$tmp = self::iban_checksum_string_replace($tmp);
		# get mod97-10 output
		$checksum = iban_mod97_10($tmp);
		return (98-$checksum);
	}
	
	# Set the correct checksum for an IBAN
	#  $iban  IBAN whose checksum should be set
	public static function iban_set_checksum($iban) {
		$iban = iban_to_machine_format($iban);
		return substr($iban,0,2) . iban_find_checksum($iban) . substr($iban,4);
	}
	
	# Character substitution required for IBAN MOD97-10 checksum validation/generation
	#  $s  Input string (IBAN)
	public static function iban_checksum_string_replace($s) {
		$iban_replace_chars = range('A','Z');
		foreach (range(10,35) as $tempvalue) { $iban_replace_values[]=strval($tempvalue); }
		return str_replace($iban_replace_chars,$iban_replace_values,$s);
	}
	
	# Perform MOD97-10 checksum calculation
	#  $s  Input string (IBAN)
	public static function iban_mod97_10($s) {
		$tr = intval(substr($s, 0, 1));
		for ($pos = 1; $pos < strlen($s); $pos++) {
			$tr *= 10;
			$tr += intval(substr($s,$pos,1));
			$tr %= 97;
		}
		return $tr;
	}
	
	# Get an array of all the parts from an IBAN
	public static function iban_get_parts($iban) {
	 return array(
	     'country'	=> 	self::iban_get_country_part($iban),
	 	 'checksum'	=>	self::iban_get_checksum_part($iban),
		 'bban'		=>	self::iban_get_bban_part($iban),
	 	 'bank'		=>	self::iban_get_bank_part($iban),
		 'country'	=>	self::iban_get_country_part($iban),
		 'branch'	=>	self::iban_get_branch_part($iban),
		 'account'	=>	self::iban_get_account_part($iban)
	        );
	}
	
	# Get the Bank ID (institution code) from an IBAN
	public static function iban_get_bank_part($iban) {
		$iban 		= self::iban_to_machine_format($iban);
		$country 	= self::iban_get_country_part($iban);
		$start 		= self::iban_country_get_bankid_start_offset($country);
		$stop 		= self::iban_country_get_bankid_stop_offset($country);
		
		if($start!=''&&$stop!='') {
			$bban 	= self::iban_get_bban_part($iban);
			return substr($bban,$start,($stop-$start+1));
		}
		return '';
	}
	
	# Get the Branch ID (sort code) from an IBAN
	public static function iban_get_branch_part($iban) {
		$branchCode = '';
		
		$iban 		= self::iban_to_machine_format($iban);
		$country 	= self::iban_get_country_part($iban);
		$start	 	= self::iban_country_get_branchid_start_offset($country);
		$stop 		= self::iban_country_get_branchid_stop_offset($country);
		
		if( $start != '' && $stop != '') {
		  	$bban 	= self::iban_get_bban_part($iban);
		  	$branchCode = substr($bban,$start,($stop-$start+1));
		}
		
		return $branchCode;
	}
	
	# Get the (branch-local) account ID from an IBAN
	public static function iban_get_account_part($iban) {
		$iban 		= self::iban_to_machine_format($iban);
		$country 	= self::iban_get_country_part($iban);
		$start 	= self::iban_country_get_branchid_stop_offset($country);
		if($start!='') {
			$bban 	= self::iban_get_bban_part($iban);
			return substr($bban,$start+1);
		}
		return '';
	}
	
	# Get the name of an IBAN country
	public static function iban_country_get_country_name($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'country_name');
	}
	
	# Get the domestic example for an IBAN country
	public static function iban_country_get_domestic_example($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'domestic_example');
	}
	
	# Get the BBAN example for an IBAN country
	public static function iban_country_get_bban_example($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'bban_example');
	}
	
	# Get the BBAN format (in SWIFT format) for an IBAN country
	public static function iban_country_get_bban_format_swift($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'bban_format_swift');
	}
	
	# Get the BBAN format (as a regular expression) for an IBAN country
	public static function iban_country_get_bban_format_regex($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'bban_format_regex');
	}
	
	# Get the BBAN length for an IBAN country
	public static function iban_country_get_bban_length($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'bban_length');
	}
	
	# Get the IBAN example for an IBAN country
	public static function iban_country_get_iban_example($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'iban_example');
	}
	
	# Get the IBAN format (in SWIFT format) for an IBAN country
	public static function iban_country_get_iban_format_swift($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'iban_format_swift');
	}
	
	# Get the IBAN format (as a regular expression) for an IBAN country
	public static function iban_country_get_iban_format_regex($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'iban_format_regex');
	}
	
	# Get the IBAN length for an IBAN country
	public static function iban_country_get_iban_length($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'iban_length');
	}
	
	# Get the BBAN Bank ID start offset for an IBAN country
	public static function iban_country_get_bankid_start_offset($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'bban_bankid_start_offset');
	}
	
	# Get the BBAN Bank ID stop offset for an IBAN country
	public static function iban_country_get_bankid_stop_offset($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'bban_bankid_stop_offset');
	}
	
	# Get the BBAN Branch ID start offset for an IBAN country
	public static function iban_country_get_branchid_start_offset($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'bban_branchid_start_offset');
	}
	
	# Get the BBAN Branch ID stop offset for an IBAN country
	public static function iban_country_get_branchid_stop_offset($iban_country) {
	 	return self::_iban_country_get_info($iban_country,'bban_branchid_stop_offset');
	}
	
	# Get the registry edition for an IBAN country
	public static function iban_country_get_registry_edition($iban_country) {
	 	return _iban_country_get_info($iban_country,'registry_edition');
	}
	
	# Get the list of all IBAN countries
	public static function iban_countries() {
	 	global $_iban_registry;
	 	return array_keys($_iban_registry);
	}
	
	public static function _iban_load_registry() {
	 	global $_iban_registry;
		# if the registry is not yet loaded, or has been corrupted, reload
		if(!is_array($_iban_registry) || count($_iban_registry)<1) {
			require_once 'registry.php';
			$data = $registry;
			$lines = preg_split("[\n]",$data);
			array_shift($lines); # drop leading description line
			
			# loop through lines
			foreach($lines as $line) {
				if($line!='') {
			    	# split to fields
			    	list($country,$country_name,$domestic_example,$bban_example,$bban_format_swift,$bban_format_regex,$bban_length,$iban_example,$iban_format_swift,$iban_format_regex,$iban_length,$bban_bankid_start_offset,$bban_bankid_stop_offset,$bban_branchid_start_offset,$bban_branchid_stop_offset,$registry_edition) = preg_split('[\|]',$line);
			    	# assign to registry
			    	$_iban_registry[$country] = array(
			        	'country'			=>	$country,
			 			'country_name'			=>	$country_name,
			 			'domestic_example'		=>	$domestic_example,
						'bban_example'			=>	$bban_example,
						'bban_format_swift'		=>	$bban_format_swift,
						'bban_format_regex'		=>	$bban_format_regex,
						'bban_length'			=>	$bban_length,
						'iban_example'			=>	$iban_example,
						'iban_format_swift'		=>	$iban_format_swift,
						'iban_format_regex'		=>	$iban_format_regex,
						'iban_length'			=>	$iban_length,
						'bban_bankid_start_offset'	=>	$bban_bankid_start_offset,
						'bban_bankid_stop_offset'	=>	$bban_bankid_stop_offset,
						'bban_branchid_start_offset'	=>	$bban_branchid_start_offset,
						'bban_branchid_stop_offset'	=>	$bban_branchid_stop_offset,
						'registry_edition'		=>	$registry_edition
			            );
			 	}
			}
		}
	}
	
	# Get information from the IBAN registry by example IBAN / code combination
	public static function _iban_get_info($iban,$code) {
		$country = iban_get_country_part($iban);
		return _iban_country_get_info($country,$code);
	}
	
	# Get information from the IBAN registry by country / code combination
	public static function _iban_country_get_info($country,$code) {
		global $_iban_registry;
		if(array_key_exists(strtoupper($country), $_iban_registry)){
			return $_iban_registry[strtoupper($country)][strtolower($code)];
		}
		else return false;
	}
	
}