SACL
====

Smart ACL (Access Control List) is meant to be a base to my personal Codeigniter (PHP) web application development. Stem from the frustation of my own incompetence in using other ACL and unsatisfying urge to try some weird stuff in Codeigniter, SACL is now released to the world.

REQUIREMENT
===========

- PHP

INSTALL
=======

1.	Copy the whole folder into new folder under public html
2.	Create new database and import tables from /migrate_ssacl.sql
3.	Make sure your database setting is correct.
4.	Login root user with as username "root@gmail.com" and password "qwerty123"

FEATURES
========

- CRUD Users
- CRUD Access Card (Access Control List)
- Users can have multiple Access Card
- OR access association. Will grant access if any of its Access Card allow access.
- Access to features is lock with Tags
- CRUD Features
- CRUD Tags
- Spoof Users: Login as another user. Mainly for debugging and development purpose.

NOTES
=====

This stuff was built from my view on how access should be manage for given users. Thus the term and jargon will always reflect my environment use of it. I believe it will be as opinionted as me. And no, it will not be having realization soon.

Discard what i said about view. Exchange it with 'what we need to use'.

LICENSE
=======

All codes in /application folder are under GPLv3 license.