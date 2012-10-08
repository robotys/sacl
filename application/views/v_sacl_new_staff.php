<?php require_once('header.php');?>

      <div class="row-fluid">
        <h2>Add New/Edit Staff</h2>
        <p>Should be only IT developer who can access this. If not, then something is wrong with you robot!</p>

        <p>
          <?php shout()?>
          <form method="post">

          Username:</br>
          <input type="text" name="username" value="<?php echo set_value('username')?>"/></br>

          Fullname:</br>
          <input type="text" name="fullname" value="<?php echo set_value('fullname')?>"/></br>
          
          Email:</br>
          <input type="text" name="email" value="<?php echo set_value('email')?>"/></br>

          Unit:</br>
          <select name="unit">
            <?php 
              foreach($tags['unit'] as $id=>$u){
                echo '<option value="'.$id.'" ';
                echo set_select('unit', $id);
                echo '>'.$u.'</option>';
              }
            ?>
          </select>
          <br/>
          Position:</br>
          <select name="position">
            <?php 
              foreach($tags['position'] as $id=>$p){
                echo '<option value="'.$id.'" ';
                echo set_select('position', $id);
                echo'>'.$p.'</option>';
              }
            ?>
          </select>

          <br/><br/>

          <input type="hidden" name="tag_raw" value='<?php echo set_value('tags');?>'/>
          <input type="hidden" name="old_unit" value='<?php echo set_value('unit');?>'/>
          <input type="hidden" name="old_position" value='<?php echo set_value('position');?>'/>


          <input type="submit" name="action" value="DELETE" class="btn btn-danger">
          <input type="submit" class="btn btn-large btn-primary" style="margin-left:70px"/>

        </form>
        </p>
      </div><!--/row-->

      <style type="text/css">
        .icons img{margin: 3px; padding: 5px;}
        .icons img:hover, img.chosen {cursor: pointer; background: #444;}

      </style>


<?php require_once('footer.php')?>
