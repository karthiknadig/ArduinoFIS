<?php 
function GetCodeBase()
{
	$code_base = array();
    $code_base['add'] = file_get_contents("codebase/fis_add.c");
    $code_base['array_operation'] = file_get_contents("codebase/fis_array_operation.c");
    $code_base['bisector'] = file_get_contents("codebase/fis_defuzz_bisector.c");
    $code_base['centroid'] = file_get_contents("codebase/fis_defuzz_centroid.c");
    $code_base['lom'] = file_get_contents("codebase/fis_defuzz_largest_of_max.c");
    $code_base['mom'] = file_get_contents("codebase/fis_defuzz_mean_of_max.c");
    $code_base['som'] = file_get_contents("codebase/fis_defuzz_smallest_of_max.c");
    $code_base['dsigmf'] = file_get_contents("codebase/fis_dsigmf.c");
    $code_base['gauss2mf'] = file_get_contents("codebase/fis_gauss2mf.c");
    $code_base['gaussmf'] = file_get_contents("codebase/fis_gaussmf.c");
    $code_base['gbellmf'] = file_get_contents("codebase/fis_gbellmf.c");
    $code_base['header'] = file_get_contents("codebase/fis_header.h");
    $code_base['mamdani'] = file_get_contents("codebase/fis_mamdani.c");
    $code_base['max'] = file_get_contents("codebase/fis_max.c");
    $code_base['MF_out'] = file_get_contents("codebase/fis_MF_out.c");
    $code_base['min'] = file_get_contents("codebase/fis_min.c");
    $code_base['pimf'] = file_get_contents("codebase/fis_pimf.c");
    $code_base['prob_or'] = file_get_contents("codebase/fis_prob_or.c");
    $code_base['prod'] = file_get_contents("codebase/fis_prod.c");
    $code_base['psigmf'] = file_get_contents("codebase/fis_psigmf.c");
    $code_base['sigmf'] = file_get_contents("codebase/fis_sigmf.c");
    $code_base['smf'] = file_get_contents("codebase/fis_smf.c");
    $code_base['sugeno_average'] = file_get_contents("codebase/fis_sugeno_average.c");
    $code_base['sugeno_sum'] = file_get_contents("codebase/fis_sugeno_sum.c");
    $code_base['system'] = file_get_contents("codebase/fis_system.c");
    $code_base['trapmf'] = file_get_contents("codebase/fis_trapmf.c");
    $code_base['trimf'] = file_get_contents("codebase/fis_trimf.c");
	return $code_base;
}

function GetHeaderFileName($fis)
{
	return "fis_header.h"; 
}

function GetMainFileName($fis)
{
	return $fis['System']['FriendlyName'].".ino";
}
function GetIndent($indent, $str)
{
	$spaces = "";
	for($i=0;$i<$indent;$i++)
	{
		$spaces .= "    ";
	}
	
	return $spaces.$str."\n";
}
function SetPrefix($content)
{
	return str_replace("/*FIS_Prefix*/", "fis", $content);
}
function SetPinModeForInputs($fis, $content)
{
	$inputs = "";
	$size = intval($fis['System']['NumInputs']);
	for($i=0;$i<$size;$i++)
	{
		$name = trim($fis['Input'][$i]['Name']);
		$inputs .= GetIndent(1, "// Pin mode for Input: $name");
		$inputs .= GetIndent(1, "pinMode($i , INPUT);");
	}
	
	return str_replace("/*PinModeSetup_Input*/", $inputs, $content);
}

function SetPinModeForOutputs($fis, $content)
{
	$outputs = "";
	$size = intval($fis['System']['NumOutputs']);
	$pinOffset = intval($fis['System']['NumInputs']);
	for($i=0;$i<$size;$i++)
	{
		$name = trim($fis['Output'][$i]['Name']);
		$pin = $pinOffset + $i;
		$outputs .= GetIndent(1, "// Pin mode for Output: $name");
		$outputs .= GetIndent(1, "pinMode($pin , OUTPUT);");
	}
	
	return str_replace("/*PinModeSetup_Output*/", $outputs, $content);
}

function SetAnalogInputRead($fis, $content)
{
	$inputs = "";
	$size = intval($fis['System']['NumInputs']);
	for($i=0;$i<$size;$i++)
	{
		$name = trim($fis['Input'][$i]['Name']);
		$inputs .= GetIndent(1, "// Read Input: $name");
		$inputs .= GetIndent(1, "g_fisInput[$i] = analogRead($i);");
	}
	
	return str_replace("/*AnalogInput_Read*/", $inputs, $content);
}
function SetAnalogOutputReset($fis, $content)
{
	$outputs = "";
	$size = intval($fis['System']['NumOutputs']);
	for($i=0;$i<$size;$i++)
	{
		$outputs .= GetIndent(1, "g_fisOutput[$i] = 0;");
	}

	return str_replace("/*AnalogOutput_Reset*/", $outputs, $content);
}
function SetAnalogOutputWrite($fis, $content)
{
	$outputs = "";
	$size = intval($fis['System']['NumOutputs']);
	$pinOffset = intval($fis['System']['NumInputs']);
    for($i=0;$i<$size;$i++)
	{
		$name = trim($fis['Output'][$i]['Name']);
		$pin = $pinOffset + $i;
		
		$outputs .= GetIndent(1, "// Set output vlaue: $name");
		$outputs .= GetIndent(1, "analogWrite($pin , g_fisOutput[$i]);");
	}

	return str_replace("/*AnalogOutput_Write*/", $outputs, $content);
}

function GetMFDependencies($mfs)
{
	$size = count($mfs);
	$arr = array();
	for($i=0;$i<$size;$i++)
	{
		switch($mfs[$i])
		{
			case 'dsigmf':
				$arr['dsigmf'] = true;
				$arr['sigmf'] = true;
			break;
			case 'gauss2mf':
				$arr['gauss2mf'] = true;
				$arr['gaussmf'] = true;
			break;
			case 'gaussmf':
				$arr['gaussmf'] = true;
			break;
			case 'gbellmf':
				$arr['gbellmf'] = true;
			break;
			case 'pimf':
				$arr['pimf'] = true;
				$arr['smf'] = true;
				$arr['zmf'] = true;
			break;
			case 'psigmf':
				$arr['psigmf'] = true;
				$arr['sigmf'] = true;
			break;
			case 'sigmf':
				$arr['sigmf'] = true;
			break;
			case 'smf':
				$arr['smf'] = true;
			break;
			case 'trapmf':
				$arr['trapmf'] = true;
			break;
			case 'trimf':
				$arr['trimf'] = true;
			break;
			case 'zmf':
				$arr['zmf'] = true;
			break;
		}
	}
	
	// This index is used to refer to implementation array
	// in the code
	$keys = array_keys($arr);
	$size = count($keys);
	
	for($i=0;$i<$size;$i++)
	{
		$arr[$keys[$i]]=$i;
	}
	return $arr;
}

function GetMFs($fis)
{
	$arr = array();
	$sizeI = intval($fis['System']['NumInputs']);
	$sizeO = intval($fis['System']['NumOutputs']);
	for($i=0;$i<$sizeI;$i++)
	{
		$size = intval($fis["Input"][$i]["NumMFs"]);
		for($j=1;$j<=$size;$j++)
		{
			$key = trim($fis["Input"][$i]["MF$j"]['MF']);
			$arr[$key] = true;
		}
	}
	for($i=0;$i<$sizeO;$i++)
	{
		$size = intval($fis["Output"][$i]["NumMFs"]);
		for($j=1;$j<=$size;$j++)
		{
			$key = trim($fis["Output"][$i]["MF$j"]['MF']);
			$arr[$key] = true;
		}
	}
	
	return GetMFDependencies(array_keys($arr));
}

function SetFISVersion($content)
{
	// Format Major.Minor.Update.Date(ddmmyyyy)
	return str_replace("/*FIS_Version*/", "2.0.0.29032014", $content);
}
function  SetFISHeaderFile($fis, $content)
{
	return str_replace("/*FIS_HeaderFile*/", GetHeaderFileName($fis), $content);
}

function  SetFISInputCount($fis, $content)
{
	return str_replace("/*FIS_InputCount*/", trim($fis['System']['NumInputs']), $content);
}

function  SetFISOutputCount($fis, $content)
{
	return str_replace("/*FIS_OutputCount*/", trim($fis['System']['NumOutputs']), $content);
}
function  SetFISRulesCount($fis, $content)
{
	return str_replace("/*FIS_RulesCount*/", trim($fis['System']['NumRules']), $content);
}
function SetFISSupportFunctions($fis, $mfs, $code_base, $content)
{
	$keys = array_keys($mfs);
	$size = count($mfs);
	
	$support = "";
	for($i=0;$i<$size;$i++)
	{
		$key = strtolower($keys[$i]);
		if(array_key_exists($key, $code_base))
		{
			$support .= $code_base[$key]."\n\n";
		}
	}
	
	$required = array();
	$required[trim($fis['System']['AndMethod'])] = true;
	$required[trim($fis['System']['OrMethod'])] = true;
	$required[trim($fis['System']['ImpMethod'])] = true;
	$required[trim($fis['System']['AggMethod'])] = true;
	
	$keys = array_keys($required);
	$size = count($required);
	for($i=0;$i<$size;$i++)
	{
		$key = strtolower($keys[$i]);
		if(array_key_exists($key, $code_base))
		{
			$support .= $code_base[$key]."\n\n";
		}
	}
	
	$support .= $code_base['array_operation']."\n";
	
	return str_replace("/*FIS_SupportFunctions*/", $support, $content);
}

function SetFISMFImplementations($fis, $mfs, $code_base, $content)
{
	$keys = array_keys($mfs);
	$size = count($mfs);
	$mflist = "    ";
	for($i=0;$i<$size;$i++)
	{
		$key = strtolower($keys[$i]);
		if(array_key_exists($key, $code_base))
		{
			if($i!= ($size-1))
			{
				$mflist .= "fis_$key, ";
			}
			else 
			{
				$mflist .= "fis_$key";
			}
		}
	}
	
	return str_replace("/*FIS_MFImplementations*/", $mflist, $content);
}

function SetFISIMFCounts($fis, $content)
{
	$size = intval($fis['System']['NumInputs']);
	$mfclist = " ";
	for($i=0;$i<$size;$i++)
	{
		$mfc = intval($fis["Input"][$i]["NumMFs"]);
		if($i!= ($size-1))
		{
			$mfclist .= "$mfc, ";
		}
		else
		{
			$mfclist .= "$mfc ";
		}
	}
	return str_replace("/*FIS_IMFCounts*/", $mfclist, $content);
}

function SetFISOMFCounts($fis, $content)
{
	$size = intval($fis['System']['NumOutputs']);
	$mfclist = " ";
	for($i=0;$i<$size;$i++)
	{
		$mfc = intval($fis["Output"][$i]["NumMFs"]);
		if($i!= ($size-1))
		{
			$mfclist .= "$mfc, ";
		}
		else
		{
			$mfclist .= "$mfc ";
		}
	}
	return str_replace("/*FIS_OMFCounts*/", $mfclist, $content);
}

function SetFISMFInputsCoeffs($fis, $content)
{
	$size = intval($fis['System']['NumInputs']);
	$mfcoeff = "";
	$mfcoefflist = " ";
	for($i=0;$i<$size;$i++)
	{
		$mfc = intval($fis["Input"][$i]["NumMFs"]);
		$mfcoeffsublist = "";
		for($j=1;$j<=$mfc;$j++)
		{
			$coeff = trim($fis["Input"][$i]["MF$j"]['MFCoeff']);
			$mfcoeff .= "FIS_TYPE fis_gMFI$i"."Coeff$j"."[] = { $coeff };\n";
			if($j != $mfc)
			{
				$mfcoeffsublist .= "fis_gMFI$i"."Coeff$j".", ";
			}
			else
			{
				$mfcoeffsublist .= "fis_gMFI$i"."Coeff$j";
			}
		}

		if($i != ($size-1))
		{
			$mfcoeff .= "FIS_TYPE* fis_gMFI$i"."Coeff[] = { $mfcoeffsublist };\n";
			$mfcoefflist .= "fis_gMFI$i"."Coeff, ";
		}
		else
		{
			$mfcoeff .= "FIS_TYPE* fis_gMFI$i"."Coeff[] = { $mfcoeffsublist };";
			$mfcoefflist .= "fis_gMFI$i"."Coeff ";
		}
	}
	$content = str_replace("/*FIS_MFInputsCoeffs*/", $mfcoeff, $content);
	return str_replace("/*FIS_MFInputsCoeffsList*/", $mfcoefflist, $content);
}

function SetFISMFOutputsCoeffs($fis, $content)
{
	$size = intval($fis['System']['NumOutputs']);
	$mfcoeff = "";
	$mfcoefflist = " ";
	for($i=0;$i<$size;$i++)
	{
		$mfc = intval($fis["Output"][$i]["NumMFs"]);
		$mfcoeffsublist = "";
		for($j=1;$j<=$mfc;$j++)
		{
			$coeff = trim($fis["Output"][$i]["MF$j"]['MFCoeff']);
			$mfcoeff .= "FIS_TYPE fis_gMFO$i"."Coeff$j"."[] = { $coeff };\n";
			if($j != $mfc)
			{
				$mfcoeffsublist .= "fis_gMFO$i"."Coeff$j".", ";
			}
			else
			{
				$mfcoeffsublist .= "fis_gMFO$i"."Coeff$j";
			}
		}

		if($i != ($size-1))
		{
			$mfcoeff .= "FIS_TYPE* fis_gMFO$i"."Coeff[] = { $mfcoeffsublist };\n";
			$mfcoefflist .= "fis_gMFO$i"."Coeff, ";
		}
		else
		{
			$mfcoeff .= "FIS_TYPE* fis_gMFO$i"."Coeff[] = { $mfcoeffsublist };";
			$mfcoefflist .= "fis_gMFO$i"."Coeff ";
		}
	}
	$content = str_replace("/*FIS_MFOutputsCoeffs*/", $mfcoeff, $content);
	return str_replace("/*FIS_MFOutputsCoeffsList*/", $mfcoefflist, $content);
}

function SetFISInputMFs($fis, $mfs, $content)
{
	$size = intval($fis['System']['NumInputs']);
	$mfvals = "";
	$mflist = " ";
	for($i=0;$i<$size;$i++)
	{
		$mfc = intval($fis["Input"][$i]["NumMFs"]);
		$mfsublist = "";
		for($j=1;$j<=$mfc;$j++)
		{
			$mfname = trim($fis["Input"][$i]["MF$j"]['MF']);
			$mfindex = $mfs[$mfname];
			if($j != $mfc)
			{
				$mfsublist .= "$mfindex, ";
			}
			else
			{
				$mfsublist .= "$mfindex";
			}
		}

		if($i != ($size-1))
		{
			$mfvals .= "int fis_gMFI$i"."[] = { $mfsublist };\n";
			$mflist .= "fis_gMFI$i, ";
		}
		else
		{
			$mfvals .= "int fis_gMFI$i"."[] = { $mfsublist };";
			$mflist .= "fis_gMFI$i";
		}
	}
	$content = str_replace("/*FIS_InputMFs*/", $mfvals, $content);
	return str_replace("/*FIS_InputMFsList*/", $mflist, $content);
}

function SetFISOutputMFs($fis, $mfs, $content)
{
	$size = intval($fis['System']['NumOutputs']);
	$mfvals = "";
	$mflist = " ";
	for($i=0;$i<$size;$i++)
	{
		$mfc = intval($fis["Output"][$i]["NumMFs"]);
		$mfsublist = "";
		for($j=1;$j<=$mfc;$j++)
		{
			$mfname = trim($fis["Output"][$i]["MF$j"]['MF']);
			$mfindex = $mfs[$mfname];
			if($j != $mfc)
			{
				$mfsublist .= "$mfindex, ";
			}
			else
			{
				$mfsublist .= "$mfindex";
			}
		}

		if($i != ($size-1))
		{
			$mfvals .= "int fis_gMFO$i"."[] = { $mfsublist };\n";
			$mflist .= "fis_gMFO$i, ";
		}
		else
		{
			$mfvals .= "int fis_gMFO$i"."[] = { $mfsublist };";
			$mflist .= "fis_gMFO$i";
		}
	}
	$content = str_replace("/*FIS_OutputMFs*/", $mfvals, $content);
	return str_replace("/*FIS_OutputMFsList*/", $mflist, $content);
}

function SetFISRuleWeightAndType($fis, $content)
{
	$size = intval($fis['System']['NumRules']);
	$weights = " ";
	$type = " ";
	for($i=0;$i<$size;$i++)
	{
		$wt = $fis['Rules'][$i]['Weight'];
		$t = $fis['Rules'][$i]['Type'];
		if($i != ($size-1))
		{
			$weights .= "$wt, ";
			$type .= "$t, ";
		}
		else 
		{
			$weights .= "$wt ";
			$type .= "$t ";
		}
	}
	$content = str_replace("/*FIS_RuleWeightsList*/", $weights, $content);
	return str_replace("/*FIS_RuleTypeList*/", $type, $content);
}

function SetFISRuleIOs($fis, $content)
{
	$size = intval($fis['System']['NumRules']);
	$Iruleitem = "";
	$Irulelist = " ";
	$Oruleitem = "";
	$Orulelist = " ";
	for($i=0;$i<$size;$i++)
	{
		$Iindexes = $fis['Rules'][$i]['Inputs'];
		$Oindexes = $fis['Rules'][$i]['Outputs'];
		if($i != ($size-1))
		{
			$Iruleitem .= "int fis_gRI$i"."[] = { $Iindexes };\n";
			$Irulelist .= "fis_gRI$i, ";
			$Oruleitem .= "int fis_gRO$i"."[] = { $Oindexes };\n";
			$Orulelist .= "fis_gRO$i, ";
		}
		else
		{
			$Iruleitem .= "int fis_gRI$i"."[] = { $Iindexes };";
			$Irulelist .= "fis_gRI$i ";
			$Oruleitem .= "int fis_gRO$i"."[] = { $Oindexes };";
			$Orulelist .= "fis_gRO$i ";
		}
	}
	
	$content = str_replace("/*FIS_RuleInputs*/", $Iruleitem, $content);
	$content = str_replace("/*FIS_RuleInputList*/", $Irulelist, $content);
	$content = str_replace("/*FIS_RuleOutputs*/", $Oruleitem, $content);
	return str_replace("/*FIS_RuleOutputList*/", $Orulelist, $content);
}

function SetFISIOMinMax($fis, $content)
{
	$size = intval($fis['System']['NumInputs']);
	$Iminlist = " ";
	$Imaxlist = " ";
	$Ominlist = " ";
	$Omaxlist = " ";
	
	for($i=0;$i<$size;$i++)
	{
		$min = $fis["Input"][$i]['Range']['Min'];
		$max = $fis["Input"][$i]['Range']['Max'];
		if ($i != ($size - 1))
		{
			$Iminlist .= "$min, ";
			$Imaxlist .= "$max, ";
		}
		else 
		{
			$Iminlist .= "$min ";
			$Imaxlist .= "$max ";
		}
	}
	
	$size = intval($fis['System']['NumOutputs']);
	for($i=0;$i<$size;$i++)
	{
		$min = $fis["Output"][$i]['Range']['Min'];
		$max = $fis["Output"][$i]['Range']['Max'];
		if ($i != ($size - 1))
		{
			$Ominlist .= "$min, ";
			$Omaxlist .= "$max, ";
		}
		else
		{
			$Ominlist .= "$min ";
			$Omaxlist .= "$max ";
		}
	}
	
	$content = str_replace("/*FIS_InputMinList*/", $Iminlist, $content);
	$content = str_replace("/*FIS_InputMaxList*/", $Imaxlist, $content);
	$content = str_replace("/*FIS_OutputMinList*/", $Ominlist, $content);
	return str_replace("/*FIS_OutputMaxList*/", $Omaxlist, $content);
}

function SetFISDataDependentSupportFunctions($fis, $mfs, $code_base, $content)
{
	$support = "";
	$defuzz = strtolower(trim($fis['System']['DefuzzMethod']));

	if ((strcasecmp($defuzz, "wtaver")==0) || (strcasecmp($defuzz, "wtsum")==0))
	{
		$support = "// None for Sugeno";
		return str_replace("/*FIS_DataDependentSupportFunctions*/", $support, $content);
	}
	
	$support .= $code_base['MF_out']."\n\n";
	$support .= $code_base[$defuzz];
	return str_replace("/*FIS_DataDependentSupportFunctions*/", $support, $content);
}

function SetFISFuzzyInput($fis, $content)
{
	$size = intval($fis['System']['NumInputs']);
	$fuzzy = "";
	$fuzzylist = " ";
	for($i=0;$i<$size;$i++)
	{
		$mfc = intval($fis["Input"][$i]["NumMFs"]);
		$value = "";
		for($j=0;$j<$mfc;$j++)
		{
			if($j != ($mfc - 1))
			{
				$value .= "0, ";
			}
			else 
			{
				$value .= "0";
			}
		}
		if ($i != ($size - 1))
		{
			$fuzzy .= "    FIS_TYPE fuzzyInput$i"."[] = { $value };\n";
			$fuzzylist .= "fuzzyInput$i, ";
		}
		else
		{
			$fuzzy .= "    FIS_TYPE fuzzyInput$i"."[] = { $value };";
			$fuzzylist .= "fuzzyInput$i, ";
		}
	}
	
	$content = str_replace("/*FIS_FuzzyInputs*/", $fuzzy, $content);
	return str_replace("/*FIS_FuzzyInputsList*/", $fuzzylist, $content);
}

function SetFISFuzzyOutput($fis, $content)
{
	$size = intval($fis['System']['NumOutputs']);
	$fuzzy = "";
	$fuzzylist = " ";
	for($i=0;$i<$size;$i++)
	{
		$mfc = intval($fis["Output"][$i]["NumMFs"]);
		$value = "";
		for($j=0;$j<$mfc;$j++)
		{
			if($j != ($mfc - 1))
			{
				$value .= "0, ";
			}
			else
			{
				$value .= "0";
			}
		}
		if ($i != ($size - 1))
		{
			$fuzzy .= "    FIS_TYPE fuzzyOutput$i"."[] = { $value };\n";
			$fuzzylist .= "fuzzyOutput$i, ";
		}
		else
		{
			$fuzzy .= "    FIS_TYPE fuzzyOutput$i"."[] = { $value };";
			$fuzzylist .= "fuzzyOutput$i, ";
		}
	}

	$content = str_replace("/*FIS_FuzzyOutputs*/", $fuzzy, $content);
	return str_replace("/*FIS_FuzzyOutputsList*/", $fuzzylist, $content);
}

function SetFISSystem($fis, $code_base, $content)
{
	$system = strtolower(trim($fis['System']['Type']));
	$defuzz = strtolower(trim($fis['System']['DefuzzMethod']));
	
	if (strcasecmp($system, "mamdani") == 0)
	{
		$content = str_replace("/*FIS_SystemImpl*/", $code_base['mamdani'], $content);
	}
	else if(strcasecmp($defuzz, "wtaver")==0)
	{
		$content = str_replace("/*FIS_SystemImpl*/", $code_base['sugeno_average'], $content);
	}
	else if(strcasecmp($defuzz, "wtsum")==0)
	{
		$content = str_replace("/*FIS_SystemImpl*/", $code_base['sugeno_sum'], $content);
	}
	
	$and = "fis_".strtolower(trim($fis['System']['AndMethod']));
	$or = "fis_".strtolower(trim($fis['System']['OrMethod']));
	$imp = "fis_".strtolower(trim($fis['System']['ImpMethod']));
	$agg = "fis_".strtolower(trim($fis['System']['AggMethod']));
	
	$content = str_replace("/*FIS_DEFUZZ*/", "fis_defuzz_$defuzz", $content);
	$content = str_replace("/*FIS_ANDOperation*/", $and, $content);
	$content = str_replace("/*FIS_OROperation*/", $or , $content);
	$content = str_replace("/*FIS_Imp*/", $imp, $content);
	return str_replace("/*FIS_Aggregator*/", $agg, $content);
}
	
function GetHeaderFileContent($fis)
{
	$headerPath = "codebase/fis_header.h";
	$content = file_get_contents($headerPath);
	$content = SetFISVersion($content);
	return $content;
}

function GetMainFileContent($fis)
{
	$code_base = GetCodeBase();
	$mfs = GetMFs($fis);
	
	$content = $code_base['system'];
	$content = SetPinModeForInputs($fis, $content);
	$content = SetPinModeForOutputs($fis, $content);
	$content = SetAnalogInputRead($fis, $content);
	$content = SetAnalogOutputReset($fis, $content);
	$content = SetAnalogOutputWrite($fis, $content);
	
	$content = SetFISVersion($content);
	$content = SetFISHeaderFile($fis, $content);
	$content = SetFISInputCount($fis, $content);
	$content = SetFISOutputCount($fis, $content);
	$content = SetFISRulesCount($fis, $content);
	$content = SetFISSupportFunctions($fis, $mfs, $code_base, $content);
	$content = SetFISMFImplementations($fis, $mfs, $code_base, $content);
	$content = SetFISIMFCounts($fis, $content);
	$content = SetFISOMFCounts($fis, $content);
	$content = SetFISMFInputsCoeffs($fis, $content);
	$content = SetFISMFOutputsCoeffs($fis, $content);
	$content = SetFISInputMFs($fis, $mfs, $content);
	$content = SetFISOutputMFs($fis, $mfs, $content);
	$content = SetFISRuleWeightAndType($fis, $content);
	$content = SetFISRuleIOs($fis, $content);
	$content = SetFISIOMinMax($fis, $content);
	$content = SetFISDataDependentSupportFunctions($fis, $mfs, $code_base, $content);
	$content = SetFISFuzzyInput($fis, $content);
	$content = SetFISFuzzyOutput($fis, $content);
	$content = SetFISSystem($fis, $code_base, $content);
	
	return $content;
}
?>