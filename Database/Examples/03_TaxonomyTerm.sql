CREATE TABLE IF NOT EXISTS TaxonomyTerm (
    taxonomyTermID INT PRIMARY KEY AUTO_INCREMENT,
    taxonomyTermName VARCHAR(255) NOT NULL,
    taxonomyTypeID INT,
    description TEXT,
    parentTaxonomyTerm INT,
    FOREIGN KEY (taxonomyTypeID) REFERENCES Taxonomy(taxonomyID),
);