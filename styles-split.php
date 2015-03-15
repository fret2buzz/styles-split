<?php
$workingPath = 'd:/temp';
$cssFiles = $workingPath . '/css';
$cssLocales = $workingPath . '/css/';
$cssFixedFiles = $workingPath . '/css/';
$pattern = "#\.ie8|ie9#msiu";
$component = 'ie';
$files = glob($cssFiles . '/*.css');
foreach ($files as $k => $v) {
	$outputCss = '';
	$outputCssFixed = '';
	$cssCodeMedia = '';
	$cssCode = '';
	$media = array();
	$normal = array();
	$basename = basename($v);
	$cssCode = file_get_contents($cssFiles . '/' . $basename);
	$cssCode = preg_replace("#\/\*[\S\s]*?\*\/#", '', $cssCode);
	$cssCode = preg_replace("#\}\s+\}#msiu", '}}', $cssCode);
	$cssCode = preg_replace("#\)\s+\{#msiu", '){', $cssCode);
	$cssCode = str_replace("\n", '', $cssCode);
	$cssCode = str_replace("@media", 'this1-is-media1-query', $cssCode);
	$cssCode = explode("this1-is-", $cssCode);

	//Separate @media styles and styles that are not related to @media
	foreach ($cssCode as $val) {
		$val = trim($val);
		if (empty($val))
			continue;
		if (strpos($val, 'media1-query') !== false) {
			$nofx = explode("}}", $val);
			foreach ($nofx as $v) {
				$v = trim($v);
				if (empty($v))
					continue;
				if (strpos($v, 'media1-query') !== false) {
					array_push($media, $v . '}}' . "\n");
				} else {
					array_push($normal, $v);
				}
			}
		} else {
			array_push($normal, $val);
		}
	}
	$cssCodeMedia = implode('', $media);
	$cssCode = implode('', $normal);

	//Process the styles that are not related to @media
	$cssCode = explode("}", $cssCode);
	foreach ($cssCode as $line) {
		$line = trim($line);
		if (empty($line))
			continue;
		$splitted = trim($line);
		$splitted = explode('{', $splitted);
		if (preg_match($pattern, $splitted[0])) {
			if (strpos($splitted[0], ',') !== false) {
				$classes = trim($splitted[0]);
				$classes = explode(',', $classes);
				foreach ($classes as $class) {
					$class = trim($class);
					if (empty($class))
						continue;
					if (!preg_match($pattern, $class))
						continue;
					$outputCss .= $class . ' {' . $splitted[1] . '}' . "\n";
				}
			} else {
				$outputCss .= $splitted[0] . ' {' . $splitted[1] . '}' . "\n";
			}
		}
		if (preg_match($pattern, $splitted[0])) {
			if (strpos($splitted[0], ',') !== false) {
				$nClasses = trim($splitted[0]);
				$nClasses = explode(',', $nClasses);
				$classAr = array();
				foreach ($nClasses as $nClass) {
					$nClass = trim($nClass);
					if (empty($nClass))
						continue;
					if (!preg_match($pattern, $nClass)) {
						array_push($classAr, $nClass);
					}
				}
				$classJn = implode(",", $classAr);
				if (!empty($classJn)) {
					$outputCssFixed .= $classJn . ' {' . $splitted[1] . '}' . "\n";
				}
				;
			}
		} else {
			$outputCssFixed .= $splitted[0] . ' {' . $splitted[1] . '}' . "\n";
		}
	}

	//Process styles that are related @media
	$newcode = explode("}}", $cssCodeMedia);
	foreach ($newcode as $medias) {
		$medias = trim($medias);
		if (empty($medias))
			continue;
		$medias = $medias . '}';
		$medias = explode("{", $medias, 2);
		$newcode2 = explode("}", $medias[1]);
		$outputCss2 = '';
		$outputCssFixed2 = '';
		foreach ($newcode2 as $line2) {
			$splittedCode = trim($line2);
			if (empty($splittedCode))
				continue;
			$splittedCode = explode('{', $splittedCode);
			if (preg_match($pattern, $splittedCode[0])) {
				if (strpos($splittedCode[0], ',') !== false) {
					$classes = trim($splittedCode[0]);
					$classes = explode(',', $classes);
					foreach ($classes as $class) {
						$class = trim($class);
						if (empty($class))
							continue;
						if (!preg_match($pattern, $class))
							continue;
						$outputCss2 .= $class . ' {' . $splittedCode[1] . '}' . "\n";
					}
				} else {
					$outputCss2 .= $splittedCode[0] . ' {' . $splittedCode[1] . '}' . "\n";
				}
			}
			if (preg_match($pattern, $splittedCode[0])) {
				if (strpos($splittedCode[0], ',') !== false) {
					$nClasses = trim($splittedCode[0]);
					$nClasses = explode(',', $nClasses);
					$classAr2 = array();
					foreach ($nClasses as $nClass) {
						$nClass = trim($nClass);
						if (empty($nClass))
							continue;
						if (!preg_match($pattern, $nClass)) {
							array_push($classAr2, $nClass);
						}
					}
					$classJn2 = implode(",", $classAr2);
					if (!empty($classJn2)) {
						$outputCssFixed2 .= $classJn2 . ' {' . $splittedCode[1] . '}' . "\n";
					}
					;
				}
			} else {
				$outputCssFixed2 .= $splittedCode[0] . ' {' . $splittedCode[1] . '}' . "\n";
			}
		}
		$medias[0] = str_replace('media1-query', '@media', $medias[0]);
		if (!empty($outputCss2)) {
			$outputCss .= $medias[0] . '{' . "\n" . $outputCss2 . "\n" . '}' . "\n";
		}
		if (!empty($outputCssFixed2)) {
			$outputCssFixed .= $medias[0] . '{' . "\n" . $outputCssFixed2 . "\n" . '}' . "\n";
		}
	}
	$basename = explode('.', $basename);

	//Write to files the results
	if (!empty($outputCss)) {
		file_put_contents($cssLocales . '/' . $basename[0] . '-' . $component . '.css', $outputCss);
	}
	if (!empty($outputCssFixed)) {
		file_put_contents($cssFixedFiles . '/' . $basename[0] . '-rest.css', $outputCssFixed);
	}
}
