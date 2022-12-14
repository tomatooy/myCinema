<!DOCTYPE html>
<html lang="en">
<?php

session_start();
require_once('./php/component.php');
require_once('./connection.php');
require_once('./php/cartFunction.php');

try {
    $dsn = 'mysql:host=localhost;dbname=mycinema';
    $username = 'root';
    $password = '';
    $db = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    echo "<p> An error occured while connecting to the database: $error_message </p>";
}

$id = $_GET['id'];
$time = $_GET['time'];
$theatre = $_GET['theatre'];
$link = mysqli_connect("localhost", "root", "", "mycinema");

$movieQuery = "SELECT * FROM all_movie WHERE id = $id";
$movieInfo = mysqli_query($link, $movieQuery);
$movies = mysqli_fetch_array($movieInfo);


?>

<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./style/booking.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/ad45f9ba34.js" crossorigin="anonymous"></script>
    <title>Book <?php echo $movies['movie_name']; ?> Now - Ticket-Master</title>


</head>

<body style="background-image: linear-gradient(to bottom right, black , #57595D);">
    <div class="booking-panel">
        <div class="booking-panel-section booking-panel-section1">
            <h1>RESERVE YOUR SEAT</h1>
        </div>
        <div class="booking-panel-section booking-panel-section2" onClick="location.href='index.php'">
            <i class="fas fa-2x fa-times rounded"></i>
        </div>
        <div class="booking-panel-section booking-panel-section3">
            <div class="movie-box">
                <?php
                echo '<img src="' . $movies['image'] . '" alt="">';
                ?>
            </div>
        </div>
        <div class="booking-panel-section booking-panel-section4">
            <div class="title"><?php echo $movies['movie_name']; ?></div>
            <div class="movie-information">
                <table>
                    <tr>
                        <td>GENRE</td>
                        <td><?php echo $movies['genre']; ?></td>
                    </tr>
                    <tr>
                        <td>DESCRIPTION</td>
                        <td><?php echo $movies['movie_description']; ?></td>
                    </tr>
                    <tr>
                        <td>RELEASE DATE</td>
                        <td><?php echo $movies['date']; ?></td>
                    </tr>
                    <tr>
                        <td>PRICE:</td>
                        <td>$50 / ticket</td>
                    </tr>
                </table>
            </div>
            <div class="booking-form-container">
                <form action="" method="POST">

                    <select name="theatre" required>
                        <option value="" disabled <?php if($theatre=="") {echo 'selected';} ?>>THEATRE</option>
                        <option value="hall-1" <?php if($theatre=="1") {echo 'selected';} ?>>Hall 1</option>
                        <option value="hall-2" <?php if($theatre=="2") {echo 'selected';} ?>>Hall 2</option>
                        <option value="hall-3" <?php if($theatre=="3") {echo 'selected';} ?>>Hall 3 </option>
                    </select>
                    <?php
                        echo "
                        <input type='hidden' name='movie_price' value='50'>
                        <input type='hidden' name='movie_name' value='".$movies['movie_name']."'>
                        <input type='hidden' name='movie_img' value=".$movies['image'].">
                        <input type='hidden' name='movie_id' value=".$movies['id'].">
                        ";
                    ?>
                    <select name="showtime" required>
                        <option value="" disabled <?php if($time=="") {echo 'selected';} ?>>TIME</option>
                        <option value="19:00" <?php if($time=="1900") {echo 'selected';} ?>>7:00 PM</option>
                        <option value="20:30" <?php if($time=="2030") {echo 'selected';} ?>>8:30 PM</option>
                        <option value="21:00" <?php if($time=="2100") {echo 'selected';} ?>>9:00 PM</option>
                    </select>

                    <input type="date" id="start" name="date" <?php if($id<=6) {echo 'min="'; $currentDate = new DateTime(); echo $currentDate->format('Y-m-d'); echo '"';}  ?>>

                    <select name="type" required>
                        <option value="" disabled selected>TYPE</option>
                        <option value="2D">2D</option>
                        <option value="IMAX">IMAX</option>
                    </select>

                    <select name="seat" required>
                        <option value="" disabled selected>SEAT</option>
                        <option value="row1">Row 1</option>
                        <option value="row2">Row 2</option>
                        <option value="row3">Row 3</option>
                        <option value="row4">Row 4</option>
                        <option value="row5">Row 5</option>
                        <option value="row6">Row 6</option>
                    </select>

                    <input placeholder="Number of Tickets" name="quantity" type="number" min="0">

                    <button type="submit" value="submit" name="add_to_cart" class="form-btn"><i class="fa-solid fa-book-open"></i><b> Add to cart</b></button>

                    
                    <?php
                    
                    if (isset($_POST['submit'])) { 

                    	$username = $_SESSION['username'];
                    	$movienametemp = $movies["movie_name"];
                    	$theatretemp = $_POST["theatre"];
                    	$typetemp = $_POST["type"];
                    	$datetemp = $_POST["bookingDate"];
                    	$timetemp = $_POST["showtime"];
                    	$quantitytemp = $_POST["quantity"];
                    	$isPayed = 0;

                    	$insert_query = "INSERT INTO bookingtable (username, moviename, theatre, type, bookingDate, showtime, quantity, hasPayed) 
						VALUES ('$username', '$movienametemp', '$theatretemp', '$typetemp', '$datetemp', '$timetemp', '$quantitytemp', '$isPayed');"; 
                    	$result = mysqli_query($link, $insert_query);

                    	if ($result) {
                    		$orderQuery = "SELECT id FROM bookingtable WHERE username = '$username' ORDER BY id DESC LIMIT 1;";
                    		$orderData = mysqli_query($link, $orderQuery);
                    		$orderNumber = mysqli_fetch_array($orderData);
                    		header("Location: ./static/paymentSuccess.html?order=".$orderNumber['id']);
                    		exit();
                    	}
                    	else {
                    		echo "Error: Reservation could not be saved. Please try again.";
                    	}
					}
                    ?>
                </form>
            </div>
        </div>
    </div>

</body>


</html>