<?php require_once('header.php');?>
      
      <div class="row-fluid">

        <div class="span12">
          <h2>Spoof Users:</h2>
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

                      echo '<a href="'.site_url('sacl/spoof/'.$user['id']).'">spoof &raquo;</a>';

                      echo "</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>

      </div><!--/row-->


<?php require_once('footer.php')?>
