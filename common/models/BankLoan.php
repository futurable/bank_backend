<?php

namespace app\models;

/**
 * This is the model class for table "bank_loan".
 *
 * @property integer $id
 * @property string $type
 * @property string $amount
 * @property integer $term
 * @property string $term_interval
 * @property string $instalment
 * @property string $repayment
 * @property string $interval
 * @property string $interest
 * @property string $interest_updated
 * @property integer $event_day
 * @property string $create_date
 * @property string $grant_date
 * @property string $accept_date
 * @property string $modify_date
 * @property string $status
 * @property integer $bank_interest_id
 * @property integer $bank_account_id
 * @property integer $bank_currency_id
 * @property integer $bank_user_id
 *
 * @property BankAccount $bankAccount
 * @property BankCurrency $bankCurrency
 * @property BankUser $bankUser
 */
class BankLoan extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'bank_loan';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['type', 'term_interval', 'interval', 'status'], 'string'],
			[['amount', 'instalment', 'repayment', 'interest'], 'number'],
			[['term', 'event_day', 'bank_interest_id', 'bank_account_id', 'bank_currency_id', 'bank_user_id'], 'integer'],
			[['term_interval', 'interest', 'bank_interest_id', 'bank_account_id', 'bank_user_id'], 'required'],
			[['interest_updated', 'create_date', 'grant_date', 'accept_date', 'modify_date'], 'safe']
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
			'amount' => 'Amount',
			'term' => 'Term',
			'term_interval' => 'Term Interval',
			'instalment' => 'Instalment',
			'repayment' => 'Repayment',
			'interval' => 'Interval',
			'interest' => 'Interest',
			'interest_updated' => 'Interest Updated',
			'event_day' => 'Event Day',
			'create_date' => 'Create Date',
			'grant_date' => 'Grant Date',
			'accept_date' => 'Accept Date',
			'modify_date' => 'Modify Date',
			'status' => 'Status',
			'bank_interest_id' => 'Bank Interest ID',
			'bank_account_id' => 'Bank Account ID',
			'bank_currency_id' => 'Bank Currency ID',
			'bank_user_id' => 'Bank User ID',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBankAccount()
	{
		return $this->hasOne(BankAccount::className(), ['id' => 'bank_account_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBankCurrency()
	{
		return $this->hasOne(BankCurrency::className(), ['id' => 'bank_currency_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBankUser()
	{
		return $this->hasOne(BankUser::className(), ['id' => 'bank_user_id']);
	}
}
