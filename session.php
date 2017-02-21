<?php
session_start();
$varFunc = function (array &$store) {
    return function &($name) use (&$store)
    {
        $keys = preg_split('~("])?(\\["|$)~', $name, -1, PREG_SPLIT_NO_EMPTY);
        $var = &$store;
        foreach($keys as $key)
        {
            if (!is_array($var) || !array_key_exists($key, $var)) {
                $var[$key] = NULL;
            }
            $var = &$var[$key];
        }
        return $var;
    };
};
function unset_null(&$arr){
    $arr = array_filter($arr);
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            $arr[$key] = unset_null($value);
        }
    }
    return $arr;
}
$vars = $varFunc($GLOBALS);
if (isset($_REQUEST['clear'])&&$_REQUEST['clear']=='1') {
	session_destroy();
	header("Location:session.php");
	exit;
}
elseif (isset($_REQUEST['keys'])) {
    $keys = json_decode($_REQUEST['keys']);
    $string = "_SESSION";
    foreach ($keys as $key) {
        $string .= '["'.$key.'"]';
    }
    $var = &$vars($string);
    $var = null;
    $session_var = $_SESSION;
    $_SESSION = unset_null($session_var);
    header("Location:session.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Session And Clear session</title>
    <style type="text/css">
    .all-clear {
        text-decoration: none; display: inline-block;
        padding            : 6px 12px;
        margin-bottom      : 0;
        font-size          : 14px;
        font-weight        : 400;
        line-height        : 1.42857143;
        text-align         : center;
        white-space        : nowrap;
        vertical-align     : middle;
        -ms-touch-action   : manipulation;
        touch-action       : manipulation;
        cursor             : pointer;
        -webkit-user-select: none;
        -moz-user-select   : none;
        -ms-user-select    : none;
        user-select        : none;
        background-image   : none;
        border             : 1px solid transparent;
        border-radius      : 4px;color: #fff;
        background-color   : #f54c00;
        border-color       : #f54c00;
        margin-top         : 30px;
    }
    .clear {
        text-decoration: none; display: inline-block;
        padding            : 0px 4px;
        margin-bottom      : 0;
        font-size          : 11px;
        font-weight        : 400;
        line-height        : 1.42857143;
        text-align         : center;
        white-space        : nowrap;
        vertical-align     : middle;
        -ms-touch-action   : manipulation;
        touch-action       : manipulation;
        cursor             : pointer;
        -webkit-user-select: none;
        -moz-user-select   : none;
        -ms-user-select    : none;
        user-select        : none;
        background-image   : none;
        border             : 1px solid transparent;
        border-radius      : 4px;color: #fff;
        background-color   : #f54c00;
        border-color       : #f54c00;
    }
    </style>
</head>
<body>
<a href="session.php?clear=1" class="all-clear">Clear</a>
<br/><br/>
<?php
sessionPrint($_SESSION);
function sessionPrint($ses,$parentKey=array()) {
    echo "<div style='padding-left: 50px;'>";
    foreach ($ses as $key => $value){ 
        $keys = $parentKey;
        $keys[] = $key;
        ?>
        <a href="session.php?keys=<?= urlencode(json_encode($keys)) ?>" class="clear">Clear</a>
        <?php
        //print_r($parentKey);
        echo "[".$key."]"."=>";
        if(is_array($value)>0){
            echo "array(<br>";
            $parentKey[]=$key;
            sessionPrint($value,$parentKey);
            array_pop($parentKey);
            echo ")";
        }
        else{
            echo $value;
        }
        echo "</br>";
    }
    echo "</div>";
} 
?>
</body>
</html>
