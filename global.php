<?php
session_start();

if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

/*class loader*/
function __autoload($class_name) 
{
	$include_path = get_include_path();
	$mfaf ='./framework';
	$magic = '/PHPImageWorkshop';

	$imageMagic = $mfaf.$magic.':'.$mfaf.$magic.'/Core:'.$mfaf.$magic.'/Core/Exception:'.$mfaf.$magic.'/Exception';
	//$imageMagic = $mfaf.$magic;
	

	//Make sure framework path is correct.
	$include_path =$mfaf.':'.$mfaf.'/component:'.$mfaf.'/trait:'.$mfaf.'/preference:';
	$include_path .= $imageMagic;
	$include_path .= ':sts';
	$include_path .= ':./mcalendar2';

	//print $include_path;
	$include_path_tokens = explode(':', $include_path);
     
	foreach($include_path_tokens as $prefix)
	{
		$path = $prefix . '/' . $class_name . '.php';


		if(file_exists($path))
		{
			require_once $path;
			return;
		}
	} 

}
?>
