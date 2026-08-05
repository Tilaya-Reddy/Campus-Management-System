<!--Full Name: Tilaya Reddy-->
<!--Internet Programming 621 Assignment-->

<?php

// --------------------------------------------------------------
// PARKING FUNCTIONS
// --------------------------------------------------------------

// Check if age is 18 or older - returns true or false
function isValidAge($age) {
    return $age >= 18;
}

// Return the price of a permit based on its type
function getPermitPrice($type) {
    if ($type === 'Student') return 450;
    if ($type === 'Staff')   return 750;
    if ($type === 'Visitor') return 100;
    return null; // Unknown type returns null
}

// Display a summary table of all permits sold and total revenue
function displayParkingSummary($permitSales) {
    // Start all counters at zero
    $studentCount = 0;
    $staffCount   = 0;
    $visitorCount = 0;
    $studentRev   = 0;
    $staffRev     = 0;
    $visitorRev   = 0;
    $totalRevenue = 0;

    // Loop through each permit sale and add to correct counter
    for ($i = 0; $i < count($permitSales); $i++) {
        $type  = $permitSales[$i]['type'];
        $price = $permitSales[$i]['price'];

        if ($type === 'Student') {
            $studentCount++;
            $studentRev += $price;
        } elseif ($type === 'Staff') {
            $staffCount++;
            $staffRev += $price;
        } elseif ($type === 'Visitor') {
            $visitorCount++;
            $visitorRev += $price;
        }

        $totalRevenue += $price;
    }
    ?>
    <table>
        <tr>
            <th>Permit Type</th>
            <th>Permits Sold</th>
            <th>Revenue</th>
        </tr>
        <tr>
            <td>Student</td>
            <td><?= $studentCount ?></td>
            <td>R<?= number_format($studentRev, 2) ?></td>
        </tr>
        <tr>
            <td>Staff</td>
            <td><?= $staffCount ?></td>
            <td>R<?= number_format($staffRev, 2) ?></td>
        </tr>
        <tr>
            <td>Visitor</td>
            <td><?= $visitorCount ?></td>
            <td>R<?= number_format($visitorRev, 2) ?></td>
        </tr>
        <tr style="background:rgba(255,255,255,0.15); font-weight:bold;">
            <td>TOTAL</td>
            <td><?= ($studentCount + $staffCount + $visitorCount) ?></td>
            <td>R<?= number_format($totalRevenue, 2) ?></td>
        </tr>
    </table>
    <?php
}

// --------------------------------------------------------------
// LIBRARY FUNCTIONS
// --------------------------------------------------------------

// Calculate late fine based on book category and number of days late
function calculateFine($category, $daysLate) {
    if ($daysLate <= 0) return 0;

    if ($category === 'Textbook')        return 5  * $daysLate; // R5 per day
    if ($category === 'Journal')         return 3  * $daysLate; // R3 per day
    if ($category === 'Reference Book')  return 10 * $daysLate; // R10 per day

    return 0;
}

// Allow a user to borrow a book if their fines are within the limit
function borrowBook(&$users, $userId, $book) {
    if (!isset($users[$userId])) {
        return "ERROR: User " . $userId . " not found.";
    }

    if ($users[$userId]['totalFines'] > 200) {
        return "BLOCKED: " . $users[$userId]['name'] . " has R" . number_format($users[$userId]['totalFines'], 2) . " in fines (limit is R200).";
    }

    $users[$userId]['borrowedBooks'][] = $book;
    return "SUCCESS: " . $users[$userId]['name'] . " borrowed '" . $book['title'] . "' (due: " . $book['dueDate'] . ").";
}

// Process a book return and apply a fine if it is late
function returnBook(&$users, $userId, $bookTitle, $returnDate) {
    if (!isset($users[$userId])) {
        return "ERROR: User " . $userId . " not found.";
    }

    for ($i = 0; $i < count($users[$userId]['borrowedBooks']); $i++) {
        if ($users[$userId]['borrowedBooks'][$i]['title'] === $bookTitle) {

            // Use timestamps to calculate days late
            $dueTimestamp    = strtotime($users[$userId]['borrowedBooks'][$i]['dueDate']);
            $returnTimestamp = strtotime($returnDate);
            $secondsDiff     = $returnTimestamp - $dueTimestamp;
            $daysLate        = ($secondsDiff > 0) ? (int)ceil($secondsDiff / 86400) : 0;

            // Calculate fine and add to user total
            $fine = calculateFine($users[$userId]['borrowedBooks'][$i]['category'], $daysLate);
            $users[$userId]['totalFines'] += $fine;

            // Save return record to history
            $users[$userId]['returnHistory'][] = array(
                'title'      => $bookTitle,
                'returnDate' => $returnDate,
                'daysLate'   => $daysLate,
                'fine'       => $fine
            );

            // Remove from borrowed list
            array_splice($users[$userId]['borrowedBooks'], $i, 1);

            if ($fine > 0) {
                return "RETURNED: '" . $bookTitle . "' - " . $daysLate . " day(s) late. Fine: R" . number_format($fine, 2) . ".";
            } else {
                return "RETURNED: '" . $bookTitle . "' - on time. No fine.";
            }
        }
    }

    return "ERROR: Book '" . $bookTitle . "' not found in " . $users[$userId]['name'] . "'s list.";
}

// Print a formatted summary of a single user's library account
function printUserSummary($user, $userId) {
    // Safely initialise keys in case they are missing
    if (!isset($user['borrowedBooks'])) $user['borrowedBooks'] = array();
    if (!isset($user['returnHistory'])) $user['returnHistory'] = array();
    if (!isset($user['totalFines']))    $user['totalFines']    = 0;

    $fineClass = ($user['totalFines'] > 200) ? 'fine-bad' : 'fine-ok';
    ?>
    <div class="user-card">
        <h4><?= $user['name'] ?> (ID: <?= $userId ?>)</h4>

        <strong>Currently Borrowed:</strong>
        <?php if (count($user['borrowedBooks']) === 0): ?>
            <p>None currently borrowed.</p>
        <?php else: ?>
            <ul>
                <?php for ($i = 0; $i < count($user['borrowedBooks']); $i++): ?>
                    <li>
                        <?= $user['borrowedBooks'][$i]['title'] ?>
                        (<?= $user['borrowedBooks'][$i]['category'] ?>)
                        - Due: <?= $user['borrowedBooks'][$i]['dueDate'] ?>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>

        <br><strong>Return History:</strong>
        <?php if (count($user['returnHistory']) === 0): ?>
            <p>No books returned yet.</p>
        <?php else: ?>
            <ul>
                <?php for ($i = 0; $i < count($user['returnHistory']); $i++): ?>
                    <li>
                        <?= $user['returnHistory'][$i]['title'] ?>
                        | Returned: <?= $user['returnHistory'][$i]['returnDate'] ?>
                        | Days Late: <?= $user['returnHistory'][$i]['daysLate'] ?>
                        | Fine: R<?= number_format($user['returnHistory'][$i]['fine'], 2) ?>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>

        <br>
        <strong>Total Outstanding Fines:
            <span class="<?= $fineClass ?>">R<?= number_format($user['totalFines'], 2) ?></span>
        </strong>
    </div>
    <?php
}

// --------------------------------------------------------------
// PERFORMANCE FUNCTIONS
// --------------------------------------------------------------

// Calculate average from an array of marks, ignoring invalid ones
function calculateAverage($marks) {
    $validMarks = array();

    for ($i = 0; $i < count($marks); $i++) {
        // Only keep marks that are numeric and between 0 and 100
        if (is_numeric($marks[$i]) && $marks[$i] >= 0 && $marks[$i] <= 100) {
            $validMarks[] = (float)$marks[$i];
        }
    }

    if (count($validMarks) === 0) return 0; // Avoid division by zero

    return array_sum($validMarks) / count($validMarks);
}

// Assign a grade result based on the average mark
function assignResult($average) {
    if ($average >= 75) return 'Distinction'; // 75 and above
    if ($average >= 50) return 'Pass';        // 50 to 74
    return 'Fail';                            // below 50
}

// Generate the full student performance report with class statistics
function generatePerformanceReport($students) {
    $averages = array(); // Store each student's average for class stats

    // First pass - calculate all averages
    for ($i = 0; $i < count($students); $i++) {
        $averages[] = calculateAverage($students[$i]['marks']);
    }

    // Class statistics
    $highest    = max($averages);
    $lowest     = min($averages);
    $classAvg   = array_sum($averages) / count($averages);
    $topIndex   = array_search($highest, $averages);
    $topStudent = $students[$topIndex]['name'];
    ?>

    <h3>Student Performance Report</h3>
    <table>
        <tr>
            <th>Student Name</th>
            <th>All Marks</th>
            <th>Valid Marks Used</th>
            <th>Average</th>
            <th>Result</th>
        </tr>
        <?php for ($i = 0; $i < count($students); $i++):
            $name   = $students[$i]['name'];
            $marks  = $students[$i]['marks'];

            // Collect valid marks for display
            $validMarks = array();
            for ($j = 0; $j < count($marks); $j++) {
                if (is_numeric($marks[$j]) && $marks[$j] >= 0 && $marks[$j] <= 100) {
                    $validMarks[] = $marks[$j];
                }
            }

            $avg    = $averages[$i];
            $result = assignResult($avg);

            // Set colour based on result
            if ($result === 'Distinction') $color = '#aaddff';
            elseif ($result === 'Pass')    $color = '#90ee90';
            else                           $color = '#ff9999';
        ?>
        <tr>
            <td><?= $name ?></td>
            <td><?= implode(', ', $marks) ?></td>
            <td><?= implode(', ', $validMarks) ?></td>
            <td><?= number_format($avg, 2) ?>%</td>
            <td style="color:<?= $color ?>; font-weight:bold;"><?= $result ?></td>
        </tr>
        <?php endfor; ?>
    </table>

    <h3>Class Statistics</h3>
    <table style="width:50%;">
        <tr>
            <th>Statistic</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Class Average</td>
            <td><?= number_format($classAvg, 2) ?>%</td>
        </tr>
        <tr>
            <td>Highest Average</td>
            <td><?= number_format($highest, 2) ?>%</td>
        </tr>
        <tr>
            <td>Lowest Average</td>
            <td><?= number_format($lowest, 2) ?>%</td>
        </tr>
        <tr style="background:rgba(255,255,255,0.15);">
            <td><strong>&#127942; Top Performer</strong></td>
            <td><strong><?= $topStudent ?> (<?= number_format($highest, 2) ?>%)</strong></td>
        </tr>
    </table>
    <?php
}
?>
