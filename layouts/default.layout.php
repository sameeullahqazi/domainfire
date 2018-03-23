<?php 
	$rnd = md5(uniqid() . time());
?>
<!DOCTYPE html>
<html>
	<head>
		<script src="/js/jquery-3.3.1.min.js?r=<?php print $rnd; ?>"></script>
		<script src="/js/default.js?r=<?php print $rnd; ?>" type="text/javascript"></script>
	</head>
	<body>
		<?php echo $content_for_layout; ?>
	</body>
</html>
