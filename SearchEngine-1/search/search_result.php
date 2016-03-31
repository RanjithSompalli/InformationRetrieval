<?php
	//Variables
	//s = search term
	//q = pagination variable (to limit x results per page)
	//setting up search options
	/* if(!isset($_SESSION))
	{
		session_start();
	} */
	
	
	
	//setting up search and pagination
	$term = $_GET['s'];
	
	//logic for performing stop word and stemming and other operations goes here
		
echo "<div id='result_numbers'>";
	if ($searchterm == '' || $searchterm == 'Search here...') 
	{
		echo "<div class='span-15 last notice' id='result_num_info'>Please enter a search term.</div>";
	}
	else 
	{
		if ($num > 0) 
		{
			echo "<div class='span-12 last success' id='result_num_info'> Searching for: 
			<b>$searchterm</b>. $num Results Found: </div>";
		}
		if ($num == 0) 
		{
			echo "<div class='span-15 last error' id='result_num_info'>
					Searching for: <b>$searchterm</b>. No Results Found </div>";
		}
	}
echo "</div>";	
	if ($num >0){
       		include_once 'search/result_form.php';
	}

?>
