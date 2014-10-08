<?php

require_once __DIR__ . '/../inc/WebCache.php';
require_once __DIR__ . '/../inc/Graphite.php';
require_once __DIR__ . '/../inc/NagfView.php';
require_once __DIR__ . '/../inc/Nagf.php';

// var_dump( Graphite::getProjects() );

// var_dump( Graphite::getHostsForProject( 'integration') );

$app = new Nagf();
$view = $app->getView();

?><!DOCTYPE html>
<html dir="ltr" lang="en-US" class="no-js">
<head>
	<meta charset="utf-8">
	<title>Nagf - WMFLabs</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="./lib/bootstrap-3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="./main.css">
	<script src="./head.js"></script>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="./">Nagf</a>
		</div>
		<div class="navbar-collapse collapse">
			<form class="navbar-form navbar-right" role="form" action="./" method="get">
				<div class="form-group">
					<?php echo $view->getProjectMenu(); ?>
				</div>
				<button type="submit" class="btn btn-success only-no-js">Show</button>
			</form>
			<?php echo $view->getHostForm(); ?>
		</div><!--/.nav-collapse -->
	</div>
</div>
<div class="container">
<?php echo $view->getPage(); ?>
</div>
<script src="./lib/jquery-1.11.0/jquery.min.js"></script>
<script src="./lib/bootstrap-3.1.1/js/bootstrap.min.js"></script>
<script src="./main.js"></script>
<footer class="nagf-footer" role="contentinfo">
	<div class="container">
		<p>Created by <a href="https://github.com/Krinkle" target="_blank">@Krinkle</a>.</p>
		<p>Code licensed under <a href="http://krinkle.mit-license.org/" target="_blank">MIT</a>.</p>
		<ul class="nagf-footer-links muted">
			<li><a dir="ltr" lang="en" href="https://github.com/wikimedia/nagf">Source repository</a></li>
			<li>·</li>
			<li><a dir="ltr" lang="en" href="https://github.com/wikimedia/nagf/issues">Issue tracker</a></li>
		</ul>
	</div>
</footer>
</body>
</html>
