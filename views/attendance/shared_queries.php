<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * queries used across multiple files
 *
 * defines multiple queries as constants and allows them to be called
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 1.1
 */

define( "SHARED_QUERY_CURRICULUM" , "SELECT c.curriculumid, c.curriculumname FROM curricula c WHERE c.df IS FALSE ORDER BY c.curriculumname ASC;");

define("SHARED_QUERY_CLASSES", "SELECT cc.curriculumid, topicname, cc.classid " .
    "FROM curriculumclasses cc, classes WHERE classes.classid = cc.classid AND classes.df IS FALSE ORDER BY cc.curriculumid;");

define("SHARED_QUERY_SITES", "SELECT s.sitename FROM sites s;");

define("SHARED_QUERY_LANGUAGES", "SELECT * FROM languages;");

define("SHARED_QUERY_FACILITATORS", "SELECT peop.firstname, peop.middleinit, peop.lastname, peop.peopleid " .
    "FROM people peop, employees emp, facilitators f " .
    "WHERE peop.peopleid = emp.employeeid " .
    "AND emp.employeeid = f.facilitatorid " .
    "AND f.df IS FALSE " .
    "ORDER BY peop.lastname ASC;"
);