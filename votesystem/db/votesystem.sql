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
  photo VARCHAR(150),
  created_on DATE NOT NULL
);

-- --------------------------------------------------------
--elections table 

CREATE TABLE elections (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    start_date TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    end_date TIMESTAMP WITHOUT TIME ZONE NOT NULL
);

-- Table structure for table positions

CREATE TABLE positions (
  id SERIAL PRIMARY KEY,
  election_id INT NOT NULL,
  description VARCHAR(1000) NOT NULL,
  max_vote INT NOT NULL,
  priority INT NOT NULL,
  FOREIGN KEY (election_id) REFERENCES elections (id) ON DELETE CASCADE
);

-- --------------------------------------------------------

-- Table structure for table candidates

CREATE TABLE candidates (
  id SERIAL PRIMARY KEY,
  election_id INT NOT NULL,
  position_id INT NOT NULL,
  firstname VARCHAR(30) NOT NULL,
  lastname VARCHAR(30) NOT NULL,
  photo VARCHAR(150),
  platform TEXT NOT NULL,
  FOREIGN KEY (election_id) REFERENCES elections (id) ON DELETE CASCADE,
  FOREIGN KEY (position_id) REFERENCES positions (id) ON DELETE CASCADE
);

-- --------------------------------------------------------
---- Table for Action Items ----

CREATE TABLE action_items (
  id SERIAL PRIMARY KEY,
  title VARCHAR(500) NOT NULL,
  description TEXT,
  date_created TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  election_id INT NOT NULL,
  FOREIGN KEY (election_id) REFERENCES elections (id) ON DELETE CASCADE
);
---------------------------------------------

-- Table structure for table voters
CREATE TABLE voters (
  id SERIAL PRIMARY KEY,
  user_id VARCHAR(15) NOT NULL UNIQUE,
  voters_id VARCHAR(15) NOT NULL,
  password VARCHAR(60) NOT NULL,
  firstname VARCHAR(30) NOT NULL,
  lastname VARCHAR(30) NOT NULL,
  photo VARCHAR(150),
  email VARCHAR(100) NOT NULL,
  is_logged_in BOOLEAN DEFAULT FALSE,
  last_login TIMESTAMP WITH TIME ZONE DEFAULT NULL
);


-- --------------------------------------------------------

-- Votes Table
CREATE TABLE votes (
    id SERIAL PRIMARY KEY,
    election_id INT NOT NULL,
    voters_id INT NOT NULL,
    candidate_id INT,  -- Nullable for abstains
    position_id INT NOT NULL,
    abstain BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (election_id) REFERENCES elections (id) ON DELETE CASCADE,
    FOREIGN KEY (voters_id) REFERENCES voters (id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates (id) ON DELETE CASCADE,
    FOREIGN KEY (position_id) REFERENCES positions (id) ON DELETE CASCADE,
    UNIQUE(voters_id, position_id)
);

-- --------------------------------------------------------

-- Create ENUM type for action items vote
CREATE TYPE action_vote AS ENUM ('Approved', 'Denied', 'Abstain');

------- Action Items Votes -----------
CREATE TABLE action_item_votes (
    id SERIAL PRIMARY KEY,
    action_item_id INTEGER NOT NULL,
    voters_id INTEGER NOT NULL,
    vote action_vote NOT NULL,  -- Using ENUM type
    vote_timestamp TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    election_id INTEGER NOT NULL,
    FOREIGN KEY (action_item_id) REFERENCES public.action_items(id) ON DELETE CASCADE,
    FOREIGN KEY (voters_id) REFERENCES public.voters(id) ON DELETE CASCADE,
    FOREIGN KEY (election_id) REFERENCES public.elections(id) ON DELETE CASCADE
);


