
<script language="javascript" src="js/view.js"></script>

<?php

echo "<div class='main-column' id='search-results'>";			

$i=0;

if ($num > 0){
	
	$count = 1 + $q;
		
	while ($row = mysql_fetch_array($result)) {
		$genus=mysql_result($result,$i,"GENUS");
		$_order=mysql_result($result,$i,"_ORDER");
		$suborder=mysql_result($result,$i,"SUBORDER");
		$family=mysql_result($result,$i,"FAMILY");
		$superfamily=mysql_result($result,$i,"SUPERFAMILY");
		$subfamily=mysql_result($result,$i,"SUBFAMILY");

		$description=mysql_result($result,$i,"DESCRIPTION");
		$geostratigraphy=mysql_result($result,$i,"GEOSTRATIGRAPHY");
		$figures=mysql_result($result,$i,"FIGURES");
		$images=mysql_result($result,$i,"IMAGES"); 
		
				
		if (strlen($description) > $limittext){
			$limit_description = substr($description, 0, strrpos(substr($description, 0, $limittext), ' ')) . '...';
		}
		else
		{
			$limit_description=$description;
		}

		if (strlen($geostratigraphy) > $limittext){

			$limit_geostratigraphy = substr($geostratigraphy, 0, strrpos(substr($geostratigraphy, 0, $limittext), ' ')) . '...';
		}
		else
		{
			$limit_geostratigraphy=$geostratigraphy;
		}

		if ($_GET['page']=="advancedsearch_result")
		{
			$limit_description=highlight($limit_description, $keywords1);
			$limit_geostratigraphy=highlight($limit_geostratigraphy, $keywords2);
			$description=highlight(mysql_result($result,$i,"DESCRIPTION"), $keywords1);
			$geostratigraphy=highlight(mysql_result($result,$i,"GEOSTRATIGRAPHY"),$keywords2);
		}
		/* EXTRACT IMAGES*/
		$figure_index=preg_replace("/Fig\. ([0-9]+).*/", "F$1", $figures, 1);
		$image_folder="figures/";
		$image_path=$image_folder.$figure_index;
		






		/* -----------------------------  Page Layout ------------------------------------------------------------- */
		echo "<div class='main-column'>";

		echo "<h1><a href='index.php?page=viewgenus&genus=$genus'>$genus</a></h1>";

                echo "<table><tbody>";

                echo "<tr><td>Order</td>";
		if (($_order!="UNKNOWN") && ($_order!="UNCERTAIN"))
		{
		  $order_name = ucfirst(strtolower($_order));
		  echo "<td><a href='index.php?page=general&type=order&name=$_order'>$order_name</a></td>";
		
		}
		echo "</tr>";



		echo "<tr><td>Suborder</td>";
		if (($suborder!="UNKNOWN") && ($suborder!="UNCERTAIN"))
		{
		  $suborder_name = ucfirst(strtolower($suborder));
		  echo "<td><a href='index.php?page=general&type=suborder&name=$suborder'>$suborder_name</a></td>";	
		}
		echo "</tr>";


		echo "<tr><td>Superfamily</td>";
		if (($superfamily!="UNKNOWN") && ($superfamily!="UNCERTAIN"))
		{
		  $superfamily_name = ucfirst(strtolower($superfamily));
		  echo "<td><a href='index.php?page=general&type=superfamily&name=$superfamily'>$superfamily_name</a></td>";
		}
		echo "</tr>";



		echo "<tr><td>Family</td>";
		if (($family!="UNKNOWN") && ($family!="UNCERTAIN"))
		{
		  $family_name = ucfirst(strtolower($family));
		  echo "<td><a href='index.php?page=general&type=family&name=$family'>$family_name</a></td>";		
		}
		echo "</tr>";
		
		echo "<tr><td>Subfamily</td>";
		if (($subfamily!="UNKNOWN") && ($subfamily!="UNCERTAIN"))
		{
		  $subfamily_name = ucfirst(strtolower($subfamily));
		  echo "<td><a href='index.php?page=general&type=subfamily&name=$subfamily'>$subfamily_name</a></td>";		
		}
		echo "</tr>";
		echo "</tbody></table>";





		if (file_exists($image_path))			
		{	
			$image_arr=explode(',',$images);
			$image_file=$image_arr[1].'.jpg';
			if (file_exists($image_path.'/'.$image_file))
			  {
			    echo "<img src=\"$image_path/$image_file\" alt=\"No Image\" width=\"75\" height=\"75\"  />";
			  }
			$image_file_2=trim($image_arr[2]).'.jpg';
			if (file_exists($image_path.'/'.$image_file_2))
			  {
			    echo "<img src=\"$image_path/$image_file_2\" alt=\"No Image\" width=\"75\" height=\"75\"  />";
			  }
			$image_file_3=trim($image_arr[3]).'.jpg';
			if (file_exists($image_path.'/'.$image_file_3))
			  {
			    echo "<img src=\"$image_path/$image_file_3\" alt=\"No Image\" width=\"75\" height=\"75\"  />";
			  }
		    closedir($handle);
		}	

		echo "</div>"; // end of single_result
		echo "<div class='main-column' id='result_DGF'>";
		echo "<h3>Description:</h3>";	
	  echo "<p class='trunk'>$description</p>";
		echo "<h3>Geostratigraphy:</h3>";
		echo "<p class='trunk'>$geostratigraphy</p>";	
		echo "</div>"; // end of result_DGF

		$i++;
		$count++;
	}
	
	$currPage = (($q/$limit) + 1);
	echo "<br />";
	
	echo "<div class='main-column'>";
	echo "<div class='span-5' id='prev_next'>";
	if ($q >= 1)
	{   //bypass prev link if s is 0
		$prevs = ($q-$limit);
		if (isset($_GET["view"]))
		{
		echo "&nbsp;<a href=\"index.php?view=$view&$view=$term&q=$prev&sb=$sb&si=$si&vol=$vol\">&lt;&lt;Prev 10</a>&nbsp&nbsp;";
		}
		else
		{
			if (isset($_GET["s"]))
			{
				echo "&nbsp;<a href=\"$PHP_SELF?q=$prevs&s=$term&sb=$sb\">&lt;&lt;Prev 10</a>&nbsp&nbsp;";
			}
			else 
			{
				echo "&nbsp;<a href=\"$PHP_SELF?q=$prevs&page=advancedsearch_result&genus_name=$genus_query&order_name=$order_query&suborder_name=$suborder_query&superfamily_name=$superfamily_query&family_name=$family_query&subfamily_name=$subfamily_query&description_add_count=$description_cnt&location_add_count=1&location1=$location_query&display_type=normal";
				for ($j=1;$j<=trim(str_replace($redundant," ",$_GET['description_add_count']));$j++)
				{
					$str="description".strval($j);
					$tmp=trim(str_replace($redundant," ",$_GET[$str]));
					if ($tmp!="")
					{
						echo "&description$j=$tmp";
					}
				}
				echo "&slidervalue=$geotime0_query&slidervalue1=$geotime1_query\">&lt;&lt;Prev 10</a>&nbsp&nbsp;";
			}
		}
	}
	// calculate number of pages needing links
	$pages=intval($num/$limit);

	// $pages now contains int of pages needed unless there is a remainder from division

	if ($num%$limit) 
	{
		// has remainder so add one page
		$pages++;
	}

	// check to see if last page
	if (!((($q+$limit)/$limit)==$pages) && $pages!=1) 
	{

		// not last page so give NEXT link
		$news=$q+$limit;
		if (isset($_GET["view"]))
		{
			echo "&nbsp;<a href=\"index.php?view=$view&$view=$term&q=$news&sb=$sb&si=$si&vol=$vol\">Next 10 &gt;&gt;</a>";
		}
		else
		{
			if (isset($_GET["s"]))
			{
				echo "&nbsp;<a href=\"$PHP_SELF?q=$news&s=$term&sb=$sb\">Next 10 &gt;&gt;</a>";
			}
			else
			{
				echo "&nbsp;<a href=\"$PHP_SELF?q=$news&page=advancedsearch_result&genus_name=$genus_query&order_name=$order_query&suborder_name=$suborder_query&superfamily_name=$superfamily_query&family_name=$family_query&subfamily_name=$subfamily_query&description_add_count=$description_cnt&location_add_count=1&location1=$location_query&display_type=normal";
				for ($j=1;$j<=trim(str_replace($redundant," ",$_GET['description_add_count']));$j++)
				{
					$str="description".strval($j);
					$tmp=trim(str_replace($redundant," ",$_GET[$str]));
					if ($tmp!="")
					{
						echo "&description$j=$tmp";
					}
				}
				echo "&slidervalue=$geotime0_query&slidervalue1=$geotime1_query\">Next 10 &gt;&gt;</a>&nbsp&nbsp;";
			}
		}
	}

	$a = $q + ($limit) ;
	if ($a > $num) 
	{ 
		$a = $num ; 
	}
	$b = $q + 1 ;
	echo "</div>";


	echo "<div class='span-9' id='show_num'>";
	echo "Showing results $b to $a of $num";
	echo "</div>";


	echo "</div>";
	
}

echo "</div>"; //end of results
?>
