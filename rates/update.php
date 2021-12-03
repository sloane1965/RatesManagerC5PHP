<?php
namespace Application\Controller\SinglePage\Dashboard\Rates;

use \Concrete\Core\Page\Controller\DashboardPageController;

/*
 * WNTBD?
 * AddOn is a newly established class to hold functions that receive & return data that will be used throughout the entire application &
 * won't really be changed. (See AddOn::getDBNameFromRateType for an example)
 * 
 * 1. In update.php we need to get data from the DB using the POST ID&RateType
 * 2. Also need to display the newly edited data to the user, and ask for confirmation on whether the edit should be made.
 * 3. If confirmed, update the data. (perhaps this page should be called "confirm" rather than "update"...
 */

class Update extends DashboardPageController
{
    public $ID;
    public $rate_type;
    
    private $add_on;
    private $table_name;
    private $db;
    
    public function on_start() {
        $this->ID = $_POST["ID"];
        $this->rate_type = $_POST["rate_type"];      
        
        $this->db = \Database::connection();
    }
    
    public function view() {
        $this->add_on = new AddOn();
        $this->table_name = $this->add_on->getTableNameFromRateType($this->rate_type);
    }
    
    public function updateTable() {
        $message = '';
        $sql_str;
        
        // This array will contain the names of the columns of the table we're acting on for the purpose of updating.
        $column_name_arr = $this->add_on->getColumnNames($this->table_name, $this->db);
        
        $l = count($column_name_arr);
        $i = 1;
        
        foreach ($column_name_arr as $column_name_a) {
            $column_name = $column_name_a["COLUMN_NAME"];
            
            if ($column_name == "ID") {
                $l -= 1;
                continue;
            }
            
            if ($i++ != $l) {
                $sql_str .= $column_name . " = '" . $_POST[$column_name] . "', ";
            }
            else {
                $sql_str .= $column_name . " = '" . $_POST[$column_name] . "'";
            }
        }
        
        
        $sql = $this->db->prepare("UPDATE $this->table_name SET $sql_str WHERE `ID` = $this->ID");
        
        if ($sql->execute()) {
            $message = "<b>$this->table_name</b> at <b>ID $this->ID</b> successfully updated. You can review this change on the <a href='/dashboard/rates/edit?rate_type=$this->rate_type'>/Rates/Edit page.</a>";
        }
        else {
            $message = "Something went wrong, please try again. If the problem persists, contact an administrator.";
        }
        
        return $message;
    }
    
}