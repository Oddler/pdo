<?

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


  // Соединение: 
  // $oDB = $DBCore->connect( $aConfigs['1'] );
  $oDB = $DBCore->connect( array(
    'host'     => 'localhost',
    'user'     => 'USER',
    'password' => 'PASS',
    'database' => 'DB',
    'charset' => 'utf8',
    'database_type' => 'mysql'
  ));	

  $oDB->setQuery('SELECT * FROM `tblTest_1` ');
  $aRows = $oDB->loadObjectsList();
  echo '<pre>';
    print_r($aRows);
  echo '</pre>';


  $oItem = new tblItem();
    $oItem->title = time();
  $oItem->save();




echo 'Done<br />';