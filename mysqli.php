<?php
class MysqlDAO
{
  private $mysqli;

  public function MysqlDAO()
  {
    $this->connect(); 	
  }

  private function connect()
  {
    $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
    if(mysqli_connect_errno()){
      return false;
    }
    $this->mysqli->set_charset('utf8');
    return true;
  }

  public function execute($sql, $params, $param_types)
  {
    if($this->mysqli == false){
      return -1;
    }
    $cnt = count($params);
    $stmt = $this->mysqli->prepare($sql);
    if(!$stmt){
      return -2;
    }
    $a_params = array();
    $param_type = '';
    for($i = 0; $i < $cnt; $i++){
      $param_type .= $param_types[$i];
    }

    $a_params[] = $param_type;
    for($i=0; $i<$cnt; $i++){
      $a_params[] = &$params[$i];
    }

    call_user_func_array(array($stmt, 'bind_param'), $a_params);	
    $stmt->execute();
    $affected_rows = $this->mysqli->affected_rows;
    $stmt->close();
    $this->mysqli->close();
    return $affected_rows;
  }


  function executeQuery($sql, $params, $param_types)
  {
    if($this->mysqli == false){
      return -1;
    }
    $cnt = count($params);
    $stmt = $this->mysqli->prepare($sql);
    if(!$stmt){
      return -2;
    }
    $a_params = array();
    $param_type = '';
    for($i = 0; $i < $cnt; $i++){
      $param_type .= $param_types[$i];
    }

    $a_params[] = $param_type;
    for($i=0; $i<$cnt; $i++){
      $a_params[] = &$params[$i];
    }

    call_user_func_array(array($stmt, 'bind_param'), $a_params);	
    $stmt->execute();   
    $a_data = array();
    $result = $stmt->get_result();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) { 
      array_push($a_data, $row);
    } 
    $result->free();
    $stmt->close();
    $this->mysqli->close();
    return $a_data;
  }
	
  public function close()
  {
    $this->mysqli->close();
  }

}
