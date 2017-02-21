/** Standardized SQL Structure - Content **/
/** This depends on: base-fields.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,drafters,users,public;
set datestyle to us;



/*** Request: Type ***/
create table managers.t_request_types (
  id bigint not null,
  id_external bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_deleted timestamp,

  constraint cu_request_types_id unique (id),
  constraint cu_request_types_name_machine unique (name_machine),

  constraint cc_request_types_id check (id > 0)
);

create sequence managers.s_request_types_id owned by managers.t_request_types.id;
alter table managers.t_request_types alter column id set default nextval('managers.s_request_types_id'::regclass);

grant select,insert,update on managers.t_request_types to reservation_users_administer;
grant select,insert,update on managers.t_request_types to reservation_users_manager;
grant select on managers.t_request_types to reservation_users_auditor;
grant select,usage on managers.s_request_types_id to reservation_users_administer;
grant select,usage on managers.s_request_types_id to reservation_users_manager;

create view users.v_request_types with (security_barrier=true) as
  select id, id_external, name_machine, name_human from managers.t_request_types
  where is_deleted is false;

grant select on users.v_request_types to reservation_users;

/** @todo: consider creating default request types **/



/*** Requests ***/
create table managers.t_requests (
  id bigint not null,
  id_owner bigint not null,
  id_revision bigint not null,
  id_type bigint not null,

  name_machine varchar(128) not null,
  name_machine_owner varchar(128) not null,
  name_human varchar(256) not null,

  is_approved boolean default false not null,
  is_cancelled boolean default false not null,
  is_deleted boolean default false not null,
  is_denied boolean default false not null,
  is_troubled boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_synced timestamp default localtimestamp not null,
  date_deleted timestamp,

  field_additional text not null,
  field_dates public.ct_date_context[] not null,
  field_fees_custodial public.ct_field_fees[] not null,
  field_fees_equipment public.ct_field_fees[] not null,
  field_fees_facilities public.ct_field_fees[] not null,
  field_fees_grounds public.ct_field_fees[] not null,
  field_fees_maintenance public.ct_field_fees[] not null,
  field_fees_other public.ct_field_fees[] not null,
  field_fees_security public.ct_field_fees[] not null,
  field_fees_university public.ct_field_fees[] not null,
  field_location public.ct_location[] not null,
  field_information_attendance bigint not null,
  field_information_organization bigint not null,
  field_information_adviser_approval bool not null,
  field_insurance_affiliated public.ct_field_insurance not null,
  field_insurance_contractor public.ct_field_insurance not null,
  field_insurance_unaffiliated public.ct_field_insurance not null,
  field_plans_activities text not null,
  field_plans_audience bigint not null,
  field_plans_description text not null,
  field_presentation_designing_material public.ct_field_used_with_contact not null,
  field_presentation_external_audio_person public.ct_field_used_with_contact not null,
  field_presentation_production public.ct_field_used_with_contact not null,
  field_presentation_printed_material public.ct_field_used_with_details not null,
  field_presentation_publicity public.ct_field_needed_with_types not null,
  field_presentation_technical_equipment public.ct_field_needed_with_types_and_microphone not null,
  field_presentation_university_logo public.ct_field_used_with_designer not null,
  field_registration_revenue public.ct_field_generated_with_types not null,
  field_registration_phone public.ct_phone_number not null,
  field_registration_required bool not null,
  field_registration_ticket_dates public.ct_date_context[] not null,
  field_registration_ticket_phone public.ct_phone_number_context not null,
  field_registration_ticket_price public.ct_money_context[] not null,
  field_registration_ticket_website text not null,
  field_registration_website text not null,
  field_setup_other_tables public.ct_field_needed_with_details not null,
  field_setup_parking_assistance public.ct_field_needed_with_details not null,
  field_setup_podium public.ct_field_needed_with_details not null,
  field_setup_portable_stage public.ct_field_needed_with_details not null,
  field_setup_rectangular_tables_8ft public.ct_field_needed_with_total not null,
  field_setup_road_closures public.ct_field_needed_with_details not null,
  field_setup_round_tables_8ft public.ct_field_needed_with_total not null,
  field_setup_security public.ct_field_needed_with_details not null,
  field_setup_special_requests public.ct_field_needed_with_details not null,
  field_setup_standard_blue_chairs public.ct_field_needed_with_total not null;
  field_services_alcohol_served public.ct_field_served_with_caterer not null,
  field_services_food public.ct_field_served_with_caterer not null,
  field_services_open_flames public.ct_field_used_with_details not null,
  field_title text not null,

  in_state bigint not null,
  in_step bigint not null,

  constraint cu_requests_id unique (id),
  constraint cu_requests_name_machine unique (name_machine),

  constraint cc_requests_id check (id > 0),

  constraint cf_requests_id_owner foreign key (id_owner, name_machine_owner) references administers.t_users (id, name_machine) on delete restrict on update cascade,
  constraint cf_requests_request_type foreign key (id_type) references managers.t_request_types (id) on delete restrict on update cascade
);

create sequence managers.s_requests_id owned by managers.t_requests.id;
alter table managers.t_requests alter column id set default nextval('managers.s_requests_id'::regclass);

grant select,insert,update on managers.t_requests to reservation_users_administer;
grant select,insert,update on managers.t_requests to reservation_users_manager;
grant select on managers.t_requests to reservation_users_auditor;
grant select,usage on managers.s_requests_id to reservation_users_administer;
grant select,usage on managers.s_requests_id to reservation_users_manager;
grant usage on managers.s_requests_id to reservation_users;


/** @todo: create all appropriate views, including individual views for is_cancelled, is_deleted, is_published, is_unpublished, is_denied, and is_troubled **/


/** @todo: create "managers.t_requests_revision" that is identical to "managers.t_requests" with all the columns allowed to be null.

commit transaction;
