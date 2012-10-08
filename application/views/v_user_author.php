<?php require_once('header.php');?>

      <div class="row-fluid">
        <div class="span4">
          
          <form method="post">

          <h2>Maklumat Penulis:</h2>

            <?php shout()?>
            <?php echo validation_errors('<div class="alert alert-error">','</div>')?>
            Nama: <br/>
            <input type="text" name="fullname" value="<?php echo set_value('fullname');?>"/> <br/>Username: <br/>
            <input type="text" name="username" value="<?php echo set_value('username');?>"/> <br/>
            Email: <br/>
            <input type="text" name="email" value="<?php echo set_value('email');?>"/> <br/>
            HP: <br/>
            <input type="text" name="hp" value="<?php echo set_value('hp');?>"/> <br/>
            IC: <br/>
            <input type="text" name="ic" value="<?php echo set_value('ic');?>"/> <br/>
            <input type="hidden" name="tags" value="<?php $tags[] = 88; echo serialize($tags);?>">
            <br/>
            Waris 1: <br/>
            <input type="text" name="waris1" value="<?php echo set_value('waris1');?>"/> <br/>
            IC Waris 1: <br/>
            <input type="text" name="ic_waris1" value="<?php echo set_value('ic_waris1');?>"/> <br/>
            Alamat Waris 1: <br/>
            <textarea name="address_waris1"><?php echo set_value('address_waris1');?></textarea><br/>
            HP Waris 1: <br/>
            <input type="text" name="hp_waris1" value="<?php echo set_value('hp_waris1');?>"/> <br/>

            <br/>
            Waris 2: <br/>
            <input type="text" name="waris2" value="<?php echo set_value('waris2');?>"/> <br/>
            IC Waris 2: <br/>
            <input type="text" name="ic_waris2" value="<?php echo set_value('ic_waris2');?>"/> <br/>
            Alamat Waris 2: <br/>
            <textarea name="address_waris2"><?php echo set_value('address_waris2');?></textarea><br/>
            HP Waris 2: <br/>
            <input type="text" name="hp_waris2" value="<?php echo set_value('hp_waris2');?>"/> <br/>



            <input type="hidden" name="tags" value="<?php $tags[] = 88; echo serialize($tags);?>">

            <input type="hidden" name="update" value="<?php echo set_value('tags')?>"/>
            <input type="submit" class="btn btn-large btn-primary"/>
            <?php
              if($this->uri->segment(3)) echo '<br/><br/> <a href="'.site_url('user/delete_author/'.$this->uri->segment(3)).'" class="btn btn-danger btn-large">delete this author &raquo;</a>'
            ?>
          </form>
        </div> <!--/span-->

        <div class="span4">
            <table class="table table-bordered table-striped table-condensed">
              <thead>
                <tr><th>#</th><th>Nama Penulis</th><th>Email</th><th>Tindakan</th></tr>
              </thead>
              <tbody>
                <?php

                  $i = 1;
                  foreach($authors as $author){
                    echo '<tr><td>'.($i++).'</td><td>'.$author['username'].'</td><td>'.$author['email'].'</td><td><a href="'.site_url('user/author/'.$author['id']).'">edit &raquo;</a></td></tr>';
                  }

                ?>
              </tbody>
            </table>
        </div><!--/span-->
      </div><!--/row-->




<?php require_once('footer.php')?>
