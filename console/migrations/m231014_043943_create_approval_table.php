<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%approval}}`.
 */
class m231014_043943_create_approval_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%approval}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger(),
            'vehicle_usage_id' => $this->bigInteger(),
            'sequence' => $this->integer(),
            'approval_status' => $this->string(),
            'status' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);
        $this->addForeignKey('approval_user', 'approval', 'user_id', 'user', 'id');
        $this->addForeignKey('vehicle_usage_approval', 'approval', 'vehicle_usage_id', 'vehicle_usage', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('approval_user', 'approval');
        $this->dropForeignKey('vehicle_usage_approval', 'approval');
        $this->dropTable('{{%approval}}');
    }
}
