-- SQLite schema for BigfootCMS
-- Converted from MySQL schema

-- Enable foreign key support
PRAGMA foreign_keys = ON;

CREATE TABLE commnetivity_components (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  "group" TEXT NOT NULL,
  type TEXT NOT NULL CHECK (type IN ('template', 'stylesheet', 'javascript')),
  weight INTEGER NOT NULL,
  path TEXT NOT NULL
);

CREATE TABLE commnetivity_content (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  virtual_path TEXT NOT NULL UNIQUE,
  page_title TEXT,
  nav_title TEXT,
  parent_id TEXT,
  cleartext_excerpts TEXT,
  encoded_content TEXT,
  internal_path TEXT,
  theme TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  weight INTEGER DEFAULT 0,
  hits INTEGER DEFAULT 0
);

CREATE TABLE commnetivity_content_hist (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  virtual_path TEXT NOT NULL,
  page_title TEXT,
  nav_title TEXT,
  parent_id TEXT,
  cleartext_excerpts TEXT,
  encoded_content TEXT,
  date_archived DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE commnetivity_dynamics (
  target_div TEXT PRIMARY KEY,
  source_path TEXT DEFAULT NULL,
  language TEXT DEFAULT 'php' CHECK (language IN ('php', 'perl', 'python'))
);

CREATE TABLE commnetivity_media (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  group_name TEXT,
  orig_filename TEXT NOT NULL,
  real_filename TEXT NOT NULL UNIQUE,
  extension TEXT,
  description TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE commnetivity_mimetypes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  extention TEXT UNIQUE,
  mimetype TEXT DEFAULT NULL
);

CREATE TABLE commnetivity_navigation (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  virtual_path TEXT NOT NULL UNIQUE,
  weight INTEGER DEFAULT 0,
  position TEXT
);

CREATE TABLE commnetivity_overrides (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  top_level_pattern TEXT NOT NULL UNIQUE,
  theme TEXT,
  internal_path TEXT,
  page_titles TEXT
);

CREATE TABLE commnetivity_presentation (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  "group" TEXT UNIQUE,
  flash_vars TEXT DEFAULT NULL,
  parameters TEXT DEFAULT NULL
);

CREATE TABLE commnetivity_redirects (
  virtual_path TEXT PRIMARY KEY,
  virtual_target TEXT DEFAULT NULL
);

-- Create indexes
CREATE INDEX idx_content_virtual_path ON commnetivity_content(virtual_path);
CREATE INDEX idx_content_parent_id ON commnetivity_content(parent_id);
CREATE INDEX idx_media_group ON commnetivity_media(group_name);
CREATE INDEX idx_navigation_virtual_path ON commnetivity_navigation(virtual_path);
CREATE INDEX idx_presentation_group ON commnetivity_presentation("group");

-- Create triggers for updated_at
CREATE TRIGGER update_content_timestamp 
AFTER UPDATE ON commnetivity_content 
FOR EACH ROW 
BEGIN 
    UPDATE commnetivity_content SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

CREATE TRIGGER update_media_timestamp 
AFTER UPDATE ON commnetivity_media 
FOR EACH ROW 
BEGIN 
    UPDATE commnetivity_media SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END; 