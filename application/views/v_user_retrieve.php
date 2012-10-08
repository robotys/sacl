<?php require_once('header.php');?>

      <div class="row">
        <div class="span4 offset4">
          <h1>Email Maklumat Login</h1>

          <?php
            shout();
          ?>
          <p> Terlupa password? Sistem PPS akan hantar kembali password anda. Sila berikan alamat email akaun PPS anda pada ruang dibawah dan klik [hantar]:
          <?php echo form_open('user/retrieve')?>
            <input type="text" name="email" placeholder="email" value="<?php echo set_value('email')?>"/> <br/>
            <input class="btn btn-primary" type="submit" value="Hantar &raquo;"/> <br/>

          </form>
          </p>
        </div>
      </div><!--/row-->

<?php require_once('footer.php')?>
