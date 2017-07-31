<?php
/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use lajax\translatemanager\bundles\TranslateManagerAsset;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
TranslateManagerAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="wrap">
            <?php

            if (Yii::$app->getRequest()->getUserIP() != '188.165.224.99' ) {
              $scan_menu = ['label' => 'Skanuoti', 'url' => ['/translatemanager/language/scan']];
              $create_menu =   ['label' => 'Kalbos sukūrimas', 'url' => ['/translatemanager/language/create']];
            } else {
              $scan_menu = '';
              $create_menu = '';
            };

                    NavBar::begin([
                        'brandLabel' => 'Biz-Catalogs.com TranslateManager',
                        'brandUrl' => '/translatemanager/',
                        'options' => [
                            'class' => 'navbar-inverse navbar-fixed-top',
                        ],
                    ]);
                    $menuItems = [
                        //['label' => Yii::t('language', 'Home'), 'url' => ['/']],
                        //['label' => Yii::t('language', 'Language'), 'items' => [
                                ['label' => 'Kalbų sąrašas', 'url' => ['/translatemanager/language/list']],
                        //         ['label' => Yii::t('language', 'Create'), 'url' => ['/translatemanager/language/create']],
                        //     ]
                        // ],
                        $create_menu,
                        $scan_menu,
                        //['label' => Yii::t('language', 'Optimize'), 'url' => ['/translatemanager/language/optimizer']],
                        // ['label' => Yii::t('language', 'Im-/Export'), 'items' => [
                                 ['label' => 'Eksportas', 'url' => ['/translatemanager/language/export']],
                                 ['label' => 'Importas', 'url' => ['/translatemanager/language/import']],
                        //      ]
                        //  ],
                    ];
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav navbar-right'],
                        'items' => $menuItems,
                    ]);
                    NavBar::end();



            ?>

            <div class="container">
                <?=
                Breadcrumbs::widget([
                    'homeLink'=> ['label' => Yii::t('yii', 'Home'), 'url' => '/translatemanager/'],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
                <?php
                foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                    echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
                } ?>
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; Biz-Catalogs.com translating system <?= date('Y') ?></p>
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
