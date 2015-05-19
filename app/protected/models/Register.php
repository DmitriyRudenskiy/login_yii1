<?php

/**
 * This is the model class for table "{{register}}".
 *
 * The followings are the available columns in table '{{register}}':
 * @property string $id
 * @property string $email
 * @property string $token
 */
class Register extends CActiveRecord
{
    /**
     * Длина токена для авторизации.
     */
    const LENGTH_TOKEN = 32;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{register}}';
	}

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'email'=>'Укажите Ваш электронный адрес',
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'length', 'max' => 45],
            ['token', 'length', 'max' => self::LENGTH_TOKEN],
            ['id, email, token', 'safe', 'on'=>'search'],
        ];
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('token',$this->token,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Register the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
