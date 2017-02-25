/** Standardized SQL Structure - Content **/
/** This depends on: base-fields.sql **/
start transaction;



/** Custom database specific settings (do this on every connection made) **/
set bytea_output to hex;
set search_path to system,administers,managers,auditors,publishers,insurers,financers,reviewers,editors,drafters,requesters,users,public;
set datestyle to us;



/*** Request: Type ***/
create table managers.t_request_types (
  id bigint not null,
  id_external bigint,

  name_machine varchar(128) not null,
  name_human varchar(256) not null,

  is_locked boolean default false not null,
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

create index ci_request_types_deleted_not on managers.t_request_types (id)
  where is_deleted is not true;

create index ci_request_types_public on managers.t_request_types (id)
  where is_deleted is not true and is_locked is not true;

create view requesters.v_request_types with (security_barrier=true) as
  select id, id_external, name_machine, name_human from managers.t_request_types
  where is_deleted is not true and is_locked is not true;

grant select on requesters.v_request_types to reservation_users_requester;

/** @todo: consider creating default request types **/



/*** Requests ***/
create table managers.t_requests (
  id bigint not null,
  id_revision bigint not null,
  id_type bigint not null,
  id_association bigint not null,
  id_creator bigint not null,

  name_machine varchar(128) not null,
  name_machine_creator varchar(128) not null,
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
  field_setup_standard_blue_chairs public.ct_field_needed_with_total not null,
  field_services_alcohol_served public.ct_field_served_with_caterer not null,
  field_services_food public.ct_field_served_with_caterer not null,
  field_services_open_flames public.ct_field_used_with_details not null,
  field_title text not null,

  in_state bigint not null,
  in_step bigint not null,

  constraint cu_requests_id unique (id),
  constraint cu_requests_name_machine unique (name_machine),

  constraint cc_requests_id check (id > 0),
  constraint cc_requests_approved check ((is_approved is true and is_denied is not true) or (is_approved is not true and is_denied is true)),

  constraint cf_requests_id_creator foreign key (id_creator, name_machine_creator) references administers.t_users (id, name_machine) on delete restrict on update cascade,
  constraint cf_requests_request_type foreign key (id_type) references managers.t_request_types (id) on delete restrict on update cascade,
  constraint cf_requests_association foreign key (id_association) references managers.t_associations (id) on delete restrict on update cascade
);

create sequence managers.s_requests_id owned by managers.t_requests.id;
alter table managers.t_requests alter column id set default nextval('managers.s_requests_id'::regclass);

grant select,insert,update on managers.t_requests to reservation_users_administer;
grant select,insert,update on managers.t_requests to reservation_users_manager;
grant select on managers.t_requests to reservation_users_auditor;
grant select,usage on managers.s_requests_id to reservation_users_administer;
grant select,usage on managers.s_requests_id to reservation_users_manager;
grant usage on managers.s_requests_id to reservation_users;


/** @todo: create all appropriate views (and indexes), including individual views for is_cancelled, is_deleted, is_published, is_unpublished, is_denied, and is_troubled **/
create index ci_requests_deleted_not on managers.t_requests (id)
  where is_deleted is not true;

create index ci_requests_approved on managers.t_requests (id)
  where is_deleted is not true and is_cancelled is not true and is_approved is true;

create index ci_requests_approved_cancelled on managers.t_requests (id)
  where is_deleted is not true and is_cancelled is true and is_approved is true;


/*** approved requests (but not cancelled) ***/
create view users.v_requests_approved with (security_barrier=true) as
  select id, id_revision, id_type, id_association, name_machine, name_human, is_troubled, date_created, date_changed, field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title, in_state, in_step from managers.t_requests
    where is_deleted is not true and is_cancelled is not true and is_approved is true;

grant select on users.v_requests_approved to reservation_users;


/*** approved requests (only cancelled) ***/
create view users.v_requests_approved_cancelled with (security_barrier=true) as
  select id, id_revision, id_type, id_association, name_machine, name_human, is_troubled, date_created, date_changed, field_additional, field_dates, field_fees_custodial, field_fees_equipment, field_fees_facilities, field_fees_grounds, field_fees_maintenance, field_fees_other, field_fees_security, field_fees_university, field_location, field_information_attendance, field_information_organization, field_information_adviser_approval, field_insurance_affiliated, field_insurance_contractor, field_insurance_unaffiliated, field_plans_activities, field_plans_audience, field_plans_description, field_presentation_designing_material, field_presentation_external_audio_person, field_presentation_production, field_presentation_printed_material, field_presentation_publicity, field_presentation_technical_equipment, field_presentation_university_logo, field_registration_revenue, field_registration_phone, field_registration_required, field_registration_ticket_dates, field_registration_ticket_phone, field_registration_ticket_price, field_registration_ticket_website, field_registration_website, field_setup_other_tables, field_setup_parking_assistance, field_setup_podium, field_setup_portable_stage, field_setup_rectangular_tables_8ft, field_setup_road_closures, field_setup_round_tables_8ft, field_setup_security, field_setup_special_requests, field_setup_standard_blue_chairs, field_services_alcohol_served, field_services_food, field_services_open_flames, field_title, in_state, in_step from managers.t_requests
    where is_deleted is not true and is_cancelled is true and is_approved is true;

grant select on users.v_requests_approved to reservation_users;


/*** requests the current user can manage (do determine this, the associations table is join and because it is already joined, add the additional fields provided by assocation). ***/
/* @todo: review this to see if I can come up with a better way than doing an inner join on associations. */
create view users.v_requests_self with (security_barrier=true) as
  select r.id, r.id_revision, r.id_type, r.id_association, a.id_group, a.id_coordinator, r.name_machine, a.name_machine_coordinator, r.name_human, a.name_human as name_human_association, r.is_troubled, r.date_created, r.date_changed, r.field_additional, r.field_dates, r.field_fees_custodial, r.field_fees_equipment, r.field_fees_facilities, r.field_fees_grounds, r.field_fees_maintenance, r.field_fees_other, r.field_fees_security, r.field_fees_university, r.field_location, r.field_information_attendance, r.field_information_organization, r.field_information_adviser_approval, r.field_insurance_affiliated, r.field_insurance_contractor, r.field_insurance_unaffiliated, r.field_plans_activities, r.field_plans_audience, r.field_plans_description, r.field_presentation_designing_material, r.field_presentation_external_audio_person, r.field_presentation_production, r.field_presentation_printed_material, r.field_presentation_publicity, r.field_presentation_technical_equipment, r.field_presentation_university_logo, r.field_registration_revenue, r.field_registration_phone, r.field_registration_required, r.field_registration_ticket_dates, r.field_registration_ticket_phone, r.field_registration_ticket_price, r.field_registration_ticket_website, r.field_registration_website, r.field_setup_other_tables, r.field_setup_parking_assistance, r.field_setup_podium, r.field_setup_portable_stage, r.field_setup_rectangular_tables_8ft, r.field_setup_road_closures, r.field_setup_round_tables_8ft, r.field_setup_security, r.field_setup_special_requests, r.field_setup_standard_blue_chairs, r.field_services_alcohol_served, r.field_services_food, r.field_services_open_flames, r.field_title, a.field_affiliation, a.field_classification, r.in_state, r.in_step from managers.t_requests r
    inner join managers.t_associations a on  r.id_association = a.id
    where r.is_deleted is not true and a.is_deleted is not true and ((id_group is null and (a.name_machine_manager)::text = (current_user)::text) or a.id in (select id from users.v_associations_self));

grant select on users.v_requests_self to reservation_users;


/** @todo: create "managers.t_requests_revision" that is identical to "managers.t_requests" with all the columns allowed to be null. **/

commit transaction;
