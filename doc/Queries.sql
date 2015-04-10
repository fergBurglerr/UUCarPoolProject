#Queries for DBMS project 

#20 SQL queries that should work with our project

#Query 1 (Find all driver names and number of seats in their car)
SELECT P.firstname, P.lastname, openSeats FROM Person P INNER JOIN person_has_car INNER JOIN Car C;
#Query 2 (Find all photos from a certain Group)
SELECT file FROM Photos P INNER JOIN Group_has_photos INNER JOIN Group G WHERE G.name = 'test';
#Query 3 (Find the names of all people from are in the "young adults" group)
SELECT P.name FROM Person P INNER JOIN Person_in_group INNER JOIN Group G WHERE group_name = 'young adults';
#Query 4 (Find the names of all people who need a ride to the event named "test_name")
SELECT P.name FROM Person P INNER JOIN Person_needs_ride INNER JOIN Event E WHERE E.name = 'test_name';
#Query 5 (Find the email addresses from all People)-----??? Check this one ???
SELECT email_address FROM Employee E INNER JOIN Person P INNER JOIN Email E INNER JOIN Person_has_email; --- this one might be wrong (check the joining of the tables)
#Query 6 (Find all names of all out of Church Events)
SELECT E.name FROM Events E INNER JOIN Out_of_Church_Event INNER JOIN Address A WHERE A.street = 'street' AND A.state = 'state' AND A.city = 'city';
#Query 7 (Find the name and person ID of all people who are going to "Happy Hour" who are not in the "Adult" group)
SELECT P.name, P.pid FROM Person P INNER JOIN Event E WHERE E.name = 'Adult Happy Hour' AND P.pid NOT IN (SELECT P.pid FROM Person P INNER JOIN GROUP G WHERE G.name = 'Adult');
#Query 8 (Find the names of all people who DO NOT have an email or a phone number)
SELECT P.name FROM Person P WHERE P.pid NOT IN (SELECT P.pid FROM Person_Email INNER JOIN Person_Phone);
#Query 9 (Find the names of all people who attend ALL events)
SELECT P.name FROM Person P WHERE NOT EXISTS (SELECT E.eid FROM Events E WHERE E.eid NOT IN (SELECT Pae.eid FROM Person_attends_Event Pae WHERE Pae.pid = P.pid));
#Query 10 (Find all announcements from the "Religious Studies" group)
SELECT message FROM Announcement INNER JOIN Group_has_announcement gha INNER JOIN Group G WHERE G.name = 'Religious Education';
#Query 11 (Find all announcements from "Pastor Roth")
SELECT message FROM Announcement INNER JOIN Person P WHERE P.name = 'Pastor Roth';
#Query 12 (Find a list of all people who need a ride to the "Happy Hour")
SELECT P.firstname, P.lastname FROM Person P INNER JOIN person_needs_ride_for_event P2 INNER JOIN Event E WHERE E.name = 'Happy Hour';
#Query 13 (Return the number of people who attended the "Bible Study" on 4-13-2012)
SELECT E.name, count(pid) FROM Person_attends_Event pae INNER JOIN Event E WHERE E.name = 'Bible Study' GROUP BY (E.name);
#Query 14 (Find how many people can drive to "Bible Study" on 3-12-2013)
SELECT count(person_drives_for_event.pid) FROM person_drives_for_event INNER JOIN Event E WHERE E.name = 'Bible Study' AND E.startTime = '2013-3-12 17:00:00' GROUP BY (person_drives_for_event.eid);
#Query 15 (Find the name of the person who attended the most events)
SELECT P.name FROM Person P INNER JOIN Person_attends_Event pae GROUP BY (pae.pid) ORDER BY count(pae.eid) DESC LIMIT 1;
#Query 16 (Find a list of all people under the age of 18 "not adults")
#Query 17 (Find a list of all events in order of attendence)
#Query 18 (Find the average number of pictures taken at all events)
#Query 19 (Find the months which have the most events on average)
#Query 20 (Find the names of everyone who has driven to AT LEAST one event)