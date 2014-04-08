<?php

namespace common\models;

/**
 * This is the model class for table "bank_profile".
 *
 * @property integer $user_id
 * @property string $lastname
 * @property string $firstname
 * @property string $company
 */
class BankProfile extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'bank_profile';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id'], 'required'],
			[['user_id'], 'integer'],
			[['lastname', 'firstname'], 'string', 'max' => 50],
			[['company'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'user_id' => 'User ID',
			'lastname' => 'Lastname',
			'firstname' => 'Firstname',
			'company' => 'Company',
		];
	}
}
