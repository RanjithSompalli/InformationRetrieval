<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<?php
	//session_start();
	error_reporting(E_ALL^E_NOTICE^E_WARNING);//
?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>Search-Engine Version-1</title>

<link rel="stylesheet" href="css/global.css" type="text/css"
	media="screen, projection">
<link rel="stylesheet" href="css/main.css" type="text/css">
<link rel="stylesheet" type="text/css" href="css/sticky-footer.css" />
<link rel="stylesheet" type="text/css" href="css/application.css" />



<!--[if lt IE 8]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen, projection"><![endif]-->

<!-- Import fancy-type plugin for the sample page. -->
<link rel="stylesheet" href="css/screen.css" type="text/css"
	media="screen, projection">
<script language="javascript" src="js/jquery-1.6.1.min.js"></script>
<script language="javascript" src="js/main.js"></script>
<script language="javascript" src="js/dropdownlogin.js"></script>
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<?php
						if (!isset($_GET["page"]))
  						{
						  if (!isset($_GET["s"])) 
						  {						    
						    include_once 'search/search_index.php';
						  }
						}
						else 					
						{
					  		switch($_GET["page"])
					 		{
					 			case "search":
						 			include_once 'search/search_index.php';
						 			break;
					 		}
						}
						if (isset($_GET["s"]))
			 			{
						       include_once 'search/search_index_top.php';
		  					   include_once 'search/search_result.php';
						}
				?>
		 </div>
	</div>
</body>
</html>
