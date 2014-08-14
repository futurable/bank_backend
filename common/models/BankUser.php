<?php

namespace common\models;

/**
 * This is the model class for table "bank_user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $activkey
 * @property integer $createtime
 * @property integer $lastvisit
 * @property integer $superuser
 * @property integer $status
 *
 * @property BankAccount[] $bankAccounts
 * @property BankLoan[] $bankLoans
 */
class BankUser extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'bank_user';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['username', 'password', 'email'], 'required'],
			[['createtime', 'lastvisit', 'superuser', 'status'], 'integer'],
			[['username'], 'string', 'max' => 64],
			[['password'], 'string', 'max' => 512],
			[['email'], 'string', 'max' => 256],
			[['activkey'], 'string', 'max' => 128],
			[['username'], 'unique']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'activkey' => 'Activkey',
			'createtime' => 'Createtime',
			'lastvisit' => 'Lastvisit',
			'superuser' => 'Superuser',
			'status' => 'Status',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBankAccounts()
	{
		return $this->hasMany(BankAccount::className(), ['bank_user_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBankLoans()
	{
		return $this->hasMany(BankLoan::className(), ['bank_user_id' => 'id']);
	}
}
