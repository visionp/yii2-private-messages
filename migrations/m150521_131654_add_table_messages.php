<?php

use yii\db\Schema;
use yii\db\Migration;

class m150521_131654_add_table_messages extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%messages}}', [
            'id'         => Schema::TYPE_PK . ' NOT NULL',
            'from_id'    => Schema::TYPE_INTEGER . ' NULL',
            'whom_id'    => Schema::TYPE_INTEGER . ' NOT NULL',
            'message'    => Schema::TYPE_STRING  . '(750) NOT NULL',
            'status'     => Schema::TYPE_INTEGER . ' DEFAULT 0',
            'is_delete_from' => Schema::TYPE_INTEGER . ' DEFAULT 0',
            'is_delete_whom' => Schema::TYPE_INTEGER . ' DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ], $tableOptions);

        $this->createIndex('idx-messages-from_id', '{{%messages}}', 'from_id');
        $this->createIndex('idx-messages-whom_id', '{{%messages}}', 'whom_id');
        $this->addForeignKey(
            'fk-messages-from_id-user-id', '{{%messages}}', 'from_id', '{{%user}}', 'id'
        );
        $this->addForeignKey(
            'fk-messages-whom_id-user-id', '{{%messages}}', 'whom_id', '{{%user}}', 'id'
        );
    }

    public function down()
    {
        echo "m150521_131654_add_table_messages cannot be reverted.\n";

        return false;
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
