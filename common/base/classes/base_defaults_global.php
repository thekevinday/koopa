<?php
/**
 * @file
 * Provides a class for provided global defaults.
 *
 * This is not indended to be included by default and is not included by default.
 *
 * The purpose here is to grant project developers full control over defaults through a single file.
 * All php source files in this project should be assumed to depend on this.
 *
 * However, no files in this project should include this file manually.
 * Instead, the caller must include this in their index.php (or equivalent) before including any other files.
 * This should grant the caller control over which file to select.
 */

class c_base_defaults_global {

  // set to NULL for auto, TRUE to always enable backtrace on error, and FALSE to always disable backtrace on error.
  const ERROR_BACKTRACE_ALWAYS = NULL;

  // set to NULL for auto, TRUE to enable backtrace for parameter-type errors, and FALSE to disable.
  // this is overriden when ERROR_BACKTRACE_ALWAYS is not NULL.
  const ERROR_BACKTRACE_ARGUMENTS = FALSE;
}
