<?php
	defined('C5_EXECUTE') or die("Access Denied.");
	$this->inc('_includes/header.php');
?>

	<?php require_once("_app/populate_headers.php"); ?>
	
	<main>
		<article>
			<table>
				<tr>
					<td id="mainContent">

						<div id="paragraph">
							<?php
						    $a = new Area('Main Content');
						    $a->display($c);
							?>
						</div>
						
						<div class="paragraph">
							<?php require_once('_app/data_accordion/view.php'); ?>
						</div>
						
						<div id="paragraph-below">
							<?php
						    $a = new Area('Main Content Below');
						    $a->display($c);
							?>
						</div>
					</td>
				</tr>
			</table>
		</article>
	</main>

<?php $this->inc('_includes/footer.php'); ?>

