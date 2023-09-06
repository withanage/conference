<?php
$ojsPath = "/var/www/html/ojs-3_3";
$locale = "en_US";
$files = array_merge(glob("{$ojsPath}/locale/{$locale}/*.po"), glob("{$ojsPath}/lib/pkp/locale/{$locale}/*.po"));
$outputPath = "{$ojsPath}/plugins/generic/conference/customLocale/en_US/";
$modifier = [];

$map = array(
	"submission" => "Conference Proceedings Submission",
	"article" => "Conference Proceedings Submission",
	"issue" => "Conference Proceedings Volume",
	"journal" => "Conference Proceedings Series"
);

foreach ($map as $key => $value) {
	$modifier['/(msgstr\s.*)(' . $key . ')(.*)/m'] = '$1' . $value . '$3';
	$modifier['/(msgstr\s.*)(' . $key . 's)(.*)/m'] = '$1' . ucfirst($value) . 's$3';
	$modifier['/(msgstr\s.*)(' . ucfirst($key) . ')(.*)/m'] = '$1' . $value . '$3';
	$modifier['/(msgstr\s.*)(' . ucfirst($key) . 's)(.*)/m'] = '$1' . ucfirst($value) . 's$3';
}

$output = array();

foreach ($files as $file) {
	$content = file_get_contents($file);
	$matching_pattern = '/\nmsgid\s\".*\"\nmsgstr\s(.*)(issue|submssion|article|Issue|Submission|Article|journal|journals|Journal|Journals)(.*)/im';

	preg_match_all($matching_pattern, $content, $matches);
	foreach ($matches[0] as $match) {
		$msgstr = preg_replace(array_keys($modifier), array_values($modifier), $match) . PHP_EOL;
		$fileName = $outputPath . str_replace($ojsPath, '', $file);
		$fileDir = dirname($fileName);
		if (file_exists($fileName)) {
			$status = unlink($fileName);
		}
		if (!is_dir($fileDir)) {
			mkdir($fileDir, 0775, true);
		}
		if (array_key_exists($fileName, $output)) {
			$output[$fileName] = $output[$fileName] . '' . $msgstr;
		} else {
			$output[$fileName] = $msgstr;
		}

	}
}

foreach ($output as $key => $value) {
	file_put_contents($key, $value);
}




