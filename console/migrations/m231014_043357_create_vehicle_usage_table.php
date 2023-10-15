<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vehicle_usage}}`.
 */
class m231014_043357_create_vehicle_usage_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vehicle_usage}}', [
            'id' => $this->bigPrimaryKey(),
            'driver_id' => $this->bigInteger(),
            'vehicle_id'=> $this->bigInteger(),
            'status' => $this->string(25),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by'=> $this->bigInteger()
        ]);
        $this->addForeignKey('vehicle_usage_driver', 'vehicle_usage', 'driver_id', 'user', 'id');
        $this->addForeignKey('vehicle_usage_created', 'vehicle_usage', 'created_by', 'user', 'id');
        $this->addForeignKey('vehicle_usage_vehicle', 'vehicle_usage', 'vehicle_id', 'vehicle', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('vehicle_usage_driver', 'vehicle_usage');
        $this->dropForeignKey('vehicle_usage_created', 'vehicle_usage');
        $this->dropForeignKey('vehicle_usage_vehicle', 'vehicle_usage');
        $this->dropTable('{{%vehicle_usage}}');
    }
}
