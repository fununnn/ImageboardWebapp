CREATE TABLE IF NOT EXISTS PostLike(
    userID INT ,
    postID INT ,
    PRIMARY KEY (userID, postID)
);