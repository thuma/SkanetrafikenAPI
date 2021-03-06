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
$newlist = array();
$idlist = 'coord.json';
if(is_file($idlist) == FALSE)
	{
	foreach($all as $key => $name)
		{
		$url = 'http://www.labs.skanetrafiken.se/v2.2/querystation.asp?inpPointfr='.rawurlencode(utf8_decode($name->cleanname));		
		$data = file_get_contents($url);
		$datas = preg_split('/\<Message \/\>/',$data);
		$datas = preg_split('/\<\/GetStartEndPointResult\>/',$datas[1]);
		$data = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?>'."<alla>".trim($datas[0]).'</alla>');
		$data = json_decode(json_encode($data));
		$lista = $data->StartPoints->Point;
		if(is_array($lista)==FALSE){
			$temp = $lista; 
			$lista = array();			
			$lista[0] = $temp;
		};
		$max = count($lista);
		for($i = 0;$i < $max; $i++)
			{
			$point = $lista[$i];		
			$newlist[$point->Id] = new stdClass;
			$newlist[$point->Id]->name = $point->Name;
			$newlist[$point->Id]->id = $point->Id;
			$rt90 = new stdClass();
			$rt90->x = $point->X;
			$rt90->y = $point->Y;
			$coord = new stdClass();
			$coord->rt90 = $rt90;
			$newlist[$point->Id]->position = $coord;
			$newlist[$point->Id]->type = $point->Type;	
			print_r($newlist[$point->Id]);	
			}
		file_put_contents($idlist,json_encode($newlist));
		}
	}

?>
