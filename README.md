SACL
====

Smart ACL (Access Control List) is meant to be a base to my personal Codeigniter (PHP) web application development. Stem from the frustation of my own incompetence in using other ACL and unsatisfying urge to try some weird stuff in Codeigniter, SACL is now released to the world.

Two main idea that SACL built from are Access Card and Features Management:

Access Card
-----------

The main idea for access control list in SACL is Access Card. Access Card contain pass to features as described by developer. Three type of access are provided which are: Public (no need to login to view), Private (open to all members), Controlled (specific group/user/tag). The Access Card then can be assign to multiple users, groups or any combination of tags. This will make it flexible in theory. But i foresee a complicated control afterwards. That will be rectified by the time it comes.

Feature Management:
-------------------

All web application usually consist of similar function (manage user, acl, etc) which SACL come with and also custom features (manage books, servers, sales, etc) that is unique from one project to another. Here the feature management come in.

Feature management will make it easy to assign feature access to Access Card. Features are accessed and filtered via specific url. The class and method for the features (url) still need to be made and code manually.

REQUIREMENT
===========

- As in CodeIgniter requirement:
	- PHP version 5.1.6 or newer.
	- A Database is required for most web application programming. Current supported databases are MySQL (4.1+), MySQLi, MS SQL, Postgres, Oracle, SQLite, and ODBC. 

* please refer CodeIgniter <http://codeigniter.com> for further information

INSTALL
=======

1.	Copy the whole folder into new folder under public html (htdocs/new_folder)
2.	Create new database and import tables from /migrate_sacl.sql
3.	Make sure your database setting (username and pass) is correct.
4.	Login as root user with as username "root@gmail.com" and password "qwerty123"

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

	The MIT License

	Copyright (C) 2012 by [Izwan Wahab](http://robotys.net)

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.

TESTING
=======

Me really should learn testing... TT___TT