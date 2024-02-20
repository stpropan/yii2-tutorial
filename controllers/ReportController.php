<?php

namespace app\controllers;

use app\models\Report;
use app\models\ReportSearch;
use app\models\Status;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReportController implements the CRUD actions for Report model.
 */
class ReportController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Report models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $user = User::getInstance();
        if (!$user) {
            return $this->goHome();
        }
        $searchModel = new ReportSearch();

        // Если администратор, то выбирать записи с любым id пользователя
        if ($user->isAdmin()) {
            $dataProvider = $searchModel->search($this->request->queryParams);

            return $this->render('index_admin', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        // Если не админ, выбирать только записи с таким id пользователя
        $dataProvider = $searchModel->search($this->request->queryParams, $user->id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Report model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $user = User::getInstance();
        if (!$user) {
            return $this->goHome();
        }

        $model = new Report();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Добавить id пользователя, который создал отчет
                $model->user_id = $user->id;
                // Добавить статус "Новая" для нового отчета
                $model->status_id = Status::NEW_STATUS_ID;
                // Перенос сохранения ниже для предварительного добавления статуса и пользователя в отчет
                if ($model->save()) {
                    return $this->redirect(['index']);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Report model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $user = User::getInstance();
        if (!$user || !$user->isAdmin()) {
            return $this->goHome();
        }

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Report model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Report the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Report::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
