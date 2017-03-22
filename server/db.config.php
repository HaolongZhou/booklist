<?php

define( 'DATABASE_TYPE' , 'sqlite' );

// For MySQL, MariaDB, MSSQL, Sybase, PostgreSQL, Oracle
define( 'DATABASE_SERVER' , 'localhost' );

define( 'DATABASE_USER' , 'root' );

define( 'DATABASE_PWD' , '123456' );

// For SQLite
define("__PATH__", dirname(__FILE__) );
define( 'DATABASE_FILE' ,__PATH__."/../storage/bookm.sqlite");// __PATH__.'/../storage/bookm.sqlite' );

// Optional
define( 'DATABASE_PORT' , '3306' );

define( 'DATABASE_CHARSET' , 'utf8' );

define( 'DATABASE_NAME' , 'bookm' );

