<?php

namespace common\models;

/**
 * This is the model class for table "vehicle".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $plate_number
 * @property string|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property VehicleMeta[] $vehicleMetas
 * @property VehicleUsage[] $vehicleUsages
 */
class Vehicle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehicle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'plate_number'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'plate_number' => 'Plate Number',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[VehicleMetas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleMetas()
    {
        return $this->hasMany(VehicleMeta::class, ['vehicle_id' => 'id']);
    }

    /**
     * Gets query for [[VehicleUsages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleUsages()
    {
        return $this->hasMany(VehicleUsage::class, ['vehicle_id' => 'id']);
    }
}
