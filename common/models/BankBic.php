<?php

namespace app\models;

/**
 * This is the model class for table "bank_bic".
 *
 * @property integer $id
 * @property integer $branch_code
 * @property string $bic
 * @property string $bank_name
 * @property string $create_date
 *
 * @property BankAccount[] $bankAccounts
 */
class BankBic extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'bank_bic';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['branch_code', 'bic', 'bank_name', 'create_date'], 'required'],
			[['branch_code'], 'integer'],
			[['create_date'], 'safe'],
			[['bic'], 'string', 'max' => 11],
			[['bank_name'], 'string', 'max' => 256],
			[['branch_code'], 'unique'],
			[['bic'], 'unique']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'branch_code' => 'Branch Code',
			'bic' => 'Bic',
			'bank_name' => 'Bank Name',
			'create_date' => 'Create Date',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBankAccounts()
	{
		return $this->hasMany(BankAccount::className(), ['bank_bic_id' => 'id']);
	}
}
