<?php require_once('header.php');?>

      <div class="row-fluid">
        <h2>Kemaskini Maklumat User</h2>
        
        <br/>
        
        <form method="post">
          <h3>Maklumat Login</h3>

          <?php shout()?>
          <?php echo validation_errors("<div class='alert alert-error'>","</div>"); ?>
          
          Username:</br>
          <input type="text" name="username" value="<?php echo set_value('username')?>"/></br>
          
          Email:</br>
          <input type="text" name="email" value="<?php echo set_value('email')?>"/></br>

          Tags:</br>
          <div class="row-fluid"><div class="span4">
          <?php
            foreach($this->m_sacl->get_tags(TRUE) as $tag){
              echo '<input type="checkbox" name="tags[]" value="'.$tag['id'].'" '.set_checkbox('tags', $tag['id']).'"> '.$tag['key'].': '.$tag['value'].'&nbsp;&nbsp;&nbsp;&nbsp;';
            }
          ?>
          </div></div>

          <br/>
          <h3>Tukar Password:</h3>
          
          Password lama: <br/> 
          <input type="password" name="old_password"  value="<?php echo set_value('password')?>" > <br/>
          Password baru: <br/> 
          <input type="password" name="new_password"> <br/>
          

          <input type="submit" class="btn btn-large btn-primary"/>
          <br/><br/><br/>
          <a href="<?php echo site_url('sacl/delete/users/id/'.$this->uri->segment(3).'/sacl/users')?>" class="btn btn-danger"onclick="return confirm('Are your sure to delete this? Once delete forever gone...')">DELETE</a>
        </form>
      </div><!--/row-->




<?php require_once('footer.php')?>
