<?php
$params = array_reverse(explode('/', get_query_var('queryParams')));

$sendParams = array();
if (!empty($params))
{
    $params = array_reverse($params);
    foreach ($params as $k => $p)
    {
        //if the number is even, grab the next index value
        if (!($k & 1))
        {
            $sendParams[$p] = $params[$k + 1];
        }
    }
}

$query = $sendParams['query'];
$base = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products';
$url = home_url('/') . $base;

if (is_page($base) && $_POST['q'])
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $prodSubmit = preg_replace('/\/$/', '', $url);
    $newQuery = str_replace(array('/query/' . $query, '/query/' . rawurlencode($query)), array('', ''), $prodSubmit);
    header('Location: ' . $newQuery . '/query/' . rawurlencode(trim($_POST['q'])));
    exit;
}
elseif ($_POST['q'])
{
    header('Location: ' . $url . '/query/' . rawurlencode(trim($_POST['q'])));
    exit;
}
?>

<div style="<?php echo $options['Additional_CSS']; ?>">
    <form class="searchform" method="POST" action="">
        <input class="prosper_field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>">
        <input class="prosper_submit" type="submit" value="Search">
    </form>
</div>