--create the database name as d2c
CREATE DATABASE d2c;


--Creating table to store the booking status and seats remaining to book in the train
CREATE TABLE `d2c`.`bookstatus` 
( 
	`row` INT(2) NOT NULL , 
	`booknum` VARCHAR(100) NOT NULL , 
	`remaining` INT(2) NOT NULL 
)
ENGINE = InnoDB;


--creating table to store the bookdetails of the members
CREATE TABLE `d2c`.`bookdetails` 
( 
	`count` INT(1) NOT NULL , 
	`name` VARCHAR(50) NOT NULL , 
	`email` VARCHAR(50) NOT NULL , 
	`booknum` VARCHAR(100) NOT NULL 
) 
ENGINE = InnoDB;

--Insering Intial all the rows and booking seats in every row
--Assume all are vacant
INSERT INTO `bookstatus` (`row`, `booknum`, `remaining`) VALUES 
('1', '', '7'), 
('2', '', '7'),
('3', '', '7'), 
('4', '', '7'),
('5', '', '7'), 
('6', '', '7'),
('7', '', '7'), 
('8', '', '7'),
('9', '', '7'), 
('10', '', '7'),
('11', '', '7'), 
('12', '', '3');


--Delete all the data from the database
--resetting the data
UPDATE `bookstatus` SET `booknum`='',`remaining`='3' WHERE row=12
UPDATE `bookstatus` SET `booknum`='',`remaining`='7' WHERE row<=11
TRUNCATE TABLE bookdetails;