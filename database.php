<?php
	session_start();
    $database_server = "localhost";
    $database_name = "softinkl_chat";
    $database_username = "softinkl_chat";
    $database_password = "t87u9vw2iDXU";

    function textencode($str){
		$str = 	str_replace("'","",$str);
		$str = 	str_replace('"',"",$str);
		$str = 	str_replace(";","",$str);
		$str = 	str_replace("--","",$str);
		$str = 	str_replace("%","",$str);
		$str = 	str_replace("=","",$str);
		return $str;
	}

	function countSql($conn, $sql){
		$result = mysqli_query($conn, $sql);
		mysqli_close($conn);
		if ($result){
			return mysqli_num_rows($result);
		}else{
			return 0;
		}
	}
?>