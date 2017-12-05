<?php 
		require_once('variables.php');
		include_once('header.php');
		include_once('nav.php'); 
?>
<!DOCTYPE html>
<html>
<head>
	<title>Search</title>
</head>
<body>
<div id="search" class="container">
    <div id="main">
      <div class="shop-container">
        <h1 class="shop-title">Search Products</h1>
				<form class="search-form" action="search.php" method="POST" enctype="multipart/form-data" name="search">
						<fieldset>	
							<legend>Search</legend>
							<input type="text" name="inventory" required autofocus>
						</fieldset>
						<input type="submit" value="Search" name="submit">
				</form>
<?php

$dbconnection = mysqli_connect(HOST,USER,PASSWORD,DB_NAME) or die ('connection failed');

$brand_query = "SELECT DISTINCT(brand) FROM final_inventory ORDER BY brand";

if (isset($_POST['submit'])) {
	$products = strtolower($_POST[inventory]);
	$productCleanUp = str_replace(',',' ',$products);
	$searchTerms = explode(' ',$productCleanUp);
	
	foreach ($searchTerms as $temp) {
		if(!empty($temp)) {
			$searchArray[] = $temp;
		} 
	}
	
	// WHERE Clause 
	$whereList = array();
	foreach($searchArray as $temp) {
		$whereList[] = "brand LIKE '%$temp%' OR model LIKE '%$temp%' OR size LIKE '%$temp%' OR color LIKE '%$temp%' OR color LIKE '%$temp%'";
	}
	$whereClause = implode(' OR ', $whereList);
	
	// Query 
	$query = "SELECT * FROM final_inventory";
	if (!empty($whereClause)) {
		$query .= " WHERE $whereClause";
	}
	
	echo "<h3>Search Results for '" .$productCleanUp. "'</h3>";
				
	// Database Results 
	$result = mysqli_query($dbconnection, $query) or die ('query failed');
	
	if (mysqli_num_rows($result) > 0) {	
		while ($row = mysqli_fetch_array($result)) {
				$searchResults = str_replace('***', 'span', $searchResults);
				
				$brand = $row['brand'];
        $query = "SELECT  id, brand, model, image, price, size, color FROM final_inventory WHERE brand = '$brand' AND $whereClause GROUP BY model ORDER BY $whereClause";
        $brand_result = mysqli_query($dbconnection, $query) or die ('query failed');
        echo '<div class="shop-container-inner">';
				
        while($row2 = mysqli_fetch_array($brand_result)) {
          echo
          '<a href="details.php?model='. $row2['model'] . '">
            <div class="shop-item search-highlight">
              <img class="shop-item-img" src= img/' . $row2['image'] . '>
              <h6 class="shop-item-title">' . $row2['brand'] . '</h6>
              <h6 class="shop-item-title">' . $row2['model'] . '</h6>
              <p class="shop-item-price">' . $row2['price'] . '</p>
              <p class="shop-item-color-size">' . $row['color'] . '</p>
              <p class="shop-item-color-size">' . $row['size'] . '</p>
            </div>
             <div  name="' . $row2['model'] . '" class="btn viewDetails" isclicked="">View Details</div>
          </a>';
          //echo '<p class="search-results">'.$searchResults.'</p>';
        }	
         echo '</div>';			
		}
	}
	else {
		echo "No products found matching <strong>$products</strong>";
	}
}
// Database Disconnect 	
mysqli_close($dbconnection);

?>	
      </div>
    </div>
</div>
</body>
</html>
