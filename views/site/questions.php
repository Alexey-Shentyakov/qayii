<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Questions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-questions">
    <h1><?= Html::encode($this->title) ?></h1>

<?php foreach ($questions as $question): ?>
<?= $question->id ?>
<br>
<?= $question->body ?>
<br>
<?= $question->created_at ?>

    <div id="answers-<?= $question->id ?>">
    <?php $question_answers = $answers[$question->id]; ?>

    <?php foreach ($question_answers as $answer): ?>

    <div class="alert alert-info" role="alert">
        <span class="label label-success"><?= $answer->id ?></span>
        <?= $answer->body ?>
    </div>

    <?php endforeach; ?>
    </div>

    <?php if (!Yii::$app->user->isGuest): ?>

    <button type="button" class="btn btn-primary" onclick="answer_question(<?= $question->id ?>)">answer</button>

    <?php endif; ?>

<hr>
<?php endforeach; ?>

<?php

echo LinkPager::widget([
    'pagination' => $pagination,
]);
?>
</div>

<script>
function get_answers(question_id) {
        $.ajax({
    url: '?r=site/get-answers-ajax&question-id=' + question_id,
    method: 'GET',
    success: function (data)
    {
        if (data.length > 0) {

            var a = '';
            
            data.forEach(
                function(answer) {
                    a += '<div class="alert alert-info" role="alert">';
                    a += '<span class="label label-success">' + answer.id + '</span>';
                    a += ' ';
                    a += answer.body;
                    a += '</div>';
                }
            );

            $('#answers-' + question_id).html(a);
        } else {
            alert("data length <= 0!");
        }
    }
});
}

function answer_question(question_id) {

    var answer_body = prompt("please enter your answer");
    
    	$.ajax({
		url: '?r=site/answer-question-ajax',
		method: 'POST',
        data: {questionid: question_id, answerbody: answer_body},
		success: function (data)
		{
			if (data.result != "ok") {
                alert("result is not ok!");
			}
            else {
                get_answers(question_id);
            }
		}
	});

	return false;
}
</script>
