<!--Full Name: Tilaya Reddy-->
<!--Internet Programming 621 Assignment-->

<?php

// Define constants FIRST before including functions
define('TEXTBOOK_FINE_RATE',  5);   // R5 per day late for textbooks
define('JOURNAL_FINE_RATE',   3);   // R3 per day late for journals
define('REFERENCE_FINE_RATE', 10);  // R10 per day late for reference books
define('MAX_FINE_THRESHOLD',  200); // Users with fines above R200 cannot borrow

// Include shared functions after constants are defined
require_once 'functions.php';

// --------------------------------------------------------------
// FORM HANDLING
// --------------------------------------------------------------
$formMessage = '';
$formClass   = '';
$formSection = '';  // tracks which form was submitted

// Add new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $formSection = 'user';
    $newUserId   = trim($_POST['new_user_id']);
    $newUserName = trim($_POST['new_user_name']);
    if ($newUserId === '' || $newUserName === '') {
        $formMessage = 'Please fill in both User ID and Name.';
        $formClass   = 'form-error';
    } elseif (isset($GLOBALS['users'][$newUserId])) {
        $formMessage = 'User ID ' . htmlspecialchars($newUserId) . ' already exists.';
        $formClass   = 'form-error';
    } else {
        $formMessage = 'User ' . htmlspecialchars($newUserName) . ' (' . htmlspecialchars($newUserId) . ') added successfully!';
        $formClass   = 'form-success';
    }
}

// Borrow a book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_book'])) {
    $formSection = 'borrow';
    $bUserId     = trim($_POST['borrow_user_id']);
    $bTitle      = trim($_POST['borrow_title']);
    $bCategory   = $_POST['borrow_category'];
    $bBorrow     = $_POST['borrow_date'];
    $bDue        = $_POST['due_date'];
    if ($bUserId === '' || $bTitle === '' || $bBorrow === '' || $bDue === '') {
        $formMessage = 'Please fill in all fields.';
        $formClass   = 'form-error';
    } else {
        $formMessage = 'Borrow request queued for processing.';
        $formClass   = 'form-success';
    }
}

// Return a book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_book'])) {
    $formSection = 'return';
    $rUserId     = trim($_POST['return_user_id']);
    $rTitle      = trim($_POST['return_title']);
    $rDate       = $_POST['return_date'];
    if ($rUserId === '' || $rTitle === '' || $rDate === '') {
        $formMessage = 'Please fill in all fields.';
        $formClass   = 'form-error';
    } else {
        $formMessage = 'Return request queued for processing.';
        $formClass   = 'form-success';
    }
}

// --------------------------------------------------------------
// DATA - Users stored in a nested associative array
// --------------------------------------------------------------
$users = array(
    'U1001' => array(
        'name'          => 'Savannah James',
        'borrowedBooks' => array(),
        'returnHistory' => array(),
        'totalFines'    => 0
    ),
    'U1002' => array(
        'name'          => 'Madelyn Chetty',
        'borrowedBooks' => array(),
        'returnHistory' => array(),
        'totalFines'    => 0
    ),
    'U1003' => array(
        'name'          => 'James Smith',
        'borrowedBooks' => array(),
        'returnHistory' => array(),
        'totalFines'    => 210  // Pre-existing fine above R200 - will be blocked
    ),
    'U1004' => array(
        'name'          => 'Milan Pillay',
        'borrowedBooks' => array(),
        'returnHistory' => array(),
        'totalFines'    => 0
    ),
);

// Apply form submissions to data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user']) && $formClass === 'form-success') {
        $users[htmlspecialchars(trim($_POST['new_user_id']))] = array(
            'name'          => htmlspecialchars(trim($_POST['new_user_name'])),
            'borrowedBooks' => array(),
            'returnHistory' => array(),
            'totalFines'    => 0
        );
    }
    if (isset($_POST['borrow_book']) && $formClass === 'form-success') {
        $borrowMessages[] = borrowBook($users, htmlspecialchars(trim($_POST['borrow_user_id'])), array(
            'title'      => htmlspecialchars(trim($_POST['borrow_title'])),
            'category'   => $_POST['borrow_category'],
            'borrowDate' => $_POST['borrow_date'],
            'dueDate'    => $_POST['due_date']
        ));
    }
    if (isset($_POST['return_book']) && $formClass === 'form-success') {
        $returnMessages[] = returnBook($users, htmlspecialchars(trim($_POST['return_user_id'])), htmlspecialchars(trim($_POST['return_title'])), $_POST['return_date']);
    }
}

// --------------------------------------------------------------
// PROCESSING - Borrow and return books
// --------------------------------------------------------------
$borrowMessages = array();
$returnMessages = array();

// U1001 borrows a Textbook - should succeed
$borrowMessages[] = borrowBook($users, 'U1001', array(
    'title' => 'Introduction to Algorithms', 'category' => 'Textbook',
    'borrowDate' => '2025-01-01', 'dueDate' => '2025-01-15'
));

// U1001 borrows a Journal - should succeed
$borrowMessages[] = borrowBook($users, 'U1001', array(
    'title' => 'IEEE Software Engineering Journal', 'category' => 'Journal',
    'borrowDate' => '2025-01-05', 'dueDate' => '2025-01-12'
));

// U1002 borrows a Reference Book - should succeed
$borrowMessages[] = borrowBook($users, 'U1002', array(
    'title' => 'Oxford Dictionary of Computing', 'category' => 'Reference Book',
    'borrowDate' => '2025-01-03', 'dueDate' => '2025-01-10'
));

// U1003 tries to borrow - should be BLOCKED (fines over R200)
$borrowMessages[] = borrowBook($users, 'U1003', array(
    'title' => 'Database Systems', 'category' => 'Textbook',
    'borrowDate' => '2025-01-06', 'dueDate' => '2025-01-20'
));

// U1004 borrows a Textbook - should succeed
$borrowMessages[] = borrowBook($users, 'U1004', array(
    'title' => 'Web Development with PHP', 'category' => 'Textbook',
    'borrowDate' => '2025-01-02', 'dueDate' => '2025-01-16'
));

// U1002 borrows another Textbook - should succeed
$borrowMessages[] = borrowBook($users, 'U1002', array(
    'title' => 'Computer Networks', 'category' => 'Textbook',
    'borrowDate' => '2025-01-04', 'dueDate' => '2025-01-18'
));

// U1001 returns Textbook 5 days late (due 15th, returned 20th)
$returnMessages[] = returnBook($users, 'U1001', 'Introduction to Algorithms', '2025-01-20');

// U1001 returns Journal on time (due 12th, returned 12th)
$returnMessages[] = returnBook($users, 'U1001', 'IEEE Software Engineering Journal', '2025-01-12');

// U1002 returns Reference Book 3 days late (due 10th, returned 13th)
$returnMessages[] = returnBook($users, 'U1002', 'Oxford Dictionary of Computing', '2025-01-13');

// U1004 returns Textbook 2 days late (due 16th, returned 18th)
$returnMessages[] = returnBook($users, 'U1004', 'Web Development with PHP', '2025-01-18');

// U1002 returns Computer Networks on time (due 18th, returned 18th)
$returnMessages[] = returnBook($users, 'U1002', 'Computer Networks', '2025-01-18');

// All logic is done above - HTML output starts below
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Borrowing </title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Bookman Old Style", sans-serif;
            min-height: 100vh;
            background-image: url('https://i.pinimg.com/1200x/f0/ae/39/f0ae3935bfd64c7c7493c2fb959f4fba.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #2c2c2c;
        }

        .content {
            position: relative;
            z-index: 1;
            padding: 30px;
        }

        h1 {
            color: #3d2b1f;
            margin-bottom: 20px;
            
        }

        h2 {
            color: #7a4e2d;
            border-bottom: 2px solid rgba(255,255,255,0.4);
            padding-bottom: 5px;
            margin: 25px 0 15px;
            
        }

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
            background: rgba(47, 47, 47, 0.4);
            color: #4a3728;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            color: ##2c2c2c;
        }

        tr:nth-child(even) { background: rgba(255,255,255,0.07); }

        /* Message styling for borrow/return results */
        .msg {
            padding: 10px;
            margin: 6px 0;
            border-radius: 6px;
            border-left: 4px solid rgba(255,255,255,0.3);
            background: rgba(0,0,0,0.2);
        }

        .success { border-color: #90ee90; }
        .blocked { border-color: #ff9999; color: #ff9999; font-weight: bold; }
        .warning { border-color: orange; }
        .error   { border-color: red; color: #ff9999; }

        /* User account card styling */
        .user-card {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
        }

        .user-card h4 { color: #90ee90; margin-bottom: 10px; }
        .user-card ul  { padding-left: 20px; }
        .user-card li  { margin: 4px 0; }

        .fine-ok  { color: #90ee90; font-weight: bold; }
        .fine-bad { color: #ff9999; font-weight: bold; }

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
        .forms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        @media (max-width: 700px) { .forms-grid { grid-template-columns: 1fr; } }

        .nav { margin-bottom: 25px; }

        .nav a {
            margin-right: 15px;
            color: #fffbf7;
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
            <a href="main.php">Home</a>
            <a href="parking.php">Parking</a>
            <a href="performance.php">Student Performance</a>
        </div>

        <h1>Library Borrowing and Fine Management </h1>

        <!-- ===== FORMS SECTION ===== -->
        <div class="forms-grid">

            <!-- Add New User -->
            <div class="glass">
                <h2>Add New User</h2>
                <form method="POST" action="">
                    <div class="form-row" style="flex-direction:column; align-items:flex-start;">
                        <div class="form-group" style="width:100%;">
                            <label for="new_user_id">User ID (e.g. U1005)</label>
                            <input type="text" id="new_user_id" name="new_user_id" placeholder="U1005" required
                                   value="<?= ($formSection === 'user' && $formClass === 'form-error') ? htmlspecialchars($_POST['new_user_id']) : '' ?>">
                        </div>
                        <div class="form-group" style="width:100%;">
                            <label for="new_user_name">Full Name</label>
                            <input type="text" id="new_user_name" name="new_user_name" placeholder="e.g. Thabo Nkosi" required
                                   value="<?= ($formSection === 'user' && $formClass === 'form-error') ? htmlspecialchars($_POST['new_user_name']) : '' ?>">
                        </div>
                        <button type="submit" name="add_user" class="btn-submit">&#43; Add User</button>
                    </div>
                    <?php if ($formSection === 'user' && $formMessage): ?>
                        <p class="<?= $formClass ?>"><?= $formMessage ?></p>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Borrow a Book -->
            <div class="glass">
                <h2>Borrow a Book</h2>
                <form method="POST" action="">
                    <div class="form-row" style="flex-direction:column; align-items:flex-start;">
                        <div class="form-group" style="width:100%;">
                            <label for="borrow_user_id">User ID</label>
                            <input type="text" id="borrow_user_id" name="borrow_user_id" placeholder="e.g. U1001" required
                                   value="<?= ($formSection === 'borrow' && $formClass === 'form-error') ? htmlspecialchars($_POST['borrow_user_id']) : '' ?>">
                        </div>
                        <div class="form-group" style="width:100%;">
                            <label for="borrow_title">Book Title</label>
                            <input type="text" id="borrow_title" name="borrow_title" placeholder="e.g. Clean Code" required
                                   value="<?= ($formSection === 'borrow' && $formClass === 'form-error') ? htmlspecialchars($_POST['borrow_title']) : '' ?>">
                        </div>
                        <div class="form-group" style="width:100%;">
                            <label for="borrow_category">Category</label>
                            <select id="borrow_category" name="borrow_category">
                                <option value="Textbook">Textbook (R5/day late)</option>
                                <option value="Journal">Journal (R3/day late)</option>
                                <option value="Reference Book">Reference Book (R10/day late)</option>
                            </select>
                        </div>
                        <div class="form-row" style="width:100%;">
                            <div class="form-group" style="flex:1;">
                                <label for="borrow_date">Borrow Date</label>
                                <input type="date" id="borrow_date" name="borrow_date" required
                                       value="<?= ($formSection === 'borrow' && $formClass === 'form-error') ? htmlspecialchars($_POST['borrow_date']) : '' ?>">
                            </div>
                            <div class="form-group" style="flex:1;">
                                <label for="due_date">Due Date</label>
                                <input type="date" id="due_date" name="due_date" required
                                       value="<?= ($formSection === 'borrow' && $formClass === 'form-error') ? htmlspecialchars($_POST['due_date']) : '' ?>">
                            </div>
                        </div>
                        <button type="submit" name="borrow_book" class="btn-submit">&#43; Borrow Book</button>
                    </div>
                    <?php if ($formSection === 'borrow' && $formMessage): ?>
                        <p class="<?= $formClass ?>"><?= $formMessage ?></p>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Return a Book -->
            <div class="glass">
                <h2>Return a Book</h2>
                <form method="POST" action="">
                    <div class="form-row" style="flex-direction:column; align-items:flex-start;">
                        <div class="form-group" style="width:100%;">
                            <label for="return_user_id">User ID</label>
                            <input type="text" id="return_user_id" name="return_user_id" placeholder="e.g. U1001" required
                                   value="<?= ($formSection === 'return' && $formClass === 'form-error') ? htmlspecialchars($_POST['return_user_id']) : '' ?>">
                        </div>
                        <div class="form-group" style="width:100%;">
                            <label for="return_title">Book Title</label>
                            <input type="text" id="return_title" name="return_title" placeholder="Must match borrowed title exactly" required
                                   value="<?= ($formSection === 'return' && $formClass === 'form-error') ? htmlspecialchars($_POST['return_title']) : '' ?>">
                        </div>
                        <div class="form-group" style="width:100%;">
                            <label for="return_date">Return Date</label>
                            <input type="date" id="return_date" name="return_date" required
                                   value="<?= ($formSection === 'return' && $formClass === 'form-error') ? htmlspecialchars($_POST['return_date']) : '' ?>">
                        </div>
                        <button type="submit" name="return_book" class="btn-submit">&#8617; Return Book</button>
                    </div>
                    <?php if ($formSection === 'return' && $formMessage): ?>
                        <p class="<?= $formClass ?>"><?= $formMessage ?></p>
                    <?php endif; ?>
                </form>
            </div>

        </div>
        <!-- ===== END FORMS SECTION ===== -->
        <div class="glass">
            <h2>Fine Rates and Rules</h2>
            <table style="width:50%;">
                <tr><th>Book Category</th><th>Fine Rate</th></tr>
                <tr><td>Textbook</td><td>R<?= TEXTBOOK_FINE_RATE ?> per day</td></tr>
                <tr><td>Journal</td><td>R<?= JOURNAL_FINE_RATE ?> per day</td></tr>
                <tr><td>Reference Book</td><td>R<?= REFERENCE_FINE_RATE ?> per day</td></tr>
                <tr><td colspan="2"><em>Users with fines above R<?= MAX_FINE_THRESHOLD ?> cannot borrow new books.</em></td></tr>
            </table>
        </div>

        <!-- Borrowing transactions -->
        <div class="glass">
            <h2>Borrowing Transactions</h2>
            <?php for ($i = 0; $i < count($borrowMessages); $i++):
                $msg = $borrowMessages[$i];
                if (strpos($msg, 'BLOCKED') !== false)     { $class = 'blocked'; }
                elseif (strpos($msg, 'ERROR') !== false)   { $class = 'error'; }
                else                                        { $class = 'success'; }
            ?>
                <div class="msg <?= $class ?>"><?= $msg ?></div>
            <?php endfor; ?>
        </div>

        <!-- Return transactions -->
        <div class="glass">
            <h2> Return Transactions</h2>
            <?php for ($i = 0; $i < count($returnMessages); $i++):
                $msg = $returnMessages[$i];
                if (strpos($msg, 'ERROR') !== false)          { $class = 'error';  }
                elseif (strpos($msg, 'Fine: R') !== false)    { $class = 'warning';  }
                else                                           { $class = 'success'; }
            ?>
                <div class="msg <?= $class ?>"> <?= $msg ?></div>
            <?php endfor; ?>
        </div>

        <!-- User account summaries -->
        <h2> User Account Summaries</h2>
        <?php
        $userIds = array_keys($users);
        for ($i = 0; $i < count($userIds); $i++):
            $uid  = $userIds[$i];
            printUserSummary($users[$uid], $uid);
        endfor;
        ?>

    </div>

</body>
</html>
