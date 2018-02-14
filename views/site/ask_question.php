<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AskQuestionForm */
/* @var $form ActiveForm */
?>
<div class="site-ask_question">

    <?php if (Yii::$app->session->hasFlash('askQuestionFormSubmitted')): ?>
    <div class="alert alert-success" role="alert">
    question submitted
    </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'body') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-ask_question -->
