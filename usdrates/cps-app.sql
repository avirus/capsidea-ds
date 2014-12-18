/*
Navicat PGSQL Data Transfer

Source Server         : capsidea-app
Source Server Version : 90305
Source Host           : app.capsidea.com:5432
Source Database       : app
Source Schema         : public

Target Server Type    : PGSQL
Target Server Version : 90305
File Encoding         : 65001

Date: 2014-12-18 15:55:11
*/


-- ----------------------------
-- Table structure for updates
-- ----------------------------
DROP TABLE IF EXISTS "public"."updates";
CREATE TABLE "public"."updates" (
"ikey" int8 NOT NULL,
"ival" text COLLATE "default",
"idate" timestamp(6),
"iapp" int8 NOT NULL,
"lastresult" text COLLATE "default"
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Alter Sequences Owned By 
-- ----------------------------

-- ----------------------------
-- Indexes structure for table updates
-- ----------------------------
CREATE INDEX "updates_ikey_idate_idx" ON "public"."updates" USING btree (ikey, idate, iapp);
