CREATE TABLE IF NOT EXISTS PostLike (
    userID INT,
    postID INT,
    PRIMARY KEY (userID, postID),
    FOREIGN KEY (userID) REFERENCES User(userID),
    FOREIGN KEY (postID) REFERENCES Post(postID)
);