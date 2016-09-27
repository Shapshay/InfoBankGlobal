<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 27.09.2016
 * Time: 12:21
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
class BDfunc
{


    public $status;
    public $count;
    public $outsql;
    public $ins_id;

    private $conn;
    private $operation = 'SELECT';
    private $table;
    private $select = '*';
    private $joins;
    private $order;
    private $order_type;
    private $limit;
    private $offset;
    private $group;
    private $having;
    private $where;



    //constructor, create connection
    public function __construct()
    {
        $xml_patch = '/var/www/html/adm/inc/config.xml';
        $xml = simplexml_load_file($xml_patch);
        $server_name=trim($xml->bd_config->server_name);
        $user_name=trim($xml->bd_config->user_name);
        $password=trim($xml->bd_config->password);
        $bd_name=trim($xml->bd_config->bd_name);
        $conn = new mysqli($server_name, $user_name, $password, $bd_name);
        $this->status="";
        // Oh no! A connect_errno exists so the connection attempt failed!
        if ($conn->connect_errno) {
            // The connection failed. What do you want to do?

            // Let's try this:
            $this->status= "Sorry, this website is experiencing problems.";

            // Something you should not do on a public site, but this example will show you
            // anyways, is print out MySQL error related information -- you might log this
            $this->status.= "Error: Failed to make a MySQL connection, here is why: \n";
            $this->status.= "Errno: " . $conn->connect_errno . "\n";
            $this->status.= "Error: " . $conn->connect_error . "\n";

            // You might want to show them something nice, but we will simply exit
            exit;
        }

        $this->conn=$conn;

        if (!$conn->set_charset("utf8")) {
            $this->status.= sprintf("Error loading character set utf8: %s\n", $conn->error);
            exit();
        } else {
            $this->status.=sprintf("Current character set: %s\n", $conn->character_set_name());
        }


    }

    //destroy connection
    public function destroy() {
        $this->conn->close();
        $this->status="";
    }

    //create data base
    public function bd_create($bd_name)
    {
        $conn=$this->conn;
        $this->status="";
        // Create database
        $sql = "CREATE DATABASE ".$bd_name;
        if ($conn->query($sql)) {
            $this->status="Database created successfully";
        } else {
            $this->status=sprintf("Error creating database - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    //create table
    public function table_create($tab_name,$tab_columns)
    {
        $conn=$this->conn;
        $this->status="";
        $columns = " (id INT(11) NOT NULL AUTO_INCREMENT";
        if (is_array($tab_columns)) {
            foreach ($tab_columns as $key => $value) {
                $columns.=", ".$key." ".$value;
            }
        }
        $columns.=", PRIMARY KEY (id))";
        $sql="CREATE TABLE ".$tab_name.$columns;
        //echo $sql;
        if ($conn->query($sql)) {
            $this->status= "Table created successfully";
        }	else {
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    public function table_create_uniq_index($tab_name,$ind_name,$field1="",$field2="")
    {
        $conn=$this->conn;
        $this->status="";
        $columns = "";
        if ($field1=="") {
            $field1=$ind_name;
        }
        if ($field2=="") {
            $columns.=$field1;
        } else {
            $columns.=$field1.",".$field2;
        }
        $sql="ALTER TABLE ".$tab_name." ADD UNIQUE ".$ind_name." (".$columns.");";
        //echo $sql;
        if ($conn->query($sql)) {
            $this->status= "Index created successfully";
        }	else {
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }


    //delete table
    public function table_delete($tab_name)
    {
        $conn=$this->conn;
        $this->status="";
        $sql="DROP TABLE IF EXISTS ".$tab_name;
        if ($conn->query($sql)) {
            $this->status= "Table droped successfully";
        }	else {
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    //create element of table
    public function element_create($tab_name,$columns)
    {
        $conn=$this->conn;
        $this->status="";
        $fields = "(";
        $values = "(";
        $first = true;
        if (is_array($columns)) {
            foreach ($columns as $key => $value) {
                if ($first) {
                    $fields.=	$key;
                    if($value!='NOW()'){
                        $values.= "'".$value."'";
                    }
                    else{
                        $values.= $value;
                    }
                    $first = false;
                } else {
                    $fields.=	", ".$key;

                    if($value!='NOW()'){
                        $values.= ", '".$value."'";
                    }
                    else{
                        $values.= ", ".$value;
                    }
                }
            }
        }
        $fields .= ")";
        $values .= ")";
        $sql="INSERT INTO ".$tab_name." ".$fields." VALUES ".$values;
        $this->outsql=$sql;
        if ($conn->query($sql)) {
            $this->status = "Element created successfully";
            $row_cnt = $conn->insert_id;
            $columns["id"]=$row_cnt;
            $this->ins_id = $row_cnt;
        }	else {
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    //updates element of the table
    public function element_update($tab_name,$id,$columns)
    {
        $conn=$this->conn;
        $this->status="";
        $conditions="";
        $first=true;
        if (is_array($columns)) {
            foreach ($columns as $key => $value) {
                if ($first) {
                    $conditions.= $key."='".$value."'";
                    $first = false;
                } else {
                    if($value!='NOW()'){
                        $conditions.= ", ".$key."='".$value."'";
                    }
                    else{
                        $conditions.= ", ".$key."=".$value;
                    }
                }
            }
        }
        $sql="UPDATE ".$tab_name." SET ".$conditions." WHERE id=".$id;
        if ($conn->query($sql)) {
            $this->status="Element updated successfully";
        }	else {
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    //updates element of the table for fields
    public function element_fields_update($tab_name,$where,$columns)
    {
        $conn=$this->conn;
        $this->status="";
        $conditions="";
        $first=true;
        if (is_array($columns)) {
            foreach ($columns as $key => $value) {
                if ($first) {
                    $conditions.= $key."='".$value."'";
                    $first = false;
                } else {
                    if($value!='NOW()'){
                        $conditions.= ", ".$key."='".$value."'";
                    }
                    else{
                        $conditions.= ", ".$key."=".$value;
                    }
                }
            }
        }
        $sql="UPDATE ".$tab_name." SET ".$conditions.$where;
        if ($conn->query($sql)) {
            $this->status="Element updated successfully";
        }	else {
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    //updates element of the table for fields
    public function element_free_update($sql)
    {
        $conn=$this->conn;
        $this->status="";
        if ($conn->query($sql)) {
            $this->status="Element updated successfully";
        }	else {
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    //find element by ID
    public function element_find($tab_name,$id)
    {
        $conn=$this->conn;
        $this->status="";
        $sql="SELECT * FROM ".$tab_name." WHERE id=".$id." LIMIT 1";
        $this->outsql=$sql;
        if ($result = $conn->query($sql)) {
            if ($result->num_rows===0) {
                $this->status="Result is empty";
                $this->count=0;
            } else {
                $this->status = "Element finded successfully";
                $element = $result->fetch_assoc();
                $this->count=1;
                return $element;
            }
        }	else {
            $this->count=0;
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }

    }

    //find element by field value
    public function element_find_by_field($tab_name,$field_name,$value)
    {
        $conn=$this->conn;
        $this->status="";
        if (is_string($value)) {
            $value="'".$value."'";
        }
        $sql="SELECT * FROM ".$tab_name." WHERE ".$field_name."=".$value." LIMIT 1";
        $this->outsql=$sql;
        if ($result = $conn->query($sql)) {
            if ($result->num_rows===0) {
                $this->status="Result is empty";
                $this->count=0;
            } else {
                $this->status= "Element finded successfully";
                $element = $result->fetch_assoc();
                $this->count=1;
                return $element;
            }
        }	else {
            $this->count=0;
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }

    }

    //delete element from the table
    public function element_delete($tab_name,$id)
    {
        $conn=$this->conn;
        $this->status="";
        $sql="DELETE FROM ".$tab_name." WHERE id=".$id;
        if ($conn->query($sql)) {
            $this->status="Element deleted successfully";
        }	else {
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    //performs request to the base
    public function dbselect($parameters) {

        if (isset($parameters["table"])) {
            $this->table=$parameters["table"];
        }
        if (isset($parameters["select"])) {
            $this->select($parameters["select"]);
        } else {
            $this->select("*");
        }
        if (isset($parameters["where"])) {
            $this->where($parameters["where"]);
        }
        if (isset($parameters["order"])) {
            $this->order($parameters["order"]);
        }
        if (isset($parameters["order_type"])) {
            $this->order_type($parameters["order_type"]);
        }
        if (isset($parameters["limit"])) {
            $this->limit($parameters["limit"]);
        }
        if (isset($parameters["offset"])) {
            $this->offset($parameters["offset"]);
        }
        if (isset($parameters["joins"])) {
            $this->joins($parameters["joins"]);
        }
        if (isset($parameters["having"])) {
            $this->having($parameters["having"]);
        }
        if (isset($parameters["group"])) {
            $this->group($parameters["group"]);
        }
        //return $this->build_select();
        $sql = $this->build_select();
        //echo $sql;
        $this->status="";
        $this->outsql=$sql;
        $conn=$this->conn;
        if ($result = $conn->query($sql)) {
            if ($result->num_rows===0) {
                $this->status= "Result is empty";
                $this->count=0;
            } else {
                $this->status = "Elements finded successfully";
                $elements = array();
                while ($actor = $result->fetch_assoc()) {
                    $elements[]=$actor;
                    //print_r($actor);
                }
                $this->count=$result->num_rows;
                return $elements;
            }
        }	else {
            $this->count=0;
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    private function where($where)
    {
        $this->where = is_array($where) ? implode(",",$where) : $where;
        return $this;
    }

    private function order($order)
    {
        $this->order = is_array($order) ? implode(",",$order) : $order;
        return $this;
    }

    private function order_type($order_type)
    {
        $this->order_type = $order_type;
        return $this;
    }

    private function group($group)
    {
        $this->group = $group;
        return $this;
    }

    private function having($having)
    {
        $this->having = $having;
        return $this;
    }

    private function limit($limit)
    {
        $this->limit = intval($limit);
        return $this;
    }

    private function offset($offset)
    {
        $this->offset = intval($offset);
        return $this;
    }

    private function select($select)
    {
        $this->operation = 'SELECT';
        $this->select = is_array($select) ? implode(",",$select) : $select;
        return $this;
    }

    private function joins($joins)
    {
        $this->joins = $joins;
        return $this;
    }

    private function build_select()
    {
        $sql = "SELECT $this->select FROM $this->table";

        if ($this->joins)
            $sql .= ' ' . $this->joins;

        if ($this->where)
            $sql .= " WHERE $this->where";

        if ($this->group)
            $sql .= " GROUP BY $this->group";

        if ($this->having)
            $sql .= " HAVING $this->having";

        if ($this->order)
            $sql .= " ORDER BY $this->order";

        if ($this->order_type)
            $sql .= " $this->order_type";

        if ($this->limit || $this->offset)
            $sql = $this->limit_exp($sql,$this->offset,$this->limit);

        $this->table = '';
        $this->select = '';
        $this->joins = '';
        $this->order = '';
        $this->order_type = '';
        $this->limit = '';
        $this->offset = '';
        $this->group = '';
        $this->having = '';
        $this->where = '';

        return $sql;
    }

    private function limit_exp($sql, $offset, $limit)
    {
        $offset = is_null($offset) ? '' : intval($offset) . ',';
        $limit = intval($limit);
        return "$sql LIMIT {$offset}$limit";
    }

    //free request to the base
    public function db_free_query($sql) {
        $this->status="";
        $this->outsql=$sql;
        $conn=$this->conn;
        if ($result = $conn->query($sql)) {
            if ($result->num_rows===0) {
                $this->status= "Result is empty";
                $this->count=0;
            } else {
                $this->status = "Elements finded successfully";
                $elements = array();
                while ($actor = $result->fetch_array()) {
                    $elements[]=$actor;
                    //print_r($actor);
                }
                $this->count=$result->num_rows;
                return $elements;
            }
        }	else {
            $this->count=0;
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

    //free request to the base
    public function db_free_del($sql) {
        $this->status="";
        $this->outsql=$sql;
        $conn=$this->conn;
        if ($result = $conn->query($sql)) {
            return 0;
        }	else {
            $this->count=0;
            $this->status=sprintf("Error - SQLSTATE %s.\n", $conn->sqlstate);
        }
    }

}

// получение страницы через GET
function get_web_page( $url ){
    $uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";

    $ch = curl_init( $url );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "inc/coo.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE,"inc/coo.txt");

    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;

    return $header;
}

// получение страницы через POST
function post_content ($url,$postdata) {
    $uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";

    $ch = curl_init( $url );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "inc/coo.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE,"inc/coo.txt");

    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;

    return $header;
}

error_reporting (E_ALL);
ini_set("display_errors", 1);
date_default_timezone_set ("Asia/Almaty");
$dbc = new BDFunc;

##############################################################################################

$rows = $dbc->dbselect(array(
    "table"=>"user_art",
    "select"=>"users.login as login,
            articles.title as title,
            user_art.art_id as art_id",
    "joins"=>"LEFT OUTER JOIN users ON user_art.user_id = users.id
        	LEFT OUTER JOIN articles ON user_art.art_id = articles.id",
    "where"=>"user_art.close = 0",
    "limit"=>100));
$sql = $dbc->outsql;
$numRows = $dbc->count;
if ($numRows > 0) {
    foreach ($rows as $row) {
        $field_arr = '';
        foreach ($row as $key=>$value){
            $field_arr.='&'.$key.'='.$value;
        }
        //$url = 'https://mybento.kz/inc/ajax/block_info.php?block=1'.$field_arr;
        //echo $url."\n";
        //$result = get_web_page( $url );
        $url = 'https://mybento.kz/inc/ajax/block_info.php';
        $postdata = 'block=1'.$field_arr;
        $result = post_content ($url,$postdata);
        echo "<p>*";
        print_r($result['content']);
    }
}