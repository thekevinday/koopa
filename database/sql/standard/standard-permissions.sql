/** Standardized SQL Structure - Permissions */
/** This depends on: everything in standard, do this after everything but before standard-last.sql **/
start transaction;



/* standard-main.sql permissions */
grant usage on schema s_administers to r_standard_administer;
grant usage on schema s_managers to r_standard_manager;
grant usage on schema s_auditors to r_standard_auditor;
grant usage on schema s_publishers to r_standard_publisher;
grant usage on schema s_insurers to r_standard_insurer;
grant usage on schema s_financers to r_standard_financer;
grant usage on schema s_reviewers to r_standard_reviewer;
grant usage on schema s_editors to r_standard_editor;
grant usage on schema s_drafters to r_standard_drafter;
grant usage on schema s_requesters to r_standard_requester;
grant usage on schema s_users to r_standard;

grant usage on schema s_tables to u_standard_revision_requests, u_standard_statistics_update, u_standard_logger, u_standard_groups_handler;


/* standard-users.sql permissions */
grant select,insert,update on s_tables.t_users to r_standard_administer;
grant select on s_tables.t_users to r_standard_auditor;

grant select,usage on s_tables.se_users_id to r_standard_administer;
grant usage on s_tables.se_users_id to r_standard, r_standard_system;

grant select on s_users.v_users_self to r_standard, r_standard_system;

grant select on public.v_users_self_session to r_standard, r_standard_system, r_standard_public;
grant select on public.v_users_self_locked_not to r_standard, r_standard_system, r_standard_public;
grant select on public.v_users_self_exists to r_standard, r_standard_system, r_standard_public;

grant insert on s_users.v_users_self_insert to r_standard, r_standard_system;
grant update on s_users.v_users_self_update to r_standard, r_standard_system;

grant select on public.v_users_self to r_standard_public, r_standard, r_standard_system;
grant select on public.v_users to r_standard, r_standard_public, r_standard_system;
grant select on public.v_users_email to r_standard, r_standard_public, r_standard_system;

grant select on s_managers.v_users to r_standard_manager;
grant insert on s_managers.v_users_insert to r_standard_manager;
grant update on s_managers.v_users_update to r_standard_manager;
grant select on s_managers.v_users to r_standard_manager;

alter materialized view s_administers.m_users_date_created_this_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_created_previous_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_created_previous_month owner to r_standard_administer;
alter materialized view s_administers.m_users_date_created_previous_year owner to r_standard_administer;

alter materialized view s_administers.m_users_date_changed_this_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_changed_previous_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_changed_previous_month owner to r_standard_administer;
alter materialized view s_administers.m_users_date_changed_previous_year owner to r_standard_administer;

alter materialized view s_administers.m_users_date_synced_this_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_synced_previous_day owner to r_standard_administer;
alter materialized view s_administers.m_users_date_synced_previous_month owner to r_standard_administer;
alter materialized view s_administers.m_users_date_synced_previous_year owner to r_standard_administer;

grant select on s_administers.m_users_date_created_this_day to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_day to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_month to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_year to r_standard_manager;

grant select on s_administers.m_users_date_changed_this_day to r_standard_manager;
grant select on s_administers.m_users_date_changed_previous_day to r_standard_manager;
grant select on s_administers.m_users_date_changed_previous_month to r_standard_manager;
grant select on s_administers.m_users_date_changed_previous_year to r_standard_manager;

grant select on s_administers.m_users_date_synced_this_day to r_standard_manager;
grant select on s_administers.m_users_date_synced_previous_day to r_standard_manager;
grant select on s_administers.m_users_date_synced_previous_month to r_standard_manager;
grant select on s_administers.m_users_date_synced_previous_year to r_standard_manager;

grant select on s_administers.m_users_date_created_this_day to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_day to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_month to r_standard_manager;
grant select on s_administers.m_users_date_created_previous_year to r_standard_manager;

alter function s_administers.f_users_insert_as_administer() owner to u_standard_grant_roles;
alter function s_administers.f_users_update_as_administer() owner to u_standard_grant_roles;
alter function s_administers.f_users_update_materialized_views() owner to r_standard_administer;


/* attempt to auto-manage postgresql standard roles with the standard database user roles. */
/* user ids 1 and 2 are explicitly reserved for anonymous/public and the database postgresql accounts. */
/* postgresql does not seem to support variables for the user with grant and revoke, therefore the execute statement is used to perform the query. */
/* @fixme: the name_machine must be forcibly sanitized to be alphanumeric, -, or _ in all cases. */
create or replace function s_administers.f_users_insert_as_administer() returns trigger security definer as $$
  declare
    name_machine constant text default quote_ident(new.name_machine);
  begin
    if (new.id = 1 or new.id = 2) then
      return null;
    end if;

    set client_min_messages to error;

    if (new.is_locked or new.is_deleted) then
      if (new.is_deleted) then
        execute 'revoke r_standard from ' || name_machine;
        execute 'revoke r_standard_system from ' || name_machine;
        execute 'revoke r_standard_public from ' || name_machine;
      elseif (new.is_public) then
        execute 'grant r_standard_public to ' || name_machine;
      elseif (new.is_system) then
        execute 'grant r_standard_system to ' || name_machine;
      elseif (new.is_requester or new.is_drafter or new.is_editor or new.is_reviewer or new.is_financer or new.is_insurer or new.is_publisher or new.is_auditor or new.is_manager or new.is_administer) then
        execute 'grant r_standard to ' || name_machine;
      end if;

      execute 'revoke r_standard_administer from ' || name_machine;
      execute 'revoke r_standard_manager from ' || name_machine;
      execute 'revoke r_standard_auditor from ' || name_machine;
      execute 'revoke r_standard_publisher from ' || name_machine;
      execute 'revoke r_standard_financer from ' || name_machine;
      execute 'revoke r_standard_insurer from ' || name_machine;
      execute 'revoke r_standard_reviewer from ' || name_machine;
      execute 'revoke r_standard_editor from ' || name_machine;
      execute 'revoke r_standard_drafter from ' || name_machine;
      execute 'revoke r_standard_requester from ' || name_machine;
    elseif (new.is_public) then
      execute 'grant r_standard_public to ' || name_machine;
      execute 'revoke r_standard from ' || name_machine;

      if (new.is_system) then
        execute 'grant r_standard_system to ' || name_machine;
      else
        execute 'revoke r_standard_system from ' || name_machine;
      end if;

      execute 'revoke r_standard_administer from ' || name_machine;
      execute 'revoke r_standard_manager from ' || name_machine;
      execute 'revoke r_standard_auditor from ' || name_machine;
      execute 'revoke r_standard_publisher from ' || name_machine;
      execute 'revoke r_standard_financer from ' || name_machine;
      execute 'revoke r_standard_insurer from ' || name_machine;
      execute 'revoke r_standard_reviewer from ' || name_machine;
      execute 'revoke r_standard_editor from ' || name_machine;
      execute 'revoke r_standard_drafter from ' || name_machine;
      execute 'revoke r_standard_requester from ' || name_machine;
    else
      if (new.is_system) then
        execute 'grant r_standard_system to ' || name_machine;

        execute 'revoke r_standard from ' || name_machine;
        execute 'revoke r_standard_public from ' || name_machine;
      elseif (new.is_requester or new.is_drafter or new.is_editor or new.is_reviewer or new.is_financer or new.is_insurer or new.is_publisher or new.is_auditor or new.is_manager or new.is_administer) then
        execute 'grant r_standard to ' || name_machine;

        execute 'revoke r_standard_system from ' || name_machine;
        execute 'revoke r_standard_public from ' || name_machine;
      end if;

      if (new.is_administer) then
        execute 'grant r_standard_administer to ' || name_machine;
      end if;

      if (new.is_manager) then
        execute 'grant r_standard_manager to ' || name_machine;
      end if;

      if (new.is_auditor) then
        execute 'grant r_standard_auditor to ' || name_machine;
      end if;

      if (new.is_publisher) then
        execute 'grant r_standard_publisher to ' || name_machine;
      end if;

      if (new.is_insurer) then
        execute 'grant r_standard_insurer to ' || name_machine;
      end if;

      if (new.is_financer) then
        execute 'grant r_standard_financer to ' || name_machine;
      end if;

      if (new.is_reviewer) then
        execute 'grant r_standard_reviewer to ' || name_machine;
      end if;

      if (new.is_editor) then
        execute 'grant r_standard_editor to ' || name_machine;
      end if;

      if (new.is_drafter) then
        execute 'grant r_standard_drafter to ' || name_machine;
      end if;

      if (new.is_requester) then
        execute 'grant r_standard_requester to ' || name_machine;
      end if;
    end if;

    reset client_min_messages;

    return null;
  end;
$$ language plpgsql;

create or replace function s_administers.f_users_update_as_administer() returns trigger security definer as $$
  declare
    name_machine constant text default quote_ident(new.name_machine);
  begin
    if (new.id = 1 or new.id = 2) then
      return null;
    end if;

    set client_min_messages to error;

    if (old.is_locked <> new.is_locked or old.is_deleted <> new.is_deleted) then
      if (old.is_deleted <> new.is_deleted) then
        if (new.is_deleted) then
          execute 'revoke r_standard from ' || name_machine;
          execute 'revoke r_standard_system from ' || name_machine;
          execute 'revoke r_standard_public from ' || name_machine;
        else
          if (new.is_public) then
            execute 'grant r_standard_public to ' || name_machine;
          elseif (new.is_system) then
            execute 'grant r_standard_system to ' || name_machine;
          elseif (new.is_requester or new.is_drafter or new.is_editor or new.is_reviewer or new.is_financer or new.is_insurer or new.is_publisher or new.is_auditor or new.is_manager or new.is_administer) then
            execute 'grant r_standard to ' || name_machine;
          end if;
        end if;
      end if;

      if (new.is_locked or new.is_deleted) then
        execute 'revoke r_standard_administer from ' || name_machine;
        execute 'revoke r_standard_manager from ' || name_machine;
        execute 'revoke r_standard_auditor from ' || name_machine;
        execute 'revoke r_standard_publisher from ' || name_machine;
        execute 'revoke r_standard_financer from ' || name_machine;
        execute 'revoke r_standard_insurer from ' || name_machine;
        execute 'revoke r_standard_reviewer from ' || name_machine;
        execute 'revoke r_standard_editor from ' || name_machine;
        execute 'revoke r_standard_drafter from ' || name_machine;
        execute 'revoke r_standard_requester from ' || name_machine;
        execute 'revoke r_standard_system from ' || name_machine;
        execute 'revoke r_standard_public from ' || name_machine;
      elseif (new.is_public) then
        execute 'grant r_standard_public to ' || name_machine;

        if (new.is_system) then
          execute 'grant r_standard_system to ' || name_machine;
        end if;
      else
        if (new.is_administer) then
          execute 'grant r_standard_administer to ' || name_machine;
        end if;

        if (new.is_manager) then
          execute 'grant r_standard_manager to ' || name_machine;
        end if;

        if (new.is_auditor) then
          execute 'grant r_standard_auditor to ' || name_machine;
        end if;

        if (new.is_publisher) then
          execute 'grant r_standard_publisher to ' || name_machine;
        end if;

        if (new.is_financer) then
          execute 'grant r_standard_financer to ' || name_machine;
        end if;

        if (new.is_insurer) then
          execute 'grant r_standard_insurer to ' || name_machine;
        end if;

        if (new.is_reviewer) then
          execute 'grant r_standard_reviewer to ' || name_machine;
        end if;

        if (new.is_editor) then
          execute 'grant r_standard_editor to ' || name_machine;
        end if;

        if (new.is_drafter) then
          execute 'grant r_standard_drafter to ' || name_machine;
        end if;

        if (new.is_requester) then
          execute 'grant r_standard_requester to ' || name_machine;
        end if;

        if (new.is_system) then
          execute 'grant r_standard_system to ' || name_machine;
        end if;
      end if;
    elseif (old.is_public <> new.is_public and new.is_public) then
      execute 'grant r_standard_public to ' || name_machine;

      execute 'revoke r_standard_administer from ' || name_machine;
      execute 'revoke r_standard_manager from ' || name_machine;
      execute 'revoke r_standard_auditor from ' || name_machine;
      execute 'revoke r_standard_publisher from ' || name_machine;
      execute 'revoke r_standard_financer from ' || name_machine;
      execute 'revoke r_standard_insurer from ' || name_machine;
      execute 'revoke r_standard_reviewer from ' || name_machine;
      execute 'revoke r_standard_editor from ' || name_machine;
      execute 'revoke r_standard_drafter from ' || name_machine;
      execute 'revoke r_standard_requester from ' || name_machine;

      if (old.is_system <> new.is_system) then
        if (new.is_system) then
          execute 'grant r_standard_system to ' || name_machine;
        else
          execute 'revoke r_standard_system from ' || name_machine;
        end if;
      end if;
    else
      if (old.is_public <> new.is_public) then
        execute 'revoke r_standard_public from ' || name_machine;
      end if;

      if (old.is_system <> new.is_system) then
        if (new.is_system) then
          execute 'grant r_standard_system to ' || name_machine;
        else
          execute 'revoke r_standard_system from ' || name_machine;
        end if;
      elseif (not new.is_system) then
        if (new.is_requester or new.is_drafter or new.is_editor or new.is_reviewer or new.is_financer or new.is_insurer or new.is_publisher or new.is_auditor or new.is_manager or new.is_administer) then
          execute 'grant r_standard to ' || name_machine;
        end if;
      end if;

      if (old.is_administer <> new.is_administer) then
        if (new.is_administer) then
          execute 'grant r_standard_administer to ' || name_machine;
        else
          execute 'revoke r_standard_administer from ' || name_machine;
        end if;
      end if;

      if (old.is_manager <> new.is_manager) then
        if (new.is_manager) then
          execute 'grant r_standard_manager to ' || name_machine;
        else
          execute 'revoke r_standard_manager from ' || name_machine;
        end if;
      end if;

      if (old.is_auditor <> new.is_auditor) then
        if (new.is_auditor) then
          execute 'grant r_standard_auditor to ' || name_machine;
        else
          execute 'revoke r_standard_auditor from ' || name_machine;
        end if;
      end if;

      if (old.is_publisher <> new.is_publisher) then
        if (new.is_publisher) then
          execute 'grant r_standard_publisher to ' || name_machine;
        else
          execute 'revoke r_standard_publisher from ' || name_machine;
        end if;
      end if;

      if (old.is_insurer <> new.is_insurer) then
        if (new.is_insurer) then
          execute 'grant r_standard_insurer to ' || name_machine;
        else
          execute 'revoke r_standard_insurer from ' || name_machine;
        end if;
      end if;

      if (old.is_financer <> new.is_financer) then
        if (new.is_financer) then
          execute 'grant r_standard_financer to ' || name_machine;
        else
          execute 'revoke r_standard_financer from ' || name_machine;
        end if;
      end if;

      if (old.is_reviewer <> new.is_reviewer) then
        if (new.is_reviewer) then
          execute 'grant r_standard_reviewer to ' || name_machine;
        else
          execute 'revoke r_standard_reviewer from ' || name_machine;
        end if;
      end if;

      if (old.is_editor <> new.is_editor) then
        if (new.is_editor) then
          execute 'grant r_standard_editor to ' || name_machine;
        else
          execute 'revoke r_standard_editor from ' || name_machine;
        end if;
      end if;

      if (old.is_drafter <> new.is_drafter) then
        if (new.is_drafter) then
          execute 'grant r_standard_drafter to ' || name_machine;
        else
          execute 'revoke r_standard_drafter from ' || name_machine;
        end if;
      end if;

      if (old.is_requester <> new.is_requester) then
        if (new.is_requester) then
          execute 'grant r_standard_requester to ' || name_machine;
        else
          execute 'revoke r_standard_requester from ' || name_machine;
        end if;
      end if;
    end if;

    reset client_min_messages;

    return null;
  end;
$$ language plpgsql;


/* standard-groups.sql permissions */
grant select,insert,update on s_tables.t_groups to r_standard_manager, u_standard_groups_handler;
grant select on s_tables.t_groups to r_standard_auditor;
grant select,usage on s_tables.se_groups_id to r_standard_manager;
grant usage on s_tables.se_groups_id to r_standard, r_standard_system, u_standard_groups_handler;

grant select on s_users.v_groups_manage_self to r_standard, r_standard_system;
grant update on s_users.v_groups_manage_update to r_standard, r_standard_system;

alter function s_administers.f_groups_group_user_insert () owner to u_standard_groups_handler;
alter function s_administers.f_groups_group_user_update () owner to u_standard_groups_handler;

grant select,insert,update on s_tables.t_groups to r_standard_manager;
grant select on s_tables.t_groups to r_standard_auditor;

grant select on s_users.v_groups_self to r_standard, r_standard_system;
grant select on s_users.v_group_users_manage to r_standard, r_standard_system;
grant insert on s_users.v_group_users_manage_insert to r_standard, r_standard_system;
grant update on s_users.v_group_users_manage_update to r_standard, r_standard_system;

grant select,insert,update,delete on s_tables.t_groups to r_standard_manager;
grant select on s_tables.t_groups to r_standard_auditor;

grant select on s_users.v_group_composites to r_standard, r_standard_system;
grant insert on s_users.v_group_composites_manage_insert to r_standard, r_standard_system;
grant update on s_users.v_group_composites_manage_update to r_standard, r_standard_system;


/* standard-types.sql permissions */
grant select,insert,update on s_tables.t_type_http_status_codes to r_standard_administer;
grant select on s_tables.t_type_http_status_codes to r_standard_manager, r_standard_auditor;

grant select,usage on s_tables.se_log_type_http_status_codes_id to r_standard_administer;
grant select on public.v_log_type_http_status_codes to r_standard, r_standard_public, r_standard_system;
grant select,insert,update on s_tables.t_type_mime_categorys to r_standard_administer;
grant select on public.v_types_mime_categorys to r_standard, r_standard_public, r_standard_system;
grant select,insert,update on s_tables.t_type_mime_categorys to r_standard_administer;
grant select on public.v_types_mime_categorys_locked_not to r_standard, r_standard_public, r_standard_system;
grant select,insert,update on s_tables.t_type_mime_types to r_standard_administer;

grant select on public.v_types_mime_types to r_standard, r_standard_public, r_standard_system;
grant select on public.v_types_mime_types to r_standard, r_standard_public, r_standard_system;


/* standard-files.sql permissions */
grant select,insert,update on s_tables.t_files to r_standard_administer;
grant select on s_tables.t_files to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_files_id to r_standard_administer;
grant usage on s_tables.se_files_id to r_standard, r_standard_system;

grant select on s_users.v_files to r_standard, r_standard_system;
grant select on public.v_path_types to r_standard, r_standard_public, r_standard_system;


/* standard-paths.sql permissions */
grant select,insert,update on s_tables.t_paths to r_standard_administer;
grant select on s_tables.t_paths to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_paths_id to r_standard_administer;

grant usage on s_tables.se_paths_id to r_standard, r_standard_system;

grant select on s_users.v_paths to r_standard, r_standard_system;
grant select on public.v_paths to r_standard_public;


/* standard-log_types.sql permissions */
grant select,insert,update on s_tables.t_log_types to r_standard_administer;
grant select on s_tables.t_log_types to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_log_types_id to r_standard_administer;

grant select on public.v_log_types to r_standard, r_standard_public, r_standard_system;

grant select,insert,update on s_tables.t_log_type_severitys to r_standard_administer;
grant select on s_tables.t_log_type_severitys to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_log_type_severitys_id to r_standard_administer;

grant select on s_users.v_log_type_severitys to r_standard, r_standard_public, r_standard_system;

grant select,insert,update on s_tables.t_log_type_facilitys to r_standard_administer;
grant select on s_tables.t_log_type_facilitys to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_log_type_facilitys_id to r_standard_administer;

grant select on s_users.v_log_type_facilitys to r_standard, r_standard_public, r_standard_system;


/* standard-log_groups.sql permissions */
grant select,usage on s_tables.se_log_groups_id to r_standard_manager, r_standard_auditor;
grant usage on s_tables.se_log_groups_id to r_standard, r_standard_system;

grant select on s_users.v_log_groups_self to r_standard, r_standard_system;

grant insert on s_users.v_log_groups_self_insert to r_standard, r_standard_system;

grant select,usage on s_tables.se_log_group_users_id to r_standard_manager, r_standard_auditor;
grant usage on s_tables.se_log_group_users_id to r_standard, r_standard_system;

grant select on s_users.v_log_group_users_self to r_standard, r_standard_system;

grant insert on s_users.v_log_group_users_self_insert to r_standard, r_standard_system;


/* standard-log_problems.sql permissions */
grant select,insert,update,delete on s_tables.t_log_problems to r_standard_manager;
grant select on s_tables.t_log_problems to r_standard_auditor;
grant select,usage on s_tables.se_log_problems_id to r_standard_manager;
grant usage on s_tables.se_log_problems_id to r_standard, r_standard_system;

grant select,insert,update,delete on s_tables.t_log_problems_users to r_standard_manager;
grant select on s_tables.t_log_problems_users to r_standard_auditor;

grant select on s_users.v_log_problems_users_self to r_standard, r_standard_system;

grant insert on s_users.v_log_problems_users_self_insert to r_standard, r_standard_system;

grant delete on s_users.v_log_problems_users_self_delete to r_standard, r_standard_system;

alter function s_tables.f_log_problems_users_delete () owner to u_standard_logger;


/* standard-log_users.sql permissions */
grant select on s_tables.t_log_users to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_log_users_id to r_standard_administer;
grant usage on s_tables.se_log_users_id to r_standard, r_standard_public, r_standard_system;

grant select on s_users.v_log_users_self to r_standard, r_standard_system;
grant insert on s_users.v_log_users_self_insert to r_standard, r_standard_system;
grant insert on public.v_log_users_self_insert to r_standard_public;

grant select on s_tables.t_log_user_activity to r_standard_manager, r_standard_auditor;
grant select,usage on s_tables.se_log_user_activity_id to r_standard_administer;
grant usage on s_tables.se_log_user_activity_id to r_standard, r_standard_public, r_standard_system;

grant select on s_users.v_log_user_activity_self to r_standard, r_standard_system;
grant insert on s_users.v_log_user_activity_self_insert to r_standard, r_standard_system;
grant insert on public.v_log_user_activity_self_insert to r_standard_public;


/* standard-statistics.sql permissions */
grant select,insert,update on s_tables.t_statistics_http_status_codes to r_standard_manager, u_standard_statistics_update;
grant select on s_tables.t_statistics_http_status_codes to r_standard_auditor;
grant select on s_tables.t_statistics_request_path to r_standard_manager, r_standard_auditor;

grant select,insert,update on s_users.v_statistics_request_path to r_standard, r_standard_system;
grant select,insert,update on public.v_statistics_request_path to r_standard_public;

alter function s_tables.f_statistics_http_status_codes_insert () owner to u_standard_statistics_update;



commit transaction;
