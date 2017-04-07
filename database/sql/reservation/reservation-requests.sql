/** Standardized SQL Structure - Requests **/
/** This depends on: reservation-fields.sql, base-workflow.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to s_administers,s_managers,s_auditors,s_publishers,s_insurers,s_financers,s_reviewers,s_editors,s_drafters,s_requesters,s_users,public;
set datestyle to us;



/*** Request: Type ***/
create table s_tables.t_request_types (
  id bigint not null,
  id_external bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_locked timestamp,
  date_deleted timestamp,

  constraint cu_request_types_id unique (id),
  constraint cu_request_types_name_machine unique (name_machine),

  constraint cc_request_types_id check (id >= 0),
  constraint cc_request_types_name_machine check (name_machine ~ '[A-Za-z]\w*')
);

create sequence s_tables.se_request_types_id owned by s_tables.t_request_types.id;
alter table s_tables.t_request_types alter column id set default nextval('s_tables.se_request_types_id'::regclass);

grant select,insert,update on s_tables.t_request_types to r_reservation_manager;
grant select on s_tables.t_request_types to r_reservation_auditor;
grant select,usage on s_tables.se_request_types_id to r_reservation_manager;

create index i_request_types_deleted_not on s_tables.t_request_types (id)
  where not is_deleted;

create index i_request_types_public on s_tables.t_request_types (id)
  where not is_deleted and not is_locked;


create view s_requesters.v_request_types with (security_barrier=true) as
  select id, id_external, name_machine, name_human from s_tables.t_request_types
  where not is_deleted and not is_locked;

grant select on s_requesters.v_request_types to r_reservation_auditor, r_reservation_requester;


create trigger tr_request_types_update_date_changed_deleted_or_locked
  before update on s_tables.t_request_types
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();



/*** Requests ***/
create table s_tables.t_requests (
  id bigint not null,
  id_revision bigint not null default 0,
  id_type bigint not null,
  id_association bigint not null,
  id_creator bigint not null,
  id_creator_session bigint not null,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_approved boolean default false not null,
  is_denied boolean default false not null,
  is_troubled boolean default false not null,
  is_cancelled boolean default false not null,
  is_locked boolean default false not null,
  is_deleted boolean default false not null,

  date_created timestamp default localtimestamp not null,
  date_changed timestamp default localtimestamp not null,
  date_synced timestamp default localtimestamp not null,
  date_approved timestamp,
  date_denied timestamp,
  date_troubled timestamp,
  date_cancelled timestamp,
  date_locked timestamp,
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
  field_setup_standard_blue_chairs public.ct_field_needed_with_total not null,
  field_services_alcohol_served public.ct_field_served_with_caterer not null,
  field_services_food public.ct_field_served_with_caterer not null,
  field_services_open_flames public.ct_field_used_with_details not null,
  field_title text not null,

  in_state bigint not null,
  in_step bigint not null,

  constraint cp_requests primary key (id),
  constraint cu_requests_name_machine unique (name_machine),

  constraint cc_requests_id check (id > 0),
  constraint cc_requests_id_revision check (id_revision > -1),
  constraint cc_requests_approved check ((is_approved and not is_denied) or (not is_approved and is_denied)),
  constraint cc_requests_name_machine check (name_machine ~ '[A-Za-z]\w*'),

  constraint cf_requests_id_creator foreign key (id_creator) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_requests_id_creator_session foreign key (id_creator_session) references s_tables.t_users (id) on delete restrict on update cascade,
  constraint cf_requests_request_type foreign key (id_type) references s_tables.t_request_types (id) on delete restrict on update cascade,
  constraint cf_requests_association foreign key (id_association) references s_tables.t_associations (id) on delete restrict on update cascade
);

create sequence s_tables.se_requests_id owned by s_tables.t_requests.id;
alter table s_tables.t_requests alter column id set default nextval('s_tables.se_requests_id'::regclass);

grant select,insert,update on s_tables.t_requests to r_reservation_manager;
grant select on s_tables.t_requests to r_reservation_auditor;
grant select,usage on s_tables.se_requests_id to r_reservation_manager;
grant usage on s_tables.se_requests_id to r_reservation, r_reservation_system;

create index i_requests_deleted_not on s_tables.t_requests (id)
  where not is_deleted;

create index i_requests_locked_not on s_tables.t_requests (id)
  where not is_deleted and not is_locked;

create index i_requests_approved on s_tables.t_requests (id)
  where not is_deleted and not is_cancelled and is_approved;

create index i_requests_approved_cancelled on s_tables.t_requests (id)
  where not is_deleted and is_cancelled and is_approved;

create index i_requests_denied on s_tables.t_requests (id)
  where not is_deleted and not is_cancelled and is_denied;

create index i_requests_troubled on s_tables.t_requests (id)
  where not is_deleted and not is_cancelled and is_troubled;

create index i_requests_cancelled on s_tables.t_requests (id)
  where not is_deleted and is_cancelled;

create index i_requests_locked on s_tables.t_requests (id)
  where not is_deleted and is_locked;


/*** approved requests (but not cancelled) ***/
create view s_users.v_requests_approved with (security_barrier=true) as
  select id, id_revision, id_type, id_association,
  name_machine, name_human,
  is_troubled,
  date_created, date_changed, date_synced, date_approved, date_troubled,
  field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
  in_state, in_step
  from s_tables.t_requests
    where not is_deleted and not is_cancelled and is_approved;

grant select on s_users.v_requests_approved to r_reservation, r_reservation_system;


/*** approved requests (only cancelled) ***/
create view s_users.v_requests_approved_cancelled with (security_barrier=true) as
  select id, id_revision, id_type, id_association,
  name_machine, name_human,
  is_troubled,
  date_created, date_changed, date_synced, date_approved, date_troubled, date_cancelled,
  field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
  in_state, in_step
  from s_tables.t_requests
    where not is_deleted and is_cancelled and is_approved;

grant select on s_users.v_requests_approved_cancelled to r_reservation, r_reservation_system;


/*** denied requests (but not cancelled) ***/
create view s_users.v_requests_denied with (security_barrier=true) as
  select id, id_revision, id_type, id_association,
  name_machine, name_human,
  is_troubled,
  date_created, date_changed, date_synced, date_denied, date_troubled,
  field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
  in_state, in_step
  from s_tables.t_requests
    where not is_deleted and not is_cancelled and is_denied;

grant select on s_users.v_requests_denied to r_reservation, r_reservation_system;


/*** troubled requests (but not cancelled) ***/
create view s_users.v_requests_troubled with (security_barrier=true) as
  select id, id_revision, id_type, id_association,
  name_machine, name_human,
  is_approved, is_denied, is_troubled,
  date_created, date_changed, date_synced, date_approved, date_denied, date_troubled,
  field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
  in_state, in_step
  from s_tables.t_requests
    where not is_deleted and not is_cancelled and is_troubled;

grant select on s_users.v_requests_troubled to r_reservation, r_reservation_system;


/*** cancelled requests ***/
create view s_users.v_requests_cancelled with (security_barrier=true) as
  select id, id_revision, id_type, id_association,
  name_machine, name_human,
  is_approved, is_denied, is_troubled,
  date_created, date_changed, date_synced, date_approved, date_denied, date_troubled, date_cancelled,
  field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
  in_state, in_step
  from s_tables.t_requests
    where not is_deleted and is_cancelled;

grant select on s_users.v_requests_cancelled to r_reservation, r_reservation_system;


/*** requests the current user belongs to or can manage. ***/
create view s_users.v_requests_self with (security_barrier=true) as
  with associations as (select id from s_users.v_associations_self)
  select id, id_revision, id_type, id_association,
  name_machine, name_human,
  is_approved, is_denied, is_troubled, is_cancelled,
  date_created, date_changed, date_synced, date_approved, date_denied, date_troubled, date_cancelled,
  field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
  in_state, in_step
  from s_tables.t_requests
    where not is_deleted and id_association in (select id from associations);

grant select on s_users.v_requests_self to r_reservation, r_reservation_system;


/*** requests the current user belongs to or can manage. ***/
create view s_users.v_requests_manage with (security_barrier=true) as
  with associations as (select id from s_users.v_associations_manage)
  select id, id_revision, id_type, id_association,
  name_machine, name_human,
  is_approved, is_denied, is_troubled, is_cancelled,
  date_created, date_changed, date_synced, date_approved, date_denied, date_troubled, date_cancelled,
  field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
  in_state, in_step
  from s_tables.t_requests
    where not is_deleted and id_association in (select id from associations);

grant select on s_users.v_requests_self to r_reservation, r_reservation_system;


/*** requests the current user belongs to or can coordinate. ***/
create view s_users.v_requests_coordinate with (security_barrier=true) as
  with associations as (select id from s_users.v_associations_coordinate)
  select id, id_revision, id_type, id_association,
  name_machine, name_human,
  is_approved, is_denied, is_troubled, is_cancelled,
  date_created, date_changed, date_synced, date_approved, date_denied, date_troubled, date_cancelled,
  field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
  in_state, in_step
  from s_tables.t_requests
    where not is_deleted and id_association in (select id from associations);

grant select on s_users.v_requests_self to r_reservation, r_reservation_system;


create trigger tr_requests_update_date_changed_deleted_or_locked
  before update on s_tables.t_requests
    for each row execute procedure s_administers.f_common_update_date_changed_deleted_or_locked();

create trigger tr_requests_enforce_creator_and_session_ids
  before insert on s_tables.t_requests
    for each row execute procedure s_administers.f_common_enforce_creator_and_session_ids();



/*** Request Original Version (store the first/initial revision) ***/
create table s_tables.t_request_revisions_original (
  id_request bigint not null,
  id_type bigint,
  id_association bigint,

  name_machine varchar(128),
  name_human varchar(256),

  is_approved boolean,
  is_denied boolean,
  is_troubled boolean,
  is_cancelled boolean,
  is_locked boolean,
  is_deleted boolean,

  field_additional text,
  field_dates public.ct_date_context[],
  field_fees_custodial public.ct_field_fees[],
  field_fees_equipment public.ct_field_fees[],
  field_fees_facilities public.ct_field_fees[],
  field_fees_grounds public.ct_field_fees[],
  field_fees_maintenance public.ct_field_fees[],
  field_fees_other public.ct_field_fees[],
  field_fees_security public.ct_field_fees[],
  field_fees_university public.ct_field_fees[],
  field_location public.ct_location[],
  field_information_attendance bigint,
  field_information_organization bigint,
  field_information_adviser_approval bool,
  field_insurance_affiliated public.ct_field_insurance,
  field_insurance_contractor public.ct_field_insurance,
  field_insurance_unaffiliated public.ct_field_insurance,
  field_plans_activities text,
  field_plans_audience bigint,
  field_plans_description text,
  field_presentation_designing_material public.ct_field_used_with_contact,
  field_presentation_external_audio_person public.ct_field_used_with_contact,
  field_presentation_production public.ct_field_used_with_contact,
  field_presentation_printed_material public.ct_field_used_with_details,
  field_presentation_publicity public.ct_field_needed_with_types,
  field_presentation_technical_equipment public.ct_field_needed_with_types_and_microphone,
  field_presentation_university_logo public.ct_field_used_with_designer,
  field_registration_revenue public.ct_field_generated_with_types,
  field_registration_phone public.ct_phone_number,
  field_registration_required bool,
  field_registration_ticket_dates public.ct_date_context[],
  field_registration_ticket_phone public.ct_phone_number_context,
  field_registration_ticket_price public.ct_money_context[],
  field_registration_ticket_website text,
  field_registration_website text,
  field_setup_other_tables public.ct_field_needed_with_details,
  field_setup_parking_assistance public.ct_field_needed_with_details,
  field_setup_podium public.ct_field_needed_with_details,
  field_setup_portable_stage public.ct_field_needed_with_details,
  field_setup_rectangular_tables_8ft public.ct_field_needed_with_total,
  field_setup_road_closures public.ct_field_needed_with_details,
  field_setup_round_tables_8ft public.ct_field_needed_with_total,
  field_setup_security public.ct_field_needed_with_details,
  field_setup_special_requests public.ct_field_needed_with_details,
  field_setup_standard_blue_chairs public.ct_field_needed_with_total,
  field_services_alcohol_served public.ct_field_served_with_caterer,
  field_services_food public.ct_field_served_with_caterer,
  field_services_open_flames public.ct_field_used_with_details,
  field_title text,

  in_state bigint,
  in_step bigint,

  constraint cp_request_revisions_original primary key (id_request),

  constraint cc_request_revisions_original_approved check ((is_approved and not is_denied) or (not is_approved and is_denied)),

  constraint cf_request_revisions_original_id_request foreign key (id_request) references s_tables.t_requests (id) on delete restrict on update cascade,
  constraint cf_request_revisions_original_request_type foreign key (id_type) references s_tables.t_request_types (id) on delete restrict on update cascade,
  constraint cf_request_revisions_original_association foreign key (id_association) references s_tables.t_associations (id) on delete restrict on update cascade
);

grant select,insert on s_tables.t_request_revisions_original to r_reservation_administer, u_reservation_revision_requests;
grant select on s_tables.t_request_revisions_original to r_reservation_manager, r_reservation_auditor;


/** automatically insert the original request into the original requests table. **/
create function s_tables.f_request_revisions_original_record_revision() returns trigger security definer as $$
  begin
    insert into s_tables.t_request_revisions
      (
        id_request, id_type, id_association,
        name_machine, name_human,
        is_approved, is_denied, is_troubled, is_cancelled, is_locked, is_deleted,
        date_created,
        field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
        in_state, in_step
      )
      values
      (
        new.id, new.id_type, new.id_association,
        new.name_machine, new.name_human,
        new.is_approved, new.is_denied, new.is_troubled, new.is_cancelled, new.is_locked, new.is_deleted,
        new.date_created,
        new.field_additional, new.field_dates, new.field_fees_custodial, new.field_fees_equipment, new.field_fees_facilities, new.field_fees_grounds, new.field_fees_maintenance, new.field_fees_other, new.field_fees_security, new.field_fees_university, new.field_location, new.field_information_attendance, new.field_information_organization, new.field_information_adviser_approval, new.field_insurance_affiliated, new.field_insurance_contractor, new.field_insurance_unaffiliated, new.field_plans_activities, new.field_plans_audience, new.field_plans_description, new.field_presentation_designing_material, new.field_presentation_external_audio_person, new.field_presentation_production, new.field_presentation_printed_material, new.field_presentation_publicity, new.field_presentation_technical_equipment, new.field_presentation_university_logo, new.field_registration_revenue, new.field_registration_phone, new.field_registration_required, new.field_registration_ticket_dates, new.field_registration_ticket_phone, new.field_registration_ticket_price, new.field_registration_ticket_website, new.field_registration_website, new.field_setup_other_tables, new.field_setup_parking_assistance, new.field_setup_podium, new.field_setup_portable_stage, new.field_setup_rectangular_tables_8ft, new.field_setup_road_closures, new.field_setup_round_tables_8ft, new.field_setup_security, new.field_setup_special_requests, new.field_setup_standard_blue_chairs, new.field_services_alcohol_served, new.field_services_food, new.field_services_open_flames, new.field_title,
        new.in_state, new.in_step
      );
  end;
$$ language plpgsql;

alter function s_tables.f_request_revisions_original_record_revision () owner to u_reservation_revision_requests;

create trigger tr_requests_save_original_revision
  after insert on s_tables.t_requests
    for each row execute procedure s_tables.f_request_revisions_original_record_revision();


/*** Request Revisions ***/
create table s_tables.t_request_revisions (
  id_request bigint not null,
  id_revision bigint not null,
  id_type bigint,
  id_association bigint,

  name_machine varchar(128),
  name_human varchar(256),

  is_approved boolean,
  is_denied boolean,
  is_troubled boolean,
  is_cancelled boolean,
  is_locked boolean,
  is_deleted boolean,

  date_changed timestamp default localtimestamp not null,

  field_additional text,
  field_dates public.ct_date_context[],
  field_fees_custodial public.ct_field_fees[],
  field_fees_equipment public.ct_field_fees[],
  field_fees_facilities public.ct_field_fees[],
  field_fees_grounds public.ct_field_fees[],
  field_fees_maintenance public.ct_field_fees[],
  field_fees_other public.ct_field_fees[],
  field_fees_security public.ct_field_fees[],
  field_fees_university public.ct_field_fees[],
  field_location public.ct_location[],
  field_information_attendance bigint,
  field_information_organization bigint,
  field_information_adviser_approval bool,
  field_insurance_affiliated public.ct_field_insurance,
  field_insurance_contractor public.ct_field_insurance,
  field_insurance_unaffiliated public.ct_field_insurance,
  field_plans_activities text,
  field_plans_audience bigint,
  field_plans_description text,
  field_presentation_designing_material public.ct_field_used_with_contact,
  field_presentation_external_audio_person public.ct_field_used_with_contact,
  field_presentation_production public.ct_field_used_with_contact,
  field_presentation_printed_material public.ct_field_used_with_details,
  field_presentation_publicity public.ct_field_needed_with_types,
  field_presentation_technical_equipment public.ct_field_needed_with_types_and_microphone,
  field_presentation_university_logo public.ct_field_used_with_designer,
  field_registration_revenue public.ct_field_generated_with_types,
  field_registration_phone public.ct_phone_number,
  field_registration_required bool,
  field_registration_ticket_dates public.ct_date_context[],
  field_registration_ticket_phone public.ct_phone_number_context,
  field_registration_ticket_price public.ct_money_context[],
  field_registration_ticket_website text,
  field_registration_website text,
  field_setup_other_tables public.ct_field_needed_with_details,
  field_setup_parking_assistance public.ct_field_needed_with_details,
  field_setup_podium public.ct_field_needed_with_details,
  field_setup_portable_stage public.ct_field_needed_with_details,
  field_setup_rectangular_tables_8ft public.ct_field_needed_with_total,
  field_setup_road_closures public.ct_field_needed_with_details,
  field_setup_round_tables_8ft public.ct_field_needed_with_total,
  field_setup_security public.ct_field_needed_with_details,
  field_setup_special_requests public.ct_field_needed_with_details,
  field_setup_standard_blue_chairs public.ct_field_needed_with_total,
  field_services_alcohol_served public.ct_field_served_with_caterer,
  field_services_food public.ct_field_served_with_caterer,
  field_services_open_flames public.ct_field_used_with_details,
  field_title text,

  in_state bigint,
  in_step bigint,

  constraint cp_request_revisions primary key (id_request, id_revision),

  constraint cc_request_revisions_id_request check (id_request > 0),
  constraint cc_request_revisions_approved check ((is_approved and not is_denied) or (not is_approved and is_denied)),

  constraint cf_request_revisions_id_request foreign key (id_request) references s_tables.t_requests (id) on delete restrict on update cascade,
  constraint cf_request_revisions_request_type foreign key (id_type) references s_tables.t_request_types (id) on delete restrict on update cascade,
  constraint cf_request_revisions_association foreign key (id_association) references s_tables.t_associations (id) on delete restrict on update cascade
);

grant select,insert on s_tables.t_request_revisions to r_reservation_administer, u_reservation_revision_requests;
grant select on s_tables.t_request_revisions to r_reservation_manager, r_reservation_auditor;


/** automatically update the request revision table. **/
create function s_tables.f_request_revisions_record_revision() returns trigger security definer as $$
  declare
  tmp_id_type bigint;
  tmp_id_association bigint;

  tmp_name_machine varchar(128);
  tmp_name_human varchar(256);

  tmp_is_approved boolean;
  tmp_is_denied boolean;
  tmp_is_troubled boolean;
  tmp_is_cancelled boolean;
  tmp_is_locked boolean;
  tmp_is_deleted boolean;

  tmp_field_additional text;
  tmp_field_dates public.ct_date_context[];
  tmp_field_fees_custodial public.ct_field_fees[];
  tmp_field_fees_equipment public.ct_field_fees[];
  tmp_field_fees_facilities public.ct_field_fees[];
  tmp_field_fees_grounds public.ct_field_fees[];
  tmp_field_fees_maintenance public.ct_field_fees[];
  tmp_field_fees_other public.ct_field_fees[];
  tmp_field_fees_security public.ct_field_fees[];
  tmp_field_fees_university public.ct_field_fees[];
  tmp_field_location public.ct_location[];
  tmp_field_information_attendance bigint;
  tmp_field_information_organization bigint;
  tmp_field_information_adviser_approval bool;
  tmp_field_insurance_affiliated public.ct_field_insurance;
  tmp_field_insurance_contractor public.ct_field_insurance;
  tmp_field_insurance_unaffiliated public.ct_field_insurance;
  tmp_field_plans_activities text;
  tmp_field_plans_audience bigint;
  tmp_field_plans_description text;
  tmp_field_presentation_designing_material public.ct_field_used_with_contact;
  tmp_field_presentation_external_audio_person public.ct_field_used_with_contact;
  tmp_field_presentation_production public.ct_field_used_with_contact;
  tmp_field_presentation_printed_material public.ct_field_used_with_details;
  tmp_field_presentation_publicity public.ct_field_needed_with_types;
  tmp_field_presentation_technical_equipment public.ct_field_needed_with_types_and_microphone;
  tmp_field_presentation_university_logo public.ct_field_used_with_designer;
  tmp_field_registration_revenue public.ct_field_generated_with_types;
  tmp_field_registration_phone public.ct_phone_number;
  tmp_field_registration_required bool;
  tmp_field_registration_ticket_dates public.ct_date_context[];
  tmp_field_registration_ticket_phone public.ct_phone_number_context;
  tmp_field_registration_ticket_price public.ct_money_context[];
  tmp_field_registration_ticket_website text;
  tmp_field_registration_website text;
  tmp_field_setup_other_tables public.ct_field_needed_with_details;
  tmp_field_setup_parking_assistance public.ct_field_needed_with_details;
  tmp_field_setup_podium public.ct_field_needed_with_details;
  tmp_field_setup_portable_stage public.ct_field_needed_with_details;
  tmp_field_setup_rectangular_tables_8ft public.ct_field_needed_with_total;
  tmp_field_setup_road_closures public.ct_field_needed_with_details;
  tmp_field_setup_round_tables_8ft public.ct_field_needed_with_total;
  tmp_field_setup_security public.ct_field_needed_with_details;
  tmp_field_setup_special_requests public.ct_field_needed_with_details;
  tmp_field_setup_standard_blue_chairs public.ct_field_needed_with_total;
  tmp_field_services_alcohol_served public.ct_field_served_with_caterer;
  tmp_field_services_food public.ct_field_served_with_caterer;
  tmp_field_services_open_flames public.ct_field_used_with_details;
  tmp_field_title text;

  tmp_in_state bigint;
  tmp_in_step bigint;

  begin
    if (old.id_type != new.id_type) then
      tmp_id_type = new.id_type;
    end if;

    if (old.id_association != new.id_association) then
      tmp_id_association = new.id_association;
    end if;

    if (old.name_machine != new.name_machine) then
      tmp_name_machine = new.name_machine;
    end if;

    if (old.name_human != new.name_human) then
      tmp_name_human = new.name_human;
    end if;

    if (old.is_approved != new.is_approved) then
      tmp_is_approved = new.is_approved;
    end if;

    if (old.is_denied != new.is_denied) then
      tmp_is_denied = new.is_denied;
    end if;

    if (old.is_troubled != new.is_troubled) then
      tmp_is_troubled = new.is_troubled;
    end if;

    if (old.is_cancelled != new.is_cancelled) then
      tmp_is_cancelled = new.is_cancelled;
    end if;

    if (old.is_locked != new.is_locked) then
      tmp_is_locked = new.is_locked;
    end if;

    if (old.is_deleted != new.is_deleted) then
      tmp_is_deleted = new.is_deleted;
    end if;

    if (old.field_additional != new.field_additional) then
      tmp_field_additional = new.field_additional;
    end if;

    if (old.field_dates != new.field_dates) then
      tmp_field_dates = new.field_dates;
    end if;

    if (old.field_fees_custodial != new.field_fees_custodial) then
      tmp_field_fees_custodial = new.field_fees_custodial;
    end if;

    if (old.field_fees_equipment != new.field_fees_equipment) then
      tmp_field_fees_equipment = new.field_fees_equipment;
    end if;

    if (old.field_fees_facilities != new.field_fees_facilities) then
      tmp_field_fees_facilities = new.field_fees_facilities;
    end if;

    if (old.field_fees_grounds != new.field_fees_grounds) then
      tmp_field_fees_grounds = new.field_fees_grounds;
    end if;

    if (old.field_fees_maintenance != new.field_fees_maintenance) then
      tmp_field_fees_maintenance = new.field_fees_maintenance;
    end if;

    if (old.field_fees_other != new.field_fees_other) then
      tmp_field_fees_other = new.field_fees_other;
    end if;

    if (old.field_fees_security != new.field_fees_security) then
      tmp_field_fees_security = new.field_fees_security;
    end if;

    if (old.field_fees_university != new.field_fees_university) then
      tmp_field_fees_university = new.field_fees_university;
    end if;

    if (old.field_location != new.field_location) then
      tmp_field_location = new.field_location;
    end if;

    if (old.field_information_attendance != new.field_information_attendance) then
      tmp_field_information_attendance = new.field_information_attendance;
    end if;

    if (old.field_information_organization != new.field_information_organization) then
      tmp_field_information_organization = new.field_information_organization;
    end if;

    if (old.field_information_adviser_approval != new.field_information_adviser_approval) then
      tmp_field_information_adviser_approval = new.field_information_adviser_approval;
    end if;

    if (old.field_insurance_affiliated != new.field_insurance_affiliated) then
      tmp_field_insurance_affiliated = new.field_insurance_affiliated;
    end if;

    if (old.field_insurance_contractor != new.field_insurance_contractor) then
      tmp_field_insurance_contractor = new.field_insurance_contractor;
    end if;

    if (old.field_insurance_unaffiliated != new.field_insurance_unaffiliated) then
      tmp_field_insurance_unaffiliated = new.field_insurance_unaffiliated;
    end if;

    if (old.field_plans_activities != new.field_plans_activities) then
      tmp_field_plans_activities = new.field_plans_activities;
    end if;

    if (old.field_plans_audience != new.field_plans_audience) then
      tmp_field_plans_audience = new.field_plans_audience;
    end if;

    if (old.field_plans_description != new.field_plans_description) then
      tmp_field_plans_description = new.field_plans_description;
    end if;

    if (old.field_presentation_designing_material != new.field_presentation_designing_material) then
      tmp_field_presentation_designing_material = new.field_presentation_designing_material;
    end if;

    if (old.field_presentation_external_audio_person != new.field_presentation_external_audio_person) then
      tmp_field_presentation_external_audio_person = new.field_presentation_external_audio_person;
    end if;

    if (old.field_presentation_production != new.field_presentation_production) then
      tmp_field_presentation_production = new.field_presentation_production;
    end if;

    if (old.field_presentation_printed_material != new.field_presentation_printed_material) then
      tmp_field_presentation_printed_material = new.field_presentation_printed_material;
    end if;

    if (old.field_presentation_publicity != new.field_presentation_publicity) then
      tmp_field_presentation_publicity = new.field_presentation_publicity;
    end if;

    if (old.field_presentation_technical_equipment != new.field_presentation_technical_equipment) then
      tmp_field_presentation_technical_equipment = new.field_presentation_technical_equipment;
    end if;

    if (old.field_presentation_university_logo != new.field_presentation_university_logo) then
      tmp_field_presentation_university_logo = new.field_presentation_university_logo;
    end if;

    if (old.field_registration_revenue != new.field_registration_revenue) then
      tmp_field_registration_revenue = new.field_registration_revenue;
    end if;

    if (old.field_registration_phone != new.field_registration_phone) then
      tmp_field_registration_phone = new.field_registration_phone;
    end if;

    if (old.field_registration_required != new.field_registration_required) then
      tmp_field_registration_required = new.field_registration_required;
    end if;

    if (old.field_registration_required != new.field_registration_required) then
      tmp_field_registration_required = new.field_registration_required;
    end if;

    if (old.field_registration_ticket_dates != new.field_registration_ticket_dates) then
      tmp_field_registration_ticket_dates = new.field_registration_ticket_dates;
    end if;

    if (old.field_registration_ticket_phone != new.field_registration_ticket_phone) then
      tmp_field_registration_ticket_phone = new.field_registration_ticket_phone;
    end if;

    if (old.field_registration_ticket_price != new.field_registration_ticket_price) then
      tmp_field_registration_ticket_price = new.field_registration_ticket_price;
    end if;

    if (old.field_registration_ticket_website != new.field_registration_ticket_website) then
      tmp_field_registration_ticket_website = new.field_registration_ticket_website;
    end if;

    if (old.field_registration_website != new.field_registration_website) then
      tmp_field_registration_website = new.field_registration_website;
    end if;

    if (old.field_setup_other_tables != new.field_setup_other_tables) then
      tmp_field_setup_other_tables = new.field_setup_other_tables;
    end if;

    if (old.field_setup_parking_assistance != new.field_setup_parking_assistance) then
      tmp_field_setup_parking_assistance = new.field_setup_parking_assistance;
    end if;

    if (old.field_setup_podium != new.field_setup_podium) then
      tmp_field_setup_podium = new.field_setup_podium;
    end if;

    if (old.field_setup_portable_stage != new.field_setup_portable_stage) then
      tmp_field_setup_portable_stage = new.field_setup_portable_stage;
    end if;

    if (old.field_setup_rectangular_tables_8ft != new.field_setup_rectangular_tables_8ft) then
      tmp_field_setup_rectangular_tables_8ft = new.field_setup_rectangular_tables_8ft;
    end if;

    if (old.field_setup_road_closures != new.field_setup_road_closures) then
      tmp_field_setup_road_closures = new.field_setup_road_closures;
    end if;

    if (old.field_setup_round_tables_8ft != new.field_setup_round_tables_8ft) then
      tmp_field_setup_round_tables_8ft = new.field_setup_round_tables_8ft;
    end if;

    if (old.field_setup_security != new.field_setup_security) then
      tmp_field_setup_security = new.field_setup_security;
    end if;

    if (old.field_setup_special_requests != new.field_setup_special_requests) then
      tmp_field_setup_special_requests = new.field_setup_special_requests;
    end if;

    if (old.field_setup_standard_blue_chairs != new.field_setup_standard_blue_chairs) then
      tmp_field_setup_standard_blue_chairs = new.field_setup_standard_blue_chairs;
    end if;

    if (old.field_services_alcohol_served != new.field_services_alcohol_served) then
      tmp_field_services_alcohol_served = new.field_services_alcohol_served;
    end if;

    if (old.field_services_food != new.field_services_food) then
      tmp_field_services_food = new.field_services_food;
    end if;

    if (old.field_services_open_flames != new.field_services_open_flames) then
      tmp_field_services_open_flames = new.field_services_open_flames;
    end if;

    if (old.field_title != new.field_title) then
      tmp_field_title = new.field_title;
    end if;

    if (old.in_state != new.in_state) then
      tmp_in_state = new.in_state;
    end if;

    if (old.in_step != new.in_step) then
      tmp_in_step = new.in_step;
    end if;

    insert into s_tables.t_request_revisions
      (
        id_request, id_revision, id_type, id_association,
        name_machine, name_human,
        is_approved, is_denied, is_troubled, is_cancelled, is_locked, is_deleted,
        date_changed,
        field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title,
        in_state, in_step
      )
      values
      (
        old.id, new.id_revision, tmp_id_type, tmp_id_association,
        tmp_name_machine, tmp_name_human,
        tmp_is_approved, tmp_is_denied, tmp_is_troubled, tmp_is_cancelled, tmp_is_locked, tmp_is_deleted,
        new.date_changed,
        tmp_field_additional, tmp_field_dates, tmp_field_fees_custodial, tmp_field_fees_equipment, tmp_field_fees_facilities, tmp_field_fees_grounds, tmp_field_fees_maintenance, tmp_field_fees_other, tmp_field_fees_security, tmp_field_fees_university, tmp_field_location, tmp_field_information_attendance, tmp_field_information_organization, tmp_field_information_adviser_approval, tmp_field_insurance_affiliated, tmp_field_insurance_contractor, tmp_field_insurance_unaffiliated, tmp_field_plans_activities, tmp_field_plans_audience, tmp_field_plans_description, tmp_field_presentation_designing_material, tmp_field_presentation_external_audio_person, tmp_field_presentation_production, tmp_field_presentation_printed_material, tmp_field_presentation_publicity, tmp_field_presentation_technical_equipment, tmp_field_presentation_university_logo, tmp_field_registration_revenue, tmp_field_registration_phone, tmp_field_registration_required, tmp_field_registration_ticket_dates, tmp_field_registration_ticket_phone, tmp_field_registration_ticket_price, tmp_field_registration_ticket_website, tmp_field_registration_website, tmp_field_setup_other_tables, tmp_field_setup_parking_assistance, tmp_field_setup_podium, tmp_field_setup_portable_stage, tmp_field_setup_rectangular_tables_8ft, tmp_field_setup_road_closures, tmp_field_setup_round_tables_8ft, tmp_field_setup_security, tmp_field_setup_special_requests, tmp_field_setup_standard_blue_chairs, tmp_field_services_alcohol_served, tmp_field_services_food, tmp_field_services_open_flames, tmp_field_title,
        tmp_in_state, tmp_in_step
      );
  end;
$$ language plpgsql;

alter function s_tables.f_request_revisions_record_revision () owner to u_reservation_revision_requests;

create trigger tr_requests_save_revision
  after insert on s_tables.t_request_revisions
    for each row execute procedure s_tables.f_request_revisions_record_revision();


commit transaction;
