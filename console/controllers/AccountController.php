<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Company;
use yii\db\QueryBuilder;
use yii\helpers\Security;
use yii\db\Transaction;
use common\models\BankUser;
use common\models\BankProfile;
use common\models\BankAccount;
use common\commands\BBANComponent;
use common\commands\IBANComponent;
use yii\db\Expression;

class AccountController extends Controller
{
    public $defaultAction = 'create';
	public $debug = true;
    
    public function actionCreate() {
    	
    	$this->debug("Create action started");
    	
       	// Get all the companies with no bank account
        $companies = Company::find()
        ->where('bank_account_created IS NULL')
        ->all();
       	
       	$this->debug( count($companies). " companies found");
       	
       	$created = 0;
       	foreach($companies as $company){
       		$this->debug("Using company '".$company->name."'");
       		
       		// Create a bank account
       		$this->createBankAccount($company);

       		// @TODO: Fix this to use DbExpression
       		$company->bank_account_created = date('Y-m-d H:i:s');
       		$company->save();
       		
       		$created++;
       		
       		$this->debug();
       	}
       	
       	$this->debug("Created $created bank accounts");
       	$this->debug("Done!");
    }

    private function debug($message = false){
    	if($message===false) $debugMessage = "\n";
    	
    	else $debugMessage = date('d-m-Y H:i:s')." ".$message."\n";
    	
    	echo $debugMessage;
		if($this->debug === true);
    }
    
    private function createBankAccount($company){
    	// TODO: Start transaction	

    	// Create bank user
    	$bankUser = new BankUser();
    	$bankUser->username = $company->tag;
    	$bankUser->email = $company->email;
    	$bankPassword = Security::generateRandomKey(8);
    	//$bankUser->password = Security::generatePasswordHash($bankPassword);
    	// @TODO: fix this hack
    	$bankUser->password = crypt($bankPassword);
    	$bankUser->status = 1;
    	$bankSuccess = $bankUser->save();

    	// Create bank profile
    	$bankProfile = new BankProfile();
    	$bankProfile->user_id = $bankUser->id;
    	$bankProfile->company = $company->name;
    	$bankSuccess = $bankProfile->save() AND $bankSuccess;

    	// Create bank account
    	$bankAccount = new BankAccount();
    	$branchCode = 970300; // TODO: get branch number from conf
    	$bban = BBANComponent::generateFinnishBBANaccount($branchCode);
    	$accountNumber = substr($bban, -6);
    	$bankAccount->iban = IBANComponent::generateFinnishIBANaccount($branchCode, $accountNumber);
    	$bankAccount->name = "Checking account";
    	$bankAccount->bank_user_id = $bankUser->id;
    	$bankSuccess = $bankAccount->save() AND $bankSuccess;
    	
        $companyPasswords = $company->companyPasswords;
        $companyPasswords->bank_password = $bankPassword;
        $companyPasswords->save();
    	
    	$this->debug("Created an account: ".$bankAccount->iban);
    }
}