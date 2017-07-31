<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\web\UploadedFile;
use lajax\translatemanager\models\ImportForm;
use lajax\translatemanager\bundles\LanguageAsset;
use lajax\translatemanager\bundles\LanguagePluginAsset;

/**
 * Class for exporting translations.
 */
class ImportAction extends \yii\base\Action {

    /**
     * @inheritdoc
     */
    public function init() {

        LanguageAsset::register($this->controller->view);
        LanguagePluginAsset::register($this->controller->view);
        parent::init();
    }

    /**
     * Show import form and import the uploaded file if posted
     * @return string
     * @throws \Exception
     */
    public function run() {

        $model = new ImportForm();

        if (Yii::$app->request->isPost) {

            $model->importFile = UploadedFile::getInstance($model, 'importFile');

            if ($model->validate()){

                    $result = $model->import();
                    $flash_type = $result[0];
                    $message = $result[1];

                    Yii::$app->getSession()->setFlash($flash_type, $message);

                    if (isset($result[2]) && count($result[2]) > 0) {

                      Yii::$app->getSession()->setFlash("warning", "Galimai blogų vertimų ID: ".implode(", ", $result[2]));

                    }


            }
        }

        return $this->controller->render('import', [
            'model' => $model,
        ]);
    }

}
