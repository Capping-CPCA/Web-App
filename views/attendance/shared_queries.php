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

global $db;

//prepare the queries
$db->prepare(
    'shared_query_curriculum',
    "SELECT c.curriculumid, c.curriculumname FROM curricula c WHERE c.df IS FALSE ORDER BY c.curriculumname ASC;"
);

$db->prepare(
    'shared_query_classes',
    "SELECT cc.curriculumid, topicname, cc.classid " .
    "FROM curriculumclasses cc, classes WHERE classes.classid = cc.classid AND classes.df IS FALSE ORDER BY cc.curriculumid;"
);

$db->prepare(
    'shared_query_sites',
    "SELECT s.sitename FROM sites s;"
);

$db->prepare(
    'shared_query_languages',
    "SELECT * FROM languages;"
);

$db->prepare(
    'shared_query_facilitators',
    "SELECT peop.firstname, peop.middleinit, peop.lastname, peop.peopleid " .
    "FROM people peop, employees emp, facilitators f " .
    "WHERE peop.peopleid = emp.employeeid " .
    "AND emp.employeeid = f.facilitatorid " .
    "AND f.df IS FALSE " .
    "ORDER BY peop.lastname ASC;"
);

$db->prepare(
    'shared_query_race_enum',
    "SELECT unnest(enum_range(NULL::race));"
);

$db->prepare(
    'shared_query_sex_enum',
    "SELECT unnest(enum_range(NULL::sex));"
);