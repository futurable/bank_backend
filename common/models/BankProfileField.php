<?php

namespace app\models;

/**
 * This is the model class for table "bank_profile_field".
 *
 * @property integer $id
 * @property string $varname
 * @property string $title
 * @property string $field_type
 * @property integer $field_size
 * @property integer $field_size_min
 * @property integer $required
 * @property string $match
 * @property string $range
 * @property string $error_message
 * @property string $other_validator
 * @property string $default
 * @property string $widget
 * @property string $widgetparams
 * @property integer $position
 * @property integer $visible
 */
class BankProfileField extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'bank_profile_field';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['varname', 'title', 'field_type'], 'required'],
			[['field_size', 'field_size_min', 'required', 'position', 'visible'], 'integer'],
			[['varname', 'field_type'], 'string', 'max' => 50],
			[['title', 'match', 'range', 'error_message', 'default', 'widget'], 'string', 'max' => 255],
			[['other_validator', 'widgetparams'], 'string', 'max' => 5000]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'varname' => 'Varname',
			'title' => 'Title',
			'field_type' => 'Field Type',
			'field_size' => 'Field Size',
			'field_size_min' => 'Field Size Min',
			'required' => 'Required',
			'match' => 'Match',
			'range' => 'Range',
			'error_message' => 'Error Message',
			'other_validator' => 'Other Validator',
			'default' => 'Default',
			'widget' => 'Widget',
			'widgetparams' => 'Widgetparams',
			'position' => 'Position',
			'visible' => 'Visible',
		];
	}
}
