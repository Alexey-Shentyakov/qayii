<?php

namespace app\models;

use Yii;
use yii\base\Model;

class AskQuestionForm extends Model {
    public $body;

    public function rules()
    {
        return [
            [['body'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'body' => 'Question Body',
        ];
    }
}
