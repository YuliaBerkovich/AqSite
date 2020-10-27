<?php //Alexey Masyuk,Yulia Berkovich Aquarium Control System
require_once('queryClass.php');
require_once('userClass.php');

// Class to handle all worck with SQL DataBase
class dbClass
{
	private $host;
	private $db;
	private $charset;
	private $user;
	private $pass;
	private $querys;           
	private $opt=array(
	PDO::ATTR_ERRMODE   =>PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC);
	private $connection;
	
	public function __construct($userCls,string $host="localhost", string $db = "php_prj",string $charset="utf8", string $user = "root", string $pass = "")
						{
							$this->host = $host;
							$this->db = $db;
							$this->charset = $charset;
							$this->user = $user;
							$this->pass = $pass;
							$this->querys = new Query($userCls);  // creating new object contain all needed querys fo SQL Data Base
						}

	private function connect()  //  Connecting to Data Base
	{
	$dns = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
	$this->connection = new PDO($dns, $this->user, $this->pass, $this->opt);
	}

	public function disconnect()
	{
	$this->connection = null;
	}

    // Function checking if given user Object username is exists in DataBase.
	// Can check if only username exists if onlyUser string given
    // or username and password if no string sended
	// Returning true or false 
	public function userExists($user,String $act="userAndPass")
	{
		$this->connect();
		$result = $this->connection->query($this->querys->getUserSelect());
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if($act=="userAndPass"&&$row["username"]==$user->getUserName()&&$row["password"]==$user->getPassword()){			
				$this->disconnect();
				return true;
			}
			if($act=="onlyUser"&&$row["username"]==$user->getUserName())
			{
				$this->disconnect();
				return true;
			}
		}
		$this->disconnect();
		return false;
	}
	
	// Function returning relevant querys to delete or create user
	// used in userHandler function
	private function handlerDecide($act)
	{
		$query=array($this->querys->getDelUser(),$this->querys->getDelTabl());
		if($act=="create")
		{
			$query=array($this->querys->getInsert(),$this->querys->getTableCreate());
		}
		return $query;
	}

	// Function to write new user in user table and new user table in DataBase 
	// or delete user from user table and delete user data table
	// If act is create handlerDecide func will generate needed query to create user and users table
    // else querys to delete users data from DataBase returned and querys activated
	// return true or false depends if querys seccsed
	public function userHandler($user,String $act="create")
	{
		$query=array();
		$query=$this->handlerDecide($act);
		$this->connect();
		if($this->connection->exec($query[0]))
		{
			if(!$this->connection->exec($query[1]))
			{
				$this->disconnect();
				return true;
			}
			else
				{
					$this->disconnect();
					return false;
				}
		}
		else
			{
				$this->disconnect();
				return false;
			}
	}
	
	// Function getting data from DataBase
	// returning it as an array
	public function getData($user)
	{
		$this->connect();
		$result = $this->connection->query($this->querys->getDataSelect());
		$data=array();
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			array_push($data,$row);
		}
		$this->disconnect();
		return $data;
	}
	
	// Function deleating wanted data from user data table in DataBase
	public function delData($time)
	{
		$this->connect();
		if($this->connection->exec($this->querys->getDelData($time)))
		{
			$this->disconnect();
			return true;
		}
		$this->disconnect();
		return false;
	}

	public function chartQuery($name)
	{
		$dataArr=array('temp'=>"",'PH'=>"",'level'=>"");		
		$this->connect();
			try {
					$result = $this->connection->query("SELECT * FROM `".$name."`");
					if($result){
						while($row = $result->fetch(PDO::FETCH_ASSOC)) {
							$ph .= "['".$row{'time'}."',".$row{'ph'}."],";
							$temp .= "['".$row{'time'}."',".$row{'temp'}."],";
							$level .="['".$row{'time'}."',".$row{'level'}."],";
						}
						$dataArr['temp']=$temp;
						$dataArr['PH']=$ph;
						$dataArr['level']=$level;
				    	return $dataArr;
					}

			} catch (Exception $e) {
		    	$this->disconnect();
			}
			$this->disconnect();
	}
}
?>