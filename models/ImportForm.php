<?php

namespace lajax\translatemanager\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;


/**
 * Import Form.
 * @author rhertogh <>
 * @since 1.5.0
 */
class ImportForm extends Model
{

    /**
     * @var UploadedFile The file to import (json or xml)
     */
    public $importFile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['importFile'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => ['csv'],
                //'mimeTypes' => ['text/plain'],
                'enableClientValidation' => false,
                'checkExtensionByMimeType' => false
            ],
        ];
    }

    /**
     * Import the uploaded file. Existing languages and translations will be updated, new ones will be created.
     * Source messages won't be updated, only created if they not exist.
     * @return array
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function import()
    {

        $importFileContent = file($this->importFile->tempName, FILE_IGNORE_NEW_LINES);

        // ++ failo ikelimas i masyva (jei nepavyksta - grazinamaklaida); jei pavyksta tuomet i masyvairasomi tik irasai kurie turi vertimus t.y. nera tusti.

        $data_array = array();

        foreach ($importFileContent as $key=>$line) {
            $line_components = explode(";", $line);
            if (count($line_components) == 3) {

                $line_components[2] = str_replace("\n", "", trim($line_components[2]));

                if ($line_components[2] != '') {
                  $data_array[$key] = $line_components;
                }

            } else {

              return ['danger', 'Blogas failo formatas!'];

            }
        }

        // ++ pirmos eilutes validacija  (ar kalba i kuriaisversti irasai egzistuoja);

          // ++ passelectinamos visos kalbos ir padaromas masyvas per array mapa
          $languages = ArrayHelper::map(Language::find()->all(), 'language_id', 'name');

          // ++ tikrinama ar source ir translate kalbos yra kalbu masyve jei ne tai grasinama klaida.

          if ( !isset($languages[$data_array[0][1]]) || !isset($languages[$data_array[0][2]]) ) {
              return ['danger', 'Vertimų faile blogai nustatytos kalbos!'];
          }

          // ++ nustatoma klaba i kuria importuosime duomenis
          $to_l = $data_array[0][2];
          unset($data_array[0]);

        // ++ viso masyvo validacija (lyginant EN-US kalba ir LAnguage to irasus). Jei randama klaidu grazinama klaida ir per kableli ID numeriai, kuriuose yra klaidu.

          // ++ paselectinami visi en-US vertimai, kurie nera tusti ir kuriu id yra source lenteleje.

            // ++ paselectina visus vertimus kurie turi buti isversti.
            $all_tr_ids = ArrayHelper::map(LanguageSource::find()->where(['disabled' => 0])->all(), 'id', 'message');

            // ++ paselctiname from language reiksmes.
            $from_l_ids = ArrayHelper::map(LanguageTranslate::find()->where(['language' => 'en-US'])->andwhere('id IN ('.implode(", ", array_keys($all_tr_ids)).')')->all(), 'id', 'translation');


          // ++ prasukami vertimu aray ir suvaliduojama su en-US kalbos masyvu.

          $errors = array();
          $warnings = array();

          if(count($data_array) > 0) {

            foreach ($data_array as $values) {

              if (isset($from_l_ids[$values[0]])) {


                $from  = str_ireplace(["\n", ";"], ["[newline]", "."], $from_l_ids[$values[0]]);

                if (Language::validateTranslation_critical($from, $values[2]) == false) {

                  $errors['badline'][$values[0]] = $values[0];

                }

                if (Language::validateTranslation_warning($from, $values[2]) == false && !isset($errors['badline'][$values[0]]) ) {

                  $warnings[$values[0]] = $values[0];

                }

              } else {

                $errors['nonexist'][$values[0]] = $values[0];

              }

            }

          }


          // ++ jei yra neatitikimu tai grazinama klaida su neatitikimu ID numeriais, kad butu sutikrintos eilutes.
          if (count($errors) > 0) {

            $msg  = array();

            if (isset($errors['badline'])) {
              $msg[] = "Blogų vertimų ID: ".implode(", ", $errors['badline']);

            }

            if (isset($errors['nonexist'])) {
              $msg[] = "Neegzistuojančių vertimų ID: ".implode(", ", $errors['nonexist']);

            }

            return ['danger', implode("<br>", $msg), $warnings];

          }

        // ++ duomenu importavimas t.y jei randama irasu tada importas vyksta. Po importo grazinamas sekmes pranesimas su kalba ik uria importavome ir kiekis importuotu vertimu.

        if(count($data_array) > 0) {

            foreach ($data_array as $values) {

              // ++ repleisinami [newline]
              $id = $values[0];
              $translation = str_ireplace("[newline]", "\n", $values[2]);

              $model = LanguageTranslate::find()->where(['id' => $id, 'language' => $to_l])->one();

              // ++ modelio issaugojimui paruosimas
              if(!$model) {
                $model = new LanguageTranslate();
                $model->id = $id;
                $model->language = $to_l;
                $model->isNewRecord = true;
              }

              $model->translation = $translation;

              $model->save(true);


            }

            return ['success', "Sėkmingai importuotų įrašų kiekis: ".count($data_array), $warnings];

        } else {

          return ['danger', "Vertimų faile nėra nė vieno vertimo!"];

        }


    }


}
