<?php
  function do_build(&$whole, &$part, $size) {
    while (strlen($part) < $size) {
      $part .= base_convert(mt_rand(), 10, 16);
    }

    do_split($whole, $part, $size);
  }

  function do_split(&$whole, &$part, $size) {
    if (strlen($part) > $size) {
      $pieces = str_split($part, $size);
      $whole .= $pieces[0];
      $part = $pieces[1];
      unset($pieces);
    }
    else {
      $whole .= $part;
      $part = '';
    }
  }

  $guid = '';
  $part = '';
  do_build($guid, $part, 8);
  $guid .= '-';

  do_build($guid, $part, 4);
  $guid .= '-';

  do_build($guid, $part, 4);
  $guid .= '-';

  do_build($guid, $part, 4);
  $guid .= '-';

  do_build($guid, $part, 12);

  print($guid);

  print("\n");
