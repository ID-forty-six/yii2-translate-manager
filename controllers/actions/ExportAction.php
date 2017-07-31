<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\web\JsonResponseFormatter;
use yii\web\Response;
use yii\web\XmlResponseFormatter;
use lajax\translatemanager\Module;
use lajax\translatemanager\models\ExportForm;
use lajax\translatemanager\models\Language;
use lajax\translatemanager\bundles\LanguageAsset;
use lajax\translatemanager\bundles\LanguagePluginAsset;


/**
 * Class for exporting translations.
 */
class ExportAction extends \yii\base\Action {

    /**
     * @inheritdoc
     */
    public function init() {

        LanguageAsset::register($this->controller->view);
        LanguagePluginAsset::register($this->controller->view);
        parent::init();
    }

    /**
     * Show export form or generate export file on post
     * @return string
     */
    public function run() {

        /** @var Module $module */
        $module = Module::getInstance();

        $model = new ExportForm([
            //'format' => $module->defaultExportFormat,
        ]);

        if ($model->load(Yii::$app->request->post())) {

            $fileName = $model->fromLanguage."_".$model->exportLanguages.".csv";

             Yii::$app->response->setDownloadHeaders($fileName);

            return Language::generatecsv($model->fromLanguage, $model->exportLanguages, $model->type);

        }else {

            // if (empty($model->languages)){
            //     $model->exportLanguages = $model->getDefaultExportLanguages($module->defaultExportStatus);
            // }

            return $this->controller->render('export', [
                'model' => $model,
            ]);
        }
    }

}
