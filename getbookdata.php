<?php



	$genres = array("barnebker", "biografier", "helse-livsstil-og-fritid", "krim", "skjnnlitteratur");

	$result 	= array();
	$long 		= array();
	$manifest = array();
	$clicktag = array();
	// https://ebok.no/lydboker/genre:barnebker/?sample=1

	define('THUMB_SIZE', 150);
	define('THUMB_SUFFIX', "x" . THUMB_SIZE . ".jpg");

	define('CLICKTAG', "clickTAG");

	define('OFFSET', 2);
	$TagNo = OFFSET;

	$clicktag["clickTAG"] = "http://ebok.no";

	foreach ($genres as $genre) {

		$result[$genre] = array();
		$long[$genre] = array();
		$itemcount = 0;
		print("retrieving genre '$genre'...");
			
		$html = file_get_contents("https://ebok.no/lydboker/genre:$genre/?sample=1");
		print("done!\n");

		$listpos = strpos($html, "<ul class=\"browser__list\">");

		if ($listpos > 0) {
			// print("Found the book list!\n");
			$html = substr($html, $listpos);
		}

		$token 		= " data-backbone='";
		$tokenEnd = "}' >";


		while ( 0 < ($itempos = strpos($html, $token))) {
			$itemcount++;
			$html = substr($html, $itempos + strlen($token));
			$itemend = strpos($html, $tokenEnd);

			$json = html_entity_decode(substr($html, 0, $itemend + 1));
//			print("DATA:\n");
			$data = json_decode($json, true);
			if ($itemcount <= 11 && $data['image_url'] && $data['sample_url']) {

				$clicktag["clickTAG".$TagNo++] = "http://ebok.no" . $data['sample_url'];
				$clicktag["clickTAG".$TagNo++] = "http://ebok.no" . $data['absolute_url'];
				$chunk = array(
					$data['name'], 					$data['simple_authors'],
					$data['image_url'], 		$data['sample_url'], 
					$data['absolute_url'], 	$data['price'],
					$data['slug'], "clickTAG".($TagNo-2), "clickTAG".($TagNo-1)
					);
				$result[$genre][] = $chunk;
			}
			$long[$genre][] = $data;
		}

		file_put_contents($genre.".json", json_encode($result[$genre]));
		print("Found $itemcount books in genre '$genre'\n");

	}

	file_put_contents("data.json", json_encode($result));
	file_put_contents("data.long.json", json_encode($result, JSON_PRETTY_PRINT));
	file_put_contents("clicktags.json", json_encode($clicktag, JSON_PRETTY_PRINT));


//https://do94b8augmkyt.cloudfront.net/thumbnail/1020345_ac1b6b5ba844b2c6feb048ae6c026c3756e5e412_x150.jpg
//https://do94b8augmkyt.cloudfront.net/thumbnail/1011935_4c3e4aa8924f1db1a808d321afc61651e42bd746_x150.jpg
?>