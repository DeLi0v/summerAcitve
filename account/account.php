<main>
    <h1>Личный кабинет</h1>
    
    <div class="dashboard-container">
        <div class="dashboard-info">
            <h2>Информация о пользователе</h2>
            <p><strong>Имя:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Телефон:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <a href="/account/edit_profile.php">Изменить профиль</a>
        </div>
        
        <div class="dashboard-content">
            <h2>Мои бронирования</h2>
            <?php if (count($bookings) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Оборудование</th>
                            <th>Дата начала</th>
                            <th>Дата окончания</th>
                            <th>Статус</th>
                            <th>Итоговая стоимость</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['equipment_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                <td><?php echo htmlspecialchars($booking['total_price']); ?> руб.</td>
                                <td>
                                    <?php if ($booking['status'] == 'В обработке'): ?>
                                        <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>">Отменить</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>У вас нет активных бронирований.</p>
            <?php endif; ?>
        </div>
    </div>
</main>
