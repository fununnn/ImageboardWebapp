CREATE TABLE IF NOT EXISTS CommentLike (
    userID INT,
    commentID INT,
    PRIMARY KEY (userID, commentID),
    FOREIGN KEY (userID) REFERENCES User(userID),
    FOREIGN KEY (commentID) REFERENCES Comment(commentID)
);