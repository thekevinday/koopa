/** Standardized SQL Structure - Last */
/** This depends on: everything (run this absolutely last) **/

/** The purpose of this is to add all initial data after all appropriate triggers are defined. ***/
start transaction;



insert into s_tables.t_date_contexts (id, name_machine, name_human) values (0, 'none', 'None');
insert into s_tables.t_date_contexts (name_machine, name_human) values ('rehearsal', 'Rehearsal / Setup');
insert into s_tables.t_date_contexts (name_machine, name_human) values ('event', 'Event / Meeting');
insert into s_tables.t_date_contexts (name_machine, name_human) values ('cleanup', 'Cleanup / Breakdown');



insert into s_tables.t_legal_types (id, name_machine, name_human) values (0, 'none', 'None');



/*** start the sequence count at 1000 to allow for < 1000 to be reserved for special uses ***/
alter sequence s_tables.se_log_types_id start 1000;
alter sequence s_tables.se_log_types_id restart;


/** create well known types that can then be user for indexes (all new types added should be considered for custom indexing). **/
insert into s_tables.t_log_types (id, name_machine, name_human) values (0, 'none', 'None');
insert into s_tables.t_log_types (id, name_machine, name_human) values (1, 'php', 'PHP');
insert into s_tables.t_log_types (id, name_machine, name_human) values (2, 'theme', 'Theme');
insert into s_tables.t_log_types (id, name_machine, name_human) values (3, 'cache', 'Cache');
insert into s_tables.t_log_types (id, name_machine, name_human) values (4, 'javascript', 'Javascript');
insert into s_tables.t_log_types (id, name_machine, name_human) values (5, 'ajax', 'AJAX');
insert into s_tables.t_log_types (id, name_machine, name_human) values (6, 'sql', 'SQL');
insert into s_tables.t_log_types (id, name_machine, name_human) values (7, 'form', 'Form');
insert into s_tables.t_log_types (id, name_machine, name_human) values (8, 'form_validate', 'Form Validation');
insert into s_tables.t_log_types (id, name_machine, name_human) values (9, 'form_submit', 'Form Submit');
insert into s_tables.t_log_types (id, name_machine, name_human) values (10, 'load', 'Load');
insert into s_tables.t_log_types (id, name_machine, name_human) values (11, 'save', 'Save');
insert into s_tables.t_log_types (id, name_machine, name_human) values (12, 'render', 'Render');
insert into s_tables.t_log_types (id, name_machine, name_human) values (13, 'client', 'Client');
insert into s_tables.t_log_types (id, name_machine, name_human) values (14, 'add', 'Add');
insert into s_tables.t_log_types (id, name_machine, name_human) values (15, 'create', 'Create');
insert into s_tables.t_log_types (id, name_machine, name_human) values (16, 'change', 'Change');
insert into s_tables.t_log_types (id, name_machine, name_human) values (17, 'delete', 'Delete');
insert into s_tables.t_log_types (id, name_machine, name_human) values (18, 'redirect', 'Redirect');
insert into s_tables.t_log_types (id, name_machine, name_human) values (19, 'login', 'Login');
insert into s_tables.t_log_types (id, name_machine, name_human) values (20, 'logout', 'Logout');
insert into s_tables.t_log_types (id, name_machine, name_human) values (21, 'session', 'Session');
insert into s_tables.t_log_types (id, name_machine, name_human) values (22, 'database', 'Database');
insert into s_tables.t_log_types (id, name_machine, name_human) values (23, 'not_found', 'Not Found');
insert into s_tables.t_log_types (id, name_machine, name_human) values (24, 'access_denied', 'Access Denied');
insert into s_tables.t_log_types (id, name_machine, name_human) values (25, 'removed', 'Removed');
insert into s_tables.t_log_types (id, name_machine, name_human) values (26, 'locked', 'Locked');
insert into s_tables.t_log_types (id, name_machine, name_human) values (27, 'timeout', 'Timeout');
insert into s_tables.t_log_types (id, name_machine, name_human) values (28, 'expire', 'Expiration');
insert into s_tables.t_log_types (id, name_machine, name_human) values (29, 'user', 'User');
insert into s_tables.t_log_types (id, name_machine, name_human) values (30, 'error', 'Error');
insert into s_tables.t_log_types (id, name_machine, name_human) values (31, 'content', 'Content');
insert into s_tables.t_log_types (id, name_machine, name_human) values (32, 'workflow', 'Workflow');
insert into s_tables.t_log_types (id, name_machine, name_human) values (33, 'draft', 'Draft');
insert into s_tables.t_log_types (id, name_machine, name_human) values (34, 'clone', 'Clone');
insert into s_tables.t_log_types (id, name_machine, name_human) values (35, 'publish', 'Publish');
insert into s_tables.t_log_types (id, name_machine, name_human) values (36, 'revert', 'Revert');
insert into s_tables.t_log_types (id, name_machine, name_human) values (37, 'validate', 'Validate');
insert into s_tables.t_log_types (id, name_machine, name_human) values (38, 'approve', 'Approve');
insert into s_tables.t_log_types (id, name_machine, name_human) values (39, 'password', 'Password');
insert into s_tables.t_log_types (id, name_machine, name_human) values (40, 'revision', 'Revision');
insert into s_tables.t_log_types (id, name_machine, name_human) values (41, 'search', 'Search');
insert into s_tables.t_log_types (id, name_machine, name_human) values (42, 'access', 'Access');
insert into s_tables.t_log_types (id, name_machine, name_human) values (43, 'unknown', 'Unknown');



/* pre-populate the path types (note: this may be redesigned later once I have more type names). */
insert into s_tables.t_path_types (id, name_machine, name_human) values (0, 'none', 'None');
insert into s_tables.t_path_types (id, name_machine, name_human) values (1, 'system', 'System');
insert into s_tables.t_path_types (id, name_machine, name_human) values (2, 'user', 'User');


/** create well known types that can then be user for indexes (all new types added should be considered for custom indexing). **/
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (0, '0', 'Undefined');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (1, '1', 'Invalid');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (2, '2', 'Unknown');

insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (100, '100', 'Continue');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (101, '101', 'Switching Protocols');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (102, '102', 'Processing');

insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (200, '200', 'OK');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (201, '201', 'Created');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (202, '202', 'Accepted');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (203, '203', 'Non-Authoritative Information');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (204, '204', 'No Content');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (205, '205', 'Reset Content');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (206, '206', 'Partial Content');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (207, '207', 'Multi-Status');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (208, '208', 'Already Reported');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (226, '226', 'IM used');

insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (300, '300', 'Multiple Choices');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (301, '301', 'Moved Permanently');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (302, '302', 'Found');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (303, '303', 'See Other');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (304, '304', 'Not Modified');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (305, '305', 'Use Proxy');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (306, '306', 'Switch Proxy');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (307, '307', 'Temporary Redirect');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (308, '308', 'Permanent Redirect');

insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (400, '400', 'Bad Request');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (401, '401', 'Unauthorized');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (402, '402', 'Payment Required');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (403, '403', 'Forbidden');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (404, '404', 'Not Found');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (405, '405', 'Method Not Allowed');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (406, '406', 'Not Acceptable');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (407, '407', 'Proxy Authentication Required');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (408, '408', 'Request Timeout');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (409, '409', 'Conflict');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (410, '410', 'Gone');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (411, '411', 'Length Required');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (412, '412', 'Precondition Failed');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (413, '413', 'Payload Too Large');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (414, '414', 'Request-URI Too Long');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (415, '415', 'Unsupported Media Type');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (416, '416', 'Requested Range Not Satisfiable');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (417, '417', 'Expectation Failed');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (422, '422', 'Misdirected Request');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (423, '423', 'Locked');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (424, '424', 'Failed Dependency');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (426, '426', 'Upgrade Required');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (428, '428', 'Precondition Required');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (429, '429', 'Too Many Requests');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (431, '431', 'Request Header Fields Too Large');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (451, '451', 'Unavailable For Legal Reasons');

insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (500, '500', 'Internal Server Error');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (501, '501', 'Not Implemented');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (502, '502', 'Bad Gateway');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (503, '503', 'Service Unavailable');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (504, '504', 'Gateway Timeout');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (505, '505', 'HTTP Version Not Supported');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (506, '506', 'Variant Also Negotiates');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (507, '507', 'Insufficient Storage');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (508, '508', 'Loop Detected');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (510, '510', 'Not Extended');
insert into s_tables.t_log_type_http_status_codes (id, name_machine, name_human) values (511, '511', 'Network Authentication Required');


/*** start the sequence count at 1000 to allow for < 1000 to be reserved for special uses ***/
alter sequence s_tables.se_log_type_severity_levels_id start 1000;
alter sequence s_tables.se_log_type_severity_levels_id restart;


/** create well known types that can then be user for indexes (all new types added should be considered for custom indexing). **/
insert into s_tables.t_log_type_severity_levels (id, name_machine, name_human) values (0, 'none', 'None');
insert into s_tables.t_log_type_severity_levels (id, name_machine, name_human) values (1, 'information', 'Information');
insert into s_tables.t_log_type_severity_levels (id, name_machine, name_human) values (2, 'notice', 'Notice');
insert into s_tables.t_log_type_severity_levels (id, name_machine, name_human) values (3, 'debug', 'Debug');
insert into s_tables.t_log_type_severity_levels (id, name_machine, name_human) values (4, 'warning', 'Warning');
insert into s_tables.t_log_type_severity_levels (id, name_machine, name_human) values (5, 'error', 'Error');
insert into s_tables.t_log_type_severity_levels (id, name_machine, name_human) values (6, 'alert', 'Alert');
insert into s_tables.t_log_type_severity_levels (id, name_machine, name_human) values (7, 'critical', 'Critical');
insert into s_tables.t_log_type_severity_levels (id, name_machine, name_human) values (8, 'emergency', 'Emergency');



insert into s_tables.t_types_mime_categorys (id, name_machine, name_human) values (0, 'none', 'None');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human) values (1, 'unknown', 'Unknown');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (2, 'provided', 'Provided', '*');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (3, 'stream', 'Stream', 'application');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (4, 'multipart', 'Multipart', 'multipart');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (5, 'text', 'Text', 'text');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (6, 'image', 'Image', 'image');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (7, 'audio', 'Audio', 'audio');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (8, 'video', 'Video', 'video');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (9, 'document', 'Document', 'application');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (10, 'container', 'Container', 'application');
insert into s_tables.t_types_mime_categorys (id, name_machine, name_human, field_category) values (11, 'application', 'Application', 'application');


insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human) values (0, 0, 'none', 'None');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human) values (1, 1, 'unknown', 'Unknown');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2, 2, 'provided', 'Provided', NULL, '*/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2, 2, 'provided_text', 'Provided Text', NULL, 'text/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2, 2, 'provided_image', 'Provided Image', NULL, 'image/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2, 2, 'provided_audio', 'Provided Audio', NULL, 'audio/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2, 2, 'provided_video', 'Provided Video', NULL, 'video/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2, 2, 'provided_application', 'Provided Application', NULL, 'application/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3, 3, 'stream', 'Stream', 'octect-stream', 'application/octect-stream');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4, 4, 'multipart', 'Form Data', 'form-data', 'multipart/form-data');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5, 11, 'application', 'URL Data', 'x-www-form-urlencoded', 'application/x-www-form-urlencoded');

insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1000, 5, 'text', 'Text', NULL, 'text/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1001, 5, 'text_plain', 'Plain Text', 'txt', 'text/plain');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1002, 5, 'text_html', 'HTML', 'html', 'text/html');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1003, 5, 'text_rss', 'RSS', 'rss', 'text/rss');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1003, 5, 'text_rss_xml', 'RSS+XML', 'rss', 'text/rss+xml');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1003, 5, 'text_rdf_xml', 'RDF+XML', 'rss', 'text/rdf+xml');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1003, 5, 'text_atom_xml', 'ATOM+XML', 'rss', 'text/atom+xml');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1004, 5, 'text_ical', 'iCalendar', 'ics', 'text/calendar');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1005, 5, 'text_csv', 'CSV', 'csv', 'text/csv');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1006, 5, 'text_xml', 'XML', 'xml', 'text/xml');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1007, 5, 'text_css', 'CSS', 'css', 'text/css');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1008, 5, 'text_js', 'Javascript', 'js', 'text/js');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1008, 5, 'text_js', 'Javascript', 'js', 'application/js');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1009, 5, 'text_json', 'JSON', 'json', 'text/json');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1010, 5, 'text_rich', 'Rich Text', 'rtf', 'text/rich');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1011, 5, 'text_xhtml', 'XHTML', 'xhtml', 'text/xhtml');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1012, 5, 'text_ps', 'Postscript', 'ps', 'text/ps');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (1013, 5, 'text_fss', 'FSS', 'setting', 'text/fss');

insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2000, 6, 'image', 'Image', NULL, 'image/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2001, 6, 'image_png', 'PNG', 'png', 'image/png');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2002, 6, 'image_gif', 'GIF', 'gif', 'image/gif');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2003, 6, 'image_jpg', 'JPEG', 'jpg', 'image/jpg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2003, 6, 'image_jpg', 'JPEG', 'jpeg', 'image/jpg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2003, 6, 'image_jpg', 'JPEG', 'jpx', 'image/jpg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2004, 6, 'image_bmp', 'Bitmap', 'bmp', 'image/bmp');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2005, 6, 'image_svg', 'SVG', 'svg', 'image/svg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2006, 6, 'image_tiff', 'Tiff', 'tiff', 'image/tiff');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (2006, 6, 'image_tiff', 'Tiff', 'tiff', 'image/tiff-fx');

insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3000, 7, 'audio', 'Audio', NULL, 'audio/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3001, 7, 'audio_wav', 'Wave', 'wav', 'audio/wav');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3002, 7, 'audio_ogg', 'OGG', 'ogg', 'audio/ogg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3003, 7, 'audio_speex', 'Speex', 'spx', 'audio/speex');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3004, 7, 'audio_flac', 'FLAC', 'flac', 'audio/flac');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3005, 7, 'audio_mp3', 'MP3', 'mp3', 'audio/mpeg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3005, 7, 'audio_mp1', 'MP1', 'mp1', 'audio/mpeg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3005, 7, 'audio_mp2', 'MP2', 'mp2', 'audio/mpeg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3006, 7, 'audio_mp4', 'MP4', 'mp4', 'audio/mp4');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3007, 7, 'audio_midi', 'SVG', 'svg', 'audio/svg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3008, 7, 'audio_basic', 'Audio', 'au', 'audio/basic');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (3008, 7, 'audio_basic', 'Audio', 'snd', 'audio/basic');

insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4000, 8, 'video', 'Video', NULL, 'video/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4001, 8, 'video_mpeg', 'MPEG', 'mp4', 'video/mp4');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4001, 8, 'video_mpeg', 'MPEG', 'mpg', 'video/mp4');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4002, 8, 'video_ogg', 'OGG', 'ogg', 'video/ogg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4003, 8, 'video_h264', 'H264', 'h264', 'video/h264');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4003, 8, 'video_x264', 'X264', 'x264', 'video/x264');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4004, 8, 'video_quicktine', 'Quicktime', 'qt', 'video/qt');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4005, 8, 'video_dv', 'Digital Video', 'dv', 'video/dv');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4006, 8, 'video_jpeg', 'Motion JPEG', 'jpg', 'video/jpeg');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (4006, 8, 'video_webm', 'Tiff', 'tiff', 'video/tiff-fx');

insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5000, 9, 'document', 'Document', NULL, 'application/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5001, 9, 'document_libre_chart', 'LibreOffice Chart', 'odc', 'application/vnd.oasis.opendocument.chart');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5002, 9, 'document_libre_formula', 'LibreOffice Formula', 'odf', 'application/vnd.oasis.opendocument.formula');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5003, 9, 'document_libre_graphic', 'LibreOffice Graphic', 'odg', 'application/vnd.oasis.opendocument.graphics');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5004, 9, 'document_libre_presentation', 'LibreOffice Presentation', 'odp', 'application/vnd.oasis.opendocument.presentation');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5005, 9, 'document_libre_spreadsheet', 'LibreOffice Spreadsheet', 'ods', 'application/vnd.oasis.opendocument.spreadsheet');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5006, 9, 'document_libre_text', 'LibreOffice Text', 'odt', 'application/vnd.oasis.opendocument.text');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5007, 9, 'document_libre_html', 'LibreOffice HTML', 'odh', 'application/vnd.oasis.opendocument.text-web');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5008, 9, 'document_pdf', 'PDF', 'pdf', 'application/pdf');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5009, 9, 'document_abi_word', 'Abiword Text', 'abw', 'application/abiword-compressed');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5010, 9, 'document_ms_word', 'Microsoft Word', 'docx', 'application/msword');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5010, 9, 'document_ms_word', 'Microsoft Word', 'doc', 'application/msword');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5011, 9, 'document_ms_excel', 'Microsoft Excel', 'xlsx', 'application/ms-excel');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5011, 9, 'document_ms_excel', 'Microsoft Excel', 'xls', 'application/ms-excel');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5012, 9, 'document_ms_powerpoint', 'Microsoft Powerpoint', 'pptx', 'application/ms-powerpoint');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (5012, 9, 'document_ms_powerpoint', 'Microsoft Powerpoint', 'ppt', 'application/ms-powerpoint');

insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (6000, 10, 'container', 'Container', NULL, 'application/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (6001, 10, 'container_tar', 'Tar', 'tar', 'application/tar');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (6002, 10, 'container_cpio', 'CPIO', 'cpio', 'application/cpio');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (6003, 10, 'container_java', 'Java', 'jar', 'application/java');

insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (7000, 11, 'application', 'Application', NULL, 'application/*');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (7001, 11, 'application_ocsp_request', 'OCSP Request', NULL, 'application/ocsp-request');
insert into s_tables.t_types_mime_types (id, id_category, name_machine, name_human, field_extension, field_mime) values (7002, 11, 'application_ocsp_response', 'OCSP Response', NULL, 'application/ocsp-response');



/** create all of the known codes, initializing them to 0. **/
insert into s_tables.t_statistics_http_status_codes (code) values (0);
insert into s_tables.t_statistics_http_status_codes (code) values (1);
insert into s_tables.t_statistics_http_status_codes (code) values (2);

insert into s_tables.t_statistics_http_status_codes (code) values (100);
insert into s_tables.t_statistics_http_status_codes (code) values (101);
insert into s_tables.t_statistics_http_status_codes (code) values (102);

insert into s_tables.t_statistics_http_status_codes (code) values (200);
insert into s_tables.t_statistics_http_status_codes (code) values (201);
insert into s_tables.t_statistics_http_status_codes (code) values (202);
insert into s_tables.t_statistics_http_status_codes (code) values (203);
insert into s_tables.t_statistics_http_status_codes (code) values (204);
insert into s_tables.t_statistics_http_status_codes (code) values (205);
insert into s_tables.t_statistics_http_status_codes (code) values (206);
insert into s_tables.t_statistics_http_status_codes (code) values (207);
insert into s_tables.t_statistics_http_status_codes (code) values (208);
insert into s_tables.t_statistics_http_status_codes (code) values (226);

insert into s_tables.t_statistics_http_status_codes (code) values (300);
insert into s_tables.t_statistics_http_status_codes (code) values (301);
insert into s_tables.t_statistics_http_status_codes (code) values (302);
insert into s_tables.t_statistics_http_status_codes (code) values (303);
insert into s_tables.t_statistics_http_status_codes (code) values (304);
insert into s_tables.t_statistics_http_status_codes (code) values (305);
insert into s_tables.t_statistics_http_status_codes (code) values (306);
insert into s_tables.t_statistics_http_status_codes (code) values (307);
insert into s_tables.t_statistics_http_status_codes (code) values (308);

insert into s_tables.t_statistics_http_status_codes (code) values (400);
insert into s_tables.t_statistics_http_status_codes (code) values (401);
insert into s_tables.t_statistics_http_status_codes (code) values (402);
insert into s_tables.t_statistics_http_status_codes (code) values (403);
insert into s_tables.t_statistics_http_status_codes (code) values (404);
insert into s_tables.t_statistics_http_status_codes (code) values (405);
insert into s_tables.t_statistics_http_status_codes (code) values (406);
insert into s_tables.t_statistics_http_status_codes (code) values (407);
insert into s_tables.t_statistics_http_status_codes (code) values (408);
insert into s_tables.t_statistics_http_status_codes (code) values (409);
insert into s_tables.t_statistics_http_status_codes (code) values (410);
insert into s_tables.t_statistics_http_status_codes (code) values (411);
insert into s_tables.t_statistics_http_status_codes (code) values (412);
insert into s_tables.t_statistics_http_status_codes (code) values (413);
insert into s_tables.t_statistics_http_status_codes (code) values (414);
insert into s_tables.t_statistics_http_status_codes (code) values (415);
insert into s_tables.t_statistics_http_status_codes (code) values (416);
insert into s_tables.t_statistics_http_status_codes (code) values (417);
insert into s_tables.t_statistics_http_status_codes (code) values (422);
insert into s_tables.t_statistics_http_status_codes (code) values (423);
insert into s_tables.t_statistics_http_status_codes (code) values (424);
insert into s_tables.t_statistics_http_status_codes (code) values (426);
insert into s_tables.t_statistics_http_status_codes (code) values (428);
insert into s_tables.t_statistics_http_status_codes (code) values (429);
insert into s_tables.t_statistics_http_status_codes (code) values (431);
insert into s_tables.t_statistics_http_status_codes (code) values (451);

insert into s_tables.t_statistics_http_status_codes (code) values (500);
insert into s_tables.t_statistics_http_status_codes (code) values (501);
insert into s_tables.t_statistics_http_status_codes (code) values (502);
insert into s_tables.t_statistics_http_status_codes (code) values (503);
insert into s_tables.t_statistics_http_status_codes (code) values (504);
insert into s_tables.t_statistics_http_status_codes (code) values (505);
insert into s_tables.t_statistics_http_status_codes (code) values (506);
insert into s_tables.t_statistics_http_status_codes (code) values (507);
insert into s_tables.t_statistics_http_status_codes (code) values (508);
insert into s_tables.t_statistics_http_status_codes (code) values (510);
insert into s_tables.t_statistics_http_status_codes (code) values (511);



/* @todo: consider creating default request types */
insert into s_tables.t_request_types (id, name_machine, name_human) values (0, 'none', 'None');




/*** start the sequence count at 1000 to allow for < 1000 to be reserved for special uses ***/
alter sequence s_tables.se_users_id start 1000;
alter sequence s_tables.se_users_id restart;


/*** create hard-coded/internal user ids ***/
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_administer) values (3, 'u_reservation_system_administer', (null, 'System', null, 'Administer', null, 'System (Administer)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_manager) values (4, 'u_reservation_system_manager', (null, 'System', null, 'Manager', null, 'System (Manager)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_auditor) values (5, 'u_reservation_system_auditor', (null, 'System', null, 'Auditor', null, 'System (Auditor)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_publisher) values (6, 'u_reservation_system_publisher', (null, 'System', null, 'Publisher', null, 'System (Publisher)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_insurer) values (7, 'u_reservation_system_insurer', (null, 'System', null, 'Insurer', null, 'System (Insurer)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_financer) values (8, 'u_reservation_system_financer', (null, 'System', null, 'Financer', null, 'System (Financer)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_reviewer) values (9, 'u_reservation_system_reviewer', (null, 'System', null, 'Reviewer', null, 'System (Reviewer)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_editor) values (10, 'u_reservation_system_editor', (null, 'System', null, 'Editor', null, 'System (Editor)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_drafter) values (11, 'u_reservation_system_drafter', (null, 'System', null, 'Drafter', null, 'System (Drafter)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_requester) values (12, 'u_reservation_system_requester', (null, 'System', null, 'Requester', null, 'System (Requester)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system, is_public) values (13, 'u_reservation_system_public', (null, 'System', null, 'Public', null, 'System (Public)'), false, true, true);
insert into s_tables.t_users (id, name_machine, name_human, is_private, is_system) values (14, 'u_reservation_system', (null, 'System', null, 'System', null, 'System'), false, true);



commit;
