<?php

namespace app\models;

/**
 * This is the model class for table "bank_loan_transaction".
 *
 * @property integer $id
 * @property integer $sequence_number
 * @property string $instalment_amount
 * @property string $interest_amount
 * @property string $notification_penalty_sent
 * @property string $create_date
 * @property string $due_date
 * @property string $event_date
 * @property integer $bank_loan_id
 * @property integer $bank_account_transaction_id
 */
class BankLoanTransaction extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'bank_loan_transaction';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['sequence_number', 'bank_loan_id', 'bank_account_transaction_id'], 'integer'],
			[['instalment_amount', 'interest_amount'], 'number'],
			[['notification_penalty_sent'], 'string'],
			[['create_date', 'due_date', 'event_date'], 'safe'],
			[['bank_loan_id', 'bank_account_transaction_id'], 'required']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'sequence_number' => 'Sequence Number',
			'instalment_amount' => 'Instalment Amount',
			'interest_amount' => 'Interest Amount',
			'notification_penalty_sent' => 'Notification Penalty Sent',
			'create_date' => 'Create Date',
			'due_date' => 'Due Date',
			'event_date' => 'Event Date',
			'bank_loan_id' => 'Bank Loan ID',
			'bank_account_transaction_id' => 'Bank Account Transaction ID',
		];
	}
}
