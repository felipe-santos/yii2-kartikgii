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

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 */

$this->title = <?= $generator->generateString('Atualizar {modelClass}: ', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?> . ' ' . $model-><?= $generator->getNameAttribute() ?>;
//$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
//$this->params['breadcrumbs'][] = <?= $generator->generateString('Atualizar') ?>;
?>
<?php /*
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">
*/ ?>
<p></p>
<div class="panel panel-info">
    <?php /*
    <div class="page-header">
        <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>
    </div>
    */ ?>
    <div class="panel-heading"><?= "<?= " ?>Html::encode($this->title) ?></div>
    
    <?= "<?= " ?>$this->render('_form', [
        'model' => $model,
    ]) ?>
    
</div>
<style>
.wrap > .container { padding: 0; }
</style>