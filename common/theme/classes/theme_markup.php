<?php
/**
 * @file
 * Provides a class for managing HTML DOM for the custom markup language used by this project.
 *
 * This is currently a draft/brainstorm and is subject to be completely rewritten/redesigned.
 *
 * This project provides a context-specific markup language for context-independent design with context-specific presentation.
 *
 * The following are the rules for this specific markup language:
 * 1) There is no "block" or "inline", all things are "blocks" and may be presented inline (or conversely).
 *   - That is to say, the markup does not define the block/not-block status, the theme does.
 *
 * 2) The only available tags are:
 *   - <tag>:
 *     - May only be defined in: <heading>, <title>, <presentation>, <context>, and <content> tags.
 *     - When defined inside of <heading>:
 *       - Provides default settings, such as: language or encoding.
 *         - Example: <tag type="default" name="language">en-us</tag>.
 *         - Example: <tag type="default" name="encoding">utf-8</tag>.
 *     - When defined inside of <title>:
 *       - Each tag may have an "id" attribute, such as: <tag id="unique_name-1"></tag>..
 *       - Must not contain any nested markup.
 *     - When defined inside of <presentation>:
 *       - Each tag may have an "id" attribute, such as: <tag id="unique_name-2"></tag>.
 *       - Each tag may have a "class" attribute, such as: <tag class="a b c"></tag>.
 *       - Each tag may have a "context" attribute, such as: <tag context="some_name"></tag>.
 *         - This represents the context of how the tag is presented (and HTML5 example would be: "banner").
 *       - Each tag may have a "content" attribute, such as: <tag content="message-1"></tag>.
 *         - Associates some block of information or text.
 *       - Each tag may have a "tooltip" attribute, such as: <tag tooltip="message-2"></tag>.
 *       - Each tag may have a "file" attribute, such as: <tag file="image-person.png"></tag>.
 *         - Alternate text must not be defined for these files here because this is "presentation" and not "content".
 *     - When defined inside of <context>:
 *       - @todo: details need to be fleshed out.
 *     - When defined inside of <content>:
 *       - Each tag must have an "id" attribute, such as: <tag id="unique_name-3"></tag>.
 *       - Each tag may have a "context" attribute, such as: <tag context="some_name"></tag>.
 *         - This represents the context of the content and not about how it is presented (and HTML5 example would be: "aside").
 *       - Each tag may have a "file" attribute, such as: <tag file="image-person.png">This text between the open and close tag is the "alternate text" or "description" describing the file.</tag>.
 *     - When defined inside of <files>:
 *       - Each tag may have an "id" attribute, such as: <tag id="css-unique_name">css/somewhere.css</tag>.
 *       - Each tag may have a "type" attribute, such as: <tag type="text/css">http://example.com/css/somewhere.css</tag>.
 *       - Must not contain any nested markup.
 *   - <title>:
 *     - A name representing the titles.
 *     - May have multiple titles (order-sensitive), such as:
 *       <title>
 *         <tag id="unique_title-1">First Title</tag>
 *         <tag id="unique_title-2">Second Title</tag>
 *       </title>
 *     - Must not contain any nested markup.
 *   - <heading>:
 *     - Provides heading information, such as external javascript or css files.
 *     - Similar to the HTML <head> tag.
 *     - May only contain the following tags: (@todo: determine what these will be).
 *     - @todo: details need to be fleshed out.
 *   - <context>:
 *     - Provides how to interpret data (such as text or images).
 *     - May only contain the following tags: <tag>.
 *     - @todo: details need to be fleshed out.
 *       - Context may or may not be provided here, instead a global standard based on HTML5, WCAG, ARIA, etc.. may be used to determine a static list of contexts.
 *   - <files>:
 *     - All javascript, css, documents, images, et al.. must be defined here.
 *       - In this way, all files associated with this content are defined here and must have a unique id to be referenced.
 *       - Example: <tag id="image-favicon.ico" type="image/ico" alternate="Minature logo for website.">/images/favicon.ico</tag>
 *   - <content>:
 *     - All content to be read is provided here.
 *     - May only contain the following tags: <tag>.
 *     - Example:
 *       <content>
 *         <tag id="message-1">This is some text that I wanted to present to you.</tag>.
 *         <tag id="message-2">This could be used anywhere, including 'tooltip' and 'title' sections.</tag>.
 *         <tag id="alternate-1">This could be used anywhere, but is likely intended for 'alt' attribute like uses.</tag>.
 *       </content>
 *   - <presentation>:
 *     - The flow and presentation of the content is provided here.
 *     - May only contain the following tags: <tag>.
 *     - example:
 */

// include required files.
require_once('common/base/classes/base_error.php');
require_once('common/base/classes/base_return.php');
require_once('common/theme/classes/theme_dom.php');

/**
 * Generic tag class for building and renderring markup.
 */
class c_theme_tag {
  const TYPE_NONE         = 0;
  const TYPE_TITLE        = 1;
  const TYPE_HEADING      = 2;
  const TYPE_FILES        = 3;
  const TYPE_CONTEXT      = 4;
  const TYPE_PRESENTATION = 5;
  const TYPE_CONTENT      = 6;

  // custom text attatched to the inside of the
  private $text = NULL;

  // the attributes, includes all attributes available.
  private $attributes = NULL;
  private $attributes_length = 0;

  // an array of child tags (each tag must be a class/subclass of c_theme_tag).
  private $tags = NULL;
  private $tags_length = NULL;

  private $type = NULL;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->text = NULL;
    $this->attributes = array();
    $this->attributes_length = 0;

    $this->tags = array();
    $this->tags_length = NULL;

    $this->type = self::TYPE_NONE;
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->text);
    unset($this->attributes);
    unset($this->attributes_length);
    unset($this->tags);
    unset($this->tags_length);
    unset($this->type);
  }

  /**
   * Assign a text to the object.
   *
   * This will not set text if child tags are defined.
   *
   * @param string $text
   *   A string to use as the text.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE is returned if the text cannot be set.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::delete_tags()
   */
  public function set_text($text) {
    if (!is_string($text)) {
      return c_base_return_error::s_false();
    }

    // Text may only be defined when there are no child tags and vice-versa.
    if ($this->tags_length > 0) {
      return new c_base_return_false();
    }
    elseif (!is_null($this->tags_length)) {
      $this->tags_length = NULL;
    }

    // prevent the assigned text from including markup by translating everything to html entities.
    $this->text = htmlspecialchars($text, ENT_HTML5 | ENT_NOQUOTES | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8');

    return new c_base_return_true();
  }

  /**
   * Set the type this tag belongs to.
   *
   * Unsupported attributes that are not allowed will be removed.
   *
   * @param int $type
   *   The numeric id representing the type of tag.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE is returned if the type cannot be set.
   *   FALSE with error bit set is returned on error.
   */
  public function set_type($type) {
    if (!is_int($type) || $type < self::TYPE_NONE) {
      return c_base_return_error::s_false();
    }

    if ($this->type === $type) {
      return new c_base_return_true();
    }
    elseif ($type === self::TYPE_NONE) {
      $new_attributes = array();
    }
    elseif ($type === self::TYPE_TITLE) {
      $new_attributes = array();

      if (array_key_exists('id', $this->attributes)) {
        $new_attributes['id'] = $this->attributes['id'];
      }
    }
    elseif ($type === self::TYPE_HEADING) {
      $new_attributes = array();

      if (array_key_exists('id', $this->attributes)) {
        $new_attributes['id'] = $this->attributes['id'];
      }

      if (array_key_exists('type', $this->attributes)) {
        $new_attributes['type'] = $this->attributes['type'];
      }

      if (array_key_exists('name', $this->attributes)) {
        $new_attributes['name'] = $this->attributes['name'];
      }
    }
    elseif ($type === self::TYPE_FILES) {
      $new_attributes = array();

      if (array_key_exists('id', $this->attributes)) {
        $new_attributes['id'] = $this->attributes['id'];
      }

      if (array_key_exists('type', $this->attributes)) {
        $new_attributes['type'] = $this->attributes['type'];
      }
    }
    elseif ($type === self::TYPE_CONTEXT) {
      $new_attributes = array();

      if (array_key_exists('id', $this->attributes)) {
        $new_attributes['id'] = $this->attributes['id'];
      }
    }
    elseif ($type === self::TYPE_PRESENTATION) {
      $new_attributes = array();

      if (array_key_exists('id', $this->attributes)) {
        $new_attributes['id'] = $this->attributes['id'];
      }

      if (array_key_exists('class', $this->attributes)) {
        $new_attributes['class'] = $this->attributes['class'];
      }

      if (array_key_exists('context', $this->attributes)) {
        $new_attributes['context'] = $this->attributes['context'];
      }

      if (array_key_exists('content', $this->attributes)) {
        $new_attributes['content'] = $this->attributes['content'];
      }

      if (array_key_exists('tooltip', $this->attributes)) {
        $new_attributes['tooltip'] = $this->attributes['tooltip'];
      }

      if (array_key_exists('file', $this->attributes)) {
        $new_attributes['file'] = $this->attributes['file'];
      }
    }
    elseif ($type === self::TYPE_CONTENT) {
      $new_attributes = array();

      if (array_key_exists('id', $this->attributes)) {
        $new_attributes['id'] = $this->attributes['id'];
      }

      if (array_key_exists('context', $this->attributes)) {
        $new_attributes['context'] = $this->attributes['context'];
      }

      if (array_key_exists('file', $this->attributes)) {
        $new_attributes['file'] = $this->attributes['file'];
      }
    }
    else {
      return new c_base_return_false();
    }

    unset($this->attributes);
    $this->attributes = $new_attributes;
    $this->attributes_length = count($new_attributes);
    unset($new_attributes);

    $this->type = $type;
    return new c_base_return_true();
  }

  /**
   * This will set a value for the given attribute name.
   *
   * This is less efficient than calling a specific attribute function such as set_attribute_name().
   * This is because the name must be processed and only allowed names will be supported.
   *
   * Child classes that add additional attributes should override this function.
   *
   * @param string $name
   *   The attribute name to assign.
   * @param string $value
   *   The value of the attribute to assign.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE is returned if unable to set the attribute (the tag type might not support the attribute).
   *   FALSE with error bit is returned on error.
   */
  public function set_attribute($name, $value) {
    return $this->pr_set_attribute($name, $value);
  }

  /**
   * Assign tags to this object.
   *
   * Objects cannot be added if text is assigned.
   *
   * @param __class__ $tag
   *   When a __class__ object, the tag is appended to the end of the tags array.
   * @param int|null $index
   *   When an integer, represents the position within the tag array to assign the tag.
   *   - May not be less than 0 or greater than the length of the array.
   *   When $tag is NULL, this does nothing.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the position the tag was added on success.
   *   When $tag is NULL, TRUE is returned on success.
   *   FALSE is returned if unable to set tags.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::set_tags()
   * @see: self::delete_text()
   */
  public function set_tag($tag, $index = NULL) {
    if (!is_object($tag) || !($tag instanceof c_theme_tag)) {
      return c_base_return_error::s_false();
    }

    // do not allow adding tags if there is assigned text.
    if (!is_null($this->text)) {
      return new c_base_return_false();
    }

    if (is_null($index)) {
      $this->tags[$this->tags_length] = $tag;
      $this->tags_length++;
      return c_base_return_int::s_new($this->tags_length - 1);
    }
    else {
      if (!is_int($index) || $index < 0 || $index > $this->tags_length) {
        return c_base_return_error::s_false();
      }

      $this->tags[$index] = $tag;
      return c_base_return_int::s_new($index);
    }

    return c_base_return_error::s_false();
  }

  /**
   * Assign multiple tags to this object.
   *
   * Objects will not be added if text is assigned.
   *
   * @param array $tags
   *   An array of __class__ objects.
   *   Each individual item is checked and then appended to the end of the array.
   *   - The array keys will not be preserved.
   *
   * @param int|null $index
   *   When an integer, represents the position within the tag array to insert the tags.
   *   - May not be less than 0 or greater than the length of the array.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the position the tag was added on success.
   *   FALSE is returned if unable to set tags.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::set_tag()
   * @see: self::delete_text()
   */
  public function set_tags($tags, $index = NULL) {
    if (!is_array($tags)) {
      return c_base_return_error::s_false();
    }

    // FALSE without error bit set is returned when no tags are to be added (empty array).
    if (empty($tags)) {
      return new c_base_return_false();
    }

    // do not allow adding tags if there is assigned text.
    if (!is_null($this->text)) {
      return new c_base_return_false();
    }

    if (is_null($index)) {
      $failure = FALSE;
      $tags_length = $this->tags_length;
      foreach ($tags as $tag) {
        // every single item must be validated before any part of the array will be allowed to be added to this object.
        if (!is_object($tag) || !($tag instanceof c_theme_tag)) {
          $failure = TRUE;
          break;
        }

        $this->tags[$this->tags_length] = $tag;
        $this->tags_length++;
      }
      unset($tag);

      if ($failure) {
        unset($failure);

        // none of the array shall be added if any of the array is invalid.
        array_splice($this->tags, $tags_length);

        $this->tags_length = $tags_length;
        unset($tags_length);

        return new c_base_return_false();
      }
      unset($failure);
      unset($tags_length);

      return c_base_return_int::s_new($this->tags_length);
    }

    foreach ($tag as $tag) {
      // every single item must be validated before any part of the array will be allowed to be added to this object.
      if (!is_object($tag) || !($tag instanceof c_theme_tag)) {
        unset($tag);
        return c_base_return_error::s_false();
      }
    }
    unset($tag);

    if ($index == 0) {
      $original_tags = $this->tags;
      unset($this->tags);

      $this->tags = $tags;

      array_merge($this->tags, $original_tags);
      unset($original_tags);

      $this->tags_length += count($tags);
      return c_base_return_int::s_new(0);
    }

    $remaining_tags = array_splice($this->tags, $index, ($this->tags_length - $index), $tags);
    array_merge($this->tags, $remaining_tags);
    unset($remaining_tags);

    $this->tags_length += count($tags);
    return c_base_return_int::s_new($index);
  }

  /**
   * Get the text assigned to this object.
   *
   * @return c_base_return_status|c_base_return_string|c_base_return_null
   *   The assigned text.
   *   NULL is returned if no text is assigned.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::get_text_decoded()
   */
  public function get_text() {
    if (is_null($this->text)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->text);
  }

  /**
   * Get the text assigned to this object.
   *
   * The returned text is decoded from the internally stored html entity format.
   *
   * @return c_base_return_status|c_base_return_string|c_base_return_null
   *   The assigned text (decoded).
   *   NULL is returned if no text is assigned.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::get_text()
   */
  public function get_text_decoded() {
    if (is_null($this->text)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new(htmlspecialchars_decode($this->text, ENT_HTML5 | ENT_NOQUOTES | ENT_IGNORE, 'UTF-8'));
  }

  /**
   * Get the type this tag belongs to.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the type is returned on success.
   *   FALSE is returned if the type is not set.
   *   FALSE with error bit set is returned on error.
   */
  public function get_type() {
    if (is_null($this->type)) {
      return new c_base_return_false();
    }

    return c_base_return_int::s_new($this->type);
  }

  /**
   * This will get a value for the given attribute name.
   *
   * This is less efficient than calling a specific attribute function such as get_attribute_name().
   * This is because the name must be processed and only allowed names will be supported.
   *
   * Child classes that add additional attributes should override this function.
   *
   * @param string $name
   *   The attribute name to get.
   *
   * @return c_base_return_status|c_base_return_string
   *   The attribute value string is returned on success.
   *   NULL is returned if the attribute is not set.
   *   FALSE is returned if unable to get the attribute (the tag type might not support the attribute).
   *   FALSE with error bit is returned on error.
   */
  public function get_attribute($name) {
    return $this->pr_get_attribute($name);
  }

  /**
   * Get the all of the attributes assigned to this object.
   *
   * @return c_base_return_status|c_base_return_array
   *   An array containing all attributes.
   *   FALSE with error bit set is returned on error.
   */
  public function get_attributes() {
    if (is_array($this->attributes)) {
      return c_base_return_array($this->attributes);
    }

    return c_base_return_error::s_false();
  }

  /**
   * Get the length of the attributes array.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the length of the attributes array.
   *   FALSE with error bit set is returned on error.
   */
  public function get_attributes_length() {
    return c_base_return_int::s_new($this->attributes_length);
  }

  /**
   * Get a single tag assigned to this object.
   *
   * @param int|null $index
   *   (optional) An integer representing the position within the tag array to return.
   *   When NULL, the tag at the end of the tags array is returned.
   *
   * @return c_base_return_status|c_theme_return_tag|c_base_return_null
   *   A c_theme_return_tag object on success.
   *   NULL might be returned if there is no object assigned to the index.
   *   Otherwise FALSE is returned.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::get_tags()
   */
  public function get_tag($index = NULL) {
    if (!is_null($index)) {
      if (!is_int($index) || $index < 0 || $index >= $this->tags_length) {
        return c_base_return_error::s_false();
      }
    }

    if (is_null($index)) {
      $last = end($this->tags);
      if (!is_array($last)) {
        unset($last);
        return new c_base_return_false();
      }

      return c_theme_return_tag::s_new($last);
    }

    if (is_null($this->tags[$index])) {
      return new c_base_return_null();
    }

    return c_theme_return_tag::s_new($this->tags[$index]);
  }

  /**
   * Get tags assigned to this object.
   *
   * @param int $index
   *   When a positive integer, an integer representing the position within the tag array to return.
   * @param int|null $length
   *   (optional) When NULL, returns all tags following the index.
   *   When an integer, the total number of tags following the index to return.
   * @param bool $preserve_keys
   *   (optional) When TRUE array keys will be preserved.
   *   When FALSE, the array keys are reset.
   *
   * @return c_base_return_status|c_theme_return_array
   *   An array of tags on success, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::get_tag()
   * @see: self::array_slice()
   */
  public function get_tags($index, $length = NULL, $preserve_keys = TRUE) {
    if (!is_int($index) || $index < 0 || $index >= $this->tags_length) {
      return c_base_return_error::s_false();
    }

    if (!is_null($length)) {
      if (!is_int($length) || $length < 1 || $length > $this->tags_length) {
        return c_base_return_error::s_false();
      }
    }

    if (!is_bool($preserve_keys)) {
      return c_base_return_error::s_false();
    }

    return c_theme_return_array::s_new(array_slice($this->tags, $index, $length, $preserve_keys));
  }

  /**
   * Removes text assigned to this object.
   *
   * @return c_base_return_false
   *   TRUE is returned on success.
   *   FALSE without error bit set is returned if text is already deleted.
   *   FALSE with error bit set is returned on error.
   */
  public function delete_text() {
    if (is_null($this->text)) {
      return new c_base_return_false();
    }

    unset($this->text);
    $this->text = NULL;
    return new c_base_return_true();
  }

  /**
   * This will delete the value for the given attribute name.
   *
   * This is less efficient than calling a specific attribute function such as delete_attribute_name().
   * This is because the name must be processed and only allowed names will be supported.
   *
   * Child classes that add additional attributes should override this function.
   *
   * @param string $name
   *   The attribute name to delete.
   *
   * @return c_base_return_status|c_base_return_string
   *   The attribute value string is returned on success.
   *   FALSE is returned for unknown attributes names.
   *   FALSE with error bit is returned on error.
   */
  public function delete_attribute($name) {
    return $this->pr_delete_attribute($name);
  }

  /**
   * This will delete all attributes assigned to this object.
   *
   * @return c_base_return_status
   *   TRUE is returned on success.
   *   FALSE with error bit is returned on error.
   */
  public function delete_attributes() {
    unset($this->attributes);
    $this->attributes = array();
    $this->attributes_length = 0;

    return new c_base_return_true();
  }

  /**
   * Delete tag from this object.
   *
   * Tags not stored at the end of the array are set to NULL instead of being deleted.
   * The length is therefore not shortened unless the deleted tag is at the end of the array.
   * Call $this->sanitize_tags() to ensure that the array structure contains none of these holes.
   *
   * @param int|null $index
   *   (optional) When an integer, represents the position of the tag to delete.
   *   - May not be less than 0 or greater than the length of the array.
   *   When $tag is NULL, deletes the tag at the end of the tag array.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the position the tag was deleted on success.
   *   Otherwise, FALSE is returned.
   *   FALSE without error bit set is returned if the tag at the specified index is already deleted.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::delete_tags()
   * @see: self::sanitize_tags()
   */
  public function delete_tag($index = NULL) {
    if (is_null($index)) {
      $position = $this->tags_length;
      $this->tags_length--;

      unset($this->tags[$this->tags_length]);

      return c_base_return_int::s_new($position);
    }

    if (!is_int($index) || $index < 0 || $index >= $this->tags_length) {
      return c_base_return_error::s_false();
    }

    if (is_null($this->tags[$index])) {
      return new c_base_return_false();
    }

    $this->tags[$index] = NULL;
    return c_base_return_int::s_new($index);
  }

  /**
   * Delete multiple tags from this object.
   *
   * Tags not stored at the end of the array are set to NULL instead of being deleted.
   * The length is therefore not shortened unless the deleted tag is at the end of the array.
   * Call $this->sanitize_tags() to ensure that the array structure contains none of these holes.
   *
   * @param int $index
   *   (optional) When an integer, represents the position of the tag to delete.
   *   - May not be less than 0 or greater than the length of the array.
   * @param int|null $length
   *   (optional) When NULL, returns all tags following the index.
   *   When an integer, the total number of tags following the index to return.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the total number of tags deleted on success.
   *   Otherwise, FALSE is returned.
   *   FALSE without error bit set is returned if no tags were deleted.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::delete_tag()
   * @see: self::sanitize_tags()
   */
  public function delete_tags($index, $length = NULL) {
    if (!is_int($index) || $index < 0 || $index >= $this->tags_length) {
      return c_base_return_error::s_false();
    }

    if (is_null($length)) {
      $total = $this->tags_length;
    }
    else {
      if (!is_int($length) || $length < 1 || $length > $this->tags_length) {
        return c_base_return_error::s_false();
      }

      $total = $length;
    }

    $count = $index;
    $deleted = 0;
    for (; $count < $total; $count++) {
      if (is_null($this->tags[$count])) {
        continue;
      }

      unset($this->tags[$count]);
      $this->tags[$count] = NULL;
      $deleted++;
    }
    unset($count);

    if ($deleted == $total) {
      $this->tags = array();
      $this->tags_length = 0;
    }

    if ($deleted == 0) {
      unset($deleted);
      return new c_base_return_false();
    }

    return c_base_return_int::s_new($deleted);
  }

  /**
   * Determine if text has been added to this object.
   *
   * @return c_base_return_status
   *   TRUE is returned if tags have been added, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function has_text() {
    if (is_null($this->text)) {
      return new c_base_return_false();
    }

    return new c_base_return_true();
  }

  /**
   * Determine if an attribute has been added to this object.
   *
   * @param string $name
   *   The attribute name to delete.
   *
   * @return c_base_return_status
   *   TRUE is returned if added, FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function has_attribute($name) {
    return $this->pr_has_attribute($name);
  }

  /**
   * Convert this object and all of its children or text to markup.
   *
   * @return c_base_return_string
   *   A string representing this object in the form of HTMl compatible markup.
   */
  public function to_markup() {
    $markup = '<tag';

    if (!is_null($this->tags_length)) {
      $markup .= 's';
    }

    if ($this->type === self::TYPE_TITLE) {
      if (isset($this->attributes['id'])) {
        $markup .= ' id="' . $this->attributes['id'] . '"';
      }
    }
    elseif ($this->type === self::TYPE_HEADING) {
      if (isset($this->attributes['id'])) {
        $markup .= ' id="' . $this->attributes['id'] . '"';
      }

      if (isset($this->attributes['type'])) {
        $markup .= ' type="' . $this->attributes['type'] . '"';
      }

      if (isset($this->attributes['name'])) {
        $markup .= ' name="' . $this->attributes['name'] . '"';
      }
    }
    elseif ($this->type === self::TYPE_FILES) {
      if (isset($this->attributes['id'])) {
        $markup .= ' id="' . $this->attributes['id'] . '"';
      }

      if (isset($this->attributes['type'])) {
        $markup .= ' type="' . $this->attributes['type'] . '"';
      }
    }
    elseif ($this->type === self::TYPE_CONTEXT) {
      if (isset($this->attributes['id'])) {
        $markup .= ' id="' . $this->attributes['id'] . '"';
      }
    }
    elseif ($this->type === self::TYPE_PRESENTATION) {
      if (isset($this->attributes['id'])) {
        $markup .= ' id="' . $this->attributes['id'] . '"';
      }

      if (isset($this->attributes['class'])) {
        $markup .= ' class="' . $this->attributes['class'] . '"';
      }

      if (isset($this->attributes['context'])) {
        $markup .= ' context="' . $this->attributes['context'] . '"';
      }

      if (isset($this->attributes['content'])) {
        $markup .= ' content="' . $this->attributes['content'] . '"';
      }

      if (isset($this->attributes['tooltip'])) {
        $markup .= ' tooltip="' . $this->attributes['tooltip'] . '"';
      }

      if (isset($this->attributes['file'])) {
        $markup .= ' file="' . $this->attributes['file'] . '"';
      }
    }
    elseif ($this->type === self::TYPE_CONTENT) {
      if (isset($this->attributes['id'])) {
        $markup .= ' id="' . $this->attributes['id'] . '"';
      }

      if (isset($this->attributes['context'])) {
        $markup .= ' context="' . $this->attributes['context'] . '"';
      }

      if (isset($this->attributes['file'])) {
        $markup .= ' file="' . $this->attributes['file'] . '"';
      }
    }

    $markup .= '>';

    if (is_null($this->tags_length)) {
      $markup .= $this->text;
      $markup .= '</tag>';
    }
    else {
      foreach ($this->tags as $tag) {
        $markup .= $tag->to_markup()->get_value_exact();
      }

      $markup .= '</tags>';
    }

    return c_base_return_string::s_new($markup);
  }

  /**
   * Converts this object into a DOMElement object.
   *
   * @param c_theme_dom|null $dom_document
   *   (optional) The DOMDocument object to operate with.
   *   If NULL, then the DOMDocument object is auto-generated.
   *
   * @return c_base_return_status|c_theme_return_dom_element
   *   A DOMElement is returned on success.
   *   Otherwise FALSE is returned.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::p_to_dom_element()
   */
  public function to_dom_element($dom_document = NULL) {
    if (is_null($dom_document)) {
      $dom = new DOMDocument();
    }
    else {
      if (!is_object($dom_document) || !($dom_document instanceof DOMDocument)) {
        return c_base_return_error::s_false();
      }

      $dom = $dom_document;
    }

    $element = $this->p_to_dom_element($dom);
    unset($dom);

    if ($element === FALSE) {
      unset($element);
      return new c_base_return_false();
    }

    return c_theme_return_dom_element::s_new($element);
  }

  /**
   * Converts DOMNode to the structure provided by this class.
   *
   * @fixme: update this based on c_theme_markup design.
   *
   * This will delete the entire contents of this object and replace it with the contents of the provided DOMElement object.
   *
   * This assumes that the class follows the rules of this object.
   *
   * @param DOMNode $dom_node
   *   The DOMNode object to load into this class.
   *   This expects the supplied markup to provided tags used by this class (tag, tags, etc..).
   *   All other tags are ignored.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE otherwise.
   *   FALSE with error bit set is returned on error.
   */
  public function from_dom_node($dom_node) {
    if (!is_object($dom_node) || !($dom_node instanceof DOMNode)) {
      return c_base_return_error::s_false();
    }

    // objects are passed via reference in function names, prevent accidents by acting on a clone.
    $node = clone($dom_node);

    // clear the contents of this class.
    $this->__construct();

    if ($this->type === self::TYPE_TAG) {
      // @todo: does this need to be sanitized, or would that result in double-sanitization?
      $this->text = $node->textContent;
    }
    elseif ($this->type === self::TYPE_TAGS && $node->hasChildNodes()) {
      foreach ($node->childNodes as $child_node) {
        if (!($child_node instanceof DOMNode)) {
          continue;
        }

        if ($child_node->nodeName == 'tag') {
          $child_tag = new c_theme_tag();
          if ($child_node->hasAttributes()) {
            foreach ($child_node->attributes as $child_attribute) {
              $child_tag->set_attribute($child_attribute->localName, $child_attribute->nodeValue);
            }
            unset($child_attribute);
          }

          // <tag> only supports text.
          $child_tag->set_text($child_node->textContent);

          $this->set_tag($child_tag);
          unset($child_tag);
        }
        elseif ($child_node->nodeName == 'tags') {
          $child_tag = new c_theme_tag();
          if ($child_node->hasAttributes()) {
            foreach ($child_node->attributes as $child_attribute) {
              $child_tag->set_attribute($child_attribute->localName, $child_attribute->nodeValue);
            }
            unset($child_attribute);
          }

          // <tags> only supports <tags> or <tag>.
          if ($child_node->hasChildNodes()) {
            foreach ($child_node->childNodes as $child_node_child) {
              $child_node_child_tag = new c_theme_tag();
              $result = $child_node_child_tag->from_dom_node($child_node_child);

              if ($result instanceof c_base_return_true) {
                $child_tag->set_tag($child_node_child_tag);
              }

              unset($result);
              unset($child_node_child_tag);
            }
            unset($child_node_child);
          }

          $this->set_tag($child_tag);
          unset($child_tag);
        }
      }
    }

    if ($dom_node->hasAttributes()) {
      foreach ($dom_node->attributes as $node_attribute) {
        $this->set_attribute($node_attribute->localName, $node_attribute->nodeValue);
      }
      unset($node_attribute);
    }

    return new c_base_return_true();
  }

  /**
   * Perform attribute "set" operation.
   *
   * This is provided to reduce duplication of code.
   * It is intended to be called by a public function and will return the expected results of that public function.
   *
   * @param string $attribute_name
   *   The name of the attribute to operate on.
   * @param string|null $attribute_value
   *   A string to use as the attribute_value.
   *
   * @return c_base_return_status
   *   TRUE on success.
   *   FALSE is returned if the attribute cannot be set (the tag type might not support the attribute).
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_set_attribute($attribute_name, $attribute_value) {
    if (!is_string($attribute_name)) {
      return c_base_return_error::s_false();
    }

    // require the non-null string to not be empty.
    // multi-byte is unnecessary here because this is a test not for characters but instead for a non-empty string.
    if (!is_string($attribute_value) || strlen($attribute_value) < 1) {
      return c_base_return_error::s_false();
    }

    $allowed = $this->p_allowed_attribute($attribute_name);
    if (is_null($allowed)) {
      unset($allowed);
      return c_base_return_error::s_false();
    }
    elseif ($allowed) {
      unset($allowed);
    }
    else {
      unset($allowed);
      return new c_base_return_false();
    }

    // attribute name needs sanitization.
    $fixed_name = preg_replace('/[^\w]/', '', $fixed_name);
    if (!is_string($fixed_name)) {
      unset($fixed_name);
      return c_base_return_error::s_false();
    }

    // double quotes are not allowed because the class is designed to wrap attribute values in double quotes.
    $this->attributes[$fixed_name] = str_replace('"', '&quot;', $attribute_value);
    unset($fixed_name);

    return new c_base_return_true();
  }

  /**
   * Perform attribute "get" operation.
   *
   * This is provided to reduce duplication of code.
   * It is intended to be called by a public function and will return the expected results of that public function.
   *
   * @param string $attribute_name
   *   The name of the attribute to operate on.
   *
   * @return c_base_return_status|c_base_return_string|c_base_return_null
   *   The assigned id string.
   *   NULL is returned if the attribute is not set.
   *   FALSE is returned if unable to get the attribute (the tag type might not support the attribute).
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_get_attribute($attribute_name) {
    if (!is_string($attribute_name)) {
      return c_base_return_error::s_false();
    }

    $allowed = $this->p_allowed_attribute($attribute_name);
    if (is_null($allowed)) {
      unset($allowed);
      return c_base_return_error::s_false();
    }
    elseif ($allowed) {
      unset($allowed);
    }
    else {
      unset($allowed);
      return new c_base_return_false();
    }

    if (!array_key_exists($attribute_name, $this->attributes)) {
      return new c_base_return_null();
    }

    return c_base_return_string::s_new($this->attributes[$attribute_name]);
  }

  /**
   * Perform attribute "delete" operation.
   *
   * This assigned the attribute value to NULL.
   *
   * This is provided to reduce duplication of code.
   * It is intended to be called by a public function and will return the expected results of that public function.
   *
   * @param string $attribute_name
   *   The name of the attribute to operate on.
   *
   * @return c_base_return_status
   *   TRUE is returned if the attribute is successfully deleted.
   *   FALSE is returned if the attribute cannot be deleted (the tag type might not support the attribute).
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_delete_attribute($attribute_name) {
    if (!is_string($attribute_name)) {
      return c_base_return_error::s_false();
    }

    $allowed = $this->p_allowed_attribute($attribute_name);
    if (is_null($allowed)) {
      unset($allowed);
      return c_base_return_error::s_false();
    }
    elseif ($allowed) {
      unset($allowed);
    }
    else {
      unset($allowed);
      return new c_base_return_false();
    }

    unset($this->attributes[$attribute_name]);
    return new c_base_return_true();
  }

  /**
   * Perform attribute "has" operation.
   *
   * This is provided to reduce duplication of code.
   * It is intended to be called by a public function and will return the expected results of that public function.
   *
   * @param string $attribute_name
   *   The name of the attribute to operate on.
   *
   * @return c_base_return_status
   *   TRUE if attribute is assigned.
   *   FALSE if the attribute is not assigned.
   *   FALSE with error bit set is returned on error.
   */
  protected function pr_has_attribute($attribute_name) {
    if (!is_string($attribute_name)) {
      return c_base_return_error::s_false();
    }

    $allowed = $this->p_allowed_attribute($attribute_name);
    if (is_null($allowed)) {
      unset($allowed);
      return c_base_return_error::s_false();
    }
    elseif ($allowed) {
      unset($allowed);
    }
    else {
      unset($allowed);
      return new c_base_return_false();
    }

    if (array_key_exists($attribute_name, $this->attributes)) {
      return new c_base_return_true();
    }

    return new c_base_return_false();
  }

  /**
   * Re-calculates the attributes array length.
   *
   * @see: self::sanitize_attributes()
   */
  private function p_sanitize_attributes() {
    $this->attributes_length = count($this->attributes);

    return TRUE;
  }

  /**
   * Check to see if the given attribute name is allowed according to the tag type.
   *
   * @param string $attribute_name
   *   The attribute name to test.
   *
   * @return bool|null
   *   TRUE is returned if allowed.
   *   FALSE is returned if not allowed.
   *   NULL is returned for unknown tag type.
   */
  private function p_allowed_attribute($attribute_name) {
    if ($this->type === self::TYPE_TITLE) {
      switch ($attribute_name) {
        case 'id':
          return TRUE;
        default:
          return FALSE;
      }
    }
    elseif ($this->type === self::TYPE_HEADING) {
      switch ($attribute_name) {
        case 'id':
        case 'type':
        case 'name':
          return TRUE;
        default:
          return FALSE;
      }
    }
    elseif ($this->type === self::TYPE_FILES) {
      switch ($attribute_name) {
        case 'id':
        case 'type':
          return TRUE;
        default:
          return FALSE;
      }
    }
    elseif ($this->type === self::TYPE_CONTEXT) {
      switch ($attribute_name) {
        case 'id':
          return TRUE;
        default:
          return FALSE;
      }
    }
    elseif ($this->type === self::TYPE_PRESENTATION) {
      switch ($attribute_name) {
        case 'id':
        case 'class':
        case 'context':
        case 'content':
        case 'tooltip':
        case 'file':
          return TRUE;
        default:
          return FALSE;
      }
    }
    elseif ($this->type === self::TYPE_CONTENT) {
      switch ($attribute_name) {
        case 'id':
        case 'context':
        case 'file':
          return TRUE;
        default:
          return FALSE;
      }
    }

    return NULL;
  }

  /**
   * Removes all NULL entries from the tags array and re-calculates the length.
   *
   * @param bool
   *   TRUE on success.
   *   FALSE otherwise.
   *
   * @see: self::sanitize_tags();
   */
  private function p_sanitize_tags() {
    if (empty($this->tags)) {
      $this->tags_length = 0;
    }
    else {
      $tags = array();
      $total = 0;
      foreach ($tags as $tag) {
        if (is_null($tag)) {
          continue;
        }

        $tags[$total] = $tag;
        $total++;
      }

      unset($this->tags);
      $this->tags = $tags;
      $this->tags_length = $total;

      unset($tags);
      unset($total);

      if ($this->type === self::TYPE_TAG) {
        $this->type = self::TYPE_TAGS;
      }
    }

    return TRUE;
  }

  /**
   * Ensures that text is consistent with the rules of this class.
   *
   * @see: self::sanitize_text()
   */
  private function p_sanitize_text() {
    if ($this->tags_length > 0) {
      $this->text = NULL;

      if ($this->type === self::TYPE_TAG) {
        $this->type = self::TYPE_TAGS;
      }
    }
    elseif (!is_null($this->text)) {
      if ($this->type !== self::TYPE_TAG) {
        $this->type = self::TYPE_TAG;
      }
    }

    return TRUE;
  }

  /**
   * Converts this object into a DOMElement object.
   *
   * @param c_theme_dom $dom
   *   The DOMDocument object to operate with.
   *
   * @return DOMElement|FALSE
   *   A DOMElement is returned on success.
   *   FALSE is returned on error.
   *
   * @see: self::to_dom_element()
   */
  public function p_to_dom_element($dom) {
    if (is_null($this->tags_length)) {
      $element = $dom->createElement('tag');

      if (!is_null($this->text)) {
        $element->appendChild(new DOMText($this->text));
      }
    }
    else {
      $element = $dom->createElement('tags');

      if ($this->tags_length > 0) {
        foreach ($this->tags as $tag) {
          if (is_null($tag)) {
            continue;
          }

          $child = $tag->to_dom_element($dom);
          if ($child instanceOf c_theme_return_dom_element) {
            $element->appendChild($child->get_value_exact());
          }
          else {
            unset($child);
            unset($tag);
            unset($element);
            return FALSE;
          }
          unset($child);
        }
        unset($tag);
      }
    }

    if ($this->type === self::TYPE_TITLE) {
      if (isset($this->attributes['id'])) {
        $element->setAttribute('id', $this->attributes['id']);
      }
    }
    elseif ($this->type === self::TYPE_HEADING) {
      if (isset($this->attributes['id'])) {
        $element->setAttribute('id', $this->attributes['id']);
      }

      if (isset($this->attributes['type'])) {
        $element->setAttribute('type', $this->attributes['type']);
      }

      if (isset($this->attributes['name'])) {
        $element->setAttribute('name', $this->attributes['name']);
      }
    }
    elseif ($this->type === self::TYPE_FILES) {
      if (isset($this->attributes['id'])) {
        $element->setAttribute('id', $this->attributes['id']);
      }

      if (isset($this->attributes['type'])) {
        $element->setAttribute('type', $this->attributes['type']);
      }
    }
    elseif ($this->type === self::TYPE_CONTEXT) {
      if (isset($this->attributes['id'])) {
        $element->setAttribute('id', $this->attributes['id']);
      }
    }
    elseif ($this->type === self::TYPE_PRESENTATION) {
      if (isset($this->attributes['id'])) {
        $element->setAttribute('id', $this->attributes['id']);
      }

      if (isset($this->attributes['class'])) {
        $element->setAttribute('class', $this->attributes['class']);
      }

      if (isset($this->attributes['context'])) {
        $element->setAttribute('context', $this->attributes['context']);
      }

      if (isset($this->attributes['content'])) {
        $element->setAttribute('content', $this->attributes['content']);
      }

      if (isset($this->attributes['tooltip'])) {
        $element->setAttribute('tooltip', $this->attributes['tooltip']);
      }

      if (isset($this->attributes['file'])) {
        $element->setAttribute('file', $this->attributes['file']);
      }
    }
    elseif ($this->type === self::TYPE_CONTENT) {
      if (isset($this->attributes['id'])) {
        $element->setAttribute('id', $this->attributes['id']);
      }

      if (isset($this->attributes['context'])) {
        $element->setAttribute('context', $this->attributes['context']);
      }

      if (isset($this->attributes['file'])) {
        $element->setAttribute('file', $this->attributes['file']);
      }
    }

    return $element;
  }
}

/**
 * A return class whose value is represented as a __class__.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 */
class c_theme_return_tag extends c_base_return_value {
  use t_base_return_value_exact;

  /**
   * @see: t_base_return_value::p_s_new()
   */
  public static function s_new($value) {
    return self::p_s_new($value, __CLASS__);
  }

  /**
   * @see: t_base_return_value::p_s_value()
   */
  public static function s_value($return) {
    return self::p_s_value($return, __CLASS__);
  }

  /**
   * @see: t_base_return_value_exact::p_s_value_exact()
   */
  public static function s_value_exact($return) {
    return self::p_s_value_exact($return, __CLASS__, '');
  }

  /**
   * Assign the value.
   *
   * @param __class__ $value
   *   Any value so long as it is a __class__.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!$value instanceof c_theme_tag) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return string|null $value
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !($this->value instanceof c_theme_tag)) {
      $this->value = NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return __class__ $value
   *   The value c_theme_dom stored within this class.
   */
  public function get_value_exact() {
    if (!($this->value instanceof c_theme_tag)) {
      $this->value = new c_theme_tag();
    }

    return $this->value;
  }
}

/**
 * A complete context markup language class.
 */
class c_theme_markup {
  private $title        = NULL;
  private $heading      = NULL;
  private $files        = NULL;
  private $context      = NULL;
  private $presentation = NULL;
  private $content      = NULL;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->title = new c_theme_tag();
    $this->title->set_type(c_theme_tag::TYPE_TITLE);

    $this->heading = new c_theme_tag();
    $this->heading->set_type(c_theme_tag::TYPE_HEADING);

    $this->files = new c_theme_tag();
    $this->files->set_type(c_theme_tag::TYPE_FILES);

    $this->context = new c_theme_tag();
    $this->context->set_type(c_theme_tag::TYPE_CONTEXT);

    $this->presentation = new c_theme_tag();
    $this->presentation->set_type(c_theme_tag::TYPE_PRESENTATION);

    $this->content = new c_theme_tag();
    $this->content->set_type(c_theme_tag::TYPE_CONTENT);
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->title);
    unset($this->heading);
    unset($this->files);
    unset($this->context);
    unset($this->presentation);
    unset($this->content);
  }

  /**
   * Assign tags to this object.
   *
   * Tag types must be set prior to this function call.
   *
   * @param c_theme_tag $tag
   *   When a c_theme_tag object, the tag is appended to the end of the tags array.
   * @param int|null $index
   *   When an integer, represents the position within the tag array to assign the tag.
   *   - May not be less than 0 or greater than the length of the array.
   *   When $tag is NULL, this does nothing.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the position the tag was added on success.
   *   When $tag is NULL, TRUE is returned on success.
   *   FALSE is returned if unable to set tag.
   *   FALSE with error bit set is returned on error.
   */
  public function set_tag($tag, $index = NULL) {
    if (!is_object($tag) || !($tag instanceof c_theme_tag)) {
      return c_base_return_error::s_false();
    }

    if (!is_null($index) && !is_int($index)) {
      return c_base_return_error::s_false();
    }

    $type = $tag->get_type();
    if (!($type instanceof c_base_return_int)) {
      return c_base_return_error::s_false();
    }

    $type = $type->get_value_exact();
    if ($type === c_theme_tag::TYPE_TITLE) {
      unset($type);
      return $this->title->set_tag($tag, $index);
    }
    elseif ($type === c_theme_tag::TYPE_HEADING) {
      unset($type);
      return $this->heading->set_tag($tag, $index);
    }
    elseif ($type === c_theme_tag::TYPE_FILES) {
      unset($type);
      return $this->files->set_tag($tag, $index);
    }
    elseif ($type === c_theme_tag::TYPE_CONTEXT) {
      unset($type);
      return $this->context->set_tag($tag, $index);
    }
    elseif ($type === c_theme_tag::TYPE_PRESENTATION) {
      unset($type);
      return $this->presentation->set_tag($tag, $index);
    }
    elseif ($type === c_theme_tag::TYPE_CONTENT) {
      unset($type);
      return $this->content->set_tag($tag, $index);
    }
    unset($type);

    return new c_base_return_false();
  }

  /**
   * Assign multiple tags to this object.
   *
   * Tag types must be set prior to this function call.
   *
   * @param array $tags
   *   An array of __class__ objects.
   *   Each individual item is checked and then appended to the end of the array.
   *   - The array keys will not be preserved.
   *
   * @param int|null $index
   *   When an integer, represents the position within the tag array to insert the tags.
   *   - May not be less than 0 or greater than the length of the array.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the position the tag was added on success.
   *   FALSE is returned if unable to set tags.
   *   FALSE with error bit set is returned on error.
   */
  public function set_tags($tags, $index = NULL) {
    if (!is_array($tags)) {
      return c_base_return_error::s_false();
    }

    if (!is_null($index) && !is_int($index)) {
      return c_base_return_error::s_false();
    }

    if (empty($tags)) {
      return c_base_return_int::s_new(0);
    }

    $total_added = 0;
    foreach ($tags as $tag) {
      if ($tag instanceof c_theme_tag) {
        $type = $tag->get_type();
        if ($type instanceof c_base_return_int) {
          $type = $type->get_value_exact();
          if ($type === c_theme_tag::TYPE_TITLE) {
            $result = $this->title->set_tag($tag, $index);
          }
          elseif ($type === c_theme_tag::TYPE_HEADING) {
            $result = $this->heading->set_tag($tag, $index);
          }
          elseif ($type === c_theme_tag::TYPE_FILES) {
            $result = $this->files->set_tag($tag, $index);
          }
          elseif ($type === c_theme_tag::TYPE_CONTEXT) {
            $result = $this->context->set_tag($tag, $index);
          }
          elseif ($type === c_theme_tag::TYPE_PRESENTATION) {
            $result = $this->presentation->set_tag($tag, $index);
          }
          elseif ($type === c_theme_tag::TYPE_CONTENT) {
            $result = $this->content->set_tag($tag, $index);
          }
          else {
            $result = new c_base_return_false();
          }
        }
        else {
          $result = new c_base_return_false();
        }
      }
      else {
        $result = new c_base_return_false();
      }

      // Don't continue if anything goes wrong.
      if ($result instanceof c_base_return_false) {
        unset($result);
        unset($tag);
        unset($type);
        unset($total_added);
        return c_base_return_error::s_false();
      }

      $total_added++;
    }
    unset($result);
    unset($tag);
    unset($type);

    return c_base_return_int::s_new($total_added);
  }

  /**
   * Get a single tag assigned to this object.
   *
   * @param int $type
   *   The tag type to load the tag from.
   * @param int|null $index
   *   (optional) An integer representing the position within the tag array to return.
   *   When NULL, the tag at the end of the tags array is returned.
   *
   * @return c_base_return_status|c_theme_return_tag|c_base_return_null
   *   A c_theme_return_tag object on success.
   *   NULL might be returned if there is no object assigned to the index.
   *   Otherwise FALSE is returned.
   *   FALSE with error bit set is returned on error.
   *
   * @see: self::get_tags()
   */
  public function get_tag($type, $index = NULL) {
    if (!is_int($type)) {
      return c_base_return_error::s_false();
    }

    if ($type === c_theme_tag::TYPE_TITLE) {
      return $this->title->get_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_HEADING) {
      return $this->heading->get_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_FILES) {
      return $this->files->get_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_CONTEXT) {
      return $this->context->get_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_PRESENTATION) {
      return $this->presentation->get_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_CONTENT) {
      return $this->content->get_tag($index);
    }

    return new c_base_return_false();
  }

  /**
   * Delete tag from this object.
   *
   * Tags not stored at the end of the array are set to NULL instead of being deleted.
   * The length is therefore not shortened unless the deleted tag is at the end of the array.
   * Call $this->sanitize_tags() to ensure that the array structure contains none of these holes.
   *
   * @param int $type
   *   The tag type to load the tag from.
   * @param int|null $index
   *   (optional) When an integer, represents the position of the tag to delete.
   *   - May not be less than 0 or greater than the length of the array.
   *   When $tag is NULL, deletes the tag at the end of the tag array.
   *
   * @return c_base_return_status|c_base_return_int
   *   An integer representing the position the tag was deleted on success.
   *   Otherwise, FALSE is returned.
   *   FALSE without error bit set is returned if the tag at the specified index is already deleted.
   *   FALSE with error bit set is returned on error.
   */
  public function delete_tag($type, $index = NULL) {
    if (!is_int($type)) {
      return c_base_return_error::s_false();
    }

    if ($type === c_theme_tag::TYPE_TITLE) {
      return $this->title->delete_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_HEADING) {
      return $this->heading->delete_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_FILES) {
      return $this->files->delete_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_CONTEXT) {
      return $this->context->delete_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_PRESENTATION) {
      return $this->presentation->delete_tag($index);
    }
    elseif ($type === c_theme_tag::TYPE_CONTENT) {
      return $this->content->delete_tag($index);
    }

    return new c_base_return_false();
  }

  /**
   * Convert this object and all of its children or text to markup.
   *
   * @return c_base_return_string
   *   A string representing this object in the form of HTMl compatible markup.
   */
  public function to_markup() {
    $markup = '<!DOCTYPE cml>';
    $markup .= '<cml>';

    $markup .= '<title>';
    $markup .= $this->title->to_markup()->get_value_exact();
    $markup .= '</title>';

    $markup .= '<heading>';
    $markup .= $this->heading->to_markup()->get_value_exact();
    $markup .= '</heading>';

    $markup .= '<files>';
    $markup .= $this->files->to_markup()->get_value_exact();
    $markup .= '</files>';

    $markup .= '<context>';
    $markup .= $this->context->to_markup()->get_value_exact();
    $markup .= '</context>';

    $markup .= '<presentation>';
    $markup .= $this->presentation->to_markup()->get_value_exact();
    $markup .= '</presentation>';

    $markup .= '<content>';
    $markup .= $this->content->to_markup()->get_value_exact();
    $markup .= '</content>';

    unset($tag_markup);
    $markup .= '</cml>';

    return c_base_return_string::s_new($markup);
  }
}

/**
 * A return class whose value is represented as a __class__.
 *
 * This should be the most commonly used class as it adds some type security over the c_base_return_value class.
 */
class c_theme_return_markup extends c_base_return_value {
  use t_base_return_value_exact;

  /**
   * @see: t_base_return_value::p_s_new()
   */
  public static function s_new($value) {
    return self::p_s_new($value, __CLASS__);
  }

  /**
   * @see: t_base_return_value::p_s_value()
   */
  public static function s_value($return) {
    return self::p_s_value($return, __CLASS__);
  }

  /**
   * @see: t_base_return_value_exact::p_s_value_exact()
   */
  public static function s_value_exact($return) {
    return self::p_s_value_exact($return, __CLASS__, '');
  }

  /**
   * Assign the value.
   *
   * @param __class__ $value
   *   Any value so long as it is a __class__.
   *   NULL is not allowed.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  public function set_value($value) {
    if (!$value instanceof c_theme_markup) {
      return FALSE;
    }

    $this->value = $value;
    return TRUE;
  }

  /**
   * Return the value.
   *
   * @return string|null $value
   *   The value array stored within this class.
   */
  public function get_value() {
    if (!is_null($this->value) && !($this->value instanceof c_theme_markup)) {
      $this->value = NULL;
    }

    return $this->value;
  }

  /**
   * Return the value of the expected type.
   *
   * @return __class__ $value
   *   The value c_theme_dom stored within this class.
   */
  public function get_value_exact() {
    if (!($this->value instanceof c_theme_markup)) {
      $this->value = new c_theme_tag();
    }

    return $this->value;
  }
}
