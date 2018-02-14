<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\AskQuestionForm;
use app\models\User;
use app\models\Question;
use app\models\Answer;
use yii\data\Pagination;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'askQuestion', 'answerQuestionAjax'],
                'rules' => [
                    [
                        'actions' => ['logout', 'askQuestion', 'answerQuestionAjax'],
                        'allow' => true,
                        'roles' => ['@'],

                        // ?: matches a guest user (not authenticated yet)
                        // @: matches an authenticated user
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
 
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }
 
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionQuestions() {
        $query = Question::find()->orderBy('id');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $questions = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        $answers = [];
        foreach ($questions as $question) {
            $answers[$question->id] = $question->getAnswers()->all();
        }
        
        return $this->render('questions', [
            'questions' => $questions,
            'answers' => $answers,
            'pagination' => $pagination
        ]);
    }

    public function actionAskQuestion() {
        $model = new AskQuestionForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                // insert new question

                $current_user = Yii::$app->user->identity;

                $question = new Question();
                $question->body = $model->body;
                $question->author_id = $current_user->id;
                $question->save();

                Yii::$app->session->setFlash('askQuestionFormSubmitted');
                
                return $this->refresh();
            }
        }
    
        return $this->render('ask_question', [
            'model' => $model,
        ]);
    }

    public function actionGetAnswersAjax() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $question_id = Yii::$app->getRequest()->getQueryParam('question-id');
        $answers = Answer::find()->where(['question_id' => $question_id])->all();
        
        return $answers;
    }

    public function actionAnswerQuestionAjax() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $question_id = Yii::$app->request->post('questionid');
        $answer_body = Yii::$app->request->post('answerbody');

        $current_user = Yii::$app->user->identity;

        $answer = new Answer();
        $answer->body = $answer_body;
        $answer->question_id = $question_id;
        $answer->author_id = $current_user->id;
        $success = $answer->save();

        if ($success) {
			# insert answer success
			return ['result' => 'ok'];
		}
		else {
			# insert answer failed
			return ['result' => 'failed'];
		}
    }
}
