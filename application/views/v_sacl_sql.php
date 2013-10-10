<?php require_once('header.php');?>

      <div class="row-fluid">

        <div class="span6">
          <h2>Update Database with below checkpoint:</h2>

          <table class="table table-bordered table-condensed table-striped">
            <thead><tr><th>#</th><th>Filename</th><th>Action</th></tr></thead>
            <tbody>
              <?php
                foreach($dumps as $i=>$file){
                  echo '<tr>
                    <td>'.($i+1).'</td>
                    <td>'.$file.'</td>
                    <td><form method="post" style="float:left; padding: 0px; line-height: 0em;"><input type="hidden" name="filename"  value="'.$file.'"><input type="submit" class="btn btn-success" value="use now &raquo"/></form></td>
                  </tr>';
                }
              ?>
            </tbody>
          </table>
        </div><!--/span--> 

        <style>
          .tableu{
            width: 100px;
            float:left;
          }
        </style>

        <div class="span6">
          <h2>Create New Checkpoint:</h2>
          <form method="post">
            <b>Tables:</b><br/>
          <?php

            foreach($tables as $i=>$table){
              echo '<input type="checkbox" name="pick[]" value="'.$table.'">&nbsp; '.$table.' &nbsp; ';
              if(($i+1)%4 == 0) echo '<br/>';
            }
          ?><br/><br/>
            <b>Notes:</b><br/>
            <input type="text" name="note" placeholder="any notes"/><br/>
            <input type="submit" class="btn btn-primary btn-large" value="Create Checkpoint Now &raquo;"/>
          </form>
        </div><!--/span-->
        
      </div><!--/row-->

<?php require_once('footer.php')?>
