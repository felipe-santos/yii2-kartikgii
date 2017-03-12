<?php

use yii\helpers\StringHelper;

/**
 * This is the template for generating CRUD search class of the specified model.
 *
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;

/**
 * <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>

{
    <?php
    $class = 'backend\models\\'.$modelClass;
    $table = $class::getTableSchema();
    foreach ($table->columns as $column) {
        if(stripos($column->name, "id_") !== false){
            echo "public \$id".ucfirst(str_replace('id_', '', $column->name)).";";
        }
    }
    ?>

    public function rules()
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function integer2($attribute, $params){
        if (!$this->hasErrors()) {
            if(stripos($this->$attribute,';') !== false){
                if(!is_numeric(str_replace(';', '', $this->$attribute))){
                    $this->addError($attribute, Yii::t('app', '"'.$attribute.'" deve ser um número inteiro.'));
                }
            } elseif(stripos($this->$attribute,'-') !== false){
                if(!is_numeric(str_replace('-', '', $this->$attribute))){
                    $this->addError($attribute, Yii::t('app', '"'.$attribute.'" deve ser um número inteiro.'));
                }
                return false;
            } elseif (!is_numeric($this->$attribute)){
                $this->addError($attribute, Yii::t('app', '"'.$attribute.'" deve ser um número inteiro.'));
                return false;
            }
        }
    }

    public function search($params)
    {
        $query = <?= isset($modelAlias) ? $modelAlias : $modelClass ?>::find();
        <?php
            $class = 'backend\models\\'.$modelClass;
            $table = $class::getTableSchema();
            foreach ($table->columns as $column) {
                if(stripos($column->name, "id_") !== false){
                    echo "\$query->joinWith('id".ucfirst(str_replace('id_', '', $column->name))."');";
                }
            }
        ?>

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        <?php
            $class = 'backend\models\\'.$modelClass;
            $table = $class::getTableSchema();
            foreach ($table->columns as $column) {
                if(stripos($column->name, "id_") !== false){
                    echo 
        "\$dataProvider->sort->attributes['id".ucfirst(str_replace('id_', '', $column->name))."'] = [
            'asc' => ['tb_".str_replace('id_', '', $column->name)."s.nome' => SORT_ASC],
            'desc' => ['tb_".str_replace('id_', '', $column->name)."s.nome' => SORT_DESC],
        ];";
                }
            }
        ?>

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        <?= implode("\n        ", $searchConditions) ?>

        return $dataProvider;
    }
}
