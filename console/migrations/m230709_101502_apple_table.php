<?php

use yii\db\Migration;

class m230709_101502_apple_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%apple}}', [
            'id'         => $this->primaryKey(),
            'owner_id'   => $this->integer()->notNull()->comment('Создатель'),
            'created_at' => $this->dateTime()->notNull()->comment('Дата создания'),
            'updated_at' => $this->dateTime()->notNull()->comment('Дата обновления'),
            'dropped_at' => $this->dateTime()->comment('Дата падения'),
            'deleted_at' => $this->dateTime()->comment('Удалено'),
            'color'      => $this->string()->notNull()->comment('Цвет'),
            'percent'    => $this->integer()->defaultValue(100)->comment('Целостность'),

        ], $tableOptions);

        $this->addForeignKey('fk_apple_owner', '{{%apple}}', 'owner_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_apple_owner', '{{%apple}}');
        $this->dropTable('{{%apple}}');
    }
}
