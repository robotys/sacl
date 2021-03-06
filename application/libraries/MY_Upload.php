<?php
	
	class MY_Upload extends CI_Upload{

		function multiple_upload($upload_dir = './uploads', $config = array())
		{
		    $CI =& get_instance();
		    $files = array();

		    if(empty($config))
		    {
		        $config['upload_path']   = realpath($upload_dir);
		        $config['allowed_types'] = 'gif|jpg|jpeg|jpe|png';
		        $config['max_size']      = '2048';
		    }
		        
	        $CI->load->library('upload', $config);
	        
	        $errors = FALSE;
	        
	        foreach($_FILES as $key => $value)
	        {            
	            if( ! empty($value['name']))
	            {
	                if( ! $CI->upload->do_upload($key))
	                {                                           
	                    $data['upload_message'] = $CI->upload->display_errors(ERR_OPEN, ERR_CLOSE); // ERR_OPEN and ERR_CLOSE are error delimiters defined in a config file
	                    $CI->load->vars($data);
	                        
	                    $errors = TRUE;
	                }
	                else
	                {
	                    // Build a file array from all uploaded files
	                    $files[$key] = $CI->upload->data();
	                }
	            }
	        }
	        
	        // There was errors, we have to delete the uploaded files
	        if($errors)
	        {                    
	            foreach($files as $key => $file)
	            {
	                @unlink($file['full_path']);    
	            }                    
	        }
	        elseif(empty($files) AND empty($data['upload_message']))
	        {
	            $CI->lang->load('upload');
	            $data['upload_message'] = ERR_OPEN.$CI->lang->line('upload_no_file_selected').ERR_CLOSE;
	            $CI->load->vars($data);
	        }
	        else
	        {
	            return $files;
	        }
	    } 

	}

?>