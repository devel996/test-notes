<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notes}}`.
 */
class m211129_105235_create_notes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notes}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'text' => $this->text(),
            'publication_date' => $this->date(),
            'user_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);

        $this->addForeignKey('FK_user_notes', '{{%notes}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_user_notes', '{{%notes}}');

        $this->dropTable('{{%notes}}');
    }
}
