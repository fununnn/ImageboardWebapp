CREATE TABLE IF NOT EXISTS PostTaxonomy (
    postTaxonomyID INT PRIMARY KEY AUTO_INCREMENT,
    postID INT,
    taxonomyID INT,
    FOREIGN KEY (postID) REFERENCES Post(postID),
    FOREIGN KEY (taxonomyID) REFERENCES Taxonomy(taxonomyID)
);