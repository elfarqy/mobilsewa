<?php

namespace common\models;

/**
 * This is the model class for table "approval".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $vehicle_usage_id
 * @property int|null $sequence
 * @property string|null $approval_status
 * @property string|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $user
 * @property VehicleUsage $vehicleUsage
 */
class Approval extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'approval';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'vehicle_usage_id', 'sequence'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['approval_status', 'status'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['vehicle_usage_id'], 'exist', 'skipOnError' => true, 'targetClass' => VehicleUsage::class, 'targetAttribute' => ['vehicle_usage_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'vehicle_usage_id' => 'Vehicle Usage ID',
            'sequence' => 'Sequence',
            'approval_status' => 'Approval Status',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[VehicleUsage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleUsage()
    {
        return $this->hasOne(VehicleUsage::class, ['id' => 'vehicle_usage_id']);
    }
}
