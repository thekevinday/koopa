<?php
/**
 * @file
 * Provides interfaces for specific Postgesql Queries parameters.
 *
 * @see: https://www.postgresql.org/docs/current/static/sql-commands.html
 */
namespace n_koopa;

/**
 * An interface for passing query parameters to a query for use by PDO or similar.
 *
 * This is defined as a way to achieve multiple inheritance for the purposes of class type detection.
 */
interface i_database_query_parameter {

  /**
   * @see: c_base_return::s_new().
   */
  public static function s_new($value);

  /**
   * @see: t_base_return_value::s_value()
   */
  public static function s_value($return);

  /**
   * @see: t_base_return_value_exact::s_value_exact()
   */
  public static function s_value_exact($return);

  /**
   * @see: c_base_return::has_value()
   */
  public function set_value($value);

  /**
   * @see: c_base_return::get_value()
   */
  public function get_value();

  /**
   * @see: c_base_return::get_value_exact()
   */
  public function get_value_exact();

  /**
   * @see: c_base_return::has_value()
   */
  public function has_value();
}
