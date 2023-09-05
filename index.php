<?php
include_once 'Mysqldump/Mysqldump.php';
require 'smtp/PHPMailerAutoload.php';

// /******** Initialize the database dump ********/
// $dump = new Ifsnop\Mysqldump\Mysqldump('mysql:host=localhost;dbname=eclinic_db', 'root', '');

// /******** SQL query to retrieve data from the "clinics" table ********/
// $sql = "SELECT * FROM clinics WHERE token = 'EDSPHCDA'";

// /******** Execute the SQL query and fetch data ********/
// $pdo = new PDO('mysql:host=localhost;dbname=eclinic_db', 'root', '');
// $stmt = $pdo->query($sql);
// $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// foreach ($rows as $row) {
//     $clinicName = $row['clinic_name'];
// }


include_once 'Mysqldump/Mysqldump.php';

// Database connection parameters
$host = 'localhost';
$dbname = 'eclinic_db';
$username = 'root';
$password = '';
$date = date('Y-m-d');
try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Get the list of tables in the database
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    // Initialize the Mysqldump object
    $dump = new Ifsnop\Mysqldump\Mysqldump("mysql:host=$host;dbname=$dbname", $username, $password);

    foreach ($tables as $table) {
        // Generate the filename
        $filename = "sql_dump/{$table}_{$date}.bat";

        // Generate the batch file content
        $batchContent = "mysqldump -u $username -p$password $dbname $table > $filename";

        // Save the batch file
        file_put_contents($filename, $batchContent);

        echo "Batch file generated for table '$table' successfully.<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
exit();

// // Generate the batch file content
// $dump = new Ifsnop\Mysqldump\Mysqldump('mysql:host=localhost;dbname=eclinic_db', 'root', '');
// // /******** SQL query to retrieve data from the "clinics" table ********/
// $sql = "SELECT * FROM clinics WHERE token = 'EDSPHCDA'";

// /******** Execute the SQL query and fetch data ********/
// $pdo = new PDO('mysql:host=localhost;dbname=eclinic_db', 'root', '');
// $stmt = $pdo->query($sql);
// $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// foreach ($rows as $row) {
//     $clinicName = $row['clinic_name'];
// }


/********  The current date and location for file names ********/
$location = $clinicName;
$date = date('Y-m-d');
$filename = "{$location}_{$date}";

try {
     /**  Start the database dump and save it to a file ********/
    // $dump->start("sql_dump/{$filename}.sql");
    $dump->start("sql_dump/{$filename}.bat");
    
    echo "Batch file generated successfully.";
    exit();
     /********  Zip the SQL file ********/
     $zip = new ZipArchive();
     $zipFileName = "sql_dump/{$filename}.zip";
     if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE)) 
     {
         $zip->addFile("sql_dump/{$filename}.sql", "{$filename}.sql");
         $zip->close();
     }

     /******** Create a new instance of PHPMailer  ********/
    $mail = new PHPMailer(true);

    /******** SMTP configuration  ********/
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = 'sundayv678@gmail.com'; 
    $mail->Password = 'ksbdqwjrrknlsmwq';
    $mail->setFrom('sundayv678@gmail.com');
    $mail->addAddress('sundayv678@gmail.com'); 
    $mail->isHTML(true);
    $mail->Subject = $clinicName.'Backup';
    $mail->Body = 'Eclinic Backup';
    $mail->addAttachment($zipFileName);
    $mail->send();
    echo '<script>alert("Backup sent successfully.");</script>';
    }
    catch (Exception $e) 
    {
    echo '<script>alert("Error Sending Backup: Please Retry and Check Internet Connection");</script>';
    }
?>
