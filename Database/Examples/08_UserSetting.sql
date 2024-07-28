CREATE TABLE IF NOT EXISTS UserSetting (
    entryID INT PRIMARY KEY AUTO_INCREMENT,
    userID INT,
    metakey VARCHAR(255),
    metavalue VARCHAR(255),
    FOREIGN KEY (userID) REFERENCES User(userID)
);