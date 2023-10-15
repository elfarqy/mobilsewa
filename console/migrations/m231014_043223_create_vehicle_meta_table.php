<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vehicle_meta}}`.
 */
class m231014_043223_create_vehicle_meta_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vehicle_meta}}', [
            'id' => $this->bigPrimaryKey(),
            'vehicle_id' => $this->bigInteger(),
            'key' => $this->string(),
            'value' => $this->text(),
            'status' => $this->string(25),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);
        $this->addForeignKey('vehicle_vehicle_meta', 'vehicle_meta', 'vehicle_id', 'vehicle', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('vehicle_vehicle_meta', 'vehicle_meta');
        $this->dropTable('{{%vehicle_meta}}');
    }
}
