CREATE TABLE IF NOT EXISTS PostTag (
    postID INT,
    tagID INT,
    PRIMARY KEY (postID, tagID),
    FOREIGN KEY (postID) REFERENCES Post(postID),
    FOREIGN KEY (tagID) REFERENCES Tag(tagID)
);