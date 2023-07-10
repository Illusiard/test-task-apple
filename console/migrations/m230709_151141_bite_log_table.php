<?php

use yii\db\Migration;

class m230709_151141_bite_log_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%bite_log}}', [
            'id'         => $this->primaryKey(),
            'apple_id'   => $this->integer()->notNull()->comment('Яблоко'),
            'eater_id'   => $this->integer()->notNull()->comment('Едок'),
            'created_at' => $this->dateTime()->notNull()->comment('Дата создания'),
            'percent'    => $this->integer()->defaultValue(100)->comment('Откушенный процент'),
        ], $tableOptions);

        $this->addForeignKey('fk_bite_log_apple', '{{%bite_log}}', 'apple_id', '{{%apple}}', 'id');
        $this->addForeignKey('fk_bite_log_user', '{{%bite_log}}', 'eater_id', '{{%user}}', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_bite_log_apple', '{{%bite_log}}');
        $this->dropForeignKey('fk_bite_log_user', '{{%bite_log}}');

        $this->dropTable('{{%bite_log}}');
    }
}
