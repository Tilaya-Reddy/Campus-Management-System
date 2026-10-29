<!--Full Name: Tilaya Reddy-->
<!--Internet Programming 621 Assignment -->

<?php

// Define constants FIRST before including functions
define('STUDENT_PERMIT_PRICE', 450);  // Student permit costs R450
define('STAFF_PERMIT_PRICE',   750);  // Staff permit costs R750
define('VISITOR_PERMIT_PRICE', 100);  // Visitor permit costs R100
define('MAX_PARKING_CAPACITY', 50);   // Maximum permits that can be issued

// Include shared functions after constants are defined
require_once 'functions.php';

// --------------------------------------------------------------
// FORM HANDLING - Process new application submitted via form
// --------------------------------------------------------------
$formMessage = '';
$formClass   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_application'])) {
    $newName = trim($_POST['new_name']);
    $newAge  = (int)$_POST['new_age'];
    $newType = $_POST['new_type'];

    if ($newName === '') {
        $formMessage = 'Please enter a full name.';
        $formClass   = 'form-error';
    } elseif ($newAge < 1 || $newAge > 120) {
        $formMessage = 'Please enter a valid age.';
        $formClass   = 'form-error';
    } else {
        $formMessage = 'Application for ' . htmlspecialchars($newName) . ' added successfully!';
        $formClass   = 'form-success';
    }
}

// --------------------------------------------------------------
// DATA - List of permit applications to process
// --------------------------------------------------------------
$applications = array(
            array('FullName' => 'Savannah James',    'age' => 22, 'PermitType' => 'Student'),  // Valid student
            array('FullName' => 'Suzanne McCharthy', 'age' => 35, 'PermitType' => 'Staff'),    // Valid staff
            array('FullName' => 'Chad McClaine',     'age' => 17, 'PermitType' => 'Student'),  // Under 18 - denied
            array('FullName' => 'Madelyn Chetty',    'age' => 45, 'PermitType' => 'Staff'),    // Valid staff
            array('FullName' => 'Rayhaan Suliman',   'age' => 19, 'PermitType' => 'Student'),  // Valid student
            array('FullName' => 'Evelyn Pieters',    'age' => 20, 'PermitType' => 'Student'),  // Valid student
            array('FullName' => 'Zanele Ntuli',      'age' => 16, 'PermitType' => 'Visitor'),  // Under 18 - denied
            array('FullName' => 'Milan Pillay',      'age' => 28, 'PermitType' => 'Student'),  // Valid student
            array('FullName' => 'Phindile Gumede',   'age' => 33, 'PermitType' => 'Staff'),    // Valid staff
            array('FullName' => 'James Smith',       'age' => 18, 'PermitType' => 'Student'),  // Valid visitor
);

// If form was submitted with a valid application, add it
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_application']) && $formClass === 'form-success') {
    $applications[] = array(
        'FullName'   => htmlspecialchars(trim($_POST['new_name'])),
        'age'        => (int)$_POST['new_age'],
        'PermitType' => $_POST['new_type']
    );
}

// --------------------------------------------------------------
// PROCESSING - Validate and issue permits
// --------------------------------------------------------------
$permitSales = array(); // Stores all approved permits
$deniedApps  = array(); // Stores all rejected applications
$totalIssued = 0;       // Running count of issued permits

for ($i = 0; $i < count($applications); $i++) {
    $name = $applications[$i]['FullName'];
    $age  = $applications[$i]['age'];
    $type = $applications[$i]['PermitType'];

    // Check if parking lot is full
    if ($totalIssued >= MAX_PARKING_CAPACITY) {
        $deniedApps[] = array('FullName' => $name, 'reason' => 'Parking lot is full');
        continue;
    }

    // Check minimum age of 18
    if (!isValidAge($age)) {
        $deniedApps[] = array('FullName' => $name, 'reason' => 'Under 18 years old (age: ' . $age . ')');
        continue;
    }

    // Get the price for this permit type
    $price = getPermitPrice($type);

    // Check permit type is valid
    if ($price === null) {
        $deniedApps[] = array('name' => $name, 'reason' => 'Unknown permit type: ' . $type);
        continue;
    }

    // All checks passed - issue the permit
    $permitSales[] = array('name' => $name, 'type' => $type, 'price' => $price);
    $totalIssued++;
}

// All logic is done above - HTML output starts below
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parking Permit Module</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Bookman Old Style", sans-serif;
            min-height: 100vh;
            background-image: url('background2.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #2c2c2c;
        }

        /* Dark overlay for readability */
        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }

        /* Content sits above overlay */
        .content {
            position: relative;
            z-index: 1;
            padding: 30px;
        }

        h1 {
            color: #3d2b1f ;
            margin-bottom: 20px;
            
        }

        h2 {
            color: #7a4e2d ;
            border-bottom: 2px solid rgba(255,255,255,0.4);
            padding-bottom: 5px;
            margin: 25px 0 15px;
            
        }

        /* Frosted glass section panels */
        .glass {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        

        table { width: 100%; border-collapse: collapse; }

        th {
            background: rgba(60, 60, 60, 0.4);
            color: #4a3728 ;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            color: #2c2c2c ;
        }

        tr:nth-child(even) { background: rgba(255,255,255,0.07); }

        .approved { color: #90ee90; font-weight: bold; }
        .denied   { color: #ff9999; font-weight: bold; }

        /* Form styles */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
            margin-top: 10px;
        }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group label { font-size: 16px; color: #7a4e2d; font-weight: bold; }
        .form-group input, .form-group select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.15);
            color: #2c2c2c;
            font-family: inherit;
            font-size: 16px;
        }
        .form-group input::placeholder { color: rgba(44,44,44,0.5); }
        .form-group select option { background: #5a3e2b; color: #fff; }
        .btn-submit {
            padding: 8px 22px;
            background: rgba(184,115,51,0.5);
            border: 1px solid rgba(184,115,51,0.8);
            color: #3d2b1f;
            font-weight: bold;
            font-family: inherit;
            font-size: 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-submit:hover { background: rgba(184,115,51,0.75); }
        .form-success { color: #90ee90; margin-top: 10px; font-weight: bold; }
        .form-error   { color: #ff9999; margin-top: 10px; font-weight: bold; }

        /* Navigation pill buttons */
        .nav { margin-bottom: 25px; }

        .nav a {
            margin-right: 15px;
            color: #fffbf7 ;
            font-weight: bold;
            text-decoration: none;
            font-size: 16px;
            background: rgba(0,0,0,0.3);
            padding: 6px 14px;
            border-radius: 20px;
        }

        .nav a:hover { background: rgba(0,0,0,0.6); }
    </style>
</head>
<body>

    <div class="overlay"></div>

    <div class="content">

        <!-- Navigation -->
        <div class="nav">
            <a href="main.php"> Home</a>
            <a href="library.php"> Library</a>
            <a href="performance.php">Student Performance</a>
        </div>

        <h1>Parking Permit Management </h1>

        <!-- Add New Application Form -->
        <div class="glass">
            <h2>Add New Permit Application</h2>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_name">Full Name</label>
                        <input type="text" id="new_name" name="new_name" placeholder="e.g. John Doe" required
                               value="<?= isset($_POST['new_name']) && $formClass === 'form-error' ? htmlspecialchars($_POST['new_name']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="new_age">Age</label>
                        <input type="number" id="new_age" name="new_age" min="1" max="120" placeholder="e.g. 22" required
                               value="<?= isset($_POST['new_age']) && $formClass === 'form-error' ? (int)$_POST['new_age'] : '' ?>" style="width:90px;">
                    </div>
                    <div class="form-group">
                        <label for="new_type">Permit Type</label>
                        <select id="new_type" name="new_type">
                            <option value="Student" <?= (isset($_POST['new_type']) && $_POST['new_type'] === 'Student') ? 'selected' : '' ?>>Student (R450)</option>
                            <option value="Staff"   <?= (isset($_POST['new_type']) && $_POST['new_type'] === 'Staff')   ? 'selected' : '' ?>>Staff (R750)</option>
                            <option value="Visitor" <?= (isset($_POST['new_type']) && $_POST['new_type'] === 'Visitor') ? 'selected' : '' ?>>Visitor (R100)</option>
                        </select>
                    </div>
                    <button type="submit" name="add_application" class="btn-submit">&#43; Add Application</button>
                </div>
                <?php if ($formMessage): ?>
                    <p class="<?= $formClass ?>"><?= $formMessage ?></p>
                <?php endif; ?>
            </form>
        </div>
        <div class="glass">
            <h2>Permit Price List</h2>
            <table>
                <tr><th>Permit Type</th><th>Price</th></tr>
                <tr><td>Student</td><td>R<?= STUDENT_PERMIT_PRICE ?></td></tr>
                <tr><td>Staff</td><td>R<?= STAFF_PERMIT_PRICE ?></td></tr>
                <tr><td>Visitor</td><td>R<?= VISITOR_PERMIT_PRICE ?></td></tr>
                <tr><td colspan="2"><em>Maximum Capacity: <?= MAX_PARKING_CAPACITY ?> permits</em></td></tr>
            </table>
        </div>

        <!-- Approved permits -->
        <div class="glass">
            <h2>Approved Permits (<?= count($permitSales) ?> issued)</h2>
            <?php if (count($permitSales) === 0): ?>
                <p>No permits were issued.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>#</th>
                        <th>Applicant Name: </th>
                        <th>Permit Type: </th>
                        <th>Amount Paid: </th>
                    </tr>
                    <?php for ($i = 0; $i < count($permitSales); $i++): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $permitSales[$i]['name'] ?></td>
                        <td><?= $permitSales[$i]['type'] ?></td>
                        <td class="approved">R<?= number_format($permitSales[$i]['price'], 2) ?></td>
                    </tr>
                    <?php endfor; ?>
                </table>
            <?php endif; ?>
        </div>

        <!-- Denied applications -->
        <div class="glass">
            <h2>Denied Applications (<?= count($deniedApps) ?> rejected)</h2>
            <?php if (count($deniedApps) === 0): ?>
                <p>No applications were denied.</p>
            <?php else: ?>
                <table>
                    <tr><th>#</th><th>Applicant Name</th><th>Reason for Denial</th></tr>
                    <?php for ($i = 0; $i < count($deniedApps); $i++): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $deniedApps[$i]['FullName'] ?></td>
                        <td class="denied"><?= $deniedApps[$i]['reason'] ?></td>
                    </tr>
                    <?php endfor; ?>
                </table>
            <?php endif; ?>
        </div>

        <!-- Summary report -->
        <div class="glass">
            <h2>Parking Permits Summary Report</h2>
            <?php displayParkingSummary($permitSales); ?>
        </div>

    </div>

</body>
</html>
