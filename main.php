<!--Full Name: Tilaya Reddy-->
<!--Student Number: 402504107-->
<!--Internet Programming 621 Assignment-->
<!--http://localhost/IPR_ASSIGNEMENT/main.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Management System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* Full page background image */
        body {
            font-family: "Bookman Old Style", sans-serif;
            min-height: 100vh;
            background-image: url('background2.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #3d2b1f;
        }

        /* Dark overlay to make text readable over the image */
        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.45);
            z-index: 0;
        }

        /* All content sits above the overlay */
        .content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .header { text-align: center; margin-bottom: 40px; }

        .header h1 {
            font-size: 3.0rem;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1rem;
            opacity: 0.9;
            
        }

        /* Cards container */
        .cards {
            display: flex;
            letter-spacing: 1.5px;
            gap: 25px;
            flex-wrap: wrap;
            justify-content: center;
        }

        /* Frosted glass card effect */
        .card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #2c2c2c ;
            border-radius: 14px;
            padding: 35px 30px;
            width: 240px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 35px rgba(0,0,0,0.5);
            background: rgba(255, 255, 255, 0.25);
        }

        .card .icon { font-size: 3rem; margin-bottom: 15px; }
        .card h2    { font-size: 1.1rem; margin-bottom: 8px; }
        .card p     { font-size: 0.85rem; opacity: 0.85; }

        /* Mark badge on each card */
        .badge {
            display: inline-block;
            background: rgba(0,0,0,0.4);
            color: white;
            font-size: 0.75rem;
            padding: 4px 14px;
            border-radius: 20px;
            margin-top: 12px;
        }

    </style>
</head>
<body>

    <!-- Dark overlay sits behind everything -->
    <div class="overlay"></div>

    <!-- All visible content -->
    <div class="content">

        <div class="header">
            <h1>Welcome to the Campus Management System</h1>
            <h3>Please select what action you would like to proceed with: </h3>
        </div>

        <div class="cards">

            <!-- Parking module card -->
            <a href="parking.php" class="card">
                <h2>Parking Permit Module</h2>
                <p>Issue and manage Student, Staff, and Visitor parking permits with age validation.</p>
            </a>

            <!-- Library module card -->
            <a href="library.php" class="card">
                <h2>Library Borrowing Module</h2>
                <p>Manage book loans, track due dates, and calculate late return fines.</p>
            </a>

            <!-- Performance module card -->
            <a href="performance.php" class="card">
                <h2>Performance Module</h2>
                <p>Analyse student marks, assign grades, and generate class statistics.</p>
            </a>

        </div>


    </div>

</body>
</html>



   