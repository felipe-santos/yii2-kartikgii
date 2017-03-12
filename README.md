## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require felipe-santos/yii2-kartikgii "dev-master"
```

or add

```
"felipe-santos/yii2-kartikgii": "dev-master"
```

to the ```require``` section of your `composer.json` file.

## Usage

```php
//if your gii modules configuration looks like below:
$config['modules']['gii'] = 'yii\gii\Module';

//change it to
$config['modules']['gii']['class'] = 'yii\gii\Module';
```

```php
//Add this into backend/config/main-local.php
$config['modules']['gii']['generators'] = [
        'kartikgii-crud' => ['class' => 'felipesantos\kartikgii\crud\Generator'],
    ];
```

```php
//Add 'gridview' into your 'modules' section in backend/config/main.php
'modules' => [
        'gridview' => [
            'class' => 'kartik\grid\Module',
        ],

    ],
```

```php
//add modules 'datecontrol' into your 'modules' section in common/config/main 
'modules' => [
        'datecontrol' =>  [
            'class' => 'kartik\datecontrol\Module',

            // format settings for displaying each date attribute
            'displaySettings' => [
                'date' => 'd-m-Y',
                'time' => 'H:i:s A',
                'datetime' => 'd-m-Y H:i:s A',
            ],

            // format settings for saving each date attribute
            'saveSettings' => [
                'date' => 'Y-m-d', 
                'time' => 'H:i:s',
                'datetime' => 'Y-m-d H:i:s',
            ],



            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,

        ]
    ],
```

## License

**yii2-kartikgii** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.
