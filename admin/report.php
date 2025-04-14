<?php
// report.php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$page_title = 'Отчеты';
include('templates/header.php');

// Получаем данные для отчетов

// 1. Количество бронирований по месяцам
$stmt = $pdo->query("SELECT MONTH(start_date) AS month, COUNT(*) AS bookings_count FROM bookings GROUP BY MONTH(start_date)");
$bookings_by_month = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Доходы по месяцам
$stmt = $pdo->query("SELECT MONTH(start_date) AS month, SUM(total_price) AS revenue FROM bookings GROUP BY MONTH(start_date)");
$revenue_by_month = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Популярность оборудования (по количеству бронирований)
$stmt = $pdo->query("SELECT equipment_id, COUNT(*) AS bookings_count FROM bookings GROUP BY equipment_id");
$equipment_popularity = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Отчеты</h1>

<h2>Количество бронирований по месяцам</h2>
<canvas id="bookingsChart"></canvas>

<h2>Доходы по месяцам</h2>
<canvas id="revenueChart"></canvas>

<h2>Популярность оборудования</h2>
<canvas id="equipmentChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // График бронирований по месяцам
    const bookingsData = <?php echo json_encode($bookings_by_month); ?>;
    const bookingsLabels = bookingsData.map(item => `Месяц ${item.month}`);
    const bookingsCounts = bookingsData.map(item => item.bookings_count);

    const bookingsChart = new Chart(document.getElementById('bookingsChart'), {
        type: 'bar',
        data: {
            labels: bookingsLabels,
            datasets: [{
                label: 'Количество бронирований',
                data: bookingsCounts,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // График доходов по месяцам
    const revenueData = <?php echo json_encode($revenue_by_month); ?>;
    const revenueLabels = revenueData.map(item => `Месяц ${item.month}`);
    const revenueValues = revenueData.map(item => item.revenue);

    const revenueChart = new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Доходы (руб.)',
                data: revenueValues,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // График популярности оборудования
    const equipmentData = <?php echo json_encode($equipment_popularity); ?>;
    const equipmentLabels = equipmentData.map(item => {
        const stmt = <?php echo json_encode($pdo->prepare("SELECT name FROM equipment WHERE id = ?")); ?>;
        stmt.execute([item.equipment_id]);
        const equipment = stmt.fetch(PDO::FETCH_ASSOC);
        return equipment.name;
    });
    const equipmentCounts = equipmentData.map(item => item.bookings_count);

    const equipmentChart = new Chart(document.getElementById('equipmentChart'), {
        type: 'pie',
        data: {
            labels: equipmentLabels,
            datasets: [{
                data: equipmentCounts,
                backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 205, 86, 0.2)'],
                borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 205, 86, 1)'],
                borderWidth: 1
            }]
        }
    });
</script>

<?php include('templates/footer.php'); ?>
