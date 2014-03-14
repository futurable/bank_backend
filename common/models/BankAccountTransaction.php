<?php

namespace app\models;

/**
 * This is the model class for table "bank_account_transaction".
 *
 * @property integer $id
 * @property string $recipient_iban
 * @property string $recipient_bic
 * @property string $recipient_name
 * @property string $payer_iban
 * @property string $payer_bic
 * @property string $payer_name
 * @property string $event_date
 * @property string $create_date
 * @property string $modify_date
 * @property string $amount
 * @property string $reference_number
 * @property string $message
 * @property string $exchange_rate
 * @property string $currency
 * @property string $status
 *
 * @property BankAccount $payerIban
 */
class BankAccountTransaction extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'bank_account_transaction';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['recipient_bic', 'payer_iban', 'payer_bic', 'payer_name'], 'required'],
			[['event_date', 'create_date', 'modify_date'], 'safe'],
			[['amount', 'exchange_rate'], 'number'],
			[['status'], 'string'],
			[['recipient_iban', 'payer_iban'], 'string', 'max' => 32],
			[['recipient_bic', 'payer_bic'], 'string', 'max' => 11],
			[['recipient_name', 'payer_name'], 'string', 'max' => 35],
			[['reference_number'], 'string', 'max' => 20],
			[['message'], 'string', 'max' => 420],
			[['currency'], 'string', 'max' => 3]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'recipient_iban' => 'Recipient Iban',
			'recipient_bic' => 'Recipient Bic',
			'recipient_name' => 'Recipient Name',
			'payer_iban' => 'Payer Iban',
			'payer_bic' => 'Payer Bic',
			'payer_name' => 'Payer Name',
			'event_date' => 'Event Date',
			'create_date' => 'Create Date',
			'modify_date' => 'Modify Date',
			'amount' => 'Amount',
			'reference_number' => 'Reference Number',
			'message' => 'Message',
			'exchange_rate' => 'Exchange Rate',
			'currency' => 'Currency',
			'status' => 'Status',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPayerIban()
	{
		return $this->hasOne(BankAccount::className(), ['iban' => 'payer_iban']);
	}
}
