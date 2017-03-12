<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;


/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";


?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Progress;
use kartik\daterange\DateRangePicker;
use kartik\dialog\Dialog;
use yii\web\JsExpression;
use yii\helpers\Url;
<?php
if (($tableSchema = $generator->getTableSchema()) !== false) {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (strpos($column->name, "id_") !== false) {
            echo "use backend\models\Tb".ucfirst(str_replace("id_", "", $column->name))."s;\n";
        }
        /*if (++$count < 6) {
            //echo $columnDisplay ."\n";
        } else {
            //echo "//" . $columnDisplay . " \n";
        }*/
    }
}
?>

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
<?= !empty($generator->searchModelClass) ? " * @var " . ltrim($generator->searchModelClass, '\\') . " \$searchModel\n" : '' ?>
 */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
    <?php /*<div class="page-header">
        <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>
    </div>*/ ?>
<?php if (!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <p>
        <?= "<?php /* echo " ?>Html::a(<?= $generator->generateString('Create {modelClass}', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?>, ['create'], ['class' => 'btn btn-success'])<?= "*/ " ?> ?>
    </p>

<?php echo "\t<?php \n"; ?>
        yii\bootstrap\Modal::begin([
            'header' => 'Carregando soliciação, aguarde.',
            'id'=>'editModalId',
            'class' =>'modal',
            'size' => 'modal-lg',
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => false, 'tabindex'=>'-1']
            
        ]);
            echo "<div class='modalContent'>";
            echo Progress::widget([
                'percent' => 100,
                'barOptions' => ['class' => 'progress-bar-success'],
                'options' => ['class' => 'active progress-striped']
            ]);
            echo "</div>";
        yii\bootstrap\Modal::end();

        $this->registerJs(
            "$(document).on('ready', function() {
                $('#editModalId').css('margin-top', ($(window).height() - 330) / 2);
                $(document).on('pjax:beforeSend', function(event, xhr, settings) {
                    $('#editModalId').modal('show');
                    <?php /*//$('body').animate({ scrollTop: 200 }, 1000);*/ ?>
                    $('body').scrollTop($(this)[0].scrollHeight);
                });
                $('#w1-togdata-all').click(function() {
                    $('#editModalId').modal('show');
                    <?php /*//$('body').animate({scrollTop:},800);*/ ?>
                });
                $('#w1-togdata-page').click(function() {
                    $('#editModalId').modal('show');
                    <?php /*//$('body').animate({scrollTop:$(this).offset().top},800);*/ ?>
                });
                $('.class-delete').on('click', function() {
                    var id = $(this).attr('id');
                    krajeeDialog.confirm('Confirma a exclusão deste item?', function (result) {
                        if (result) {
                            $.ajax({
                                url: '".Url::toRoute("<?= str_replace("-", "", Inflector::camel2id(StringHelper::basename($generator->modelClass))); ?>/delete")."?id='+id,
                                type: 'POST',
                                data: {'id': id},
                                success: function(result2) {
                                    krajeeDialog.alert('Registro removido com sucesso!');
                                    //$.pjax.reload({container:'#my-grid-view'});
                                    location.reload();
                                },
                                error: function(result3) {
                                    krajeeDialog.alert('Erro ao remover registro!');
                                },
                            });
                            
                        }
                    });
                });
            });
        ");
        $this->registerJs(
            "jQuery(document).ready(function($){
                $(document).on('pjax:success', function() {
                    $('#editModalId').modal('hide');
                });
            });"
        );
    ?>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= 
    "<?php Pjax::begin([
         'id' => 'lessons-grid-container-id',
            'timeout' => false,
            'enablePushState' => false,
        ]);
    ?>" 
    ?>

    <?= "<?php echo " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            //['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if($column->name == "id"){
            $columnDisplay = "\t\t\t[
                'attribute' => 'id',
                'contentOptions'=>['style'=>'width: 8%; text-align: center;']
            ],";
        } elseif($column->type == "smallint"){
            $columnDisplay = "\t\t\t[
                'attribute' => '$column->name',
                'class' => 'kartik\grid\BooleanColumn',
                'contentOptions'=>['style'=>'width: 8%; text-align: center;']
            ],";
        }elseif(stripos($column->name, "id_") !== false){
            $columnDisplay = "\t\t\t[
                'attribute' => 'id".ucfirst(str_replace("id_", "", $column->name))."',
                'value' => 'id".ucfirst(str_replace("id_", "", $column->name)).".nome',
                //'filter'=> ArrayHelper::map(Tb".ucfirst(str_replace("id_", "", $column->name))."s"."::find()->asArray()->all(), 'id', 'nome'),
                'filter' => Html::activeDropDownList(\$searchModel, '"."id".ucfirst(str_replace("id_", "", $column->name))."', ArrayHelper::map(Tb".ucfirst(str_replace("id_", "", $column->name))."s"."::find()->asArray()->all(), 'id', 'nome'),['class'=>'form-control','prompt' => 'Selecione ".ucfirst(str_replace("id_", "", $column->name))."'])
            ],";
        } elseif ($column->type === 'date') {
            $columnDisplay = "            ['attribute' => '$column->name','format' => ['date',(isset(Yii::\$app->modules['datecontrol']['displaySettings']['date'])) ? Yii::\$app->modules['datecontrol']['displaySettings']['date'] : 'd-m-Y']],";

        } elseif ($column->type === 'time') {
            $columnDisplay = "            ['attribute' => '$column->name','format' => ['time',(isset(Yii::\$app->modules['datecontrol']['displaySettings']['time'])) ? Yii::\$app->modules['datecontrol']['displaySettings']['time'] : 'H:i:s A']],";
        } elseif ($column->type === 'datetime' || $column->type === 'timestamp') {
            $columnDisplay = "            [
                'attribute' => '$column->name',
                //'format' => ['datetime',(isset(Yii::\$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::\$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']
                'contentOptions' => ['style'=>'width: 10%; text-align: center'],
                'value'=> function (\$model){
                    return date('d/m/Y H:i:s', strtotime(\$model->$column->name));
                },
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' =>([
                    'model' => \$searchModel,
                    'attribute' => '$column->name',
                    'presetDropdown' => TRUE,                
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'timePicker' => true,
                        'timePickerIncrement' => 10,
                        'locale' => [
                            'format' => 'd/m/Y H:i'
                        ],
                        'opens' => 'left'
                    ]
                ])
            ],";
        } else {
            $columnDisplay = "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',";
        }
        if (++$count < 6) {
            echo $columnDisplay ."\n";
        } else {
            echo "" . $columnDisplay . " \n";
        }
    }
}
?>

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style'=>'width: 8%; text-align: center;'],
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return '<a title="Excluir" aria-label="Excluir" data-pjax="0" >
                                    <span style="cursor:pointer;" id="'.$model->id.'" class="glyphicon glyphicon-trash class-delete"></span>
                                </a>';
                    },
                    /*'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['<?= (empty($generator->moduleID) ? '' : $generator->moduleID . '/') . $generator->controllerID?>/view', <?= $urlParams ?>, 'edit' => 't']),
                            ['title' => Yii::t('yii', 'Edit'),]
                        );
                    }*/
                ],
            ],
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => false,

        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type' => 'info',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> Criar', ['create'], ['class' => 'btn btn-success']).' '.Html::a('<i class="glyphicon glyphicon-repeat"></i> Limpar filtro', ['index'], ['class' => 'btn btn-info']),
            //'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> Limpar filtro', ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]); Pjax::end(); ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>

</div>
<style>
.wrap > .container { padding: 0; }
</style>