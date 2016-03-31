<div id="search-wrapper-top"> 
	<script language="JavaScript" src="js/chk_str.js"></script>
	<form name="SearchForm">
		<?php
			if (isset($_GET["s"]))
		  	{
			    $search_value = $_GET["s"];
			    echo "<input class='search-field' type='text' value=$search_value name='s'  />";
		  	}
			else
		  	{
		    	echo "<input class='default-value search-field'type='text' value='Enter Query Terms Here...' name='s'  />";
		  	}
	   ?>
   
       <div class="mask roundedCorners">
			<button type="submit" class="button roundedCorners gradient"  onClick="return chk_genus('s','message','<b>Please enter a string like Silver.')" >Search</button>
       </div>
	</form>
</div>

<div id="message" style="margin-left:250px;"></div>