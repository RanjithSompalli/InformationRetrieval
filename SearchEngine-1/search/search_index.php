<div id="search-wrapper"> 
<script language="JavaScript" src="js/chk_str.js"></script>
<form name="SearchForm">
	<input class="default-value" id="mainsearch" type="text" name="s" value="Enter Query Terms Here..." />
	<div class="mask roundedCorners">
     	<button type="submit" class="button roundedCorners gradient"  onClick="return chk_genus('s','message','<b>Please enter a string like Silver.')" >Search</button>			
    </div>
	<div id='message'></div>
	</form>
</div>