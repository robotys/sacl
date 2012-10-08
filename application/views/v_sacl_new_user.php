<?php require_once('header.php');?>

      <div class="row-fluid">
        <h2>New User</h2>
        
        <br/>
        
        <form method="post">
          <h3>Maklumat Login</h3>

          <?php shout()?>
          <?php echo validation_errors("<div class='alert error'>","</div>"); ?>
          
          Username:</br>
          <input type="text" name="username" value="<?php echo set_value('username')?>"/></br>
          
          Email:</br>
          <input type="text" name="email" value="<?php echo set_value('email')?>"/></br>

          Password:</br>
          <input type="text" name="password" value="qwerty123" readonly="readonly"/></br>

          Tags:</br>
          <div class="row-fluid"><div class="span4">
          <?php
            
            $tags = $this->m_sacl->get_tags(TRUE);
            
            foreach($tags as $tag){
              echo '<input type="checkbox" name="tags[]" value="'.$tag['id'].'" '.set_checkbox('tags', $tag['id']).'"> '.$tag['key'].': '.$tag['value'].'&nbsp;&nbsp;&nbsp;&nbsp;';
            }
          ?>
          </div></div>

          <br/>
          <p class="alert">Nota: Mohon user tukar password sebaik log masuk ke dalam akaun.</p>

          <input type="submit" class="btn btn-large btn-primary"/>
        </form>
      </div><!--/row-->




<?php require_once('footer.php')?>
