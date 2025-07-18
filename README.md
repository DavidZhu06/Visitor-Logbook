# Digital Visitor Kiosk (Web App) – IDCI IT Internship Project

This was my first time building a web application from the ground up, and it’s been an incredibly valuable learning experience. Throughout this project, I gained a deeper understanding of how different programming tools and systems work together—from front-end development and responsive design, to backend scripting, server configuration, and database management. Any feedback is appreciated, I'm still learning as I go!

The Digital Visitor Kiosk is an interactive web application with a user friendly design that offers a more secure and automated method for storing visitor data electronically, while providing a seamless and professional first impression for visitors. Built with a responsive frontend and up-to-date backend, this solution enables businesses to automate guest registration, manage visitor logs efficiently, and ensure compliance through NDAs and parking information notices.

Below are some **KEY FEATURES**: 
1. Responsive Design
  - Fully responsive layout adapts seamlessly to desktops and tablets of all dimensions.
2. Automatic Email Notifications:
  - Sends confirmation emails to guests with a copy of their form input using the PHPMailer and mPDF PHP libraries. PDF Form copies are auto-generated and emailed from the official IDCI Mail System - mailsystem@idci.ca.
3. NDA & Parking Info Pages
  - Updated, concise formats with available language toggle (EN/FR), with required signature capture. 
4. Visitor Data Storage
  - All visitor form data stored in MySQL database - admin can easily access and manage data via MySQL Workbench with option to export logs to CSV.     Vistor signatures are stored as Base64 strings, and copies of visitors' form submissions are also saved.
  
**Tech Stack**:
Frontend: HTML, CSS, JavaScript
Backend: PHP 
Automatic Email & PDF: PHPMailer & mPDF libraries
Database: MySQL (accessible via MySQL Workbench for admin without coding experience to use)
Server: Microsoft IIS

**Requirements**:
PHP Version 8.4.x or higher
MySQL Server 8.0.41 or higher
  - You must also have a properly configured MySQL database with the required tables already created exactly like that in the PHP files (otherwise      will fail to post the data) , as well as a valid email account set up for sending emails. 
IIS Web Server with FastCGI enabled
PHPMailer Library: https://github.com/phpmailer
mPDF Library: https://github.com/mpdf/mpdf
Composer (for mPDF installation)

**Installation**:
[View Installation PDF (may or may not be entirely accurate)](./docs/Installation of Visitor Logbook.pdf)

**CONTACT**:
For questions or feedback, please contact:
**David Zhu**
david.zhu@idci.ca


