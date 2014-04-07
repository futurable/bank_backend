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

class AccountController extends Controller
{
    public $defaultAction = 'create';
	public $debug = true;
    
    public function actionCreate() {
    	
    	$this->debug("Create action started");
    	
    	// TODO: fix this hack
    	$connection = \Yii::$app->db_core;
    	
       	// Get all the companies with no bank account
       	$command = $connection->createCommand('SELECT * FROM company WHERE bank_account_created IS NULL');
       	$companies = $command->query();
       	
       	$this->debug( count($companies). " companies found");
       	
       	$created = 0;
       	foreach($companies as $tmpcompany){
       		$company = new Company();
       		$company->attributes = $tmpcompany;
       		$company->id = $tmpcompany['id'];
       		
       		$this->debug("Using company '".$company->name."'");
       		
       		// Create a bank account
       		$this->createBankAccount($company);
       		
       		$command = $connection->createCommand('UPDATE company SET bank_account_created = NOW()');
       		$command->query();
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
    	$bankUser->password = Security::generatePasswordHash($bankPassword);
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
    	
    	// TODO: fix this hack
    	$connection = \Yii::$app->db_core;
    	
    	// @TODO: Fix this to use AR
    	$command = $connection->createCommand("INSERT INTO company_passwords SET bank_password = '{$bankPassword}', company_id ='{$company->id}'");
    	$command->query();
    	
    	$this->debug("Created an account: ".$bankAccount->iban);
    }
}