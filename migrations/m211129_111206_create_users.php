<?php

use app\models\User;
use yii\db\Migration;

/**
 * Class m211129_111206_create_users
 */
class m211129_111206_create_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin123'

            ],
            [
                'username' => 'user',
                'email' => 'user@gmail.com',
                'password' => 'user123'
            ],
        ];

        foreach ($users as $value) {
            $user = new User();

            $user->username = $value['username'];
            $user->email = $value['email'];
            $user->setPassword($value['password']);
            $user->generateAuthKey();
            $user->generateEmailVerificationToken();

            $user->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211129_111206_create_users cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211129_111206_create_users cannot be reverted.\n";

        return false;
    }
    */
}
