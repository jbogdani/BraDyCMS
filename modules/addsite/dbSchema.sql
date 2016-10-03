
-- Table: articles
CREATE TABLE articles (
    id       INTEGER         PRIMARY KEY,
    title    VARCHAR( 255 ),
    textid   VARCHAR( 100 ),
    sort     INT( 11 ),
    summary  VARCHAR( 255 ),
    text     LONGTEXT,
    keywords VARCHAR( 255 ),
    author   VARCHAR( 200 ),
    status   TINYINT( 1 ),
    created  DATE,
    publish  DATE,
    expires  DATE,
    updated  TIMESTAMP       DEFAULT 'CURRENT_TIMESTAMP'
);--end
-- Table: menu
CREATE TABLE menu (
    id     INTEGER         PRIMARY KEY,
    item   VARCHAR( 100 ),
    href   VARCHAR( 255 ),
    target VARCHAR( 20 ),
    title  VARCHAR( 200 ),
    menu   VARCHAR( 100 ),
    sort   INT( 11 ),
    subof  INT( 11 )       DEFAULT ( NULL )
);--end
-- Table: tag
CREATE TABLE tag (
    id    INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT
);--end
-- Table: articles_tag
CREATE TABLE articles_tag (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    articles_id INTEGER,
    tag_id      INTEGER,
    FOREIGN KEY ( articles_id ) REFERENCES articles ( id ) ON DELETE CASCADE
                                                               ON UPDATE SET NULL,
    FOREIGN KEY ( tag_id ) REFERENCES tag ( id ) ON DELETE CASCADE
                                                     ON UPDATE SET NULL
);--end
-- Table: menutrans
CREATE TABLE menutrans (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    lang    TEXT,
    status  INTEGER,
    item    TEXT,
    title   TEXT,
    menu_id INTEGER,
    FOREIGN KEY ( menu_id ) REFERENCES menu ( id ) ON DELETE SET NULL
                                                       ON UPDATE SET NULL
);--end
-- Table: arttrans
CREATE TABLE arttrans (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    lang        TEXT,
    status      INTEGER,
    title       TEXT,
    summary     TEXT,
    text        TEXT,
    keywords    TEXT,
    articles_id INTEGER,
    FOREIGN KEY ( articles_id ) REFERENCES articles ( id ) ON DELETE SET NULL
                                                               ON UPDATE SET NULL
);--end
-- Index: index_foreignkey_menutrans_menu
CREATE INDEX index_foreignkey_menutrans_menu ON menutrans (
    menu_id
);--end
-- Index: index_foreignkey_arttrans_articles
CREATE INDEX index_foreignkey_arttrans_articles ON arttrans (
    articles_id
);
