<!DOCTYPE html>
<head>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="js/chartjs/Chart.min.js" type="text/javascript"></script>
<script>
	$(document).ready(function(){
		$('#search-terms').submit(function(event){
			
	    	event.preventDefault();

	    	var form = $(this);

	    	var inputs = form.find("input, select, button, textarea");

	    	var serializedData = form.serialize();

	    	console.log(serializedData);

		    countTweets(serializedData);
		})
	});


</script>
<script>
	function getTweets(){
		jQuery.ajax({
			type: "GET",
			url: "http://127.0.0.1:8888/mstith/dev/twitter/includes/tweets.php",
			success: function(data){
	  			$('#results').append(data);	
			}	
		})
	}

	function countTweets(terms){


		$.ajax({
			type: "GET",
			url: "http://127.0.0.1:8888/mstith/dev/twitter/includes/tweet-count.php",
			data: terms,
			dataType: "json",
			success: function(data){ 			
		  		
		  		handleTweets(data);

			}	
		})
	}

	function handleTweets(data){
		
		terms = [];
		termsCount = [];

		for (var prop in data) {
			if( data.hasOwnProperty( prop ) ) {
			    console.log(data[prop].term + " = " +  data[prop].count);
			    terms.push(data[prop].term);
			    termsCount.push(data[prop].count);
			} 
		}
				var data = {
				    labels: terms,
				    datasets: [	     
				        {
				            label: "My Second dataset",
				            fillColor: "rgba(151,187,205,0.2)",
				            strokeColor: "rgba(151,187,205,1)",
				            pointColor: "rgba(151,187,205,1)",
				            pointStrokeColor: "#000",
				            pointHighlightFill: "#000",
				            pointHighlightStroke: "rgba(151,187,205,1)",
				            data: termsCount
				        }	
			   		]
				};

				var ctx = document.getElementById("myChart").getContext("2d");				
				var myRadarChart = new Chart(ctx).Radar(data);
	}


</script>
<body>
<div class="chartResults">
	<canvas id="myChart" width="400" height="400"></canvas>
</div>

<form id="search-terms">
	<div>
		<label for="term">Search Term</label><input type="text" name="term1" id="term1" />
	</div>
	<div>
		<label for="term">Search Term</label><input type="text" name="term2" id="term2" />
	</div>
	<div>
		<label for="term">Search Term</label><input type="text" name="term3" id="term3" />
	</div>
	<div>
		<label for="term">Search Term</label><input type="text" name="term4" id="term4" />
	</div>
	<div>
		<label for="term">Search Term</label><input type="text" name="term5" id="term5" />
	</div>
	<div>
		<label for="term">Search Term</label><input type="text" name="term6" id="term6" />
	</div>
	<div>
		<input type="submit" value="Search Terms" />
	</div>
</form>
<div id="results">
</div>

<script>

</script>
</head>
</body>
</html>