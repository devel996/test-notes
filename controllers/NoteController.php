<?php

namespace app\controllers;

use app\helpers\ResponseHelper;
use app\models\Note;
use app\models\User;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * NoteController implements the CRUD actions for Note model.
 */
class NoteController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return array_merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => HttpBearerAuth::class,
                    'except' => ['view', 'index']
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'index' => ['GET'],
                        'view' => ['GET'],
                        'create' => ['POST'],
                        'update' => ['PUT'],
                        'delete' => ['DELETE'],
                    ],
                ],
            ]
        );
    }

    public function beforeAction($action)
    {
        $bearerToken = explode(' ', \Yii::$app->request->headers['authorization']);

        if ($bearerToken[0] == 'Bearer') {
            $user = User::findIdentityByAccessToken($bearerToken[1]);
            if ($user) {
                \Yii::$app->user->login($user);
            }
        } elseif(\Yii::$app->user->id) {
            \Yii::$app->user->logout();
        }

        return parent::beforeAction($action);
    }

    public function actionIndex($p = 1)
    {
        return ResponseHelper::success(Note::getAllNotes($p));
    }

    public function actionView($id)
    {
        if ($note = Note::getNoteById($id)) {
            return ResponseHelper::success($note);
        }

        return ResponseHelper::error('The requested page does not exist.');
    }

    public function actionCreate()
    {
        $model = new Note();

        if ($model->create($this->request->post())) {
            return ResponseHelper::success($model->getResponseColumns());
        }

        return ResponseHelper::error('Bad Request!', $model->errors);
    }

    public function actionUpdate($id)
    {
        $model = Note::findModel($id);

        if (!$model) {
            return ResponseHelper::error('The requested page does not exist.');
        }

        if(!$model->checkForConstraints()) {
            return ResponseHelper::error('Forbidden!', Note::$errors, 404);
        }

        if ($model->edit($this->request->post())) {
            return ResponseHelper::success($model->getResponseColumns());
        }

        return ResponseHelper::error('Bad Request!', $model->errors);
    }

    public function actionDelete($id)
    {
        $model = Note::findModel($id);

        if (!$model) {
            return ResponseHelper::error('The requested page does not exist.');
        }

        if(!$model->checkForConstraints()) {
            return ResponseHelper::error('Forbidden!', Note::$errors, 404);
        }

        if ($model->delete()) {
            return ResponseHelper::success();
        }

        return ResponseHelper::error('Something was wrong!', $model->errors);
    }
}
