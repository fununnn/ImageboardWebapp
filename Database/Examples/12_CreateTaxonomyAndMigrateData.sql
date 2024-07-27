-- Taxonomyテーブルにデータを挿入
INSERT INTO Taxonomy (taxonomyName, description) 
VALUES ('Tag', 'Tags for posts'), ('Category', 'Categories for posts');

-- Tagデータの移行
INSERT INTO TaxonomyTerm (taxonomyTermName, taxonomyID)
SELECT tagName, (SELECT taxonomyID FROM Taxonomy WHERE taxonomyName = 'Tag')
FROM Tag;

-- Categoryデータの移行
INSERT INTO TaxonomyTerm (taxonomyTermName, taxonomyID)
SELECT categoryName, (SELECT taxonomyID FROM Taxonomy WHERE taxonomyName = 'Category')
FROM Category;

-- 古いテーブルの削除
DROP TABLE IF EXISTS Tag;
DROP TABLE IF EXISTS Category;

-- Postテーブルの更新（CategoryIDをTaxonomyTermIDに変更）
ALTER TABLE Post
DROP FOREIGN KEY fk_CategoryID_Post;

ALTER TABLE Post
CHANGE COLUMN CategoryID taxonomyTermID INT;

ALTER TABLE Post
ADD CONSTRAINT fk_TaxonomyTermID_Post
FOREIGN KEY (taxonomyTermID) REFERENCES TaxonomyTerm(taxonomyTermID);

-- PostTagテーブルの更新
ALTER TABLE PostTag
DROP FOREIGN KEY fk_tagID_PostTag;

ALTER TABLE PostTag
CHANGE COLUMN tagID taxonomyTermID INT;

ALTER TABLE PostTag
ADD CONSTRAINT fk_TaxonomyTermID_PostTag
FOREIGN KEY (taxonomyTermID) REFERENCES TaxonomyTerm(taxonomyTermID);