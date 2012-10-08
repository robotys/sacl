<?php require_once('header.php');?>

      <div class="row-fluid">
        <?php //dumper($_SERVER['HTTP_REFERER'])?>
        
      <div class="span6">
        <form method="post">
        <h2>New Access Control Card</h2>
        Select Feature: <br/>
          <select name="feature_id">       
            <?php 
              foreach($features as $feature){
                echo '<option value="'.$feature['id'].'">'.$feature['site_url'].'</option>';
              }
            ?> 
          </select>
        <br/>Type: <br/>

          <input type="radio" name="type" value="users" /> Users &nbsp;
          <?php
            foreach($tags as $tag){
              $keys[$tag['key']] = $tag['key'];
            }

            //make radio button for each type of tags
            foreach($keys as $key){
              echo '<input type="radio" name="type" value="'.$key.'" /> '.ucwords($key).' &nbsp;';

            }

            echo '<br/><br/>';

            //make the select and options for the tags
            foreach($keys as $key){

              echo '<div class="hidethis '.$key.'"> Select '.ucwords($key).':<br/> <select name="'.$key.'_id">';
              foreach($tags as $tag){
                if($tag['key'] == $key) echo '<option value="'.$tag['id'].'">'.$tag['value'].'</option>';
              }
              echo '</select><br/></div>';
            }

          ?>
          <!--
          <input type="radio" name="type" value="unit" /> Unit &nbsp;
          <input type="radio" name="type" value="position" /> Position &nbsp; <br/>

                <br/>
        <div class="select_unit">
        Unit: <br/>

          <select name="unit_id">       
            <?php 
              foreach($tags as $tag){
                if($tag['key'] == 'unit') echo '<option value="'.$tag['id'].'">'.$tag['value'].'</option>';
              }
            ?> 
          </select>
        </div>

        <div class="select_position">
        Position: <br/>

          <select name="position_id">       
            <?php 
              foreach($positions as $position){
                echo '<option value="'.$position['id'].'">'.$position['name'].'</option>';
              }
            ?> 
          </select>
        </div>
      -->

        <div class="hidethis users"> 
        Select User: <br/>

          <select name="users_id">       
            <?php 
              foreach($users as $user){
                if($user['username'] != '')echo '<option value="'.$user['id'].'">'.$user['username'].'</option>';
              }
            ?> 
          </select>
        </div>

        <br/>
          <input type="submit" class="btn"/>
        </form>

        <!--ACL lists-->
        <table class="table table-bordered table-striped table-condensed">
          <thead>
            <tr><th>#</th><th>URL</th><th>Title</th><th>type:id</th><th>action</th></tr>
          </thead>
          <tbody>
            <?php
              $i = 1;
              //dumper($acls);
              foreach($acls as $acl){
                echo '<tr><td>'.($i++).'</td><td>'.$acl['site_url'].'</td><td>'.$titles[$acl['site_url']].'</td><td>'.$acl['verbose'].'</td><td><a href="'.site_url('sacl/delete_acl/'.$acl['id']).'" onclick="return confirm(\'Confirm Delete?\')">delete &raquo;</a></td></tr>';
              }
            ?>
          </tbody>
        </table>
      </div>
      </div><!--/row-->

      <script src="<?php echo base_url()?>assets/js/jquery.js"></script>
      <script type="text/javascript">
        $(document).ready(function(){
          $('.hidethis').hide();
          $('input[name=type]').change(function(){
            $('.hidethis').hide();
            var which = '.'+$(this).val();
            $(which).show();
          });
        });
      </script>

<?php require_once('footer.php')?>
