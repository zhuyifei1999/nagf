<?php

require_once __DIR__ . '/../inc/WebCache.php';
require_once __DIR__ . '/../inc/Graphite.php';
require_once __DIR__ . '/../inc/NagfView.php';
require_once __DIR__ . '/../inc/Nagf.php';

header('content-type: text/html; charset=utf-8');

try {
	$app = new Nagf();
	$view = $app->getView();
	$html = $view->output();
} catch (Exception $e) {
	$html = NagfView::error($e);
}

echo $html;
