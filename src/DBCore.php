<?php

namespace Oddler\Pdo;

use Oddler\Pdo\Database;
use Oddler\Pdo\Table;

  /**
  * Класс создает подключения и хранит инстансы
  */
  class DBCore
  {

    /**
	* 
	* @var array массив всех подключений
	* 
	*/
    protected $_aInstances			= array();


    /**
	* 
	* @var object instance
	* 
	*/
    protected static $_instance		= '';



    /**
     * Локальный обработчик ошибок
     *
     * @param string $sText
     *
     * @return void
     */
    protected function _setError($sText)
    {
      die('<br /><b style="color: red">'.$sText.'</b>');
    }

    /**
     * Singleton
     *
     * @return object
     */
    public static function getInstance()
    {
      if (!self::$_instance)
      {
        self::$_instance = new self();
      }

      return self::$_instance;
    }

    /**
	* Создаем новое подключение к ДБ
	* 
	* @param array $aConfig
	* @param string $sName
	* 
	* @return object
	*/
    public function connect($aConfig, $sName = '__MAIN__') 
    {
	  if (isset($this->_aInstances[$sName]))
	  {
	    $this->_setError('DB error: name "'.$sName.'" already in use');
	  }
	  else
	  {
	    $this->_aInstances[$sName] = database::createConnection($aConfig);
	  }
	  
	  
	  return $this->_aInstances[$sName];
    }

    /**
	* Возвращает подключение к ДБ
	* 
	* @param string $sName
	* 
	* @return object
	*/
    public function getConnection($sName = '__MAIN__') 
    {
	  if (isset($this->_aInstances[$sName]))
	  {
        return $this->_aInstances[$sName];
	  }
	  else
	  {
	    $this->_setError('DB error: no connection "'.$sName.'"');
            return null;
	  }

    }

  }