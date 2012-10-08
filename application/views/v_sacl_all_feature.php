<?php require_once('header.php');?>

      <div class="row-fluid">
        <div class="span12">
          <h2>Click edit link to edit the stuffs</h2>
          <table class="table table-bordered table-condensed table-striped">
            <thead><tr><th>id</th><th>icon</th><th>title</th><th>description</th><th>url</th><th>access</th><th>status</th><th>dashboard</th><th>action</th></tr></thead>
            <tbody>
          <?php

            $access[1] = "Public";
            $access[2] = "Private";
            $access[3] = "Controlled";

            foreach($features as $i=>$feature){
              if($feature['icon'] == '') $icon = 'none';
              else $icon = '<img src="'.base_url().'assets/img/big_icon/'.$feature['icon'].'"/>';
              echo '<tr><td>'.($i+1).'</td><td>'.$icon.'</td><td>'.$feature['title'].'</td><td>'.$feature['description'].'</td><td>'.$feature['site_url'].'</td><td>'.$access[$feature['access']].'</td><td>'.$feature['status'].'</td><td>';
              if($feature['dashboard']) echo 'show';
              else echo '-';
              echo '</td><td><a href="'.site_url('sacl/edit_feature/'.$feature['id']).'">edit &raquo;</a> &nbsp; &nbsp;<a href="'.site_url('sacl/delete_feature/'.$feature['id']).'">delete &raquo;</a></td></tr>';
            }


          ?>
            </tbody>
          </table>
        </div>
      </div><!--/row-->


<?php require_once('footer.php')?>
