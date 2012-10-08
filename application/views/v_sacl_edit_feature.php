<?php require_once('header.php');?>

      <div class="row-fluid">
        <h2>Edit Features</h2>
        <p>Should be only IT developer who can access this. If not, then something is wrong with you robot!</p>

        <p>Anyway, please define your new features as preferred below:</p>

        <p>
          <?php shout()?>
          <form method="post">
          Title:</br>
          <input type="text" name="title" value="<?php echo set_value('title')?>"/></br>

          Description:</br>
          <input type="text" name="description" value="<?php echo set_value('description')?>"/></br>

          Status:</br>

          <input type="radio" name="status" value="indev" <?php echo set_radio('status', 'indev', TRUE); ?>/> indev &nbsp;
          <input type="radio" name="status" value="on" <?php echo set_radio('status', 'on'); ?>/> on &nbsp;
          <input type="radio" name="status" value="off" <?php echo set_radio('status', 'off'); ?>/> off &nbsp; <br/>

          </br>

          Site Url:</br>
          <input type="text" name="site_url" value="<?php echo set_value('site_url')?>"/></br>
          Access:</br>
          <input type="radio" name="access" value="1" <?php echo set_radio('access', '1', TRUE); ?>/> Public &nbsp;
          <input type="radio" name="access" value="2" <?php echo set_radio('access', '2'); ?>/> Private &nbsp;
          <input type="radio" name="access" value="3" <?php echo set_radio('access', '3'); ?>/> Controlled &nbsp; <br/>
          <br/>
          Show on Dashboard:</br>
          <input type="radio" name="dashboard" value="1" <?php echo set_radio('dashboard', '1'); ?>/> Show &nbsp;
          <input type="radio" name="dashboard" value="0" <?php echo set_radio('dashboard', '0', TRUE); ?>/> Don`t Show &nbsp; <br/>

          <br/>
          Icons: </br>
          <div class="icons">
          <?php 
            foreach($icons as $icon){
              if($icon == $_POST['icon']) $class='class="chosen"';
              else $class = '';

              echo '<img src="'.base_url().'assets/img/big_icon/'.$icon.'" data-icon="'.$icon.'" '.$class.'/>';
            }
          ?>
          </div>
          <div class="icons_chosen">
            <?php 
              if(set_value('icon') != '') echo '<input type="hidden" name="icon" value="'.set_value('icon').'">';
            ?>
          </div>

          <br/><br/>
          <input type="submit" class="btn btn-large btn-primary"/>
        </form>
        </p>
      </div><!--/row-->

      <style type="text/css">
        .icons img{margin: 3px; padding: 5px;}
        .icons img:hover, img.chosen {cursor: pointer; background: #444;}

      </style>

      <script src="<?php echo base_url()?>assets/js/jquery.js"></script>
      <script type="text/javascript">
        $(document).ready(function(){
          $('.icons img').click(function(){
            //alert($(this).attr('data-icon'));
            $('.icons img').removeClass('chosen');
            $(this).addClass('chosen');

            $('.icons_chosen').html('');
            $('.icons_chosen').append('<input type="hidden" name="icon" value="'+$(this).attr('data-icon')+'"/>');

          });
        });
      </script>


<?php require_once('footer.php')?>
