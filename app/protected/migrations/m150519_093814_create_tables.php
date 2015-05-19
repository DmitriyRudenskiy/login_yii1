<?php

class m150519_093814_create_tables extends CDbMigration
{
	public function up()
	{
        $tableOptions = null;

        if (Yii::app()->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{register}}',
            [
                'id' => 'pk',
                'email' => 'varchar(45) unique NOT NULL',
                'token' => 'varchar(32)'
            ],
            $tableOptions
        );

        $this->createTable(
            '{{user}}',
            [
                'id' => 'pk',
                'register_id' => 'int unique NOT NULL',
                'name' => 'varchar(50) NOT NULL',
            ],
            $tableOptions
        );

        if (Yii::app()->db->driverName === 'mysql') {
            $this->addForeignKey(
                'FK_register_user',
                '{{user}}',
                'register_id',
                '{{register}}',
                'id'
            );
        }

	}

	public function down()
	{
        $this->dropTable('{{user}}');
        $this->dropTable('{{register}}');
	}
}