<?php

namespace common\models;

/**
 * This is the model class for table "company".
 *
 * @property integer $id
 * @property string $name
 * @property string $tag
 * @property string $business_id
 * @property string $email
 * @property integer $employees
 * @property integer $active
 * @property string $create_time
 * @property string $bank_account_created
 * @property string $openerp_database_created
 * @property integer $token_key_id
 * @property integer $industry_id
 *
 * @property Industry $industry
 * @property TokenKey $tokenKey
 * @property CostbenefitCalculation[] $costbenefitCalculations
 * @property Order[] $orders
 * @property Remark $remark
 * @property Salary[] $salaries
 */
class Company extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'company';
	}
	
    public static function getDb()
    {
        return \Yii::$app->db_core;
    }

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'tag', 'business_id', 'email', 'employees', 'token_key_id', 'industry_id'], 'required'],
			[['employees', 'active', 'token_key_id', 'industry_id'], 'integer'],
			[['create_time', 'bank_account_created', 'openerp_database_created'], 'safe'],
			[['name', 'email'], 'string', 'max' => 256],
			[['tag'], 'string', 'max' => 32],
			[['business_id'], 'string', 'max' => 9],
			[['tag'], 'unique']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Name',
			'tag' => 'Tag',
			'business_id' => 'Business ID',
			'email' => 'Email',
			'employees' => 'Employees',
			'active' => 'Active',
			'create_time' => 'Create Time',
			'bank_account_created' => 'Bank Account Created',
			'openerp_database_created' => 'Openerp Database Created',
			'token_key_id' => 'Token Key ID',
			'industry_id' => 'Industry ID',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIndustry()
	{
		return $this->hasOne(Industry::className(), ['id' => 'industry_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTokenKey()
	{
		return $this->hasOne(TokenKey::className(), ['id' => 'token_key_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCostbenefitCalculations()
	{
		return $this->hasMany(CostbenefitCalculation::className(), ['company_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrders()
	{
		return $this->hasMany(Order::className(), ['company_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRemark()
	{
		return $this->hasOne(Remark::className(), ['company_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSalaries()
	{
		return $this->hasMany(Salary::className(), ['company_id' => 'id']);
	}
}
