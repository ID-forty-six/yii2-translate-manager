<?php

use lajax\translatemanager\models\ExportForm;
use lajax\translatemanager\models\Language;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

/* @var $this yii\web\View */
/* @var $model ExportForm */

$this->title = Yii::t('language', 'Export');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="language-export col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fromLanguage')->dropDownList(ArrayHelper::map(

    Yii::$app->db->createCommand("SELECT `language_id`, `name_ascii`

        FROM language WHERE `status` > 0  AND

        ((select count(*)  from language_source where disabled = 0)
        -
        (select count(*)  from language_translate

        INNER JOIN language_source on language_source.id = language_translate.id AND language_source.disabled = 0

        where language = language.language_id AND trim(translation) != '' )) = 0

        ORDER BY name_ascii ASC")->queryAll()

      , 'language_id', 'name_ascii'),

      ['options' => ['en-US' => ['Selected'=>true]], 'prompt' => Yii::t('app', 'choose the language ...')]

      )->label('Translation from language (100% translated only)') ?>


    <?= $form->field($model, 'exportLanguages')->listBox(ArrayHelper::map(

    Yii::$app->db->createCommand("SELECT `language_id`,

      ((select count(*)  from language_source where disabled = 0)
      -
      (select count(*)  from language_translate

      INNER JOIN language_source on language_source.id = language_translate.id AND language_source.disabled = 0

      where language = language.language_id AND trim(translation) != '' )) AS `numero`,

       CONCAT(`name_ascii`, ' - (',

          (select count(*)  from language_source where disabled = 0)
          -
          (select count(*)  from language_translate

          INNER JOIN language_source on language_source.id = language_translate.id AND language_source.disabled = 0

          where language = language.language_id AND trim(translation) != '' )


          ,')') AS `line` FROM language WHERE status > 0 ORDER BY numero DESC")->queryAll()

      , 'language_id', 'line'), [
        'multiple' => false,
        'size' => 10,
    ])->label('Export language to translate') ?>

    <?php $model->type = 0;?>

    <?= $form->field($model, 'type')->radioList([
             0 => 'missing entries',
             1 => 'all entries',
    ])->label('Export') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('language', 'Export'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
