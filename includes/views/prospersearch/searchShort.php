<div style="<?php echo 'width:' . $pieces['w'] . $pieces['ws'] . ';' . $pieces['css']; ?>">
    <form class="searchform" method="POST" action="/" rel="nolink">
        <input class="prosper_field" type="text" name="q" id="s" placeholder="<?php echo $pieces['sbar'] ? $options['sbar'] : 'Search Products'; ?>">
        <input class="prosper_submit" type="submit" value="<?php echo $pieces['sbu'] ? $pieces['sbu'] : 'Search'; ?>">
    </form>
</div>
