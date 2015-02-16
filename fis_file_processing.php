<?php
function GetFISIOParts($content)
{
	$parts = array();
	$contentSize = count($content);

	$system = 0;
	$rule = 0;
	$output = 0;
	$input = 0;
	for($i=0;$i<$contentSize;$i++)
	{
		if(strpos($content[$i], "[System]") === 0) {$system++;}
		elseif(strpos($content[$i], "[Rules]") === 0) {$rule++;}
		elseif(strpos($content[$i], "[Output") === 0) {$output++;}
		elseif(strpos($content[$i], "[Input") === 0) {$input++;}
	}
	
	$parts['System'] = $system;
	$parts['Rules'] = $rule;
	$parts['Output'] = $output;
	$parts['Input'] = $input;

	return $parts;
}
function GetFISPart($content, $part)
{
	$pset = array();
	$size = count($content);
	
	$i=0;
	$j=0;
	while($i<$size) 
	{
		if(strpos($content[$i++], $part) === 0)
			break;
	}
	
	for(;$i<$size;$i++)
	{
		if($content[$i] == "\n")
			break;
		if(strchr($content[$i], '=') !== false)
		{
			$parts = explode("=", $content[$i]);
			$parts[1] = str_replace("'", "", $parts[1]);
			$value = $parts[1];
			if(strrchr($parts[1],':') !== false)
			{
				$mfParts = explode(":", $parts[1]);
				$value = array();
				$value['MFName'] = $mfParts[0];
				$mfParts = explode(",", $mfParts[1]);
				$value['MF'] = $mfParts[0];
				$mfParts[1] = str_replace("[", "", $mfParts[1]);
				$mfParts[1] = str_replace("]", "", $mfParts[1]);
				$value['MFCoeff'] = str_replace(" ", ", ", $mfParts[1]);
				//echo implode(" ", $value);
				$pset[$parts[0]] = $value;
			}
			else if(strrchr($parts[1],'[') !== false)
			{
				$range = str_replace("[", "", $parts[1]);
				$range = str_replace("]", "", $range);
				$range = explode(" ", $range);
				
				$value = array();
				$value['Min'] = trim($range[0]);
				$value['Max'] = trim($range[1]);
				//echo implode(" ", $value);
				$pset[$parts[0]] = $value;
			}
			else
			{
				$pset[$parts[0]] = $value;
				//echo $parts[0]."=".$value;
			}
		}
		else
		{
			$value = array();
			$parts = explode(":", $content[$i]);
			$value['Type'] = trim($parts[1]);
			$parts = explode("(", $parts[0]);
			$value['Weight'] = trim(str_replace(")","", $parts[1]));
			$parts = explode(",", $parts[0]);
			$value['Inputs'] = trim(str_replace(" ", ", ", $parts[0]));
			$value['Outputs'] = trim(str_replace(" ", ", ", trim($parts[1])));
			$pset[$j++] = $value;
		}
	}	
	return $pset;
}

function GetFrindlyName($name)
{
	$frendly = "fis".preg_replace('/[^A-Za-z0-9_\-]/', '_', mb_convert_encoding($name, "ASCII"));
	return substr($frendly, 0, 12);
}

function GetFISData($content)
{
	$fis = array();
	$fisParts = GetFISIOParts($content);
	$fis['System'] = GetFISPart($content, "[System]"); 
	$fis['Rules'] = GetFISPart($content, "[Rules]");
	$fis['Output'] = array();
	for($i=1;$i<=$fisParts['Output'];$i++)
		$fis['Output'][$i-1] = GetFISPart($content, "[Output$i]");
	$fis['Input'] = array();
	for($i=1;$i<=$fisParts['Input'];$i++)
		$fis['Input'][$i-1] = GetFISPart($content, "[Input$i]");
	
	$fis['System']['FriendlyName'] = GetFrindlyName(trim($fis['System']['Name']));
	return $fis;
}
?>