<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Poststatus;
use common\models\Adminuser;

/* @var $this yii\web\View */
/* @var $model common\models\Post */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'tags')->textarea(['rows' => 6]) ?>
    <?php
    //硬编码,不推荐
    //    echo $form->field($model, 'status')
    //        ->dropDownList([1 => '草稿', 2 => '已发布'],['prompt' => '请选择状态'])
    ?>

    <?php
    //第一种方法：
//    $psObjs = Poststatus::find()->all();
//    $allStatus = ArrayHelper::map($psObjs, 'id', 'name');
//    echo $form->field($model, 'status')->dropDownList($allStatus, ['prompt' => '请选择状态']);
    ?>
    <?php
    //第二种方法：
    //    $psArray = Yii::$app->db->createCommand('select id,name from poststatus')->queryAll();
    //    $allStatus = ArrayHelper::map($psArray, 'id', 'name');
    //    echo $form->field($model, 'status')->dropDownList($allStatus, ['prompt' => '请选择状态']);
    ?>
    <?php
    //第三种方法:查询构建器:DAO,与数据库无关的
    //    $allStatus = (new \yii\db\Query())
    //        ->select(['name', 'id'])
    //        ->from('poststatus')
    //        ->indexBy('id')
    //        /**
    //         * column()
    //         *Array
    //         * (
    //         * [0] => 草稿
    //         * [1] => 已发布
    //         * [2] => 已归档
    //         * )
    //         */
    //        ->column();
    //    echo $form->field($model, 'status')->dropDownList($allStatus, ['prompt' => '请选择状态']);

    /**
     * all()
     * Array
     * (
     * [0] => Array
     * (
     * [name] => 草稿
     * [id] => 1
     * )
     *
     * [1] => Array
     * (
     * [name] => 已发布
     * [id] => 2
     * )
     *
     * [2] => Array
     * (
     * [name] => 已归档
     * [id] => 3
     * )
     *
     * )
     */
    //->all();
    /**
     * scalar()
     * 草稿
     */
    //    ->scalar();
    ?>
    <?php
    //第四种方法:
    $allStatus = Poststatus::find()
        ->select(['name', 'id'])
        ->orderBy('position')
        ->indexBy('id')
        ->column();
    echo $form->field($model, 'status')->dropDownList($allStatus, ['prompt' => '请选择状态']);

    ?>
    <?php //$form->field($model, 'status')->textInput() ?>

    <?php // $form->field($model, 'create_time')->textInput() ?>

    <?php // $form->field($model, 'update_time')->textInput() ?>

    <?php // $form->field($model, 'author_id')->textInput() ?>
    <?= $form->field($model,'author_id')
        ->dropDownList(Adminuser::find()
            ->select(['nickname','id'])
            ->indexBy('id')
            ->column(),
            ['prompt'=>'请选择作者']);?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
