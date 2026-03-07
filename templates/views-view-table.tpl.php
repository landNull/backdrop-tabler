<?php
/**
 * @file
 * Views table. Uses Tabler's table classes.
 */
?>
<div class="table-responsive">
  <table class="<?php print implode(' ', $classes); ?> table table-vcenter card-table"<?php print backdrop_attributes($attributes); ?>>
    <?php if (!empty($title) || !empty($caption)) : ?>
      <caption><?php print $caption . $title; ?></caption>
    <?php endif; ?>
    <?php if (!empty($header)) : ?>
      <thead>
        <tr>
          <?php foreach ($header as $field => $label): ?>
            <th <?php if ($header_classes[$field]) { print 'class="'. implode(' ', $header_classes[$field]) . '" '; } ?> scope="col">
              <?php print $label; ?>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
    <?php endif; ?>
    <tbody>
      <?php foreach ($rows as $row_count => $row): ?>
        <tr <?php if (!empty($row_classes[$row_count])) { print 'class="' . implode(' ', $row_classes[$row_count]) .'"';  } ?>>
          <?php foreach ($row as $field => $content): ?>
            <td <?php if ($field_classes[$field][$row_count]) { print 'class="'. implode(' ', $field_classes[$field][$row_count]) . '" '; } ?><?php print backdrop_attributes($field_attributes[$field][$row_count]); ?>>
              <?php print $content; ?>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
