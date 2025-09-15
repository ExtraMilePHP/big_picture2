<?php 

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;



// AWS credentials
$credentials = [
    'key'    => $key,
    'secret' => $secretkey,
    'region' => $region, // For example, 'us-east-1'
];

// print_r($credentials);

// Create an S3 client
$s3 = new S3Client([
    'version'     => $version,
    'region'      => $credentials['region'],
    'credentials' => $credentials,
]);

function uniqueName($basename="") {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < 35; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return (isset($_SESSION["sessionId"])) ? $_SESSION["sessionId"]."-".$randomString.$basename:$randomString.$basename;
}


function uploadOnS3($filename,$tmp_name){
    global $s3_bucket,$s3_mainbucket,$s3,$s3_folder;   // note that s3 mainbucket is the bucket name and s3_bucket is just a folder name.eg. UAT/ , PROD/
    $key =$s3_bucket.$s3_folder.'/'.$filename; // Setting key for the S3 object
    if(isset($s3_folder)){
        try {
            // Upload file to S3
            $result = $s3->putObject([
                'Bucket' => $s3_mainbucket,
                'Key'    => $key,
                'Body'   => fopen($tmp_name, 'rb')
            ]);
    
            $s3->waitUntil('ObjectExists', array(
                'Bucket' => $s3_mainbucket,
                'Key'    => $key
            ));
    
            return true;
        } catch (S3Exception $e) {
            echo "Error uploading file: " . $e->getMessage();
        }
    }else{
        echo "missing s3 folder"; // add this variable it in dao/config.php
    }

}

function getFromS3($filename){
    global $s3_bucket,$s3_mainbucket,$s3,$s3_folder;   // note that s3 mainbucket is the bucket name and s3_bucket is just a folder name.eg. UAT/ , PROD/
    try {
        $keyPath = $s3_bucket.$s3_folder."/".$filename;
        $command = $s3->getCommand('GetObject', array(
            'Bucket'      => $s3_mainbucket,
            'Key'         => $keyPath,
            'ContentType' => 'image/png',
            'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"'
        ));
        $signedUrl = $s3->createPresignedRequest($command, "+6 days");
        // Create a signed URL from the command object that will last for
        // 6 days from the current time
        return (string)$signedUrl->getUri();
    } catch (Aws\S3\Exception\S3Exception $e) {
        echo "S3 Exception: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage();
    }
}

function possibleOnS3($location,$filename){
    if(strlen($filename)<25){
         return $location.$filename;
    }else{
         return getFromS3($filename);
    }
}
?>