<?php require_once('header.php');?>

      <div class="row-fluid">
        <div class="span6">
          <?php shout()?>

          <h2>Urus Tag</h2>
          <p>Tag adalah kad akses yang boleh digunakan oleh user. Kad akses ini menentukan features yang boleh pengguna gunakan. Pengguna boleh memiliki banyak kad akses aka tag. Jika fungsi yang ingin digunakan ada dalam senarai fungsi salah satu tag tersebut maka pengguna tersebut dibenarkan membuka fungsi tersebut.</p>
          <form method="post">
            <h3>Maklumat Tag:</h3>
            Key: <br/> 
            <input type="text" name="key" value="<?php echo set_value('key')?>"/> <br/>
            Value: <br/> 
            <input type="text" name="value" value="<?php echo set_value('value')?>"/> <br/>
            <input type="submit" class="btn btn-success"/> <br/><br/>
            <?php if($this->uri->segment(3)){?>
            <a href="<?php echo site_url('sacl/delete_tag/'.$this->uri->segment(3))?>" class="btn btn-danger">DELETE this tag &raquo;</a>
            <?php }?>
          </form>
        </div>
        <div class="span6">
          <h2>Senarai Tag</h2>
          <table class="table table-condensed table-striped table-bordered">
            <thead><tr><th>#</th><th>Key</th><th>Value</th><th>Action</th></tr></thead>
            <tbody>
              <?php
                $i = 1;
                foreach($tags as $group=>$tag){
                  //echo '<tr><th colspan="3"><strong>'.$group.'</strong></th></tr>';
                  foreach($tag as $id=>$value){
                    echo '<tr><td>'.($i++).'</td><td>'.$group.'</td><td>'.$value.'</td><td><a href="'.site_url('sacl/edit_tag/'.$id).'">edit/delete &raquo;</a></td></tr>';
                  }
                }
              ?>
            </tbody>
          </table>
        </div>
      </div><!--/row-->


<?php require_once('footer.php')?>
