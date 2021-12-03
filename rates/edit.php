<?php
namespace Application\Controller\SinglePage\Dashboard\Rates;

use \Concrete\Core\Page\Controller\DashboardPageController;

/*
 * WWNTD
 * The tables are now update-able within our Rates single page. We now need to:
 * 
 * 1. Add all the rate types & fill them with rates
 * 2. Develop a shortcode-like system (SEE: https://www.concrete5.org/community/forums/customizing_c5/shortcodes-in-c5)
 * so that rates can be generated straight from html using shortcode. The problem is that some pages (namely the main Rates page)
 * loads every rate, which is a lot of db calls. must figure out way to get data only once, populate an array with it, and pull from an array instead.
 */

class Edit extends DashboardPageController
{
    public $rate_type; // This is to be used for all controller functions. Adding, editing, etc.
    private $table_name;
    private $add_on;
    private $db;
    
    public function on_start() {
        $al = \Concrete\Core\Asset\AssetList::getInstance();
        $al->register('javascript', 'rates-edit', 'js/single_page/dashboard/rates/rates.js');
        $al->register('css', 'rates-edit', 'css/single_page/dashboard/rates/rates.css');
        
        // Let's set the data from rate_type as the currently selected table.
        if ($_GET["rate_type"]) {
            $this->rate_type = $_GET["rate_type"];
        }
        
        $this->db = \Database::connection();
        
        // Initialize add_on from the start.
        $this->add_on = new AddOn();
    }
    
    public function view()
    {
        $this->requireAsset('javascript', 'rates-edit');
        $this->requireAsset('css', 'rates-edit');
        $this->table_name = $this->add_on->getTableNameFromRateType($this->rate_type);
        
        $this->set('message', "You are currently editing $this->rate_type");
    }
    
    public function getAllRates() {
        $column = $this->rate_type;
        
        // Set which values appear from database based on $rate_type (which is set by rate_type GET req.)
        // All types should end in "_rates" for readability.
        $r = $this->add_on->getTableNameFromRateType($column);
        
        $sql = 'SELECT * FROM ' . $r . ' ORDER BY list_order';

        if ($this->db->fetchAll($sql)) {
            $query = $this->db->fetchAll($sql);
        }
        
        return $query;
    }
    
    public function displayTable($arr) {        
        $table;
        $length = count($arr);
                
        // The form portion still needs to post to a page.
        // We will create a new Single Page "Rates/Save" that will process rate edits
        // using the form data.
        
        $form_method = "POST";
        $form_action = "update";
        
        // This is an array of the column names for this particular rate type.
        // We can use it to automatically populate each form.
        $column_names = $this->add_on->getColumnNames($this->table_name, $this->db);

        for ($i = 0; $i < $length; $i++) {
            $table .= "<tr class='row_container'>";
            
                $table .= "<td class='td-ID'><input form='editForm-$i' name='ID' class='hide' value='" . $arr[$i]["ID"] . "'><input form='editForm-$i' name='rate_type' class='hide' value='$this->rate_type'>" . $arr[$i]["ID"] . "</td>";
                
                foreach ($column_names as $name) {
                    if ($name["COLUMN_NAME"] == "ID") { continue; }
                    if ($name["COLUMN_NAME"] == "list_order") {
                       $table .= "<td data-item-type='".$name["COLUMN_NAME"]."' class='editable'><input type='number' min='0' max='100' step='.5' name='".$name["COLUMN_NAME"]."' form='editForm-$i' value='".$arr[$i][$name["COLUMN_NAME"]]."' class='hide' type='text'><p><b>".$arr[$i][$name["COLUMN_NAME"]]."</p></td>";
                    }
                    else {
                        $table .= "<td data-item-type='".$name["COLUMN_NAME"]."' class='editable'><input name='".$name["COLUMN_NAME"]."' form='editForm-$i' value='".$arr[$i][$name["COLUMN_NAME"]]."' class='hide' type='text'><p>".$arr[$i][$name["COLUMN_NAME"]]."</p></td>";
                    }
                }
                
                $table .= "<td class='button_container'><button type='button' class='stop_edit_button sib hide'>Stop</button><button type='button' class='edit_table_button sib'>Edit</button> <button form='editForm-$i' type='submit' class='update_table_button hide sib'>Update</button></td>";
                $table .= "<form action='$form_action'  data-item-type='editForm-$i' id='editForm-$i' method='$form_method'></form>"; // The Post Form, using attributes to determine which inputs are under it.
            $table .= "</tr>";
        }
        
        /*
        switch ($column) {
            case "mortgage_rates":
                for ($i = 0; $i < $length; $i++) {
                    $table .= "<tr class='row_container'>";
                        $table .= "<td><input form='editForm-$i' name='ID' class='hide' value='" . $arr[$i]["ID"] . "'><input form='editForm-$i' name='rate_type' class='hide' value='$column'>" . $arr[$i]["ID"] . "</td>";
                        $table .= "<td data-item-type='mortgage_type' class='editable'><input name='mortgage_type' form='editForm-$i' value='". $arr[$i]["mortgage_type"] ."' class='hide' type='text'><p>" . $arr[$i]["mortgage_type"] . "</p></td>";
                        $table .= "<td data-item-type='mortgage_rate' class='editable'><input name='mortgage_rate' form='editForm-$i' value='".$arr[$i]["mortgage_rate"]."' class='hide' type='text'><p>" . $arr[$i]["mortgage_rate"] . "</p></td>";
                        $table .= "<td data-item-type='mortgage_points' class='editable'><input name='mortgage_points' form='editForm-$i' value='".$arr[$i]["mortgage_points"]."' class='hide' type='text'><p>" . $arr[$i]["mortgage_points"] . "</p></td>";
                        $table .= "<td data-item-type='mortgage_apr' class='editable'><input name='mortgage_apr' form='editForm-$i' value='".$arr[$i]["mortgage_apr"]."' class='hide' type='text'><p>" . $arr[$i]["mortgage_apr"] . "</p></td>";
                        $table .= "<td data-item-type='mortgage_ppt' class='editable'><input name='mortgage_ppt' form='editForm-$i' value='".$arr[$i]["mortgage_ppt"]."'  class='hide' type='text'><p>" . $arr[$i]["mortgage_ppt"] . "</p></td>";
                        $table .= "<td class='button_container'><button type='button' class='edit_table_button'>Edit</button> <button form='editForm-$i' type='submit' class='update_table_button'>Update</button></td>";
                        $table .= "<form action='$form_action' target='_blank' data-item-type='editForm-$i' id='editForm-$i' method='$form_method'></form>"; // The Post Form, using attributes to determine which inputs are under it.
                    $table .= "</tr>";
                }
                break;
            case "auto_rates":
                for ($i = 0; $i < $length; $i++) {
                    $table .= "<tr class='row_container'>";
                        $table .= "<td><input form='editForm-$i' name='ID' class='hide' value='" . $arr[$i]["ID"] . "'><input form='editForm-$i' name='rate_type' class='hide' value='$column'>" . $arr[$i]["ID"] . "</td>";
                        $table .= "<td data-item-type='auto_type' class='editable'><input name='auto_type' form='editForm-$i' value='". $arr[$i]["auto_type"] ."' class='hide' type='text'><p>" . $arr[$i]["auto_type"] . "</p></td>";
                        $table .= "<td data-item-type='auto_apr' class='editable'><input name='auto_apr' form='editForm-$i' value='".$arr[$i]["auto_apr"]."' class='hide' type='text'><p>" . $arr[$i]["auto_apr"] . "</p></td>";
                        $table .= "<td data-item-type='auto_term' class='editable'><input name='auto_term' form='editForm-$i' value='".$arr[$i]["auto_term"]."' class='hide' type='text'><p>" . $arr[$i]["auto_term"] . "</p></td>";
                        $table .= "<td data-item-type='auto_remarks' class='editable'><input name='auto_remarks' form='editForm-$i' value='".$arr[$i]["auto_remarks"]."' class='hide' type='text'><p>" . $arr[$i]["auto_remarks"] . "</p></td>";
                        $table .= "<td class='button_container'><button type='button' class='edit_table_button'>Edit</button> <button form='editForm-$i' type='submit' class='update_table_button'>Update</button></td>";
                        $table .= "<form action='$form_action' data-item-type='editForm-$i' id='editForm-$i' method='$form_method'></form>"; // The Post Form, using attributes to determine which inputs are under it.
                    $table .= "</tr>";
                }
                break;
            case "home_equity_rates":
                for ($i = 0; $i < $length; $i++) {
                    $table .= "<tr class='row_container'>";
                    $table .= "<td><input form='editForm-$i' name='ID' class='hide' value='" . $arr[$i]["ID"] . "'><input form='editForm-$i' name='rate_type' class='hide' value='$column'>" . $arr[$i]["ID"] . "</td>";
                    $table .= "<td data-item-type='auto_type' class='editable'><input name='auto_type' form='editForm-$i' value='". $arr[$i]["auto_type"] ."' class='hide' type='text'><p>" . $arr[$i]["auto_type"] . "</p></td>";
                    $table .= "<td data-item-type='auto_apr' class='editable'><input name='auto_apr' form='editForm-$i' value='".$arr[$i]["auto_apr"]."' class='hide' type='text'><p>" . $arr[$i]["auto_apr"] . "</p></td>";
                    $table .= "<td data-item-type='auto_term' class='editable'><input name='auto_term' form='editForm-$i' value='".$arr[$i]["auto_term"]."' class='hide' type='text'><p>" . $arr[$i]["auto_term"] . "</p></td>";
                    $table .= "<td data-item-type='auto_remarks' class='editable'><input name='auto_remarks' form='editForm-$i' value='".$arr[$i]["auto_remarks"]."' class='hide' type='text'><p>" . $arr[$i]["auto_remarks"] . "</p></td>";
                    $table .= "<td class='button_container'><button type='button' class='edit_table_button'>Edit</button> <button form='editForm-$i' type='submit' class='update_table_button'>Update</button></td>";
                    $table .= "<form action='$form_action' data-item-type='editForm-$i' id='editForm-$i' method='$form_method'></form>"; // The Post Form, using attributes to determine which inputs are under it.
                    $table .= "</tr>";
                }
                break;
            
            default:
                break;
        }
        */
        
        return $table;
    }
}
