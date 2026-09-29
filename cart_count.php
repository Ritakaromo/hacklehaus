<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

header("Content-Type: application/json");

// Get order data from frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['customerEmail']) || empty($data['orderItems'])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid order data."
    ]);
    exit;
}

// Extract data from payload
$customerName   = $data['customerName'] ?? "Customer";
$customerEmail  = $data['customerEmail'];
$phone          = $data['phone'] ?? "Not provided";
$address        = $data['address'] ?? "Not provided";
$orderItems     = $data['orderItems'];
$totalKES       = $data['totalKES'] ?? "0.00";
$totalUSD       = $data['totalUSD'] ?? "0.00";
$orderDate      = date("d M Y, H:i");
$shipmentType   = $data['shipmentType'] ?? "Standard";
$shipmentMethod = $data['shipmentMethod'] ?? "N/A";
$paymentMethod  = $data['payment'] ?? "Not specified";
$orderNotes     = $data['orderNotes'] ?? "";

// Build order table HTML
$orderTable = "<table border='1' cellpadding='5' cellspacing='0'>
<tr>
<th>Item</th><th>Color</th><th>Hook Size</th><th>Quantity</th><th>Total Price</th>
</tr>";

foreach($orderItems as $item){
    $orderTable .= "<tr>
    <td>{$item['name']}</td>
    <td>{$item['color']}</td>
    <td>{$item['hookSize']}</td>
    <td>{$item['qty']}</td>
    <td>{$item['price']}</td>
    </tr>";
}

$orderTable .= "</table>";

// Append order notes if provided
if(!empty($orderNotes)){
    $orderTable .= "<p><strong>Notes:</strong> {$orderNotes}</p>";
}

// Customer Email
$customerBody = "
<h2>🎣 Thank You for Your Order!</h2>
<p>Dear {$customerName},</p>
<p>We’ve received your order placed on {$orderDate} and we’re excited to get your flies ready for the catch.</p>

<h3>🧾 Order Summary</h3>
{$orderTable}
<p><strong>Total:</strong> KES {$totalKES} / USD {$totalUSD}</p>

<h3>🚚 Shipment Details</h3>
<p>Shipment Type: {$shipmentType}<br>
Shipment Method: {$shipmentMethod}</p>

<h3>💳 Payment Method</h3>
<p>Selected Method: {$paymentMethod}</p>

<h3>📍 Delivery Address</h3>
<p>{$address}</p>

<p>If you have any questions or need to make changes, feel free to reach out to us at <a href='mailto:hacklehausflyco@gmail.com'>hacklehausflyco@gmail.com</a>.</p>

<p>We’ll notify you once your order is shipped. Until then, tight lines and happy casting!</p>

<p>HackleHausFly Co. | Nairobi, Kenya<br>
Mon–Fri: 8:00am – 6:00pm | Sat: 9:00am – 4:00pm</p>
";

// Company Email
$companyBody = "
<h2>🧵 New Order Notification</h2>
<p>A new order has been placed on HackleHausFly Co.</p>
<p>Order Date: {$orderDate}</p>

<h3>👤 Customer Information</h3>
<p>Name: {$customerName}<br>
Email: {$customerEmail}<br>
Phone: {$phone}<br>
Address: {$address}</p>

<h3>🚚 Shipment Details</h3>
<p>Shipment Type: {$shipmentType}<br>
Shipment Method: {$shipmentMethod}</p>

<h3>💳 Payment Method</h3>
<p>Selected Method: {$paymentMethod}</p>

<h3>🧾 Order Summary</h3>
{$orderTable}
<p><strong>Total:</strong> KES {$totalKES} / USD {$totalUSD}</p>

<p>This email was generated automatically by HackleHausFly Co.<br>
📧 hacklehausflyco@gmail.com</p>
";

// Send emails
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = "hacklehausflyco@gmail.com";  // ✅ your email
    $mail->Password   = "uzqz bzpi zsse arzs";         // ✅ app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->isHTML(true);
    $mail->setFrom('hacklehausflyco@gmail.com', 'HackleHausFly Co.');

    // 1️⃣ Send to customer
    $mail->clearAllRecipients();
    $mail->addAddress($customerEmail, $customerName);
    $mail->Subject = "Order Confirmation - HackleHausFly Co.";
    $mail->Body = $customerBody;
    $mail->send();

    // 2️⃣ Send to company
    $mail->clearAllRecipients();
    $mail->addAddress('hacklehausflyco@gmail.com', 'HackleHausFly Co.');
    $mail->Subject = "New Order Notification - HackleHausFly Co.";
    $mail->Body = $companyBody;
    $mail->send();

    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully!"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"
    ]);
}
?>
