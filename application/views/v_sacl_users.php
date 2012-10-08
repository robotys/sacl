<?php require_once('header.php');?>
      
      <div class="row-fluid">

        <div class="span12">
          <h2>Manage Users:</h2>
        </div>
      </div>

      <div class="row-fluid">
        <div class="span6">
          <?php shout()?>
          <table class="table table-bordered table-condensed table-striped">
            <thead>
              <tr><th>#</th><th>Username</th><th>Email</th><th>Action</th></tr>
            </thead>
            <tbody>
              <?php
                $i = 1;
                
                foreach($users as $user){
                
                      echo "<tr><td>".($i++)."</td><td>".ucwords($user['username'])."</td><td>".$user['email']."</td><td>";

                      echo '<a href="'.site_url('sacl/edit_user/'.$user['id']).'">edit &raquo;</a>';

                      echo "</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>

        <div class="span6">
          <?php shout()?>
          <table class="table table-bordered table-condensed table-striped">
            <thead>
              <tr><th>#</th><th>Username</th><th>Email</th><th>Action</th></tr>
            </thead>
            <tbody>
              <?php
                $i = 1;
                
                foreach($user_by_tags as $tag_id=>$unders){
                  echo "<tr><td colspan='4'>".ucwords($tags[$tag_id]['key'])." / ".ucwords($tags[$tag_id]['value'])."</td></tr>";

                  
                    foreach($unders as $user_id){

                      echo "<tr><td>".($i++)."</td><td>".ucwords($users[$user_id]['username'])."</td><td>".$users[$user_id]['email']."</td><td>";

                      echo '<a href="'.site_url('sacl/edit_user/'.$user_id).'">edit &raquo;</a>';

                      echo "</td></tr>";
                    }
                }
              ?>
            </tbody>
          </table>
        </div>
      </div><!--/row-->


<?php require_once('footer.php')?>
