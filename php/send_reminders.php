<?php
include 'db_connect.php';
require '../vendor/autoload.php'; // Include the ClickSend API PHP library

use ClickSend\Api\SMSApi;
use ClickSend\Configuration;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;

// Set up ClickSend API configuration
$config = Configuration::getDefaultConfiguration()
    ->setUsername('lucsun@airforcerp.com') // Your ClickSend username
    ->setPassword('41A1E196-41C9-73E0-B326-143562A7446E');

// Initialize ClickSend API client
$smsApi = new SMSApi(new GuzzleHttp\Client(), $config);

$sql = "
    SELECT 
        u.firstName as parent_name, u.phone as parent_phone, 
        c.first_name as child_name, 
        m.name as medication_name, m.start_date, m.end_date, 
        mts.time as medication_time, c.timezone
    FROM medicine m
    JOIN children c ON m.child_id = c.id
    JOIN users u ON c.parent_id = u.id
    JOIN medicine_time_slots mts ON m.id = mts.medicine_id
    WHERE m.start_date <= CURDATE() AND m.end_date >= CURDATE()
";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {

    $parentName = $row['parent_name'];
    $parentPhone = $row['parent_phone'];
    $childName = $row['child_name'];
    $medicationName = $row['medication_name'];
    $medicationTime = $row['medication_time'];
    $timezone = $row['timezone'];

    $dateTimeNow = new DateTime('now', new DateTimeZone($timezone));
    $medicationDateTime = new DateTime($medicationTime, new DateTimeZone($timezone));

    $interval = $dateTimeNow->diff($medicationDateTime);
    $hours = (int)$interval->format('%h');
    $minutes = (int)$interval->format('%i');
    $isPast = $interval->invert; // 0 if medication time is in future, 1 if in past

    if (!$isPast) {
        $message = null;
        if ($hours === 1 && $minutes === 0) {
            $message = "Hello $parentName,\n\nYour child $childName's medicine $medicationName time is 1 hour from now.\n\nMedicine: $medicationName\nTime: " . $medicationDateTime->format('h:i A') . "\nDate: " . $medicationDateTime->format('Y-m-d');
        } elseif ($hours === 0 && $minutes === 30) {
            $message = "Hello $parentName,\n\nYour child $childName's medicine $medicationName time is 30 minutes from now.\n\nMedicine: $medicationName\nTime: " . $medicationDateTime->format('h:i A') . "\nDate: " . $medicationDateTime->format('Y-m-d');
        } elseif ($hours === 0 && $minutes === 0) {
            $message = "Hello $parentName,\n\nIt's time for your child $childName's medicine $medicationName.\n\nMedicine: $medicationName\nTime: " . $medicationDateTime->format('h:i A') . "\nDate: " . $medicationDateTime->format('Y-m-d');
        }

        if ($message) {
            $smsMessage = new SmsMessage([
                'source' => 'php',
                'from' => 'Medication Reminder',
                'body' => $message,
                'to' => $parentPhone,
            ]);

            $smsMessages = new SmsMessageCollection(['messages' => [$smsMessage]]);

            try {
                $response = $smsApi->smsSendPost($smsMessages);
                $response = json_decode($response, true);

                if ($response['http_code'] != 200) {
                    // Log the error
                    echo 'message send failed';
                } else{
                    echo 'message send';
                }
            } catch (Exception $e) {
                // Log the exception
            }
        }
    }
}

$conn->close();
?>
