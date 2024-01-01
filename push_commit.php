#!/usr/bin/php
<?php
  
  class soMain
  {
    /**
	* выводит на экран сообщение
	* 
	* @param string $sMsg
	* 
	* @return void
	*/
    protected function _printLn($sMsg)
    {
      echo $sMsg."\n";
	}
    
    /**
	* Основной метод
	* 
	* @return void
	*/
    public function go()
    {
      $aIn = getopt('c:');
      
      if (isset($aIn['c']))
	  {
	    $this->_printLn(exec('git add *'));
	    $this->_printLn(exec('git commit -m "'.$aIn['c'].'"'));
	    $this->_printLn(exec('git push -u origin master'));
	  }
	  else
	  {
	    $this->_printLn('Error (1): no comment given');
	  }
	}
  }
  
  $oMain = new soMain();
  $oMain->go();
