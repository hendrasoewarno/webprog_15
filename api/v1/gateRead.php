<?php

include_once "library.php";
include_once "read/vacancy.php";

# atur zona waktu server penerima (receiver) ke Jakarta (WIB / GMT+7)
date_default_timezone_set("Asia/Jakarta");

# ambil waktu server penerima (receiver)
// $curr_unix_time = 1567645682; # contoh hasil fungsi time() untuk Thursday, 05-Sep-19 01:08:02 UTC (GMT)
$curr_unix_time = time(); # ambil waktu saat ini dalam bentuk Unix Timestamp UTC (tidak terpengaruh zona waktu)
$token_exp_secs = 10; # atur batas waktu token untuk keperluan validasi
 
# ambil request header yang dikirimkan oleh server pengirim
$query = isset($_GET['query']) ? $_GET['query'] : NULL;
$header_token = isset($_SERVER['HTTP_API_TOKEN']) ? $_SERVER['HTTP_API_TOKEN'] : NULL;
$header_api_key = isset($_SERVER['HTTP_API_KEY']) ? $_SERVER['HTTP_API_KEY'] : NULL;
$header_request_time = isset($_SERVER['HTTP_X_REQUEST_TIME']) ? $_SERVER['HTTP_X_REQUEST_TIME'] : NULL;  
 
# sementara kita hardcoding dulu, harusnya diambil dari database
$api_key = 'key-live:h3oiu8b54que';
$secret_key = 'secret-live:dTvCnLh58A';
  
try {
	#harus open koneksi, karena adanya pencatatan log pada database
	$con = openConnection();
	
	$payload = NULL;
	  
	if ($header_token != NULL && $header_api_key != NULL && $header_request_time != NULL) {
		
		# validasi selisih waktu
		# selisih waktu sekarang dikurangi waktu request harus lebih kecil atau sama dengan $token_exp_secs				
		if ($curr_unix_time - $header_request_time <= $token_exp_secs) {
		
			# ambil request body yang berbentuk JSON string (Post Body)
			$body = file_get_contents('php://input');
			
			# decode post body dari JSON string ke Associative Array
			$payload = json_decode($body, true); //AHM->convert jadi arr pair value
				
			if ($payload) {			  			
				
				if ($query!=NULL) {			
						
					if ($header_token === hash('sha256', $header_api_key.$secret_key.$header_request_time)) {
								
						#penambahan try catch untuk sendEmail error
						try {
							#pemanggilan kepada dgi-function
							if ($query=="ping") {
								$arrRet = array("data"=>"{\"ping\":\"pong\"}");
							} else if ($query=="vacancy") {
								$arrRet = vacancy($payload);
							} else {
								throw new Exception("1:Unrecognized API Call type!");
							}
							header('Content-Type:application/json');
							echo APIResponseSucceed($arrRet);

						#ada error dalam proses
						} catch (Exception $e) {
							
							throw new Exception($e->getMessage());
							
						}
					} else {
						
						throw new Exception("0:Invalid credential!");
					}
				} else {
					
					throw new Exception("0:Query parameter not provided!");
					
				}
			
			} else {
				
				throw new Exception("0:Invalid post body format!");
			
			}
			
		} else {
			
			throw new Exception("0:Token expired!");			
			
		}
	} else {
		
		throw new Exception("0:Invalid request header!");
		
	}
} catch (Exception $e) {
    # atur response header Content-Type
    header('Content-Type:application/json');
	echo APIResponseFailed($e->getMessage());
} 
?>
