<?php
// SG-lite
// We're using this class for all data that should only be loaded from one pattern, i.e. singleton

namespace Application\Controller\SinglePage\Dashboard\Rates;

use \Concrete\Core\Page\Controller\DashboardPageController;

class AddOn extends DashboardPageController
{
    private $db_prefix = "RatesManager";
    
    public function __construct() {
        
    }
    
    public function on_start() {

    }
    
    // Get the Database Name from a given Rate Type
    public function getTableNameFromRateType($rate_type) {
        switch ($rate_type) {
            case "mortgage_rates":
                $db_name = "Mortgages";
                break;
            case "auto_rates":
                $db_name = "Auto";
                break;
            case "home_equity_rates":
                $db_name = "HomeEquity";
                break;
            case "home_improvement_rates":
                $db_name = "HomeImprovement";
                break;
            case "home_flex_rates":
                $db_name = "HomeFlex";
                break;
            case "personal_rates":
                $db_name = "PersonalLoans"; // Personal Loans
                break;
            case "savings_rates":
                $db_name = "SavingsAccounts";
                break;
            case "money_market_rates":
                $db_name = "MoneyMarket";
                break;
            case "certificate_rates":
                $db_name = "Certificates";
                break;
            case "checking_account_rates":
                $db_name = "Checking";
                break;
            default:
                $db_name = "Mortgages";
                break;
        }
        
        $db_name = $this->db_prefix . "_" . $db_name;
        return $db_name;
    }
    
    public function getColumnNames($table_name, $db) {
        $sql = $db->prepare("SELECT DISTINCT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'$table_name'");
        $sql->execute();
        
        if ($sql) {
            return $sql->fetchAll();
        }
        else {
            return "Execution unsuccessful at getColumnNames.";
        }
    }
    
}
