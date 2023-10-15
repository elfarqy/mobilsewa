<?php

namespace backend\models;

use common\models\Approval;
use common\models\VehicleUsage;
use Yii;
use yii\db\Exception;

class PermissionTransportForm extends \yii\base\Model
{
    public $driver, $approvals, $vehicle, $tmpVal;

    public function rules()
    {
        return [
            [['driver', 'approvals', 'vehicle'], 'required'],
            [['tmpVal'], 'safe'],
        ];
    }


    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $decodedData = json_decode($this->tmpVal);
        $user = Yii::$app->user->identity;

        $transaction = Yii::$app->db->beginTransaction();

        $form = new VehicleUsage([
            'driver_id' => $this->driver,
            'vehicle_id' => $this->vehicle,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $user->id,
        ]);

        if (!$form->save()){
            $this->addErrors($form->errors);
            return false;
        }

        $approval = [];
        $sequence = 1;
        foreach ($decodedData as $decodedDatum){
            $approval[] = [
                'user_id' => $decodedDatum->id,
                'vehicle_usage_id' => $form->id,
                'sequence' => $sequence,
                'approval_status' => 'pending',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $sequence += 1;
        }

        Yii::$app->db->createCommand()->batchInsert(Approval::tableName(), array_keys(reset($approval)), $approval)->execute();

        try {
            $transaction->commit();
        } catch (Exception $exception){
            $transaction->rollBack();
            return false;
        }


        return true;
    }
}