<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
//$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<p></p>
<?php /*
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
*/ ?>
<div class="panel panel-info">
    <?php /*<div class="page-header">
        <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>
    </div>*/ ?>
    <div class="panel-heading"><?= "<?= " ?>Html::encode($this->title) ?></div>
    
    <?= "<?= " ?>DetailView::widget([
        'model' => $model,
        'condensed' => false,
        'hover' => true,
        /*'mode' => Yii::$app->request->get('edit') == 't' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
        'panel' => [
            'heading' => $this->title,
            'type' => DetailView::TYPE_INFO,
        ],*/
        'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {

        $format = $generator->generateColumnFormat($column);
        if (stripos($column->name, "id_") !== false) {
            echo
"            [
                'attribute' => '$column->name',
                'value' => \$model->id".ucfirst(str_replace("id_", "", $column->name))."->nome,
            ],\n";
        }
        if ($column->type === 'date') {
            echo
"            [
                'attribute' => '$column->name',
                'format' => [
                    'date', (isset(Yii::\$app->modules['datecontrol']['displaySettings']['date']))
                        ? Yii::\$app->modules['datecontrol']['displaySettings']['date']
                        : 'd-m-Y'
                ],
                'type' => DetailView::INPUT_WIDGET,
                'widgetOptions' => [
                    'class' => DateControl::classname(),
                    'type' => DateControl::FORMAT_DATE
                ]
            ],\n";

        } elseif ($column->type === 'time') {
            echo
"            [
                'attribute' => '$column->name',
                'format' => [
                    'time', (isset(Yii::\$app->modules['datecontrol']['displaySettings']['time']))
                        ? Yii::\$app->modules['datecontrol']['displaySettings']['time']
                        : 'H:i:s A'
                ],
                'type' => DetailView::INPUT_WIDGET,
                'widgetOptions' => [
                    'class' => DateControl::classname(),
                    'type' => DateControl::FORMAT_TIME
                ]
            ],\n";
        } elseif ($column->type === 'datetime' || $column->type === 'timestamp') {
            echo
"            [
                'attribute' => '$column->name',
                'format' => [
                    'datetime', (isset(Yii::\$app->modules['datecontrol']['displaySettings']['datetime']))
                        ? Yii::\$app->modules['datecontrol']['displaySettings']['datetime']
                        : 'd-m-Y H:i:s A'
                ],
                'type' => DetailView::INPUT_WIDGET,
                'widgetOptions' => [
                    'class' => DateControl::classname(),
                    'type' => DateControl::FORMAT_DATETIME
                ]
            ],\n";
        } else {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
        ],
        'deleteOptions' => [
            'url' => ['delete', 'id' => $model-><?=$generator->getTableSchema()->primaryKey[0]?>],
        ],
        'enableEditMode' => true,
    ]) ?>
    <div class="panel-footer">
        <?= "<?="; ?> Html::a('Voltar', ['<?= str_replace('-', '', Inflector::camel2id(StringHelper::basename($generator->modelClass))) ?>/index', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<style>
.wrap > .container { padding: 0; }
</style>