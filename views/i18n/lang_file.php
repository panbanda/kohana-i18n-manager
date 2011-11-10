<?php

echo "<?php defined('SYSPATH') OR die('No direct access allowed.');\n\n";

echo "// I18n generated at: " . date('Y-m-d H:i:s T') . "\n\n";

echo "return array\n(\n";

foreach ($langs as $lang)
{
	echo "\t'".str_replace("'", "\'", $lang->key)."' => '".str_replace("'", "\'", $lang->text)."',\n";
}

echo ");";