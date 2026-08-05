<!--Full Name: Tilaya Reddy-->
<!--Internet Programming 621 Assignment-->

<?php
// Include shared functions
require_once 'functions.php';

// --------------------------------------------------------------
// FORM HANDLING - Add a new student
// --------------------------------------------------------------
$formMessage = '';
$formClass   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $newStudentName = trim($_POST['student_name']);
    $rawMarks       = trim($_POST['student_marks']);

    if ($newStudentName === '' || $rawMarks === '') {
        $formMessage = 'Please fill in the student name and marks.';
        $formClass   = 'form-error';
    } else {
        $formMessage = 'Student ' . htmlspecialchars($newStudentName) . ' added successfully!';
        $formClass   = 'form-success';
    }
}

// --------------------------------------------------------------
// DATA - Students with their marks in a nested array
// At least 4 students with at least 6 marks each as required
// Some marks are intentionally invalid to test graceful handling
// --------------------------------------------------------------
$students = array(
    array('name' => 'Savannah James',   'marks' => array(72, 68, 85, 90, 55, 78)),        // All valid
    array('name' => 'Evelyn Pieters',   'marks' => array(45, 38, 50, 42, 60, 35)),         // All valid - likely fail
    array('name' => 'Milan Pillay',     'marks' => array(92, 88, 95, 91, 'abc', 89)),      // 'abc' invalid - skipped
    array('name' => 'Rayhaan Suliman', 'marks' => array(76, 80, -5, 82, 78, 76)),        // -5 and 200 invalid - skipped
    array('name' => 'James Smith',     'marks' => array(55, 62, 70, 48, 65, 58)),         // All valid
    array('name' => 'Chad McClaine', 'marks' => array(30, 25, 40, 35, 28, 22)),         // All valid - likely fail
);

// Add new student from form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student']) && $formClass === 'form-success') {
    $markParts = explode(',', $_POST['student_marks']);
    $parsedMarks = array();
    for ($m = 0; $m < count($markParts); $m++) {
        $val = trim($markParts[$m]);
        $parsedMarks[] = is_numeric($val) ? (float)$val : $val;
    }
    $students[] = array(
        'name'  => htmlspecialchars(trim($_POST['student_name'])),
        'marks' => $parsedMarks
    );
}

// All logic is done above - HTML output starts below
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Performance Module</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Bookman Old Style", sans-serif;
            min-height: 100vh;
            background-image: url('background2.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #2c2c2c ;
        }

        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
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
            color: #7a4e2d ;
            border-bottom: 2px solid rgba(255,255,255,0.4);
            padding-bottom: 5px;
            margin: 25px 0 15px;
            
        }

        h3 {
            color: #7a4e2d;
            margin: 20px 0 10px;
            
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
            color: #ffe6ff ;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            color: #2c2c2c ;
        }

        tr:nth-child(even) { background: rgba(255,255,255,0.07); }

        /* Note box for invalid marks warning */
        .note {
            background: rgba(255, 165, 0, 0.2);
            border-left: 4px solid orange;
            padding: 12px;
            margin: 15px 0;
            border-radius: 6px;
        }

        .nav { margin-bottom: 25px; }

        .nav a {
            margin-right: 15px;
            color: #b87333;
            font-weight: bold;
            text-decoration: none;
            font-size: 16px;
            background: rgba(0,0,0,0.3);
            padding: 6px 14px;
            border-radius: 20px;
        }

        .nav a:hover { background: rgba(0,0,0,0.6); }

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
        .form-group input {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.15);
            color: #2c2c2c;
            font-family: inherit;
            font-size: 16px;
        }
        .form-group input::placeholder { color: rgba(44,44,44,0.5); }
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
    </style>
</head>
<body>

    <div class="overlay"></div>

    <div class="content">

        <!-- Navigation -->
        <div class="nav">
            <a href="main.php">Home</a>
            <a href="parking.php">Parking Permits</a>
            <a href="library.php">Library</a>
        </div>

        <h1> Student Performance</h1>

        <!-- Add New Student Form -->
        <div class="glass">
            <h2>Add New Student</h2>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group" style="flex:1; min-width:180px;">
                        <label for="student_name">Student Name</label>
                        <input type="text" id="student_name" name="student_name" placeholder="e.g. Thabo Nkosi" required
                               value="<?= ($formClass === 'form-error' && isset($_POST['student_name'])) ? htmlspecialchars($_POST['student_name']) : '' ?>">
                    </div>
                    <div class="form-group" style="flex:2; min-width:260px;">
                        <label for="student_marks">Marks (comma-separated, e.g. 75, 80, 65, 90, 55, 70)</label>
                        <input type="text" id="student_marks" name="student_marks"
                               placeholder="e.g. 75, 80, 65, 90, 55, 70" required
                               value="<?= ($formClass === 'form-error' && isset($_POST['student_marks'])) ? htmlspecialchars($_POST['student_marks']) : '' ?>">
                    </div>
                    <button type="submit" name="add_student" class="btn-submit">&#43; Add Student</button>
                </div>
                <p style="font-size:0.8rem; color:#7a4e2d; margin-top:6px;">
                    Invalid marks (non-numeric, below 0, or above 100) will be automatically excluded from calculations.
                </p>
                <?php if ($formMessage): ?>
                    <p class="<?= $formClass ?>"><?= $formMessage ?></p>
                <?php endif; ?>
            </form>
        </div>
        <div class="glass">
            <h2>Grading Criteria: </h2>
            <table style="width:40%;">
                <tr><th>Result</th><th>Average Required</th></tr>
                <tr><td style="color:#aaddff; font-weight:bold;">Distinction</td><td>75% and above</td></tr>
                <tr><td style="color:#90ee90; font-weight:bold;">Pass</td><td>50% to 74%</td></tr>
                <tr><td style="color:#ff9999; font-weight:bold;">Fail</td><td>Below 50%</td></tr>
            </table>
        </div>

        <!-- Invalid marks note -->
        <div class="note">
            <strong>&#9888; Note on Invalid Marks:</strong>
            Marks that are non-numeric, below 0, or above 100 are automatically excluded.
            Only valid marks between 0 and 100 are used in calculations.
        </div>

        <!-- Performance report and class statistics using shared function -->
        <div class="glass">
            <?php generatePerformanceReport($students); ?>
        </div>

        <!-- Legend -->
        <div class="glass">
            <h2>Legend</h2>
            <p>
                <span style="color:#aaddff; font-weight:bold;">&#9632; Distinction (75%+)</span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <span style="color:#90ee90; font-weight:bold;">&#9632; Pass (50%-74%)</span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <span style="color:#ff9999; font-weight:bold;">&#9632; Fail (below 50%)</span>
            </p>
        </div>

    </div>

</body>
</html>
