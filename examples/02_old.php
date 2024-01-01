<?php

  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  // Подключаем основной файл
  require_once('lib/pdo/core.php');

  /**
  * Тестовый класс для таблицы (active record test)
  */
  class tblItem extends soDBTable
  {
    public $id			= '';
    public $title		= '';
    public $text		= '';

    public function __construct(&$oDB = NULL)
    {
      $this->construct('tblTest_1', 'id', $oDB);
    }
  }


  
  /**
  * Класс для тестов
  */
  class soMain
  {
    /**
	* Пример того, как можно создать несколько подключений к разным БД
	* 
	* @return void
	*/
    public function initDbS()
    {
      $aConfigs = require_once('config.php');
      
      $DBCore = soDBCore::getInstance();

      // Первое соединение
      //- $oDB1 = $DBCore->connect( $aConfigs['1'] );
      $oDB1 = $DBCore->connect( array(
          'host'     => 'localhost',
          'user'     => 'USER',
          'password' => 'PASS',
          'database' => 'DB',
          'charset' => 'utf8',
          'database_type' => 'mysql'
      ) );
/*
      $oDB1->setQuery('SELECT * FROM `tblTest_1` ');
      $aRows = $oDB1->loadObjectsList();
      echo '<pre>';
	    print_r($aRows);
	  echo '</pre>';
//*/

      // Второе соединение                      // Имя соединения
      $oDB2 = $DBCore->connect( $aConfigs['2'], '123sys' );
/*
      $oDB2->setQuery('SELECT * FROM `tblTest2` ');
      $aRows2 = $oDB2->loadObjectsList();
      echo '<pre>';
	    print_r($aRows2);
	  echo '</pre>';
//*/
	}

    /**
	* Варианты разных запросов
	* 
	* @return void
	*/
    public function queries()
    {
      // Получаем основное соединение
      $oDB = soDBCore::getInstance()->getConnection();
      
      // Обычный запрос
/*
      $oDB->setQuery('SELECT * FROM `tblTest_1` WHERE id = 2 ');
      $oTMP = $oDB->loadObject();
      echo $oDB->showLastQuery().'<br />';
      echo '<pre>';
	    print_r($oTMP);
	  echo '</pre>';
*/

      // Запрос с параметром
/*
      $oDB->setQuery('SELECT * FROM tblTest_1 WHERE id=:id');
      $oDB->setBinds(array('id' => 1));
      $aRows = $oDB->loadObjectsList();
      echo '<pre>';
	    print_r($aRows);
	  echo '</pre>';
*/

     // Запрос с кириллицей
/*
      $oDB->setQuery('SELECT * FROM tblTest_1 WHERE text LIKE "%Настройки%"');
      $aRows = $oDB->loadObjectsList();
      echo '<pre>';
	    print_r($aRows);
	  echo '</pre>';
*/

      // Запрос со строковым параметром
/*
      $oDB->setQuery('SELECT * FROM tblTest_1 WHERE text LIKE :text');
      $sText = "%Настройки%";
      //$sText = iconv('utf-8', 'windows-1251', $sText);
      $oDB->setBinds(array('text' => $sText));
      $aRows = $oDB->loadObjectsList();
      echo $oDB->showLastQuery().'<br />';
      echo '<pre>';
	    print_r($aRows);
	  echo '</pre>';
*/

      // Получение соединения по имени
/*
      $DBCore = soDBCore::getInstance();
      $oDB0 = $DBCore->getConnection();
      $oDB2 = $DBCore->getConnection('123sys');
      echo '<pre>';
	    print_r($oDB0);
	    print_r($oDB2);
	  echo '</pre>';
*/


      // 1) Делаем запрос с попыткой вставить "левые значения" в плейсхолдер
      // 2) Выводим сам запрос, с подставленными значениями
/*
      $oDB->setQuery('SET profiling=1');
      $oDB->query();
      
      $oDB->setQuery('SELECT * FROM tblTest_1 WHERE id=:id');
      $oDB->setBinds(array('id' => "1 AND '1=2 '\'/'--"));
      $aRows = $oDB->loadObjectsList();
      echo $oDB->showLastQuery().'<br />';
      echo '<pre>';
	    print_r($aRows);
	  echo '</pre>';

      $oDB->setQuery('SHOW PROFILES');
      $aRows = $oDB->loadObjectsList();
      echo '<pre>';
	    print_r($aRows);
	  echo '</pre>';
*/
	}
	
	
	/**
	* Работа с таблицами (active record)
	* 
	* @return void
	*/
	public function tables()
	{
      $oItem = new tblItem();

      // Загружаем, меняем, сохраняем
      $oItem->load(1);
        $oItem->text .= 'z';
      $oItem->save();

/*      // Создаем новую запись 
      $oItem = new tblItem();
        $oItem->title = 'Алекс 2';
        $oItem->text = 'Алекс';
      $oItem->save();
      echo $oItem->id.'<br />';*/
      
/*      // Удаляем уже загруженную запись 
      $oItem->load( $oItem->id );
      $oItem->delete();*/

/*      echo '<pre>';
	    print_r($oItem);
	  echo '</pre>';*/
	}

  }


  //------------------------------------
  $oMain = new soMain();
  
  // Подключение к разным базам
  $oMain->initDbS();

  // Варианты разных запросов
  $oMain->queries();

  // Работа с таблицами (active record)
  $oMain->tables();