<?php

  namespace Oddler\Pdo;

  /**
  * Класс active record
  */
  class Table
  {
    /**
	* 
	* @var array настройки таблицы (Имя, ключевое поле и т.д.) 
	* 
	*/
    protected $__aParams                    = array();

    /**
     * Установка основных параметров. 
     *
     * @param string $sTable    - название таблицы
     * @param string $sKey      - ключь таблицы
     * @param string $oDB       - DB объект
     *
     * @return object $this
     */
    public function construct($sTable, $sKey, &$oDB)
    {
      if(!$oDB)
	  {
	    $oDB = DBCore::getInstance()->getConnection();
	  }
      
      $this->__aParams['tables'] = $sTable;
      $this->__aParams['key']    = $sKey;
      $this->__aParams['db']     = $oDB;

      return $this;
    }

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
     * Заполнение своих полей на основе другого объекта
     *
     * @param object $obj - объект источник
     *
     * @return object $this
     */
    protected function _assign($obj)
    {
      if (is_object($obj))
      {
        $class_vars = get_class_vars(get_class($this));

        foreach ($class_vars as $name => $value)
        {
          if ($name != '__aParams')
          {
            $this->$name = $obj->$name;
          }
        }
      }

      return $this;
    }

    /**
     * Загрузка данных из БД
     *
     * @param int $id - ID записи 
     *
     * @return object $this
     */
    public function load($id)
    {
      $oDB = $this->__aParams['db'];
      $oDB->setQuery('SELECT * FROM `'.$this->__aParams['tables'].'` WHERE '.$this->__aParams['key'].' = :key');
      $oDB->setBinds(array('key' => $id));
        $o = $oDB->loadObject();
          $this->_assign($o);
        unset($o);

      return $this;
    }

    /**
     * Сохранение значения полей в БД
     *
     * @return boolean
     */
    public function save()
    {
      $key = $this->__aParams['key'];
      $oDB = $this->__aParams['db'];

      if ($this->$key != 0)
      {
        $bRet = $oDB->updateObject($this);
      }
      else
      {
        $bRet = $oDB->insertObject($this);
        $this->$key = $oDB->getLastInsertId();
      }

      return $bRet;
    }

    /**
     * Получение значения параметров (настрок)
     *
     * @param string $key - ключ
     * @param mixed $sDefVal - значение по умолчанию
     *
     * @return mixed
     */
    public function getParam($key, $sDefVal = Null)
    {
      return isset($this->__aParams[$key])?$this->__aParams[$key]:$sDefVal;
    }

    /**
     * Удаление активной(текущей) записи
     *
     * @return boolean
     */
    public function delete()
    {
      $oDB = $this->__aParams['db'];

      $bRet = $oDB->delete($this);

      return $bRet;
    }
  }
