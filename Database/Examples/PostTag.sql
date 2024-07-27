CREATE TABLE IF NOT EXISTS PostTag (
    postID INT,
    tagID INT ,
    PRIMARY KEY (postID, tagID)
);