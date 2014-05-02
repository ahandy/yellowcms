	<html>	
<head>
	<title>Yellow :: Content Management System</title>

	<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH; ?>datatables/media/css/jquery.dataTables.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>cms/fonts.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>cms/style.css" />
	
	<script src="<?php echo JS_PATH; ?>jquery/jquery.js" type="text/javascript"></script>
  	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<script src="<?php echo JS_PATH; ?>dropzone/dropzone.js" type="text/javascript"></script>
	<script src="<?php echo JS_PATH; ?>yllw/rows.js" type="text/javascript"></script>
</head>

<body>
<div class='page-content'>
	<div class='yllw'>
		<div class='left'>Yellow Content Management System</div>
		<div class='right'><a href='<?php echo HTTP; ?>logout'>Logout</a></div>
		<div class='clear'></div>
	</div>

	<div class='sidebar'>
		<h1>Tables</h1>
		<ul class='menu'>
		<?php
		foreach($this -> tables as $table) {
			echo "<li><a href='" . HTTP . "tables/show/{$table['clean']}'>{$table['display']}</a> <a href='" . HTTP . "rows/add/{$table['clean']}'>(+)</a></li>";
		}
		?>
		</ul>
	</div>

	<div class='content'>
