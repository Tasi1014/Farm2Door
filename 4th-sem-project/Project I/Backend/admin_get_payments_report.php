<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

// Check Auth
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$data = json_decode(file_get_contents("php://input"), true);
$start_date = isset($data['start_date']) ? $data['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($data['end_date']) ? $data['end_date'] : date('Y-m-d');

// SQL to get aggregated payments by date and status
// We need to UNION Paid/Refunded transactions from 'payments' and Refund events from 'refunds'
$sql = "SELECT day, status, SUM(amount) as total_amount FROM (
            -- 1. Money that ever came IN (Status Paid or Refunded means it was collected)
            SELECT DATE(payment_date) as day, 'Paid' as status, amount_paid as amount 
            FROM payments 
            WHERE payment_status IN ('Paid', 'Refunded') AND DATE(payment_date) BETWEEN ? AND ?
            
            UNION ALL
            
            -- 2. Money that actually went OUT (Actual Refund event)
            SELECT DATE(refund_date) as day, 'Refunded' as status, refund_amount as amount 
            FROM refunds 
            WHERE DATE(refund_date) BETWEEN ? AND ?
        ) as combined
        GROUP BY day, status
        ORDER BY day ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $start_date, $end_date, $start_date, $end_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$daily_data = [];
$total_paid = 0;
$total_refunded = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $day = $row['day'];
    $status = $row['status'];
    $amount = (float)$row['total_amount'];

    if (!isset($daily_data[$day])) {
        $daily_data[$day] = ['Paid' => 0, 'Refunded' => 0];
    }
    $daily_data[$day][$status] = $amount;

    if ($status === 'Paid'){
        $total_paid += $amount;
    } 
else if ($status === 'Refunded') $total_refunded += $amount;
}

// Prepare chart response
$labels = [];
$paid_amounts = [];
$refunded_amounts = [];

// Ensure all dates are present in the response (optional but cleaner for charts)
$current = strtotime($start_date);
$last = strtotime($end_date);
while ($current <= $last) {
    $d = date('Y-m-d', $current);
    $labels[] = $d;
    $paid_amounts[] = isset($daily_data[$d]['Paid']) ? $daily_data[$d]['Paid'] : 0;
    $refunded_amounts[] = isset($daily_data[$d]['Refunded']) ? $daily_data[$d]['Refunded'] : 0;
    $current = strtotime("+1 day", $current);
}

// Calculate Admin Commission (Profit) - 10%
$admin_commission = $total_paid * 0.10;
$net_flow = $total_paid - $total_refunded;

echo json_encode([
    'success' => true,
    'labels' => $labels,
    'paid_amounts' => $paid_amounts,
    'refunded_amounts' => $refunded_amounts,
    'summary' => [
        'total_paid' => $total_paid,
        'total_refunded' => $total_refunded,
        'commission' => $admin_commission,
        'net_flow' => $net_flow
    ]
]);

mysqli_close($conn);
?>
