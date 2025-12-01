<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professional') {
    die("Unauthorized.");
}

require_once "db.php";
$pro_id = $_SESSION['user_id'];
?>

<!-- ===================================================== -->
<!--                        SERVICES                        -->
<!-- ===================================================== -->

<section class="panel">
    <h2>My Services</h2>

    <!-- Add Service -->
    <form method="POST" action="service_add.php" class="panel" style="margin-bottom:20px;">
        <h3>Add New Service</h3>

        <div class="form-row">
            <label>Category</label>
            <select name="category" required>
                <option value="Hair">Hair</option>
                <option value="Makeup">Makeup</option>
                <option value="Skincare">Skincare</option>
                <option value="Bodycare">Bodycare</option>
                <option value="Nails">Nails</option>
            </select>
        </div>

        <div class="form-row">
            <label>Title</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-row">
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>

        <div class="form-row">
            <label>Duration (minutes)</label>
            <input type="number" name="duration" required>
        </div>

        <div class="form-row">
            <label>Price (SAR)</label>
            <input type="number" step="0.01" name="price" required>
        </div>

        <div class="form-row">
            <label>Tags</label>
            <input type="text" name="tags">
        </div>

        <button class="btn" type="submit">Add Service</button>
    </form>

    <hr>

    <!-- List Services -->
    <h3>Your Services</h3>

    <table class="styled-table">
        <tr>
            <th>Category</th>
            <th>Title</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Description</th>
            <th>Tags</th>
            <th>Actions</th>
        </tr>

        <?php
        $q = $conn->prepare("SELECT * FROM Service WHERE professional_id = ?");
        $q->bind_param("i", $pro_id);
        $q->execute();
        $services = $q->get_result();

        while ($s = $services->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($s['category']); ?></td>
                <td><?= htmlspecialchars($s['title']); ?></td>
                <td><?= $s['duration']; ?> min</td>
                <td>SAR <?= $s['price']; ?></td>
                <td><?= htmlspecialchars($s['description']); ?></td>
                <td><?= htmlspecialchars($s['tags']); ?></td>
                <td>
                    <form action="service_delete.php" method="POST" style="display:inline;">
                        <input type="hidden" name="service_id" value="<?= $s['service_id']; ?>">
                        <button class="btn danger" onclick="return confirm('Delete this service?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>

<!-- ===================================================== -->
<!--                 PENDING BOOKING REQUESTS              -->
<!-- ===================================================== -->

<section class="panel">
    <h2>Pending Booking Requests</h2>

    <?php
    $pending = $conn->prepare("
        SELECT 
            R.request_id,
            R.preferred_date,
            R.preferred_time,
            U.name AS client_name,
            S.title AS service_title
        FROM BookingRequest R
        JOIN User U ON R.client_id = U.user_id
        JOIN Service S ON R.service_id = S.service_id
        WHERE R.professional_id = ? AND R.status = 'pending'
        ORDER BY R.preferred_date, R.preferred_time
    ");
    $pending->bind_param("i", $pro_id);
    $pending->execute();
    $pendingRes = $pending->get_result();
    ?>

    <table class="styled-table">
        <tr>
            <th>Client</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Actions</th>
        </tr>

        <?php while ($p = $pendingRes->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($p['client_name']); ?></td>
                <td><?= htmlspecialchars($p['service_title']); ?></td>
                <td><?= $p['preferred_date']; ?></td>
                <td><?= $p['preferred_time']; ?></td>
                <td>
                    <form method="POST" action="request_update.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= $p['request_id']; ?>">
                        <input type="hidden" name="status" value="accepted">
                        <button class="btn success">Accept</button>
                    </form>

                    <form method="POST" action="request_update.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= $p['request_id']; ?>">
                        <input type="hidden" name="status" value="rejected">
                        <button class="btn danger">Reject</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>

<!-- ===================================================== -->
<!--                   CONFIRMED BOOKINGS                  -->
<!-- ===================================================== -->

<section class="panel">
    <h2>Confirmed Bookings</h2>

    <?php
    $confirmed = $conn->prepare("
        SELECT 
            B.time,
            U.name AS client_name,
            S.title AS service_title
        FROM Booking B
        JOIN User U ON B.client_id = U.user_id
        JOIN Service S ON B.service_id = S.service_id
        WHERE B.professional_id = ? AND B.status = 'confirmed'
        ORDER BY B.time ASC
    ");
    $confirmed->bind_param("i", $pro_id);
    $confirmed->execute();
    $confirmedRes = $confirmed->get_result();
    ?>

    <table class="styled-table">
        <tr>
            <th>Client</th>
            <th>Service</th>
            <th>Date/Time</th>
        </tr>

        <?php while ($c = $confirmedRes->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($c['client_name']); ?></td>
                <td><?= htmlspecialchars($c['service_title']); ?></td>
                <td><?= $c['time']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>
