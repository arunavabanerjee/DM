
CREATE TABLE [IF NOT EXISTS] [schema_name].table_name (
 column_1 data_type PRIMARY KEY,
   column_2 data_type NOT NULL,
 column_3 data_type DEFAULT 0,
 table_constraint
) [WITHOUT ROWID];
----------------------------------------------------------
CREATE TABLE contacts (
 contact_id integer PRIMARY KEY,
 first_name text NOT NULL,
 last_name text NOT NULL,
 email text NOT NULL UNIQUE,
 phone text NOT NULL UNIQUE
);
----
CREATE TABLE groups (
 group_id integer PRIMARY KEY,
 name text NOT NULL
);
----
CREATE TABLE contact_groups (
 contact_id integer,
 group_id integer,
 PRIMARY KEY (contact_id, group_id),
 FOREIGN KEY (contact_id) REFERENCES contacts (contact_id) 
 ON DELETE CASCADE ON UPDATE NO ACTION,
 FOREIGN KEY (group_id) REFERENCES groups (group_id) 
 ON DELETE CASCADE ON UPDATE NO ACTION
);

----------------------------------------------------------
<?php
/**
 * SQLite 3 DB Writer 
 *
 *
 * @copyright  Copyright (c) 2018-2019, Arunava Banerjee (email: mrarunbanerjee.sdn@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SQLiteDB_Writer extends SQLite3{
 
    private static $DATABASE = NULL;
    

    /* protected constructor */
    /**
     * Opens the database and creates one if not present
     *
     * @param string $db
     * @return bool value
     */
    private function _construct($db)
    {
        $this->open($db);
    }

    /*
     * Opens the database and creates one if not present
     *
     * @param string $db
     * @return bool value
     */
    public static function opendb($db){
       if(self::$DATABASE == NULL){
         self::$DATABASE = new SQLiteDB_Writer($db);
       }
       return self::$DATABASE;
    }

    /**
     * Function createTable
     * Creates table in the database
     *	
     * @param array $csv_fields
     * @param array $dArray
     * @return array $colVal
     */
    public function createTable($csv_fields, $dArray){ 
      $colVal = array(); $c=0; 
      $sql = 'CREATE TABLE IF NOT EXISTS MVPROPERTIES('; 
      foreach($csv_fields as $dItem){ 
        $colVal[$c] = array_search($dItem, $dArray);
        if($c == 0){ $sql .= 'ApNo INT PRIMARY KEY NOT NULL,'; }
        elseif( $c == (sizeof($csv_fields)-1)){ $sql .= $dItem. ' TEXT NOT NULL'; }
        else{ $sql .= $dItem. ' TEXT NOT NULL,'; } 
	$c++;	
      }
      $sql .= ');'; //echo $sql; 
      $retVal = self::$DATABASE->exec($sql);
      //self::$DATABASE->close();	
      return $colVal;
    }	

    /**
     * Function insertTable
     * Creates table in the database
     *	
     * @param array $colVal
     * @param array $dArray
     * @return bool $retVal
     */
    public function insertIntoTable($csv_fields, $colVal, $dArray){ 
      $c=1; $sql = 'INSERT INTO MVPROPERTIES('; 
      //setup fieldset
      foreach($csv_fields as $field){ $sql .=  ($c != sizeof($csv_fields)) ? $field.',' : $field; $c++; }			 
      $sql .= ') VALUES ('; $c=1;  
      foreach($dArray as $indx => $dItem){
	if(in_array($indx, $colVal)){ 
	  $sql .= ($c != sizeof($csv_fields)) ?  ($c == 1) ? $dItem."," : "'".$dItem."',"  : "'".$dItem."'"; $c++; }	
      }
      $sql .= ');'; //echo $sql; 
      $retVal = self::$DATABASE->exec($sql);
      //self::$DATABASE->close(); 
      return $dArray[0];
    }	


}
