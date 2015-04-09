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
#Query 11
#Query 12
#Query 13
#Query 14
#Query 15
#Query 16
#Query 17
#Query 18
#Query 19
#Query 20