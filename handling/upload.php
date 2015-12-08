<?php
	$name = $_FILES['photo']['name'];
	$tmp_name = $_FILES['photo']['tmp_name'];


	if(isset($name)) {
		if (!empty($name)) {
			$location = 'upload';
			if(!file_exists("$location/$name")){
				if(move_uploaded_file($tmp_name, "$location/$name")){
		    		echo 'Success';
                }
				else
					echo 'Error';
			}
		}
	}

?>