<?php

use yii\db\Migration;
use app\models\User;
use app\models\Note;

/**
 * Class m211129_122234_create_notes
 */
class m211129_122234_create_notes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $user = User::findOne(1);

        $notes = [
            [
                'title' => 'What is Lorem Ipsum?',
                'text' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                'user_id' => $user->id,
                'publication_date' => '-2 day'
            ],
            [
                'title' => 'Why do we use it?',
                'text' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                'user_id' => $user->id,
                'publication_date' => 'now'
            ],
            [
                'title' => 'Where does it come from?',
                'text' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                'user_id' => $user->id,
                'publication_date' => 'now'
            ],
            [
                'title' => 'Where can I get some?',
                'text' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                'user_id' => $user->id,
                'publication_date' => '+2 day'
            ],
        ];

        foreach ($notes as $note) {

            $date = new DateTime($note['publication_date']);

            $model = new Note();
            $model->title = $note['title'];
            $model->text = $note['text'];
            $model->user_id = $note['user_id'];
            $model->publication_date = $date->format('Y-m-d');

            $model->save();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211129_122234_create_notes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211129_122234_create_notes cannot be reverted.\n";

        return false;
    }
    */
}
