-- ##############################################
-- KURS: DT161G
-- Projekt
-- förnamn efternamn
-- Create table called xxx
-- Create table called yyy
-- etc...................................
-- ##############################################


DROP SCHEMA IF EXISTS webproject CASCADE;

CREATE SCHEMA webproject;


-- ##############################################
-- First we create the member table
-- ##############################################
DROP TABLE IF EXISTS webproject.member;

CREATE TABLE webproject.member (
  id        SERIAL PRIMARY KEY,
  username  text NOT NULL CHECK (username <> ''),
  password  text NOT NULL CHECK (password  <> ''),
  CONSTRAINT unique_user UNIQUE(username)
)
WITHOUT OIDS;

-- ##############################################
-- Now we insert some values
-- ##############################################
INSERT INTO webproject.member (username, password) VALUES ('m','$2y$10$5tCLkA3UcvttR/o5XG4zGO/FYxLkuBSku4lrvBhUAattwsZ1zC662');
INSERT INTO webproject.member (username, password) VALUES ('a','$2y$10$6UBD9fn3hX6R.dxoow3wv.GnC0hk2rpZl9ffCfvERhxvl7bxx21MO');

-- ##############################################
-- Then we create the role table
-- ##############################################
DROP TABLE IF EXISTS webproject.role;

CREATE TABLE webproject.role (
  id        SERIAL PRIMARY KEY,
  role      text NOT NULL CHECK (role <> ''),
  roletext  text NOT NULL CHECK (roletext <> ''),
  CONSTRAINT unique_role UNIQUE(role)
)
WITHOUT OIDS;

-- ##############################################
-- Now we insert some values
-- ##############################################
INSERT INTO webproject.role (role, roletext) VALUES ('member','Ordinary member');
INSERT INTO webproject.role (role, roletext) VALUES ('admin','Administrator');

-- ##############################################
-- Then we create the member_role table
-- ##############################################
DROP TABLE IF EXISTS webproject.member_role;

CREATE TABLE webproject.member_role (
  id        SERIAL PRIMARY KEY,
  member_id integer REFERENCES webproject.member (id) ON DELETE CASCADE,
  role_id   integer REFERENCES webproject.role (id),
  CONSTRAINT unique_member_role UNIQUE(member_id, role_id)
)
WITHOUT OIDS;

-- ##############################################
-- Now we insert some values
-- ##############################################
INSERT INTO webproject.member_role (member_id, role_id) VALUES (1,1);
INSERT INTO webproject.member_role (member_id, role_id) VALUES (2,1);
INSERT INTO webproject.member_role (member_id, role_id) VALUES (2,2);

-- ##############################################
-- Category table
-- ##############################################
DROP TABLE IF EXISTS webproject.category;

CREATE TABLE webproject.category (
  id        SERIAL PRIMARY KEY,
  member_id integer REFERENCES webproject.member (id)
  ON DELETE CASCADE,
  category text NOT NULL CHECK (category <> ''),

  CONSTRAINT unique_member_category UNIQUE(member_id, category)
)
WITHOUT OIDS;

INSERT INTO webproject.category (member_id, category) VALUES (1,'Photography');
INSERT INTO webproject.category (member_id, category) VALUES (1,'Misc');
INSERT INTO webproject.category (member_id, category) VALUES (2,'Misc');
INSERT INTO webproject.category (member_id, category) VALUES (2,'Planning');
INSERT INTO webproject.category (member_id, category) VALUES (2,'Animals');

-- ##############################################
-- Image table
-- ##############################################
DROP TABLE IF EXISTS webproject.image;

CREATE TABLE webproject.image (
  id        SERIAL PRIMARY KEY,
  category_id integer REFERENCES webproject.category (id)
  ON DELETE CASCADE,
  date_taken timestamp NOT NULL,
  hash text NOT NULL CHECK (hash <> ''),
  image text NOT NULL CHECK (image <> '')
)
WITHOUT OIDS;