Backup Cleaner
=======================
* Maintained by: Alex Walker, University of Glasgow
* License: GPL v3

Description
===========

In Moodle, it's very easy for users to create course backups and then forget about them. Over time, this can consume a large amount of disk space.

Backup Cleaner is a very simple plugin that finds course backups older than a certain age, and deletes them. That's all it does. It's very simple.

There are two options in Backup Cleaner:

* How long you want to keep backups for, from 1 month up to 10 years. The default is 10 years.
* How many backups you want to delete every time the scheduled task runs, from 1 up to 1,000. The default is 10.

Backup Cleaner is a "set it and forget it" plugin. You install it and set it up, and it silently does its job in the background. A scheduled task runs every so often and deletes a few old backups each time. There's no button that you have to remember to go and push every few months, and it doesn't hammer your site by deleting thousands of files at once.

The scheduled task logs will show you how many files Backup Cleaner has deleted and how much disk space it has saved, every time it runs.

Installation
============

Requirements
------------
Backup Cleaner was built and tested on Moodle 3.11, but it's a small, simple plugin that doesn't do anything fancy. It should work on any recent version of Moodle.

How to install
--------------
* Download the plugin and add the 'backupcleaner' folder to the 'local' folder in Moodle.
* Log in as a site administrator and visit the Site Administration page. The plugin will install. If it doesn't, try visiting the Notifications page or the Plugins page.
* Choose how long you want to keep backups for, and how many backups should be deleted each time the plugin runs.

That's it. By default, Backup Cleaner will run at 04:00 every morning and start deleting backups.

If you go to Site Administration > Server > Scheduled Tasks, you can set the scheduled task to run more often. You can also click the 'Run now' button to run Backup Cleaner manually (if your server administrator allows this), and view the logs to see how much space Backup Cleaner has saved you.


Useful links
============
* GitHub: https://github.com/lexxkoto/local_backupcleaner

Release history
===============
* 1.0.1 - Coding standards improvements. No new features.
* 1.0.0 - Initial Release

