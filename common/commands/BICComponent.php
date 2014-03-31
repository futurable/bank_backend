<?php
namespace common\commands;

use yii\base\Formatter;

class BICComponent
{
    public function getBICFromIBAN($IBAN){
        $BIC = false;
        
        if(IBANComponent::verify_iban($IBAN)){
            $branchCode = IBANComponent::getBranchCode($IBAN);
            $BIC = self::getBICFromBranchCode($branchCode);
        }
        else $BIC = false;
        
        return $BIC;
    }
    
    public function getBICFromBranchCode($branchCode) {
        $BIC = new Bic();
        
        $record=Bic::model()->find(array(
            'select'=>'bic',
            'condition'=>'branch_code=:branch_code',
            'params'=>array(':branch_code'=>$branchCode),
        ));

        return $record->bic;
    }
}
?>