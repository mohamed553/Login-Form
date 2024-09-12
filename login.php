<?php
$dsn ='mysql:host=localhost;dbname=mohamed';
$user ='root';
$pass = '';
$option =array(

    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);
try {
    $con =new PDO($dsn , $user , $pass , $option);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch (PDOException $e){
    echo 'failed to connect' . $e->getMessage();

}
/**

/* session_start(); */

// include 'connent.php';  page that contain database connection

/* if (isset($_SESSION['Username'])){ // check if there is a session or not
    header('Location: index.php');
  } */
ob_start();
if( isset($_COOKIE['user'] )){


    $userid =($_COOKIE['user']);

    $stmt = $con->prepare("select name ,full_name , user_pass from users where name='".$userid."'");
    $stmt->execute(array($userid));
    $row = $stmt->fetch();
    $count = $stmt->rowCount();


    if( $count > 0 ){
        echo  '<div style="width: 150px;background-color: aqua;margin-left: 610px;height: 50px;"> welcome !</br>' . $row['full_name'] . '</div>' ;
        header('Location: test.php ');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username   = $_POST['user'];
    $password   = $_POST['pass'];



    if (empty($username)){ // UserName and Password  validation

        echo 'User Name Cant Be Empty';


    }elseif (empty($password)){

        echo 'User Name Cant Be Empty';

    }else{

        $stmt = $con->prepare("SELECT name ,full_name , user_pass   FROM users WHERE  name = ? AND  user_pass = ? ");
        $stmt->execute(array($username, $password));
        $row = $stmt->fetch();
        $count = $stmt->rowCount();

        if ($count > 0) { // check if this user exist or not

            echo  '<div style="width: 150px;background-color: aqua;margin-left: 610px;height: 50px;"> welcome !</br>' . $row['full_name'] . '</div>' ;

            if(isset($_POST['remember_me']))
            {
                $hour = time() + 3600 * 24 * 30;
                setcookie('user', $username, $hour);
            }
            header('Location: test.php ');
            exit();

            /*
             $_SESSION['Username'] = $username; // register session name
             $_SESSION['ID'] = $row['UserID'];
             header('Location: index.php');
             exit();
            */


        } else {
            echo '<div style="width: 150px;background-color: aqua;margin-left: 610px;height: 50px;"><strong>' . 'invalid username or password' . '</strong></div>'; // error massage
        }

    }

}

?>


<form style="margin-left: 605px;margin-top: 18px;background-color: darkslategrey;margin-right: 550px;padding: 73px 11px;width: 175px;height: 160px;" action="<?php echo $_SERVER['PHP_SELF'] ?> " method="POST">

    <h3 style=" text-align: center; background-color: aliceblue; "> Welcome ! </h3>
    <input required type="text" name="user" placeholder="Enter Your Name" /><br>
    <input style=" margin-top: 10px; " required type="password" name="pass" placeholder="Enter Your Password"  /><br>

    <input style=" margin-top: 10px; "  type="checkbox"  name="remember_me" >Remember me


    <input style=" margin-left: 55px; margin-top: 33px; " type="submit" value="Log IN" />


</form>
<?php  ob_end_flush(); ?>
