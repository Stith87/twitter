<?php

 	 function getTweets(){

		$mysqli = new mysqli('localhost', 'root', '', 'tweets'); 

		if ($mysqli->connect_errno) {
	    	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}

		$results = $mysqli->query("SELECT * FROM guitar where text like '%playing guitar%' ORDER BY 'id' DESC limit 10000 ");

		$numrows = mysqli_num_rows($results);

		$tweetObjects = array();

		while ($row = mysqli_fetch_array($results)){
			$row_data = array(
				'screen_name' => $row['screen_name'],
				'text' => $row['text']

				);

			array_push($tweetObjects, $row_data);
			
		}

		$mysqli->close();
	}

	getTweets();
?>
