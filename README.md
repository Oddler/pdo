# PDO

Данный модуль состоит из трех классов **DBCore**, **Database** и **Table**.

soDBCore – создает подключения и хранит инстансы
soDatabase – осуществляет взаимодействие с БД (подключение и запросы)
soDBTable – active record


## Requirements

- PHP 5.3 or higher

## Installation

```json
composer require oddler/pdo
```

Или: composer.json

```json
{
    "require": {
        "oddler/pdo": "v1.5.6"
    }
}
```

# Usage

## PHP side

```php
<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');


  require_once('vendor/autoload.php');

  use Oddler\Pdo\DBCore;
  $DBCore = DBCore::getInstance();


  class tblItem extends Oddler\Pdo\Table
  {
    public $id			= '';
    public $title		= '';

    public function __construct(&$oDB = NULL)
    {
      $this->construct('tblTest_1', 'id', $oDB);
    }
  }

  //- $oDB1 = $DBCore->connect( $aConfigs['1'] );

  $oDB1 = $DBCore->connect( array(
    'host'     => 'localhost',
    'user'     => 'USER',
    'password' => 'PASS',
    'database' => 'DB',
    'charset' => 'utf8',
    'database_type' => 'mysql'
  ));	

  $oDB1->setQuery('SELECT * FROM `tblTest_1` ');
  $aRows = $oDB1->loadObjectsList();
  echo '<pre>';
    print_r($aRows);
  echo '</pre>';
```

# Methods
## DBCore:

* connect
* _setError
* getInstance

## Database:

* _setError
* _pdoException
* createConnection
* setPrefix
* _setCharset
* _afterConnect
* _connect
* setQuery
* setBinds
* showQuery
* _prepareQuery
* loadObjectsList
* loadObject
* query
* getLastInsertId
* updateObject
* insertObject
* delete

## DBTable:

* _setError
* construct
* _assign
* load
* save
* getParam
* delete
