<link rel="stylesheet" type="text/css" href="<?php echo $this->getThemePath(); ?>/_app/data_accordion/view.css">

<?php
require_once('controller.php');
$controller = new DAccordController();

$controller->display($c);