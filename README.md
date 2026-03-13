
________________________________________
🗄️ MySQL Database Structure
Database Name: realestate_db
1. admin_users (for login)
Column	Type	Description
id	INT (PK, AI)	Unique ID
username	VARCHAR(50)	Admin username
password	VARCHAR(255)	Hashed password (secure)
created_at	TIMESTAMP	When the admin was added
________________________________________
2. properties (main property listings)
Column	Type	Description
id	INT (PK, AI)	Property ID
title	VARCHAR(255)	Property name/title
price	VARCHAR(50)	e.g., "₦25,000,000"
location	VARCHAR(255)	Location
category	VARCHAR(50)	House, Land, Apartment, Commercial, etc.
size	VARCHAR(50)	Optional (e.g., “500sqm”)
description	TEXT	Full description
created_at	TIMESTAMP	When added
________________________________________
3. property_images (multiple images per property)
Column	Type	Description
id	INT (PK, AI)	Unique ID
property_id	INT (FK)	Links to the property
image_path	VARCHAR(255)	Stored path of the image
________________________________________
4. inquiries (messages sent from contact or property page)
Column	Type	Description
id	INT (PK, AI)	Inquiry ID
name	VARCHAR(255)	Sender name
email	VARCHAR(255)	Sender email
phone	VARCHAR(50)	Sender phone number
message	TEXT	Main message
property_id	INT (FK NULL)	If the inquiry relates to a property
created_at	TIMESTAMP	Time message was submitted
________________________________________

✔ Frontend pages
•	Home
•	About
•	Construction Services
•	Properties
•	Single Property Page
•	Contact Page
•	Theme system (Green + switchable color schemes)
✔ Backend (PHP + MySQL)
•	DB connection file
•	Admin authentication
•	Add property
•	Edit property
•	Delete property
•	Upload images
•	Manage inquiries
•	Admin dashboard UI
✔ Database SQL file
You will get a ready-to-import .sql file.
________________________________________
