<?php

namespace lajax\translatemanager\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Controller for managing multilinguality.
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class LanguageController extends Controller {

    /**
     * @var \lajax\translatemanager\Module TranslateManager module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public $defaultAction = 'list';

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'change-status', 'optimizer', 'scan', 'translate', 'save', 'dialog', 'message', 'view', 'create', 'update', 'delete', 'delete-source'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'change-status', 'optimizer', 'scan', 'translate', 'save', 'dialog', 'message', 'view', 'create', 'update', 'delete', 'delete-source'],
                        'roles' => $this->module->roles,
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'list' => [
                'class' => 'lajax\translatemanager\controllers\actions\ListAction',
            ],
            'change-status' => [
                'class' => 'lajax\translatemanager\controllers\actions\ChangeStatusAction',
            ],
            'optimizer' => [
                'class' => 'lajax\translatemanager\controllers\actions\OptimizerAction',
            ],
            'scan' => [
                'class' => 'lajax\translatemanager\controllers\actions\ScanAction',
            ],
            'translate' => [
                'class' => 'lajax\translatemanager\controllers\actions\TranslateAction',
            ],
            'save' => [
                'class' => 'lajax\translatemanager\controllers\actions\SaveAction',
            ],
            'dialog' => [
                'class' => 'lajax\translatemanager\controllers\actions\DialogAction',
            ],
            'message' => [
                'class' => 'lajax\translatemanager\controllers\actions\MessageAction',
            ],
            'view' => [
                'class' => 'lajax\translatemanager\controllers\actions\ViewAction',
            ],
            'create' => [
                'class' => 'lajax\translatemanager\controllers\actions\CreateAction',
            ],
            'update' => [
                'class' => 'lajax\translatemanager\controllers\actions\UpdateAction',
            ],
            'delete' => [
                'class' => 'lajax\translatemanager\controllers\actions\DeleteAction',
            ],
            'delete-source' => [
                'class' => 'lajax\translatemanager\controllers\actions\DeleteSourceAction',
            ],
            'import' => [
                'class' => 'lajax\translatemanager\controllers\actions\ImportAction',
            ],
            'export' => [
                'class' => 'lajax\translatemanager\controllers\actions\ExportAction',
            ],
        ];
    }

    /**
     * Finds the Language model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Language the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id) {
        if (($model = \lajax\translatemanager\models\Language::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
    /**
     * Returns an ArrayDataProvider consisting of language elements.
     * @param array $languageSources
     * @return \yii\data\ArrayDataProvider
     */
    public function createLanguageSourceDataProvider($languageSources) {

        $data = [];
        foreach ($languageSources as $category => $messages) {
            foreach ($messages as $message => $boolean) {
                $data[] = [
                    'category' => $category,
                    'message' => $message
                ];
            }
        }

        return new \yii\data\ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false
        ]);
    }

}
