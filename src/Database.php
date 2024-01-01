<?php

  namespace Oddler\Pdo;

  /**
  * Основной класс для работы с БД
  */
  class Database
  {
    // Данные для подключения к БД
    /**
	* 
	* @var string имя или адрес хоста
	* 
	*/
    private $_host                          = '';

    /**
	* 
	* @var string имя ползователя
	* 
	*/
    private $_user                          = '';

    /**
	* 
	* @var string пароль
	* 
	*/
    private $_password                      = '';

    /**
	* 
	* @var string имя базы данных
	* 
	*/
    private $_db                            = '';

    /**
	* 
	* @var string тип подключения
	* 
	*/
    private $_db_type                       = '';

    /**
	* 
	* @var string символ для префикса таблиц
	* 
	*/
    private $_prefix_sign                   = '#_';

    /**
	* 
	* @var string префикс таблиц
	* 
	*/
    private $_prefix                        = '';

    /**
	* 
	* @var string кодировка
	* 
	*/
    private $_charset                       = 'utf8'; // utf8 cp1251

    /**
	* 
	* @var string порт соединения
	* 
	*/
    private $_port                          = '';

    /**
	* 
	* @var string Строка запроса
	* 
	*/
    private $_sQuery                        = '';

    /**
	* 
	* @var array массив со значениями для плейсхолдеров 
	* 
	*/
    private $_aBinds                        = array();

    // Текущее соедененик
    /**
	* 
	* @var object PDO Object
	* 
	*/
    private $_dbh                           = NULL;

    /**
	* 
	* @var resource PDOStatement
	* 
	*/
    private $_sth                           = NULL;

    /**
	* 
	* @var array Массив ошибок
	* 
	*/
    private $_aErrors                       = array();

    //----------------------------------------------------------------
    //----------------------------------------------------------------
    //----------------------------------------------------------------

    /**
     * Создание соединения к БД
     * 
     * @param array $aConfig - настройки доступа к базе данных
              'host'		=> 'localhost',
              'user'		=> 'root',
              'password'	=> '',
              'database'	=> 'db_test',
              'port'		=> '',
              'charset'		=> '',
              'prefix_sign'	=> '#_',
              'prefix'		=> 'tbl_',
              'database_type' => 'mysql' ('postgre', 'sqlite', 'oracle', 'mssql', 'firebird')

     * @return object
     */
    public static function createConnection($aConfig)
    {
      $o = new self();
        // TODO Проверка на обязательные параметры
        // Для разных _db_type они разные
        $o->_host                   = $aConfig['host'];
        $o->_user                   = $aConfig['user'];
        $o->_password               = $aConfig['password'];
        $o->_db                     = $aConfig['database'];
        $o->_db_type                = $aConfig['database_type'];
        $o->_prefix_sign            = isset($aConfig['prefix_sign'])?$aConfig['prefix_sign']:$o->_prefix_sign;
        $o->_prefix                 = isset($aConfig['prefix'])?$aConfig['prefix']:'';
        if (isset($aConfig['charset']))
        {
          $o->_charset              = $aConfig['charset'];
        }

      $o->_connect();

      return $o;
    }

    /**
     * Устанавливает перфикс
     *
     * @param string $sPrefix - Префикс
     *
     * @return object $this
     */
    public function setPrefix($sPrefix)
    {
      $this->_prefix = $sPrefix;

      return $this;
    }

    /**
     * Делает запросы для устанавки кодировки соедениения
     *
     * @return object $this
     */
    private function _setCharset()
    {
      if($this->_charset)
	  {
        $this->setQuery('SET character_set_client="'.$this->_charset.'"');
        $this->query();

        $this->setQuery('SET character_set_results="'.$this->_charset.'"');
        $this->query();

        $this->setQuery('SET names "'.$this->_charset.'"');
        $this->query();


        // $this->setQuery('SET collation_connection="'.$this->_charset.'"'); //cp1251_general_ci
        // $this->query();
	  }

      return $this;
    }

    /**
     * Метод производит необходимые, после удачного соеденения, операции.
     * 
     * @return object $this
     */
    private function _afterConnect()
    {
      $this->_setCharset();
      //TODO Добавить вызов пользовательских функций

      return $this;
    }


    /**
     * Уснанавливает соединение с БД
     *
     * @return object $this
     */
    private function _connect()
    {
      try
      {
        switch ($this->_db_type)
        {
          case 'mysql':
            // See more: http://www.php.net/manual/en/ref.pdo-mysql.php
            $this->_dbh = new \PDO('mysql:host='.$this->_host.'; dbname='.$this->_db, $this->_user, $this->_password );
          break;

          case 'postgre':
            // TODO удалить "{" и "}"
            // See more: http://www.php.net/manual/en/ref.pdo-pgsql.connection.php
            $this->_dbh = new \PDO('pgsql:host={'.$this->_host.'}; port={'.$this->_port.'}; dbname={'.$this->_db.'}; user={'.$this->_user.'}; password={'.$this->_password.'}' );
          break;

          case 'sqlite':
            // See more: http://www.php.net/manual/en/ref.pdo-sqlite.connection.php
            $this->_dbh = new \PDO('sqlite:{'.$this->_db.'}.sqlite');
          break;

          case 'oracle':
            // See more: http://www.php.net/manual/en/ref.pdo-oci.connection.php
            $this->_dbh = new \PDO('oci:dbname={'.$this->_host.'}/{'.$this->_db.'}; charset={'.$this->_charset.'}', $this->_user, $this->_password );
          break;

          case 'mssql':
            // See more: http://www.php.net/manual/en/ref.pdo-sqlsrv.php
            // not implemented yet.
          break;

          case 'firebird':
            // See more: http://www.php.net/manual/en/ref.pdo-firebird.php
            // not implemented yet.
          break;

          default:
            $this->_setError('Not supporter DB type "'.$this->_db_type.'"');
          break;
        }
      }
      catch(\Exception $ex) // PDOException
      {
          $is_console = PHP_SAPI == 'cli';
          if ($is_console){
              $sMSG = 'Error: '.$ex->getMessage()."\n";
          }
          else {
              $sMSG = '<b>Error:</b>: '.$ex->getMessage()."<br />\n";
          }
          die ($sMSG);
      }

      $this->_dbh->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
      $this->_afterConnect();

      return $this;
    }

    /**
     * Устанавливает SQL запрос
     *
     * @param string $Query - строка запроса
     * @param array $aBinds - Массив парраметров для запроса
     *
     * @return object $this
     */
    public function setQuery($Query, $aBinds = array())
    {
      if ($this->_prefix)
      {
        $Query = str_replace($this->_prefix_sign, $this->_prefix, $Query);
      }
      $this->_sQuery = $Query;
      if (is_array($aBinds))
      {
        $this->setBinds($aBinds);
      }

      return $this;
    }

    /**
     * Установка парраметров для плейсхолдеров
     *
     * @param array aParams - Массив парраметров для запроса
     *
     * @return object $this
     */
    public function setBinds($aParams)
    {
      $this->_aBinds = $aParams;

      return $this;
    }

    /**
     * Подготовка SQL запроса к выполнению
     *
     * @return void
     */
    protected function _prepareQuery()
    {
      $this->_sth = $this->_dbh->prepare($this->_sQuery);
    }

    /**
	* Возвращает текущий запрос
	* 
	* @return string
	*/
    public function showLastQuery()
    {
      return $this->_sth->queryString;
    }

    /**
     * Выполняет запрос и возвращает результат в виде массива
     *
     * @return array
     */
    public function loadArraysList()
    {
      try
      {
        $this->query();
        //$this->_sth->setFetchMode(\PDO::FETCH_COLUMN);
        $rows = $this->_sth->fetchAll();
        return $rows;
      }
      catch (Exception $e)
      {
        $this->_setError( $e->getMessage() );
      }
    }


    /**
     * Выполняет запрос и возвращает результат в виде массива объектов
     *
     * @return array
     */
    public function loadObjectsList()
    {
      try
      {
        $this->query();
        $this->_sth->setFetchMode(\PDO::FETCH_OBJ);
        $rows = $this->_sth->fetchAll();
        return $rows;
      }
      catch (Exception $e)
      {
        $this->_setError( $e->getMessage() );
      }
    }


    /**
     * Выполняет запрос и возвращает один результат в виде объекта или Null
     *
     * @return object
     */
    public function loadObject()
    {
      try
      {
        //TODO добавить лимит 1. Но учесть, что для разных sql запрос разный
        if($this->_db_type == 'mysql')
		{
		  $this->_sQuery .= ' LIMIT 0,1';
		}
         
        $this->query();
        $this->_sth->setFetchMode(\PDO::FETCH_OBJ);
        $rows = $this->_sth->fetchAll();
        $oRet = count($rows)?$rows[0]:Null;
        return $oRet;
      }
      catch (Exception $e)
      {
        $this->_setError( $e->getMessage() );
      }
    }

    /**
     * Выполняет SQL запрос
     *
     * @return boolean
     */
    public function query()
    {
      $bRet = False;
      try
      {
        $this->_prepareQuery();
        $bRet = $this->_sth->execute($this->_aBinds);
      }
      catch (Exception $e)
      {
        $this->_setError( $e->getMessage() );
      }
/*
mysql_free_result( $cur );
*/
      return $bRet;
    }

    /**
     * Локальный обработчик ошибок
     *
     * @param string $sMessage - сообщение об ошибке
     *
     * @return void
     */
    private function _setError($sMessage)
    {
      $this->_aErrors['error'] = $sMessage;
      $this->_aErrors['SQL'] = $this->_sQuery;
      //TODO: Сделано грубо, переделать
        echo '<pre>';
          print_r($this->_aErrors);
        echo '</pre>';
      die();
      //return $this;
    }

    /**
     * Возвращает ID последнего добавленного элемента
     *
     * @return int
     */
    function getLastInsertId()
    {
      return $this->_dbh->lastInsertId();
    }

    /**
     * Запрос на обновление данных (update)
     *
     * @param object $obj - объект
     *
     * @return boolean
     */
    function updateObject(&$obj)
    {
      $tables   = $obj->getParam('tables');
      $key      = $obj->getParam('key');

      $aParams1 = array();
      $aBinds = array();
      $class_vars = get_object_vars($obj);
      foreach ($class_vars as $name => $value)
      {
        $aParams1[] = '`'.$name.'`=:'.$name;
        $aBinds[$name] = $value;
      }
      $sSQL = 'UPDATE `'.$tables.'` SET '.  implode(', ', $aParams1).' WHERE '.$key.'='.$obj->$key;
      $this->setQuery($sSQL);
        $this->setBinds($aBinds);
      return $this->query();
    }

    /**
     * Запрос на добавление данных (insert)
     *
     * @param object $obj - объект
     *
     * @return boolean
     */
    function insertObject(&$obj)
    {
      $tables   = $obj->getParam('tables');
      $key      = $obj->getParam('key');

      $aParams1 = array();
      $aParams2 = array();
      $aBinds   = array();
      $class_vars = get_object_vars($obj);
      foreach ($class_vars as $name => $value)
      {
        $aParams1[] = '`'.$name.'`';
        $aParams2[] = ':'.$name;
        $aBinds[$name] = $value;
      }
      $aBinds[$key] = '0';

      $sSQL = 'INSERT INTO `'.$tables.'` ('.  implode(', ', $aParams1).') VALUES ('.  implode(', ', $aParams2).')';

      $this->setQuery($sSQL);
        $this->setBinds($aBinds);
      return $this->query();
    }

    /**
     * Удаление записи по переданному объекту класса table
     *
     * @param object $obj - объект
     *
     * @return boolean
     */
    function delete($obj)
    {
      $tables   = $obj->getParam('tables');
      $key      = $obj->getParam('key');

      $aBinds   = array();

      $sSQL = 'DELETE FROM '.$tables.' WHERE '.$key.'=:id';
      $aBinds['id'] = $obj->$key;

      $this->setQuery($sSQL, $aBinds);
      return $this->query();
    }
  }
