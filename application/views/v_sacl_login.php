<?php require_once('header.php');?>
      
      <div class="row">
        <div class="span6 offset4">
          <h1>Login</h1>
          <?php
            shout();

            //echo hashim('qwerty123');
          ?>
        </div>
        <div class="span4 offset4">
          <?php echo form_open('login')?>
            <input type="text" name="email" placeholder="email"/> <br/>
            <input type="password" name="password" placeholder="password"/> <br/>
            <a href="<?php echo site_url('user/retrieve')?>">&laquo; Lupa Password</a>
            <input style='margin-left: 52px;' class="btn btn-primary" type="submit" value="Login &raquo;"/> <br/>

          </form>

          <p>Klik link berikut untuk <a href="<?php echo $google_login_link?>">Login dengan menggunakan akaun gmail anda &raquo;</a></p>
          
        </div>

      </div><!--/row-->

      <style>
        .lama{text-decoration: underline; color: #008aff; cursor:pointer}
      </style>

      
<?php require_once('footer.php')?>
