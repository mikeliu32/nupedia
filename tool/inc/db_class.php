<?php
class DB 
{
    var $_dbConn = 0;
    var $_queryResource = 0;
    
    function DB()
    {
        //do nothing
    }
    
    function connect_db($host, $user, $pwd, $dbname)
    {
        $mysqli = new mysqli($host, $user, $pwd, $dbname);
        if ($mysqli->connect_errno)
            die ("MySQL Connect Error");

		$mysqli->set_charset("utf8");

        $this->_dbConn = $mysqli;
        return true;
    }

    function query($sql)
    {
        if (! $queryResource =  $this->_dbConn->query($sql))
            die ("MySQL Query Error".$this->_dbConn->error);
        $this->_queryResource = $queryResource;
        return $queryResource;        
    }
    
    /** Get array return by MySQL */
    function fetch_array()
    {
        return $this->_queryResource->fetch_assoc();
    }
	
	function fetch_object()
	{
		return $this->_queryResource->fetch_obj();
	}
    

    /** Get the cuurent id */    
    function get_insert_id()
    {
        return $this->_queryResource->insert_id;
    }
	
	function close()
    {
        return $this->_queryResource->close();
    }
    
}
?>
