<?php
		
		
		$count = count($_GET);


		$mysqli = new mysqli('localhost', 'root', '', 'tweets'); 

		if ($mysqli->connect_errno) {
	    	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}	

		for($i=1; $i <= $count; $i++){
			$query = $_GET['term' . $i . ''];
			if (empty($query)) {
			    // no data passed by get
			} else {

				$results = $mysqli->query("SELECT * FROM guitar where text like '%{$query}%' ORDER BY 'id' DESC ");
				$numrows = mysqli_num_rows($results);

				$data['term' . $i] = array(
					'count' => $numrows,
					'term' => $query
				);		
			}
	
		}

		echo json_encode($data);

		$mysqli->close();

?>