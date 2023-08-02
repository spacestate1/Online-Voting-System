-- Database: votesystem

-- Set timezone
SET TIME ZONE 'UTC';

-- Set encoding
SET CLIENT_ENCODING TO 'UTF8';

-- --------------------------------------------------------

-- Table structure for table admin

CREATE TABLE admin (
  id SERIAL PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(60) NOT NULL,
  firstname VARCHAR(50) NOT NULL,
  lastname VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL,
  photo VARCHAR(150) NOT NULL,
  created_on DATE NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table positions

CREATE TABLE positions (
  id SERIAL PRIMARY KEY,
  description VARCHAR(50) NOT NULL,
  max_vote INT NOT NULL,
  priority INT NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table candidates

CREATE TABLE candidates (
  id SERIAL PRIMARY KEY,
  position_id INT NOT NULL,
  firstname VARCHAR(30) NOT NULL,
  lastname VARCHAR(30) NOT NULL,
  photo VARCHAR(150) NOT NULL,
  platform TEXT NOT NULL,
  FOREIGN KEY (position_id) REFERENCES positions (id) ON DELETE CASCADE
);

-- --------------------------------------------------------

-- Table structure for table voters

CREATE TABLE voters (
  id SERIAL PRIMARY KEY,
  user_id VARCHAR(15) NOT NULL UNIQUE,
  voters_id VARCHAR(15) NOT NULL,
  password VARCHAR(60) NOT NULL,
  firstname VARCHAR(30) NOT NULL,
  lastname VARCHAR(30) NOT NULL,
  photo VARCHAR(150) NOT NULL,
  email VARCHAR(100) NOT NULL
);
-- --------------------------------------------------------

-- Table structure for table votes

CREATE TABLE votes (
  id SERIAL PRIMARY KEY,
  voters_id INT NOT NULL,
  candidate_id INT NOT NULL,
  position_id INT NOT NULL,
  FOREIGN KEY (voters_id) REFERENCES voters (id) ON DELETE CASCADE,
  FOREIGN KEY (candidate_id) REFERENCES candidates (id) ON DELETE CASCADE,
  FOREIGN KEY (position_id) REFERENCES positions (id) ON DELETE CASCADE
);

-- --------------------------------------------------------

-- Inserting data for table admin

INSERT INTO admin (username, password, firstname, lastname, photo, created_on) VALUES
('crce', '$2y$10$kLqXG4BAJrPbsOjJ/.B4eeZn6oojNhAb8l5/cb9eZvFnYU.pz2qni', 'CRCE', 'Admin', 'WhatsApp Image 2021-05-27 at 17.55.34.jpeg', '2018-04-02');
