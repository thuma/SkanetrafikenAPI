<?php
header('Content-type: application/json; charset=utf-8');

$namelist = 'names.json';
$all = array();
if(is_file($namelist) == FALSE)
	{
	$bokstaver = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','Å','Ä','Ö');
	for($i = 0; $i < count($bokstaver); $i++)
		{
		$newdata = TRUE;
		$page = 0;
		while($newdata)
			{
			$newdata = FALSE;
			$doc = new DOMDocument();
			@$doc->loadHTML(file_get_contents('http://www.reseplaneraren.skanetrafiken.se/indexes.aspx?optType=0&selKommun=0&sLetter='.urlencode(utf8_decode($bokstaver[$i])).'&iPage='.$page.'&Language=se&optFrTo=0&TNSource='));
			$list = $doc->getElementById('add-fetch')->getElementsByTagName('a');
			if($list->length == 0)
			{
				break;
			}
			foreach($list as $node)
				{
				$station['name'] = trim($node->nodeValue);
				$station['cleanname'] = preg_replace('/[ |\t]+/', ' ', $station['name']);
				$all[] = $station;
				print $station['name']."\n";
				$newdata = TRUE;
				}
			$page++;
			}
		}
	file_put_contents($namelist,json_encode($all));
	}

// Load all stations into object.
$all = json_decode(file_get_contents($namelist));

$idlist = 'coord.json';
if(is_file($idlist) == FALSE)
	{
	foreach($all as $key => $name)
		{
		$url = 'http://www.labs.skanetrafiken.se/v2.2/querystation.asp?inpPointfr='.rawurlencode(utf8_decode($name->cleanname));		
		$data = file_get_contents($url);
		$datas = preg_split('/\<Message \/\>/',$data);
		$datas = preg_split('/\<\/GetStartEndPointResult\>/',$datas[1]);
		print $datas[0];
		$data = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?>'."<alla>".trim($datas[0]).'</alla>');
		$rt90 = new stdClass();
		$rt90->x = 
		$rt90->y = 
		$coord = new stdClass();
		$coord->rt90 = $rt90;
		$all[$key]->position = $coord;
		$all[$key]->id = 
		$all[$key]->type = 
		print_r($all[$key]);
		file_put_contents($idlist,json_encode($all));
		}
	}
$all = json_decode(file_get_contents($idlist));
?>
