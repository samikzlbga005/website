<?php
    $file_path = "./".rand(0,1000000);
    setcookie("getCookie", $file_path);
    $bool = false;
?>

<!doctype html >
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Image Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <style>
        
        .wo{
            margin-top: 20px;
            justify-content: center;
            width: 20%;
        }
        .col{
            display: inline-block;
            width: 50%;
            margin: 15px;
        }
        .col-md-4{
            margin: 20px;
            border: 1px solid;
        }
        .buton{
            width: 180px;
            height: 20px;
        }
        .a{
            margin: 10px;
        }
        .btn{
            margin-bottom: 20px;
        }
        .input-group{
            width:350px;
        }
        
    </style>
    
</head>
  <body class = "bg-light">
   <form action="" enctype="multipart/form-data" method="post">
    <center>
        <main>
            <div class = "container">
                <div class="row">
                    <div class="col">
                        <h3 id = "h3">Choose File</h3>
                        <div class="input-group">
                            <input type="file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload" name = "_image" accept="jpg jpeg png">
                        </div>
                    </div>
                </div>
                <div class = "col buton">
                    <div class = "row">
                    <input type="submit" class="btn btn-primary" name="Small" value ="Small Image">
                    </div>
                    <div class = "row">
                        <input type="submit" class="btn btn-primary" name="Send" value ="Save to Database">
                    </div>
                    <div class = "row">
                        <input type="submit" class="btn btn-primary" name="Get" value ="Getting Images From DB">
                    </div>
                </div>
            </div>
        </main>
       </center>
        
   </form>

   <?php
        if(isset($_POST["Small"])){
            $original_file = $_FILES["_image"]["tmp_name"];
            if($original_file == null){
                $bool = false;
                setcookie("getBool",$bool);
                echo "<script type='text/javascript'>alert('Please Choose a file');</script>";
            }
            else {
                    $bool = true;
                    setcookie("getBool",$bool);
                    mkdir($file_path);
                    $features = getimagesize($original_file);
                    $type = $features[2];
                    $width = $features[0];
                    $height = $features[1];
                    $percent = 0.5;
                    $new_width =  $percent * $width;
                    $new_height = $percent * $height;
                    $new_image = imagecreatetruecolor($new_width, $new_height);
                    $old_image = imagecreatetruecolor($width,$height);
                    if($type == IMAGETYPE_PNG){
                        $image = imagecreatefrompng($original_file);
                        imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagecopyresized($old_image, $image, 0, 0, 0, 0, $width, $height, $width, $height);
                        echo "<hr>";
                        imagejpeg($new_image,$file_path."/new.jpg");
                        imagejpeg($old_image,$file_path."/old.jpg");
                    }
                    else if($type == IMAGETYPE_JPEG){
                        $image = imagecreatefromjpeg($original_file);
                        imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagecopyresized($old_image, $image, 0, 0, 0, 0, $width, $height, $width, $height);
                        echo "<hr>";
                        imagejpeg($new_image,$file_path."/new.jpg");
                        imagejpeg($old_image,$file_path."/old.jpg");

                }
                else{
                    $image = imagecreatefromjpg($original_file);
                    imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagecopyresized($old_image, $image, 0, 0, 0, 0, $width, $height, $width, $height);
                    echo "<hr>";
                    imagejpeg($new_image,$file_path."/new.jpg");
                    imagejpeg($old_image,$file_path."/old.jpg");
                }
                $i = 0;
                $handle = opendir($file_path);
                if ($handle) {
                    while (($entry = readdir($handle)) !== FALSE) {
                        $arrFiles[] = $entry;
                         
                    }
                }
                
                $im = file_get_contents($file_path."/new.jpg");
                if($im){
                    echo "<img src = $file_path/new.jpg />";
                    echo "<hr>";
                    echo "<img src = $file_path/old.jpg />";
                }
                
            }
            
        }
        if(isset($_POST["Send"])){
            if(!isset($_COOKIE["getBool"])){
                echo "<script type='text/javascript'>alert('First of all, upload image after then resize image');</script>";

            }
            else {
                $bool = false;
                setcookie("getBool",$bool);
                $nfile = "new.jpg";
                $ofile = "old.jpg";
                $folder = $_COOKIE["getCookie"]."/";
                $database = "eu-cdbr-west-03.cleardb.net";
                $username = "b6541dda9d06e1";
                $password = "b3a2e76c";
                $databaseName = "heroku_a33e0eae3c98e2d";
                $connect = new mysqli($database,$username,$password,$databaseName,3306);
                if($connect->connect_error){
                    die("not connected:".$connect->connect_error);
                }
                else{
                    echo "<hr>";
                    echo "Images successfully insert database";
                    echo "<hr>";
                    $sql = "INSERT INTO image_table (s_image, b_image) VALUES ('$folder$nfile','$folder$ofile')";
                    mysqli_query($connect, $sql);
                }
            }
        }

        if(isset($_POST["Get"])){
            echo "<br>";
            $connect = new mysqli("localhost","root","","test",3306);
            if($connect->connect_error){
                die("not connected:".$connect->connect_error);
            }
            $query = "SELECT * FROM `image_table`;";
                $result = $connect->query($query);
                
                if ($result->num_rows > 0) 
                {
                    while($row = $result->fetch_assoc())
                    {
                        echo "<img src = $row[s_image] />";
                        echo "<hr>";
                        echo "<img src = $row[b_image] />";
                        echo "<hr>";

                    }
                } 
                else {
                    echo "0 results";
                }
            
            $connect->close();
        }
   ?>
    
   
   
    
    

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
  </body>
</html>
