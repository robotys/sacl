<?php require_once('header.php');?>

      <div class="row-fluid">
        <h2>Add New Features</h2>
        <p>Should be only IT developer who can access this. If not, then something is wrong with you robot!</p>

        <p>Anyway, please define your new features as preferred below:</p>

        <p>
          <?php shout()?>
          <form method="post">

          Title:</br>
          <input type="text" name="title"/></br>

          Description:</br>
          <input type="text" name="description"/></br>

          Site Url:</br>
          <input type="text" name="site_url"/></br>

          Feature Group:</br>
          <?php 
            foreach($feature_group as $fg){
              echo '<input type="radio" name="group" value="'.$fg['id'].'"> &nbsp; '.ucwords($fg['display']).' &nbsp &nbsp;';
            }

          ?>
          <a href='<?php echo site_url('sacl/feature_group')?>' style='font-size: 0.8em;'>Add new feature group &raquo;</a> 
          <br/><br/>

          Access:</br>
          <input type="radio" name="access" value="1" checked="checked"/> Public &nbsp;
          <input type="radio" name="access" value="2"/> Private &nbsp;
          <input type="radio" name="access" value="3"/> Controlled &nbsp; <br/><br/>

          Show on Dashboard:</br>
          <input type="radio" name="dashboard" value="1"/> Show &nbsp;
          <input type="radio" name="dashboard" value="0" checked="checked"/> Don`t Show &nbsp; <br/>

          <br/>
          Icons: </br>
          <div class="icons">
          <?php 
            foreach($icons as $icon){
              echo '<img src="'.base_url().'assets/img/big_icon/'.$icon.'" data-icon="'.$icon.'"/>';
            }
          ?>
          </div>
          <div class="icons_chosen"></div>
          <input type='hidden' name="status" value="indev"/>
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
