<?php require_once('header.php');?>

      <div class="row-fluid">
        <div class="span3">
           <h2>Manage Feature Group</h2> 
           <p>Feature group will make the management of features easier. it provide grouping function for same function category.</p>

           <?php
              shout();
              validation_errors();
              rbt_makeform($inputs);
           ?>
          <??>
        </div><!--/span-->

        <div class="span3">
          <?php
            if($this->uri->segment(3)) echo '<a href="'.site_url('sacl/feature_group').'" class="btn btn-success">&laquo;  Back to New Feature Group</a><br/><br/>';
          ?>
          <table class="table table-bordered table-striped table-condensed">
            <thead><tr><th>#</th><th>Display name</th><th>Action</th></tr></thead>
            <?php
              $i = 1;
              foreach($feature_groups as $fg){
                echo '<tr><td>'.($i++).'</td><td>'.$fg['display'].'</td><td>
                  <a href="'.site_url('sacl/delete/feature_group/id/'.$fg['id']).'">delete &raquo;</a> &nbsp <a href="'.site_url('sacl/feature_group/'.$fg['id']).'">update &raquo;</a>

                </td></tr>';
              }
            ?>
          </table>
        </div>
      </div><!--/row-->

<?php require_once('footer.php')?>
