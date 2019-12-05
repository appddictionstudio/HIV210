<?php
// Template Name: Publications

// Store the publication template location
$template       = 'templates/publications/publication.php';
$template_empty = 'templates/publications/publication_empty.php';

// Setup publication categories
$categories = [
    'comprehensive-plan' => 'Comprehensive Plan',
    'needs-assessment'   => 'Needs Assessment',
    'aam'                => 'Assessment of Administrative Mechanism',
    'soc'                => 'Standard of Care',
    'pcb'                => 'Planning Council Bylaws',
    'daa'                => 'Directives to Administrative Agency',
    'resource-guide'     => 'Resource Guides',
    'drug-formulary'	 => 'Drug Formulary',	
    'dshs-taxonomy'      => 'Department of State Health Services Taxonomy',
    'qmp'                => 'Quality Management Plan',
    'nhsus'              => 'National HIV/AIDS Strategy for the United States',
    'psra'               => 'Priority Setting and Resource Allocation'
];

// Category counter
$cat_num = 1;

// Get all publications from DB
$query = new WP_Query([
    'post_type'      => 'publication',
    'posts_per_page' => -1
]);

$publications = $query->posts;
?>

<section id="reports-page" style="margin-top: 40px; margin-bottom: 40px;">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 text-xs-center">
        <h1 class="text-underline">Publications</h1>
      </div>
    </div>
  </div>

  <?php foreach ($categories as $key => $category) : ?>
    <?php
    if ($cat_num % 2 == 0) {
      $bg_color = '#ffffff';  // If $cat_num is odd, we make the row background white
    } else {
      $bg_color = '#e3e3e3';  // If $cat_num is even, we make the row background a gray color
    }

    $cat_num++;
    ?>

    <div style='background-color:<?php echo $bg_color; ?>;padding-top:10px;padding-bottom:20px;'>
      <div class='row' style='padding-top:15px;background-color:<?php echo $bg_color; ?>;'>
        <div class="col-xs-12 text-xs-center">
          <h3 style='font-size:1.5em;'><?php echo $category; ?></h3>
        </div>
      </div>
      <div class="row" style='background-color:<?php echo $bg_color; ?>;'>
        <?php
        $count = 0; // Keep track of how many agendas are in DB

        foreach ($publications as $publication) {
          // Get the publication category slug
          $slug = get_term($publication->publication_category)->slug;

          if ($slug == $key) {
            include $template;
            $count++;
          }
        }

        if ($count == 0) { // Happens when there are no template files for category
          include $template_empty;
        }
        ?>
      </div>
    </div>
  <?php endforeach; ?>
  
</section>