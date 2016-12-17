<?php
/**
 * @file
 * Provides a class for htmo markup.
 *
 * This is currently a draft/brainstorm and is subject to be completely rewritten/redesigned.
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');

/**
 * A generic class for html tags.
 *
 * The structure and attributes may be used to communicate information, therefore the attributes extend to both input and output (theme).
 * This class is not intended to be used for generate the theme but is instead intended to be used as a base class for both the input and the output classes for their respective purposes.
 *
 * Each tag has an internal id that is expected to be processed.
 * This is not the same as the HTML 'id' attribute but can be the same.
 *
 * Many of the attributes are defined from HTML forms because of the number of forms.
 *
 * @see: https://www.w3.org/TR/html5/forms.html#forms
 */
class c_base_markup_tag {
  private $id;
  private $attributes;
  private $tags;
  private $tags_total;
  private $parent;
  private $children;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->id = NULL;
    $this->attributes = array();
    $this->tags = NULL;
    $this->tags_total = 0;
    $this->parent = NULL;
    $this->children = array();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->id);
    unset($this->attributes);
    unset($this->tags);
    unset($this->tags_total);
    unset($this->parent);
    unset($this->children);
  }

  /**
   * Assign the internal unique numeric tag id.
   *
   * @param int $id
   *   The internal numeric id to assign.
   *
   * @return c_base_return_status
   *   TRUE on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function set_id($id) {
    if (!is_int($id)) {
      return c_base_return_error::s_false();
    }

    $this->id = $id;
    return new c_base_return_true();
  }

  /**
   * Get the unique numeric tag id assigned to this object.
   *
   * @return c_base_return_int|c_base_return_status
   *   The tag type assigned to this class.
   *   FALSE is returned if the unique numeric tag id is not set.
   *   FALSE with error bit set is returned on error.
   */
  public function get_id() {
    if (!isset($this->id)) {
      return new c_base_return_false();
    }

    return new c_base_return_int($this->id);
  }

  /**
   * Add or append a given tag to the object.
   *
   * If the tag is not assigned a unique internal id, then one is generated.
   *
   * @param c_base_markup_tag $tag
   *   The tag to assign.
   * @param int|null $index
   *   (optional) A position within the children array to assign the tag.
   *   If NULL, then the tag is appended to the end of the children array.
   *
   * @return c_base_return_int|$c_base_return_status
   *   The position in which the tag was added is returned on success.
   *   FALSE is reeturned if an tag at the specified index already exists.
   *   FALSE with error bit set is returned on error.
   */
  public function set_tag($tag, $index = NULL) {
    if (!($tag instanceof c_base_markup_tag)) {
      return c_base_return_error::s_false();
    }

    if (!is_null($index) && (!is_int($index) && $index < 0)) {
      return c_base_return_error::s_false();
    }

    $tag_id = $tag->get_id();
    if (!($tag_id instanceof c_base_return_int)) {
      // PHP fails to provide an end() equivalent to get the end key.
      // This performs a less efficient process of generating an array of keys and then calling end() on that array.
      // This is then used to get the last key so that the end key can be used as the tag id.
      $keys = array_keys($this->tags);
      $tag_id = end($keys);
      unset($keys);

      $tag->set_id($tag_id);
    }

    if (!array_key_exists($tag_id, $this->tags)) {
      $this->tags[$tag_id] = $tag;
    }

    if (is_null($index)) {
      $this->children[] = $tag;

      // PHP fails to provide an end() equivalent to get the end key.
      // This performs a less efficient process of generating an array of keys and then calling end() on that array.
      // This is then used to get the last key so that the end key can be used as the tag children position.
      $keys = array_keys($this->tags);
      $index = end($keys);
      unset($keys);
    }
    else {
      if (array_key_exists($index, $this->children)) {
        return new c_base_return_false();
      }

      $this->children[$index] = $tag;
      ksort($this->children);
    }
    unset($tag_id);

    $this->tags_total++;
    return new c_base_return_int::s_new($index);
  }
}
