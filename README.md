# Campus Management System

A PHP application that handles three campus admin tasks in one place: **parking tickets**, **library management**, and **student marks**. Built as a second-year university project.

## Features

- **Parking:** issue and manage parking tickets (`parking.php`)
- **Library:** manage library records and loans (`library.php`)
- **Performance:** capture student marks, calculate the class average and identify the first in class (`performance.php`)
- Shared helper functions in `functions.php`, with `main.php` as the entry point

<img width="1600" height="737" alt="WhatsApp Image 2026-10-29 at 10 15 42" src="https://github.com/user-attachments/assets/ec2a7f86-9c9e-413f-9207-c163e406f196" />
<img width="1600" height="668" alt="WhatsApp Image 2026-10-29 at 10 33 02" src="https://github.com/user-attachments/assets/c1758e62-a918-4754-bcc1-8f205eac2874" />
<img width="1600" height="671" alt="WhatsApp Image 2026-10-29 at 10 32 48" src="https://github.com/user-attachments/assets/54aa0941-7427-4f37-ba46-98e1ea08b2fe" />
<img width="1600" height="666" alt="WhatsApp Image 2026-10-29 at 10 33 15" src="https://github.com/user-attachments/assets/6f2140e8-b8b1-444e-9572-a60e7171a7e7" />


## Requirements

- PHP 8.0 or newer

## Run it

```bash
git clone https://github.com/Tilaya-Reddy/Campus-Management-System.git
cd Campus-Management-System

php main.php                      # if it's a terminal app
# or copy the folder into xampp/htdocs and open http://localhost/Campus-Management-System/main.php
```

## Structure

```
├── main.php          entry point / main menu
├── parking.php       parking ticket module
├── library.php       library module
├── performance.php   marks, averages, first in class
└── functions.php     shared helper functions
```

## What I practised

- Splitting a program into modules with shared helper functions
- Input validation and processing in PHP

## Licence

Student project, shared for learning and portfolio purposes.
