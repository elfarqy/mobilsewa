<?php

/** @var yii\web\View $this */

$this->title = 'Perijinan Sewa';

\backend\assets\PerijinanAsset::register($this);
?>
<div class="site-index">
    <div class="mt-5 offset-lg-3 col-lg-6">
        <h1><?= \yii\bootstrap5\Html::encode($this->title) ?></h1>

        <p>Isi kolom dibawah ini untuk membuat perijinan sewa</p>

        <?php $form = \yii\bootstrap5\ActiveForm::begin(); ?>

        <?= $form->field($model, 'vehicle')
            ->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Vehicle::find()->all(), 'id', function ($model) {
                return "[{$model['plate_number']}] {$model['name']}";
            }))->label('Kendaraan') ?>

        <?= $form->field($model, 'driver')
            ->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\User::find()->where(['role' => 'driver'])->all(), 'id', 'username')) ?>

        <?= $form->field($model, 'approvals')->dropDownList([]) ?>
        <?= $form->field($model, 'tmpVal')->hiddenInput()->label(false) ?>

        <div class="form-group">
            <?= \yii\bootstrap5\Html::submitButton('Buat', ['class' => 'btn btn-primary btn-block', 'name' => 'create-button', 'id' => 'btnSubmit']) ?>
        </div>

        <?php \yii\bootstrap5\ActiveForm::end(); ?>
    </div>


</div>
