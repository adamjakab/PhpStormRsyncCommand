PhpStormRsyncCommand
====================

This is an external command for syncronizing your projects using rsync.
The sync.php file is to be used in PhpStorm as an "External Tools".

## How to use

You will need to create two new commands: Sync-Up and Sync-Down.

Open your PhpStorm settings and go to the "External Tools" section.

Add a new tool, give it a name, a description and in the options section flag "Open console".

In "Shown in" section flag "Project view".

In "Tools settings" set:

- Program: `/usr/bin/php` (or whatever your path is to the php cli)
- Parameters: `sync.php down $FilePath$` (use "up" or "down" as first parameter)
- Working Directory: (set the path where sync.php is found)

## Project setup
In each project you need to create a .SyncConfig directory with a configuration file(config.json)
and the optional excludes_up.txt and excludes_down.txt files.

Look at the example configuration files in this project.


