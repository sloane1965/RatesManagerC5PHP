<?php

/**
 * The controller for the content block.
 *
 * @package Blocks
 * @subpackage Content
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class DAccordController
{
    protected $btInterfaceWidth = "600";
    protected $btInterfaceHeight = "465";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutputLifetime = 0; // until manually updated or cleared
    
    private $ratesTables = array(
        "RatesManager_Mortgages" => array("mortgage_rates", "Mortgages"),
        "RatesManager_Auto" => array("auto_rates", "Vehicle Loans"),
        "RatesManager_Checking" => array("checking_account_rates", "Checking Accounts"),
        "RatesManager_HomeEquity" => array("home_equity_rates", "Home Equity Loans"),
        "RatesManager_HomeFlex" => array("home_flex_rates", "Homeflex Lines of Credit"),
        "RatesManager_HomeImprovement" => array("home_improvement_rates", "Home Improvement Loans"),
        "RatesManager_MoneyMarket" => array("money_market_rates", "Money Market Accounts"),
        "RatesManager_PersonalLoans" => array("personal_rates", "Personal Loans & Lines of Credit"),
        "RatesManager_SavingsAccounts" => array("savings_rates", "Savings Accounts"),
	    "RatesManager_Certificates" => array("cert_rates", "Certificates")
    );

    /**
     * @var \Concrete\Core\Statistics\UsageTracker\AggregateTracker
     */
    protected $tracker;

    public function getBlockTypeDescription()
    {
        return t("A special accordion block for using Rates data.");
    }

    public function getBlockTypeName()
    {
        return t("Data Accordion Block");
    }
    
    private function getRates() {
        $_results = array();
        
        $db = \Database::connection();
        
        foreach ($this->ratesTables as $table => $table_s_name) {
            $query = $db->prepare("SELECT * FROM $table ORDER BY list_order");
            
            if ($query->execute()) {
                $_results[$table_s_name[1]] = $query->fetchAll();
            }
        }

        return $_results;
    }
    
    
    public function display($page) {
        $_results = $this->getRates();
        
        $m = 0;
        
        foreach ($_results as $result => $value) {
            $tableStructure = $this->getTableStructure($result);
            
            echo "<div class='data-accordion-container'>";
                echo "<div class='data-accordion-title' >";
                    echo "<a class='data-accordion-collapse-button' role='button' data-toggle='collapse' href='#dt-$m'></a>";
                    echo "<span>$result</span>";
                echo "</div>";

                echo "<div class='data-accordion-table collapse hidden' id='dt-$m'>";
                
                    echo "<div class='accordion-content'>";
                        $a = new Area("Table Additional Content Top $m");
                        $a->display($page);
                    echo "</div>";
                    
                    echo "<table>";
                        echo $tableStructure[0];
                        
                        echo "<tbody>";
                            foreach ($value as $v) {
                                echo "<tr>";
                                foreach ($tableStructure[1] as $columnDBName) {
                                    if ($v[$columnDBName] != "") {
                                    	echo "<td>".$v[$columnDBName]."</td>";
                                    }
                                    else {
                                        
                                    }
                                }
                                echo "</tr>";
                            }
                        echo "</tbody>";
                    echo "</table>";
                    
                    echo "<div class='accordion-content'>";
                        $a = new Area("Table Additional Content Bottom $m");
                        $a->display($page);
                    echo "</div>";
                    
                echo "</div>";
            echo "</div>";
            
            $m++;
        }
    }
    
    // This function will take in table type and output
    // what type of table head it needs & what indexes to search by (mortgage_points, checking_min_to_open, etc)
    
    private function getTableStructure($tableName) { // This is where the result from display() should get passed, or a similar string
        $_tHead;
        $_tableStructure = array(); // This array should first contain a table head, and in the second position contain what to sort the array by in display.

        switch ($tableName) {
            case "Mortgages":
                $tLength = 5; // Total column number or table length
                $tHeadItems = array("Mortgage Type", "Rate", "APR", "Payment Per $1,000", "Points",); // Column names
                $tColumnDBNames = array("mortgage_type", "mortgage_rate", "mortgage_apr", "mortgage_ppt", "mortgage_points"); // These are the names of the columns in the DB. Used to sort through array in display().
                break;
            case "Vehicle Loans":
                $tLength = 4;
                $tHeadItems = array("Account Type", "APR", "Term / Repayment", "Remarks");
                $tColumnDBNames = array("auto_type", "auto_apr", "auto_term", "auto_remarks");
                break;
            case "Checking Accounts":
                $tLength = 4;
                $tHeadItems = array("Account Type", "Dividend Rate", "APY", "Minimum Balance To Open", "Minimum Balance To Earn Dividend");
                $tColumnDBNames = array("checking_type", "checking_dividend_rate", "checking_apy", "checking_min_to_open", "checking_min_balance");
                break;
            case "Home Equity Loans":
                $tLength = 5;
                $tHeadItems = array("APR", "Term / Repayment", "Maximum Amount", "Remarks");
                $tColumnDBNames = array("home_equity_apr", "home_equity_term", "home_equity_max", "home_equity_remarks");
                break;
            case "Homeflex Lines of Credit":
                $tLength = 5;
                $tHeadItems = array("Account Type", "APR", "Term / Repayment", "Maximum Amount", "Remarks");
                $tColumnDBNames = array("home_flex_type", "home_flex_apr", "home_flex_term", "home_flex_max", "home_flex_remarks");
                break;
            case "Home Improvement Loans":
                $tLength = 5;
                $tHeadItems = array("APR", "Term / Repayment", "Maximum Amount", "Payment Per $1,000", "Remarks");
                $tColumnDBNames = array("home_improvement_apr", "home_improvement_term", "home_improvement_max", "home_improvement_ppt", "home_improvement_remarks");
                break;
            case "Personal Loans & Lines of Credit";
                $tLength = 5;
                $tHeadItems = array("Account Type", "APR", "Repayment Period", "Maximum Amount", "Remarks");
                $tColumnDBNames = array("personal_type", "personal_apr", "personal_term", "personal_max", "personal_remarks");
                break;
            case "Savings Accounts":
                $tLength = 3;
                $tHeadItems = array("Account Type", "Dividend Rate", "APY", "Minimum Balance to Open", "Minimum Balance to Earn Dividend");
                $tColumnDBNames = array("share_type", "share_dividend_rate", "share_apy", "minimum_balance_to_open", "share_minimum_balance");
                break;
            case "Money Market Accounts":
                $tLength = 4;
                $tHeadItems = array("Account Type", "Dividend Rate", "APY", "Minimum Balance to Open", "Minimum Balance To Earn Dividend");
                $tColumnDBNames = array("money_market_type", "money_market_dividend_rate", "money_market_apy", "money_market_minimum_to_open", "money_market_minimum_balance");
                break;
            case "Certificates":
                $tLength = 5;
                $tHeadItems = array("Account Type", "Dividend Rate", "APY", "Term", "Minimum Balance to Open", "Minimum Balance to Earn Dividend");
                $tColumnDBNames = array("cert_type", "cert_dividend_rate", "cert_apy", "cert_term", "cert_minimum", "cert_remarks");
                break;
            default:
                $tLength = 0;
                $tHeadItems = array();
                $tColumnDBNames = array();
                break;
        }
        
        $_tHead .= "<thead><tr>";
        
        foreach ($tHeadItems as $th) {
            $_tHead .= "<th>$th</th>";
        }
        
        $_tHead .= "</thead></tr>";
        
        $_tableStructure[0] = $_tHead;
        $_tableStructure[1] = $tColumnDBNames;
        $_tableStructure[2] = $tLength;
        
        return $_tableStructure;
    }
}
