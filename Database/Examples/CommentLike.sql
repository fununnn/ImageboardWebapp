CREATE TABLE IF NOT EXISTS CommentLike (
    userID INT,
    commentID INT,
    PRIMARY KEY (userID, commentID)
);