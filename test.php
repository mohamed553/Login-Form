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

  ob_start();

echo  '<div style="width: 150px;background-color: aqua;margin-left: 610px;height: 50px;"> welcome !</br>'. '</div>' ;


/*  categories  */

      $do = isset($_GET['do']) ?$_GET['do'] : 'Manage';




          $stmt2 = $con->prepare("SELECT * FROM categories  ");
          $stmt2->execute();
          $cats = $stmt2->fetchAll();
         if (!empty($cats)){ ?>

          <div style=" margin-left: 600px; margin-top: 100px; " class="container" >
              <div class="row">
          <h1 class="text-center">Manage Categories</h1>

          <table class="main-table manage-members  text-center table table-bordered" >
           <tr>

              <td style=" background-color: black; color: white; padding: 5px 15px; ">#ID</td>
              <td style=" background-color: black; color: white; padding: 5px 15px; ">Name</td>
              <td style=" background-color: black; color: white; padding: 5px 15px; ">Parent</td>
               <td style=" background-color: black; color: white; padding: 5px 15px; ">Control</td>

            </tr>

              <?php
              foreach ($cats as $item){

                  echo '<tr>';
                  echo '<td style="background-color:#addde8 ">'. $item['cat_id'] .'</td>';
                  echo '<td style=" background-color: #addde8;">'. $item['cat_name'] .'</td>';

                  $stmt2 = $con->prepare("SELECT * FROM categories  ");
                  $stmt2->execute();
                  $catss = $stmt2->fetchAll();

                  echo '<td style=" background-color: #addde8;">';

                  if ($item['parent'] == 0)
                  {
                      echo 'NO Parent';
                  }
              foreach ($catss as $items) {

                  if ($item['parent'] == $items['cat_id']) {
                      echo $items['cat_name'];
                  }
              }
                  echo '</td>';
                  echo '<td style=" background-color: #addde8;">
 
                        <a href="test.php?do=Edit&itemid='.$item['cat_id'] .'" class="btn btn-success"><i class="fa fa-edit"></i>  Edit</a>
                        <a href="test.php?do=Delete&itemid='.$item['cat_id'] .' " class="btn btn-danger confirm"><i class="fa fa-close"></i> Delete</a> ';

                  echo '</td>';
                  echo '</tr>';

              }
              ?>

          </table>
                  <a href="test.php?do=Add" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> New category</a>
              </div>
          </div>

     <?php  }



      if($do == 'Add'){ ?>

          <h1 style="margin-left: 570px;background-color: #4896ce;margin-right: 374px;text-align:  center;" class="text-center">Add New Category</h1>

          <div style=" width: 380px; margin-left: 533px; background-color: #87bdb3; padding: 15px 45px; " class="container">


              <form class="form-horizontal" action="?do=insert" method="POST">

                  <div class="form-group form-group-lg">
                      <label class="col-sm-2 control-label">Name</label>
                      <div class="col-sm-10 col-md-4">
                          <input type="text" name="name" class="form-control"  required="required"
                                 placeholder="Name Of The Category"/>
                      </div>
                  </div>


                  <div class="form-group form-group-lg">
                      <label class="col-sm-2 control-label">Category Parent</label>
                      <div class="col-sm-10 col-md-4">
                          <select name="parent">
                              <option value="0">None</option>
                              <?php

                              $stmt2 = $con->prepare("SELECT * FROM categories");
                              $stmt2->execute();
                              $allcats = $stmt2->fetchAll();

                              foreach ($allcats as $cat){
                                  echo "<option value='" . $cat['cat_id'] . "'>" . $cat['cat_name'] . "</option>" ;
                              }
                              ?>
                          </select>
                      </div>
                  </div>

                  <div class="form-group form-group-lg">
                      <div class="col-sm-offset-2 col-sm-10">
                          <input style=" margin-left: 115px; " type="submit" value="Add Category" class="btn btn-primary btn-sm"/>
                      </div>
                  </div>


              </form>

          </div>

     <?php }elseif($do == 'insert'){


      if ($_SERVER['REQUEST_METHOD'] == 'POST'){


          echo "<h1 class='text-center'>Insert Category</h1>";
          echo "<div class = 'container'>";

          $name    =$_POST['name'];
          $parent  =$_POST['parent'];


          // validation

          $stmt2 = $con->prepare("SELECT cat_name FROM categories WHERE cat_name= ?");
          $stmt2->execute(array($name));
          $check = $stmt2->fetchAll();

                if ($check == 1) {

                    echo "<div style=\" margin-left: 612px; background-color: #addde8; margin-right: 475px; text-align: center; \">".'Sorry This Category Is <strong>Exist</strong></div>';

                }else {

                    //Insert cate info in database
                    $stmt = $con->prepare("INSERT INTO  
                          categories(cat_name ,parent  )
                              VALUES(:zname ,:zparent  )");
                    $stmt->execute(array(
                        'zname'   => $name,
                        'zparent' => $parent

                    ));

                    echo "<div style=\" margin-left: 612px; background-color: #addde8; margin-right: 475px; text-align: center; \">" . $stmt->rowCount() . 'Record Inserted </div>';
                    header("refresh:3; url = test.php ");
                    exit();
                }
      }
          echo "</div>";

      }elseif($do == 'Edit'){

          $itemid =isset($_GET['itemid']) && is_numeric($_GET['itemid'])?intval($_GET['itemid']) :0;

          $stmt = $con->prepare("SELECT * FROM categories WHERE cat_id = ?");
          $stmt->execute(array($itemid));
          $item = $stmt->fetch();
          $count = $stmt->rowCount();
          if ($count > 0) { ?>

              <h1 style="margin-left: 570px;background-color: #4896ce;margin-right: 374px;text-align:  center;">Edit categories</h1>

              <div style=" width: 380px; margin-left: 533px; background-color: #87bdb3; padding: 15px 45px; " class="container">

      <form class="form-horizontal" action="?do=Update" method="POST">

    <input type="hidden" name="itemid" value="<?php echo  $itemid ?>"/>
    <div class="form-group form-group-lg">
        <label class="col-sm-2 control-label">Name</label>
        <div class="col-sm-10 col-md-4">
            <input type="text" name="name" class="form-control"  required="required"
                   placeholder="Name Of The Item"
                   value="<?php echo $item['cat_name']?>" />
        </div>
    </div>
    <div class="form-group form-group-lg">
        <label class="col-sm-2 control-label">Category Parent</label>
        <div class="col-sm-10 col-md-4">
            <select name="parent">
                <option value="0">No Parent</option>


                <?php
                $stmt2 = $con->prepare("SELECT * FROM categories");
                $stmt2->execute();
                $allcats = $stmt2->fetchAll();

                foreach ($allcats as $cat){
                    echo "<option value='" . $cat['cat_id'] ."'";if ($item['parent']== $cat['cat_id']){echo  'selected';} echo "> ". $cat['cat_name'] . "</option>";

                }
                ?>
            </select>
        </div>
    </div>
          <div class="form-group form-group-lg">
              <div class="col-sm-offset-2 col-sm-10">
                  <input style=" margin-left: 115px; " type="submit" value="Save Edit" class="btn btn-primary btn-sm"/>
              </div>
          </div>

       </form>

              </div>

<?php }
      }elseif($do == 'Update'){

          echo "<h1 style=\"margin-left: 570px;background-color: #4896ce;margin-right: 374px;text-align:  center;\" class='text-center'>Update category</h1>";
          echo "<div class = 'container'>";
     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         $id = $_POST['itemid'];
         $name = $_POST['name'];
         $desc = $_POST['parent'];

         //update the database
         $stmt = $con->prepare("UPDATE 
                                            categories 
                                            SET 
                                               cat_name = ? ,
                                               parent =? 
                                               
                                            WHERE 
                                                cat_id = ?");
         $stmt->execute(array($name, $desc, $id));
         echo "<div style=\" margin-left: 612px; background-color: #addde8; margin-right: 475px; text-align: center; \">" . $stmt->rowCount() . 'Record Updated </div>';
         header("refresh:3; url = test.php ");
         exit();

     }
     echo '</div>';

     }elseif($do == 'Delete'){

          echo "<h1 class='text-center'>Delete category</h1>";
          $itemid =isset($_GET['itemid']) && is_numeric($_GET['itemid'])?intval($_GET['itemid']) :0;
          $stmt = $con->prepare("DELETE FROM categories  WHERE  cat_id = :zid");
          $stmt->bindParam(":zid" ,$itemid);
          $stmt->execute();
          echo "<div style=\" margin-left: 612px; background-color: #addde8; margin-right: 475px; text-align: center; \">" . $stmt->rowCount() . 'Record Deleted </div>';
          header("refresh:3; url = test.php ");
          exit();


      }

     //  categories









/* Start Items Page */


$do = isset($_GET['do']) ?$_GET['do'] : 'Manage1';



$stmt2 = $con->prepare("SELECT * FROM items  ");
$stmt2->execute();
$itemms = $stmt2->fetchAll();
if (!empty($itemms)){ ?>

<div style=" margin-left: 600px; margin-top: 100px; " class="container" >
    <div class="row">
        <h1 class="text-center">Manage Items</h1>

        <table class="main-table manage-members  text-center table table-bordered" >
            <tr>

                <td style=" background-color: black; color: white; padding: 5px 15px; ">#ID</td>
                <td style=" background-color: black; color: white; padding: 5px 15px; ">Name</td>
                <td style=" background-color: black; color: white; padding: 5px 15px; ">Category</td>
                <td style=" background-color: black; color: white; padding: 5px 15px; ">Image</td>
                <td style=" background-color: black; color: white; padding: 5px 15px; ">Control</td>

            </tr>

            <?php
            foreach ($itemms as $itemm){

                echo '<tr>';
                echo '<td style="background-color:#addde8 ">'. $itemm['item_ID'] .'</td>';
                echo '<td style=" background-color: #addde8;">'. $itemm['item_name'] .'</td>';

                $stmt2 = $con->prepare("SELECT * FROM categories  ");
                $stmt2->execute();
                $catsss = $stmt2->fetchAll();

                echo '<td style=" background-color: #addde8;">';

                foreach ($catsss as $itemsss) {

                    if ($itemm['item_cat'] == $itemsss['cat_id']) {
                        echo $itemsss['cat_name'];
                    }
                }
                echo '</td>';
                echo "<td style=\" background-color: #addde8;\">";
                if (empty($itemm['avatar'])){
                    echo 'No Image';
                }else{
                    echo "<img style=\"width: 40px;\" src='avatar/".$itemm['avatar'] . "' alt=''/>";
                }
                echo "</td>";
                echo '<td style=" background-color: #addde8;">
 
                        <a href="test.php?do=Edit1&itemid='.$itemm['item_ID'] .'" class="btn btn-success"><i class="fa fa-edit"></i>  Edit</a>
                        <a href="test.php?do=Delete1&itemid='.$itemm['item_ID'] .' " class="btn btn-danger confirm"><i class="fa fa-close"></i> Delete</a> ';

                echo '</td>';
                echo '</tr>';

            }
            ?>

        </table>
        <a href="test.php?do=Add1" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> New Item</a>
    </div>
</div>

<?php  }



if($do == 'Add1'){ ?>

    <h1 style="margin-left: 570px;background-color: #4896ce;margin-right: 374px;text-align:  center;" class="text-center">Add New Item</h1>

    <div style=" width: 380px; margin-left: 533px; background-color: #87bdb3; padding: 15px 45px; " class="container">


        <form class="form-horizontal" action="?do=insert1" method="POST" enctype="multipart/form-data">

            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10 col-md-4">
                    <input type="text" name="item_name" class="form-control"  required="required"
                           placeholder="Name Of The Item"/>
                </div>
            </div>


            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Item Category </label>
                <div class="col-sm-10 col-md-4">
                    <select name="item_cat">
                        <option value="0">...</option>
                        <?php

                        $stmt2 = $con->prepare("SELECT * FROM categories");
                        $stmt2->execute();
                        $allcats = $stmt2->fetchAll();

                        foreach ($allcats as $cat){
                            echo "<option value='" . $cat['cat_id'] . "'>" . $cat['cat_name'] . "</option>" ;
                        }
                        ?>
                     </select>
                </div>
            </div>

            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Item Image</label>
                <div class="col-sm-10 col-md-4">
                    <input type="file" name="avatar" class="form-control" required="required" />
                </div>
            </div>

            <div class="form-group form-group-lg">
                <div class="col-sm-offset-2 col-sm-10">
                    <input style=" margin-left: 115px; " type="submit" value="Add Item" class="btn btn-primary btn-sm"/>
                </div>
            </div>

        </form>

    </div>

<?php }elseif($do == 'insert1'){


    if ($_SERVER['REQUEST_METHOD'] == 'POST'){


        echo "<h1 class='text-center'>Insert Item</h1>";
        echo "<div class = 'container'>";

        $avatar = $_FILES['avatar'];

        $avatarName = $_FILES['avatar']['name'];
        $avatarSize = $_FILES['avatar']['size'];
        $avatarTmp  = $_FILES['avatar']['tmp_name'];
        $avatarType = $_FILES['avatar']['type'];

        $avatarAllowedExtension = array("jpeg", "jpg", "png", "gif");

        $avatarExtension = strtolower(end(explode('.', $avatarName)));

        $name    =$_POST['item_name'];
        $cat     =$_POST['item_cat'];


        $stmt2 = $con->prepare("SELECT item_name FROM items WHERE item_name= ?");
        $stmt2->execute(array($name));
        $check = $stmt2->fetchAll();

        if ($check == 1) {

            echo "<div style=\" margin-left: 612px; background-color: #addde8; margin-right: 475px; text-align: center; \">".'Sorry This Item Is <strong>Exist</strong></div>';

        }else {

            $avatar =rand(0,1000000).'-'. $avatarName;
            move_uploaded_file($avatarTmp, "avatar\\".$avatar);

            //Insert cate info in database
            $stmt = $con->prepare("INSERT INTO  
                          items(item_name ,item_cat,avatar )
                              VALUES(:zitem_name ,:zitem_cat ,:zavatar )");
            $stmt->execute(array(
                'zitem_name'   => $name,
                'zitem_cat' => $cat ,
                'zavatar'   => $avatar

            ));

            echo "<div style=\" margin-left: 612px; background-color: #addde8; margin-right: 475px; text-align: center; \">" . $stmt->rowCount() . 'Record Inserted </div>';
            header("refresh:3; url = test.php ");
            exit();
        }
        echo "</div>";
    }

}elseif($do == 'Edit1'){

    $itemid =isset($_GET['itemid']) && is_numeric($_GET['itemid'])?intval($_GET['itemid']) :0;

    $stmt = $con->prepare("SELECT * FROM items WHERE item_ID = ?");
    $stmt->execute(array($itemid));
    $item = $stmt->fetch();
    $count = $stmt->rowCount();
    if ($count > 0) { ?>

        <h1 style="margin-left: 570px;background-color: #4896ce;margin-right: 374px;text-align:  center;">Edit Item</h1>

        <div style=" width: 380px; margin-left: 533px; background-color: #87bdb3; padding: 15px 45px; " class="container">

            <form class="form-horizontal" action="?do=Update1" method="POST">

                <input type="hidden" name="itemid" value="<?php echo  $itemid ?>"/>
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10 col-md-4">
                        <input type="text" name="name" class="form-control"  required="required"
                               placeholder="Name Of The Item"
                               value="<?php echo $item['item_name']?>" />
                    </div>
                </div>
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label"> Item Category </label>
                    <div class="col-sm-10 col-md-4">
                        <select name="itemcat">
                            <option value="0">...</option>
                            <?php

                            $stmt2 = $con->prepare("SELECT * FROM categories");
                            $stmt2->execute();
                            $allcats = $stmt2->fetchAll();

                            foreach ($allcats as $catt){
                                echo "<option value='" . $catt['cat_id'] ."'";if ($catt['cat_id'] == $item['item_cat']){echo  'selected';} echo ">". $catt['cat_name'] . "</option>";

                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-lg">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input style=" margin-left: 115px; " type="submit" value="Save Edit" class="btn btn-primary btn-sm"/>
                    </div>
                </div>

            </form>

        </div>

    <?php }
}elseif($do == 'Update1'){

    echo "<h1 style=\"margin-left: 570px;background-color: #4896ce;margin-right: 374px;text-align:  center;\" class='text-center'>Update Item</h1>";
    echo "<div class = 'container'>";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $id = $_POST['itemid'];
        $name = $_POST['name'];
        $cat = $_POST['itemcat'];

        //update the database

        $stmt = $con->prepare("UPDATE 
                                            items 
                                            SET 
                                               item_name = ? ,
                                               item_cat =? 
                                               
                                            WHERE 
                                                item_ID = ?");
        $stmt->execute(array($name, $cat, $id));

        echo "<div style=\" margin-left: 612px; background-color: #addde8; margin-right: 475px; text-align: center; \">" . $stmt->rowCount() . 'Record Updated </div>';
        header("refresh:3; url = test.php ");
        exit();
    }
    echo '</div>';

}elseif($do == 'Delete1'){

    echo "<h1 class='text-center'>Delete Item</h1>";
    $itemid =isset($_GET['itemid']) && is_numeric($_GET['itemid'])?intval($_GET['itemid']) :0;
    $stmt = $con->prepare("DELETE FROM items  WHERE  item_ID = :zid");
    $stmt->bindParam(":zid" ,$itemid);
    $stmt->execute();
    echo "<div style=\" margin-left: 612px; background-color: #addde8; margin-right: 475px; text-align: center; \">" . $stmt->rowCount() . 'Record Deleted </div>';
    header("refresh:3; url = test.php ");
    exit();


}

//  categories

ob_end_flush();
?>




















