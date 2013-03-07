<div style="<?php echo $options['Additional_CSS']; ?>">
    <form id="searchform" method="GET" action="<?php echo $options['Base_URL'] ? '/' . $options['Base_URL'] : '/products'; ?>">
        <input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="width:60%; padding:4px 4px 7px;">
        <input type="submit" value="Search">
    </form>
</div>
