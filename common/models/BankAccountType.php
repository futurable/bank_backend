<?php

namespace app\models;

/**
 * This is the model class for table "bank_account_type".
 *
 * @property integer $id
 * @property string $type
 * @property string $description
 *
 * @property BankAccount[] $bankAccounts
 * @property BankInterest[] $bankInterests
 */
class BankAccountType extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'bank_account_type';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['type'], 'string', 'max' => 32],
			[['description'], 'string', 'max' => 256]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'type' => 'Type',
			'description' => 'Description',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBankAccounts()
	{
		return $this->hasMany(BankAccount::className(), ['bank_account_type_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBankInterests()
	{
		return $this->hasMany(BankInterest::className(), ['bank_account_type_id' => 'id']);
	}
}
