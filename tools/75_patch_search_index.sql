INSERT INTO search_index (cp, term, weight)
SELECT cp, CONCAT('na:', LOWER(na)), 100 FROM codepoints WHERE na <> '';
INSERT INTO search_index (cp, term, weight)
SELECT cp, CONCAT('na1:', LOWER(na1)), 90 FROM codepoints WHERE na1 <> '';
INSERT INTO search_index (cp, term, weight)
SELECT cp, CONCAT('kDefinition:', LOWER(kDefinition)), 50 FROM codepoints WHERE kDefinition <> '';
