/** Standardized SQL Structure - Content **/
/** This depends on: base-users.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;



commit transaction;
