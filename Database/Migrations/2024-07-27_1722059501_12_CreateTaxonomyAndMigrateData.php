<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class CreateTaxonomyAndMigrateData implements SchemaMigration
{
    public function up(): array
    {
        return [
            "-- Taxonomyテーブルにデータを挿入
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
FOREIGN KEY (taxonomyTermID) REFERENCES TaxonomyTerm(taxonomyTermID);"
        ];
    }

    public function down(): array
    {
        return [
            "-- Tag テーブルの再作成
CREATE TABLE IF NOT EXISTS Tag (
    tagID INT PRIMARY KEY AUTO_INCREMENT,
    tagName VARCHAR(255)
);

-- Category テーブルの再作成
CREATE TABLE IF NOT EXISTS Category (
    category INT PRIMARY KEY AUTO_INCREMENT,
    categoryName VARCHAR(255)
);

-- Tag データを元に戻す
INSERT INTO Tag (tagID, tagName)
SELECT tt.taxonomyTermID, tt.taxonomyTermName
FROM TaxonomyTerm tt
JOIN Taxonomy t ON tt.taxonomyID = t.taxonomyID
WHERE t.taxonomyName = 'Tag';

-- Category データを元に戻す
INSERT INTO Category (category, categoryName)
SELECT tt.taxonomyTermID, tt.taxonomyTermName
FROM TaxonomyTerm tt
JOIN Taxonomy t ON tt.taxonomyID = t.taxonomyID
WHERE t.taxonomyName = 'Category';

-- PostTag テーブルを元に戻す
ALTER TABLE PostTag
DROP FOREIGN KEY fk_TaxonomyTermID_PostTag;

ALTER TABLE PostTag
CHANGE COLUMN taxonomyTermID tagID INT;

ALTER TABLE PostTag
ADD CONSTRAINT fk_tagID_PostTag
FOREIGN KEY (tagID) REFERENCES Tag(tagID);

-- Post テーブルを元に戻す
ALTER TABLE Post
DROP FOREIGN KEY fk_TaxonomyTermID_Post;

ALTER TABLE Post
CHANGE COLUMN taxonomyTermID CategoryID INT;

ALTER TABLE Post
ADD CONSTRAINT fk_CategoryID_Post
FOREIGN KEY (CategoryID) REFERENCES Category(category);

-- TaxonomyTerm からデータを削除
DELETE FROM TaxonomyTerm 
WHERE taxonomyID IN (SELECT taxonomyID FROM Taxonomy WHERE taxonomyName IN ('Tag', 'Category'));

-- Taxonomy から 'Tag' と 'Category' を削除
DELETE FROM Taxonomy WHERE taxonomyName IN ('Tag', 'Category');"
        ];
    }
}