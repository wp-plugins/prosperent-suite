<div style="<?php echo $options['Additional_CSS']; ?>">
	<form id="searchform" method="GET" action="<?php echo !$options['Base_URL'] ?  '/products' : '/' . $options['Base_URL']; ?>">
		<input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 7px;">
		<input class="submit" type="submit" id="searchsubmit" value="Search">
	</form>
</div>