<h1>Event Management </h1>

<p>
Event Management Platform that has pesapal enabled
</p>

Current Features
---
 - Beautiful mobile friendly event pages
 - Easy attendee management - Refunds, Messaging etc.
 - Data export - attendees list to XLS, CSV etc.
 - Generate print friendly attendee list
 - Ability to manage unlimited organisers / events
 - Manage multiple organisers
 - Real-time event statistics
 - Customizable event pages
 - Multiple currency support
 - Quick and easy checkout process
 - Customizable tickets - with QR codes, organiser logos etc.
 - Fully brandable - Have your own logos on tickets etc.
 - Affiliate tracking
    - track sales volume / number of visits generated etc.
 - Widget support - embed ticket selling widget into existing websites / WordPress blogs
 - Social sharing
 - Support multiple payment gateways - Stripe, PayPal & Coinbase so far, with more being added
 - Support for offline payments
 - Refund payments - partial refund & full refunds
 - Ability to add service charge to tickets
 - Messaging - eg. Email all attendees with X ticket
 - Public event listings page for organisers
 - Ability to ask custom questions during checkout
 - Browser based QR code scanner for door management

Upcoming changes in v2.0.0
---

 - Localisation
 - Increased test coverage
 - Laravel 5.4
 - IOS/Android check-in / door management apps
 - Coupon/discount code support
 - Support for more payment providers
 - WordPress Plug-in


 <h3>Installation</h3>
 Do a git clone to this repository
 After cloning to the following

 #Do a composer install, This Installs Dependencies that are in the composer.json file

 - composer install

 #Copy the .env.example file to .env and make the necessary changes to the environment file, including adding the database, and pesapal configurations, remembering that the default database is mysql

 cp .env.example .env

#Install the platform, this will prompt you of your first_name, lastname, email, and password, this is the admin account.

php artisan attendize:install

YOU CAN NOW ACCESS THE PLATFORM ON YOUR ROOT FOLDER

There are two ways of accessing.

1. Through Apache server

[server_root]/[application_folder]/public

example is

localhost/EventManagement/public

2. Through PHP artisan server , On Which you need to run

php artisan serve

And
