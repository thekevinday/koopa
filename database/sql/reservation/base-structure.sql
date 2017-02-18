/** Standardized SQL Structure - Structure **/
/** This depends on: base-content_type.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;



commit transaction;
